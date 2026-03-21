<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    /**
     * Dapatkan base URL sesuai mode (sandbox/production).
     */
    private static function getBaseUrl(): string
    {
        $isProduction = setting('midtrans_is_production', '0') === '1';
        return $isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    private static function getApiUrl(): string
    {
        $isProduction = setting('midtrans_is_production', '0') === '1';
        return $isProduction
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    private static function getSnapUrl(): string
    {
        $isProduction = setting('midtrans_is_production', '0') === '1';
        return $isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public static function getClientKey(): string
    {
        return setting('midtrans_client_key', '');
    }

    public static function getSnapJsUrl(): string
    {
        return static::getSnapUrl();
    }

    private static function getServerKey(): string
    {
        return setting('midtrans_server_key', '');
    }

    /**
     * Buat Snap transaction token.
     */
    public static function createTransaction(Order $order): array
    {
        $serverKey = static::getServerKey();

        if (empty($serverKey)) {
            return ['success' => false, 'message' => 'Server key Midtrans belum dikonfigurasi.'];
        }

        $items = $order->items->map(fn($item) => [
            'id'       => (string) $item->product_id,
            'price'    => (int) $item->price,
            'quantity' => $item->quantity,
            'name'     => mb_substr($item->product_name, 0, 50),
        ])->toArray();

        // Tambah item diskon jika ada
        if ($order->discount_amount > 0) {
            $items[] = [
                'id'       => 'DISCOUNT',
                'price'    => -1 * (int) $order->discount_amount,
                'quantity' => 1,
                'name'     => 'Diskon Kupon ' . ($order->coupon_code ?? ''),
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $order->name,
                'email'      => $order->email ?? '',
                'phone'      => $order->phone,
            ],
            'callbacks' => [
                'finish' => route('payment.finish', $order->order_number),
            ],
        ];

        try {
            $snapResponse = Http::withBasicAuth($serverKey, '')
                ->post(static::getApiUrl() . '/snap/v1/transactions', $payload);

            if ($snapResponse->successful()) {
                $data = $snapResponse->json();

                $order->update([
                    'payment_token'      => $data['token'] ?? null,
                    'payment_url'        => $data['redirect_url'] ?? null,
                    'payment_method'     => 'midtrans',
                ]);

                return [
                    'success'      => true,
                    'token'        => $data['token'] ?? null,
                    'redirect_url' => $data['redirect_url'] ?? null,
                ];
            }

            Log::error('Midtrans Snap error', [
                'status' => $snapResponse->status(),
                'body'   => $snapResponse->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat transaksi pembayaran: ' . ($snapResponse->json('error_messages.0') ?? $snapResponse->body()),
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal terhubung ke Midtrans: ' . $e->getMessage()];
        }
    }

    /**
     * Handle notification callback dari Midtrans.
     */
    public static function handleNotification(array $payload): array
    {
        $serverKey = static::getServerKey();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? 'accept';

        // Verify signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($signatureKey !== $expectedSignature) {
            Log::warning('Midtrans invalid signature', compact('orderId', 'signatureKey', 'expectedSignature'));
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $order = Order::where('order_number', $orderId)->first();
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }

        $order->update([
            'payment_gateway_id' => $payload['transaction_id'] ?? null,
        ]);

        // Map status
        if ($transactionStatus === 'capture' || $transactionStatus === 'settlement') {
            if ($fraudStatus === 'accept') {
                $order->update([
                    'status'  => 'processing',
                    'paid_at' => now(),
                ]);

                // Kirim notifikasi
                if ($order->user_id) {
                    try {
                        \App\Models\Notification::orderStatusChanged($order, 'pending');
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            if ($order->status === 'pending') {
                $order->update(['status' => 'cancelled']);

                // Kembalikan stok
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }
        }
        // pending: do nothing, order stays pending

        return ['success' => true, 'order' => $order];
    }
}
