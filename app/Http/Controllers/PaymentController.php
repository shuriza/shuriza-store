<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PaymentService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Halaman pembayaran — tampilkan Snap popup.
     */
    public function pay(string $orderNumber)
    {
        $order = Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->where('status', 'pending')
            ->firstOrFail();

        // Authorization: order harus milik user yang valid
        $this->authorizePaymentAccess($order);

        if (!PaymentService::isEnabled()) {
            return redirect()->route('order.success', $order->order_number)
                ->with('error', 'Payment gateway tidak aktif.');
        }

        // Buat token jika belum ada
        if (!$order->payment_token) {
            $result = PaymentService::createTransaction($order);

            if (!$result['success']) {
                return redirect()->route('order.success', $order->order_number)
                    ->with('error', $result['message'] ?? 'Gagal membuat transaksi pembayaran.');
            }

            $order->refresh();
        }

        $provider = PaymentService::getProvider();
        $snapJsUrl = MidtransService::getSnapJsUrl();
        $clientKey = MidtransService::getClientKey();

        return view('payment', compact('order', 'provider', 'snapJsUrl', 'clientKey'));
    }

    /**
     * Callback setelah pembayaran selesai (redirect dari Snap).
     */
    public function finish(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Authorization: order harus milik user yang valid
        $this->authorizePaymentAccess($order);

        // Cek apakah sudah dibayar
        $order->refresh();

        if ($order->paid_at) {
            return redirect()->route('order.success', $order->order_number)
                ->with('success', 'Pembayaran berhasil! Terima kasih.');
        }

        return redirect()->route('order.success', $order->order_number)
            ->with('info', 'Pembayaran sedang diproses. Status akan diupdate otomatis.');
    }

    /**
     * Authorize payment access for an order.
     * Allows: order owner, guest who just created it (via session), or admin.
     */
    private function authorizePaymentAccess(Order $order): void
    {
        // Admin selalu bisa akses
        if (auth()->check() && auth()->user()->isAdmin()) {
            return;
        }

        // Jika order milik user yang login
        if ($order->user_id && auth()->check() && $order->user_id === auth()->id()) {
            return;
        }

        // Jika guest order, cek session
        if (!$order->user_id) {
            $recentOrderNumbers = session('recent_order_numbers', []);
            if (in_array($order->order_number, $recentOrderNumbers)) {
                return;
            }
        }

        // Tidak authorized
        abort(403, 'Anda tidak memiliki akses untuk membayar order ini.');
    }

    /**
     * Webhook / notification handler dari Midtrans.
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        Log::info('Payment notification received', $payload);

        $result = PaymentService::handleNotification($payload);

        return response()->json(['status' => $result['success'] ? 'ok' : 'error']);
    }
}
