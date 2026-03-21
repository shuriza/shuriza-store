<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Tampilkan halaman checkout.
     */
    public function checkout()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kamu masih kosong!');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->effective_price);
        $discount = 0;
        $coupon = null;
        $couponCode = session('coupon_code');

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                session()->forget('coupon_code');
                $coupon = null;
            }
        }

        $total = $subtotal - $discount;

        return view('checkout', compact('cartItems', 'subtotal', 'total', 'discount', 'coupon'));
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $coupon = Coupon::where('code', strtoupper(trim($request->coupon_code)))->first();

        if (!$coupon) {
            return back()->with('error', 'Kode kupon tidak ditemukan.');
        }

        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->effective_price);

        if (!$coupon->isValid($subtotal)) {
            $message = 'Kupon tidak valid.';
            if ($coupon->min_order > $subtotal) {
                $message = 'Minimum order Rp ' . number_format($coupon->min_order, 0, ',', '.') . ' untuk menggunakan kupon ini.';
            } elseif ($coupon->expires_at && now()->gt($coupon->expires_at)) {
                $message = 'Kupon sudah kedaluwarsa.';
            } elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                $message = 'Kupon sudah habis digunakan.';
            }
            return back()->with('error', $message);
        }

        session(['coupon_code' => $coupon->code]);
        $discount = $coupon->calculateDiscount($subtotal);

        return back()->with('success', "Kupon {$coupon->code} berhasil diterapkan! Diskon: Rp " . number_format($discount, 0, ',', '.'));
    }

    /**
     * Proses order baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string|max:500',
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang kamu kosong, tidak bisa checkout.');
        }

        // Cek minimum order
        $minOrder = (int) setting('min_order_amount', 0);
        if ($minOrder > 0) {
            $cartTotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->effective_price);
            if ($cartTotal < $minOrder) {
                return back()->with('error', 'Minimum order adalah Rp ' . number_format($minOrder, 0, ',', '.') . '. Total belanja kamu Rp ' . number_format($cartTotal, 0, ',', '.') . '.');
            }
        }

        DB::beginTransaction();
        try {
            // Lock & re-check stok di dalam transaksi untuk mencegah race condition
            foreach ($cartItems as $item) {
                $product = \App\Models\Product::lockForUpdate()->find($item->product_id);
                if (!$product || $product->stock < $item->quantity) {
                    DB::rollBack();
                    return back()->with(
                        'error',
                        "Stok {$item->product->name} tidak mencukupi. Tersisa " . ($product->stock ?? 0) . " item."
                    );
                }
                // Simpan instance terkunci untuk decrement nanti
                $item->setRelation('product', $product);
            }

            $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->product->effective_price);

            // Apply coupon
            $couponCode = session('coupon_code');
            $discount = 0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('used_count');
                }
            }
            $total = $subtotal - $discount;

            // Buat order
            $order = Order::create([
                'user_id'         => Auth::id(),
                'order_number'    => Order::generateOrderNumber(),
                'name'            => $request->name,
                'phone'           => $request->phone,
                'email'           => $request->email,
                'total'           => $total,
                'coupon_code'     => $discount > 0 ? $couponCode : null,
                'discount_amount' => $discount,
                'status'          => 'pending',
                'notes'           => $request->notes,
            ]);

            // Buat order items & kurangi stok
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->effective_price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => $item->quantity * $item->product->effective_price,
                ]);

                // Kurangi stok
                $item->product->decrement('stock', $item->quantity);
            }

            // Hapus cart & coupon session
            $this->clearCart();
            session()->forget('coupon_code');

            DB::commit();

            // Kirim email konfirmasi order (non-blocking)
            if ($order->email) {
                try {
                    Mail::to($order->email)->queue(new OrderConfirmation($order->load('items')));
                } catch (\Throwable $e) {
                    // Jangan gagalkan order jika email gagal kirim
                    report($e);
                }
            }

            // Redirect ke payment gateway jika aktif
            if (\App\Services\PaymentService::isEnabled()) {
                return redirect()->route('payment.pay', $order->order_number);
            }

            return redirect()->route('order.success', $order->order_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses order. Silakan coba lagi.');
        }
    }

    /**
     * Halaman sukses setelah order dibuat.
     */
    public function success(string $orderNumber)
    {
        $order = Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Tandai waktu WhatsApp dikirim
        if (! $order->whatsapp_sent_at) {
            $order->update(['whatsapp_sent_at' => now()]);
        }

        return view('order-success', compact('order'));
    }

    /**
     * Redirect ke WhatsApp dengan pesan order.
     */
    public function whatsapp(string $orderNumber)
    {
        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return redirect($order->getWhatsAppUrl());
    }

    /**
     * Riwayat order user.
     */
    public function history(Request $request)
    {
        $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        $status = $request->string('status')->toString();
        $search = $request->string('search')->trim()->toString();

        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->when(in_array($status, $allowedStatuses, true), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('order_number', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('order-history', compact('orders', 'status', 'search'));
    }

    /**
     * Detail order milik user.
     */
    public function show(string $orderNumber)
    {
        $order = Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->when(! Auth::user()?->isAdmin(), function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->firstOrFail();

        return view('order-detail', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function getCartItems()
    {
        $query = CartItem::with(['product.category']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        return $query->whereHas('product', fn($q) => $q->where('is_active', true))->get();
    }

    private function clearCart(): void
    {
        $query = CartItem::query();

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', session()->getId());
        }

        $query->delete();
    }
}
