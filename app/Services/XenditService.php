<?php

namespace App\Services;

use App\Models\Order;

/**
 * Stub — akan diimplementasi penuh saat provider Xendit diaktifkan.
 */
class XenditService
{
    public static function createTransaction(Order $order): array
    {
        return ['success' => false, 'message' => 'Xendit belum diimplementasi. Segera hadir!'];
    }

    public static function handleNotification(array $payload): array
    {
        return ['success' => false, 'message' => 'Xendit belum diimplementasi.'];
    }
}
