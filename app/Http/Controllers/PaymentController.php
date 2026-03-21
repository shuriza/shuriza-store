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

        // Pastikan order milik user atau guest
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

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
