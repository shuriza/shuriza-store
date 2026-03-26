<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private static function getApiUrl(): string
    {
        return 'https://api.xendit.co';
    }

    private static function getSecretKey(): string
    {
        return setting('xendit_secret_key', '');
    }

    private static function getCallbackToken(): string
    {
        return setting('xendit_callback_token', '');
    }

    public static function createTransaction(Order $order): array
    {
        $secretKey = static::getSecretKey();

        if (empty($secretKey)) {
            return ['success' => false, 'message' => 'Secret key Xendit belum dikonfigurasi.'];
        }

        $description = 'Pembayaran order ' . $order->order_number . ' - ' . setting('store_name', 'Shuriza Store');

        $payload = [
            'external_id' => $order->order_number,
            'amount' => (int) $order->total,
            'description' => mb_substr($description, 0, 200),
            'currency' => 'IDR',
            'invoice_duration' => 24 * 60 * 60,
            'success_redirect_url' => route('payment.finish', $order->order_number),
            'failure_redirect_url' => route('order.success', $order->order_number),
            'customer' => [
                'given_names' => $order->name,
                'email' => $order->email,
                'mobile_number' => $order->phone,
            ],
            'customer_notification_preference' => [
                'invoice_created' => ['email'],
                'invoice_reminder' => ['email'],
                'invoice_paid' => ['email'],
            ],
            'metadata' => [
                'order_number' => $order->order_number,
                'store' => setting('store_name', 'Shuriza Store'),
            ],
        ];

        if (empty($order->email)) {
            unset($payload['customer']['email']);
            $payload['customer_notification_preference'] = [
                'invoice_created' => [],
                'invoice_reminder' => [],
                'invoice_paid' => [],
            ];
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->post(static::getApiUrl() . '/v2/invoices', $payload);

            if ($response->successful()) {
                $data = $response->json();

                $order->update([
                    'payment_token' => $data['id'] ?? null,
                    'payment_url' => $data['invoice_url'] ?? null,
                    'payment_method' => 'xendit',
                ]);

                return [
                    'success' => true,
                    'token' => $data['id'] ?? null,
                    'redirect_url' => $data['invoice_url'] ?? null,
                ];
            }

            Log::error('Xendit create invoice error', [
                'status' => $response->status(),
                'body' => $response->json() ?: $response->body(),
                'order_number' => $order->order_number,
            ]);

            return [
                'success' => false,
                'message' => 'Gagal membuat invoice Xendit: ' . ($response->json('message') ?? $response->body()),
            ];
        } catch (\Throwable $e) {
            Log::error('Xendit exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gagal terhubung ke Xendit: ' . $e->getMessage()];
        }
    }

    public static function handleNotification(array $payload, array $headers = []): array
    {
        $callbackToken = static::getCallbackToken();

        if (!empty($callbackToken)) {
            $headerValue = $headers['x-callback-token'][0] ?? null;

            if (!$headerValue || !hash_equals($callbackToken, (string) $headerValue)) {
                Log::warning('Xendit invalid callback token', [
                    'provided' => $headerValue ? 'present' : 'missing',
                ]);
                return ['success' => false, 'message' => 'Invalid callback token'];
            }
        }

        $orderNumber = $payload['external_id'] ?? null;
        $invoiceId = $payload['id'] ?? null;
        $status = strtoupper((string) ($payload['status'] ?? ''));
        $amount = (int) round((float) ($payload['paid_amount'] ?? $payload['amount'] ?? 0));

        if (!$orderNumber || !$status) {
            return ['success' => false, 'message' => 'Payload tidak lengkap'];
        }

        $order = Order::with('items.product')->where('order_number', $orderNumber)->first();
        if (!$order) {
            return ['success' => false, 'message' => 'Order not found'];
        }

        if ($amount > 0 && $amount !== (int) $order->total) {
            Log::warning('Xendit amount mismatch', [
                'order_number' => $order->order_number,
                'expected' => (int) $order->total,
                'received' => $amount,
            ]);
            return ['success' => false, 'message' => 'Invalid amount'];
        }

        if ($invoiceId && $order->payment_gateway_id === $invoiceId && $order->paid_at && in_array($status, ['PAID', 'SETTLED'], true)) {
            return ['success' => true, 'order' => $order, 'idempotent' => true];
        }

        if ($invoiceId) {
            $order->update(['payment_gateway_id' => $invoiceId]);
        }

        if (in_array($status, ['PAID', 'SETTLED'], true)) {
            if (!$order->paid_at) {
                $oldStatus = $order->status;
                $order->update([
                    'status' => 'processing',
                    'paid_at' => now(),
                ]);

                if ($order->user_id) {
                    try {
                        \App\Models\Notification::orderStatusChanged($order, $oldStatus);
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }
            }
        } elseif (in_array($status, ['EXPIRED', 'FAILED'], true)) {
            if ($order->status === 'pending') {
                $order->update(['status' => 'cancelled']);

                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }
        }

        return ['success' => true, 'order' => $order->fresh()];
    }
}
