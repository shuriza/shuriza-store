<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar semua order.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Stats untuk header cards
        $stats = [
            'total'      => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed'  => Order::where('status', 'completed')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
            'revenue'    => Order::where('status', 'completed')->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Tampilkan detail order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.category']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Jika dibatalkan, kembalikan stok produk
        if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }
        }

        // Jika dipulihkan dari cancelled, kurangi stok lagi
        if ($oldStatus === 'cancelled' && $request->status !== 'cancelled') {
            foreach ($order->items as $item) {
                if ($item->product && $item->product->stock >= $item->quantity) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
        }

        // Kirim email notifikasi perubahan status
        if ($order->email && $oldStatus !== $request->status) {
            try {
                Mail::to($order->email)->queue(new OrderStatusUpdated($order->load('items'), $oldStatus));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Kirim notifikasi in-app
        if ($order->user_id && $oldStatus !== $request->status) {
            try {
                \App\Models\Notification::orderStatusChanged($order, $oldStatus);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Status order #{$order->order_number} diubah ke {$order->status_label}.",
                'status'  => $order->status,
                'label'   => $order->status_label,
            ]);
        }

        return back()->with('success', "Status order #{$order->order_number} berhasil diubah ke \"{$order->status_label}\".");
    }

    /**
     * Update catatan admin pada order.
     */
    public function updateNotes(Request $request, Order $order)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $order->update(['admin_notes' => $request->admin_notes]);

        return back()->with('success', "Catatan admin untuk order #{$order->order_number} berhasil disimpan.");
    }

    /**
     * Hapus order (hanya yang sudah cancelled).
     */
    public function destroy(Order $order)
    {
        if ($order->status !== 'cancelled') {
            return back()->with('error', 'Hanya order berstatus "Dibatalkan" yang bisa dihapus.');
        }

        $orderNumber = $order->order_number;
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', "Order #{$orderNumber} berhasil dihapus.");
    }

    /**
     * Bulk update status pesanan.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        $count = 0;

        foreach ($orders as $order) {
            $oldStatus = $order->status;
            if ($oldStatus === $request->status) continue;

            $order->update(['status' => $request->status]);

            if ($request->status === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            if ($oldStatus === 'cancelled' && $request->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->stock >= $item->quantity) {
                        $item->product->decrement('stock', $item->quantity);
                    }
                }
            }

            $count++;
        }

        $statusLabel = Order::make(['status' => $request->status])->status_label;

        return back()->with('success', "{$count} pesanan berhasil diubah ke \"{$statusLabel}\".");
    }

    /**
     * Redirect ke WhatsApp customer.
     */
    public function contactWhatsApp(Order $order)
    {
        $phone   = preg_replace('/[^0-9]/', '', $order->phone);

        // Normalisasi nomor Indonesia
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (! str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        $storeName = setting('store_name', 'Shuriza Store Kediri');
        $message = "Halo *{$order->name}*, terima kasih sudah order di *{$storeName}* 🛍️\n\n";
        $message .= "Order kamu dengan nomor *{$order->order_number}* sedang kami proses.\n";
        $message .= "Status saat ini: *{$order->status_label}*\n\n";
        $message .= "Ada yang bisa kami bantu? 😊";

        return redirect("https://wa.me/{$phone}?text=" . urlencode($message));
    }

    /**
     * Export orders ke CSV.
     */
    public function export(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('order_number', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $orders = $query->get();

        $filename = 'orders-shuriza-store-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Header CSV
            fputcsv($handle, [
                'No. Order', 'Nama', 'No. HP', 'Email',
                'Total', 'Status', 'Catatan', 'Tanggal',
            ]);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->name,
                    $order->phone,
                    $order->email ?? '-',
                    $order->total,
                    $order->status_label,
                    $order->notes ?? '-',
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Kirim data delivery digital untuk item order.
     */
    public function deliverItem(Request $request, Order $order, \App\Models\OrderItem $item)
    {
        // Validate item belongs to this order
        if ($item->order_id !== $order->id) {
            abort(404, 'Item tidak ditemukan dalam order ini.');
        }

        $request->validate([
            'delivery_type' => 'required|in:account,link,code,other',
            'delivery_data' => 'required|string|max:2000',
        ]);

        $item->update([
            'delivery_type' => $request->delivery_type,
            'delivery_data' => $request->delivery_data,
            'delivered_at'  => now(),
        ]);

        // Kirim notifikasi in-app ke customer
        if ($order->user_id) {
            try {
                \App\Models\Notification::digitalDelivery($order, $item);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', "Data delivery untuk \"{$item->product_name}\" berhasil dikirim.");
    }

    /**
     * Perbarui data delivery yang sudah pernah dikirim.
     */
    public function redeliverItem(Request $request, Order $order, \App\Models\OrderItem $item)
    {
        if ($item->order_id !== $order->id) {
            abort(404, 'Item tidak ditemukan dalam order ini.');
        }

        $request->validate([
            'delivery_type' => 'required|in:account,link,code,other',
            'delivery_data' => 'required|string|max:2000',
        ]);

        $item->update([
            'delivery_type' => $request->delivery_type,
            'delivery_data' => $request->delivery_data,
            'delivered_at'  => now(),
        ]);

        // Kirim ulang notifikasi in-app ke customer
        if ($order->user_id) {
            try {
                \App\Models\Notification::digitalDelivery($order, $item);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', "Data delivery untuk \"{$item->product_name}\" berhasil diperbarui dan dikirim ulang.");
    }
}
