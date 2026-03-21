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
        return setting('payment_gateway_provider', 'midtrans');
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
    public static function handleNotification(array $payload): array
    {
        $provider = static::getProvider();

        return match ($provider) {
            'midtrans' => MidtransService::handleNotification($payload),
            'xendit'   => XenditService::handleNotification($payload),
            default    => ['success' => false],
        };
    }
}
