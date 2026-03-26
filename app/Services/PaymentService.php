<?php

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    /**
     * Cek apakah payment gateway aktif.
     */
    public static function isEnabled(): bool
    {
        return setting('payment_gateway_enabled', '0') === '1';
    }

    /**
     * Dapatkan provider yang aktif.
     */
    public static function getProvider(): string
    {
        $provider = (string) setting('payment_gateway_provider', 'midtrans');

        return in_array($provider, ['midtrans', 'xendit'], true)
            ? $provider
            : 'midtrans';
    }

    /**
     * Resolve provider-specific identity for webhook deduplication.
     *
     * @return array{event_id:?string,order_number:?string}
     */
    public static function resolveNotificationIdentity(string $provider, array $payload, array $headers = []): array
    {
        if ($provider === 'xendit') {
            $eventId = isset($payload['id']) ? (string) $payload['id'] : null;
            $orderNumber = isset($payload['external_id']) ? (string) $payload['external_id'] : null;

            if (!$eventId && $orderNumber) {
                $eventId = $orderNumber . ':' . strtoupper((string) ($payload['status'] ?? ''));
            }

            return [
                'event_id' => $eventId,
                'order_number' => $orderNumber,
            ];
        }

        $transactionId = isset($payload['transaction_id']) ? (string) $payload['transaction_id'] : null;
        $orderNumber = isset($payload['order_id']) ? (string) $payload['order_id'] : null;

        $eventId = $transactionId;
        if (!$eventId && $orderNumber) {
            $eventId = implode(':', array_filter([
                $orderNumber,
                (string) ($payload['status_code'] ?? ''),
                (string) ($payload['transaction_status'] ?? ''),
            ]));
        }

        return [
            'event_id' => $eventId ?: null,
            'order_number' => $orderNumber,
        ];
    }

    /**
     * Buat transaksi pembayaran.
     */
    public static function createTransaction(Order $order): array
    {
        $provider = static::getProvider();

        return match ($provider) {
            'midtrans' => MidtransService::createTransaction($order),
            'xendit'   => XenditService::createTransaction($order),
            default    => ['success' => false, 'message' => "Provider '{$provider}' tidak didukung."],
        };
    }

    /**
     * Handle callback/notification dari payment gateway.
     */
    public static function handleNotification(array $payload, array $headers = []): array
    {
        $provider = static::getProvider();

        return static::handleNotificationForProvider($provider, $payload, $headers);
    }

    public static function handleNotificationForProvider(string $provider, array $payload, array $headers = []): array
    {
        return match ($provider) {
            'midtrans' => MidtransService::handleNotification($payload, $headers),
            'xendit'   => XenditService::handleNotification($payload, $headers),
            default    => ['success' => false],
        };
    }
}
