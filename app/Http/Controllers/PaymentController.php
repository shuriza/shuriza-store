<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentWebhookEvent;
use App\Services\PaymentService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $authUser = Auth::user();

        // Admin selalu bisa akses
        if ($authUser instanceof \App\Models\User && $authUser->isAdmin()) {
            return;
        }

        // Jika order milik user yang login
        if ($order->user_id && Auth::check() && $order->user_id === Auth::id()) {
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
        $headers = $request->headers->all();
        $provider = PaymentService::getProvider();
        $identity = PaymentService::resolveNotificationIdentity($provider, $payload, $headers);

        $webhookEvent = $this->captureWebhookEvent($provider, $identity, $payload, $headers);

        if ($webhookEvent && $webhookEvent->status === 'processed') {
            return response()->json(['status' => 'ok', 'duplicate' => true]);
        }

        Log::info('Payment notification received', [
            'provider' => $provider,
            'payload_keys' => array_keys($payload),
            'event_id' => $identity['event_id'],
        ]);

        $result = PaymentService::handleNotification($payload, $headers);

        if (!$result['success']) {
            if ($webhookEvent) {
                $webhookEvent->update([
                    'status' => 'failed',
                    'processed_at' => now(),
                    'error_message' => (string) ($result['message'] ?? 'Notification rejected'),
                    'response_code' => 400,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Notification rejected',
            ], 400);
        }

        if ($webhookEvent) {
            $webhookEvent->update([
                'status' => 'processed',
                'processed_at' => now(),
                'error_message' => null,
                'response_code' => 200,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function captureWebhookEvent(string $provider, array $identity, array $payload, array $headers): ?PaymentWebhookEvent
    {
        $eventId = $identity['event_id'] ?? null;

        if ($eventId) {
            $existing = PaymentWebhookEvent::where('provider', $provider)
                ->where('event_id', $eventId)
                ->first();

            if ($existing) {
                $existing->increment('attempts');
                $existing->update([
                    'payload' => $payload,
                    'headers' => $headers,
                    'payload_hash' => hash('sha256', json_encode($payload)),
                    'order_number' => $identity['order_number'] ?? $existing->order_number,
                ]);

                return $existing->fresh();
            }
        }

        return PaymentWebhookEvent::create([
            'provider' => $provider,
            'event_id' => $eventId,
            'order_number' => $identity['order_number'] ?? null,
            'endpoint' => 'payment.notification',
            'payload' => $payload,
            'headers' => $headers,
            'payload_hash' => hash('sha256', json_encode($payload)),
            'status' => 'received',
            'attempts' => 1,
        ]);
    }
}
