<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     * Falls back to config('app.{key}') then to $default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $value = Setting::get($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        } catch (\Throwable $e) {
            // Table may not exist yet during initial setup
        }

        // Fallback to config
        $configKey = 'app.' . $key;
        return config($configKey, $default);
    }
}

if (!function_exists('censor_phone')) {
    /**
     * Sensor nomor telepon untuk ditampilkan publik.
     * Input: 081775093906 → Output: 0817****3906
     * Input: 6281775093906 → Output: 6281****3906
     */
    function censor_phone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $len = strlen($phone);

        if ($len < 8) {
            // Terlalu pendek, sensor sebagian
            return substr($phone, 0, 3) . '****';
        }

        // Tampilkan 4 digit awal + **** + 4 digit akhir
        return substr($phone, 0, 4) . '****' . substr($phone, -4);
    }
}

if (!function_exists('format_phone_display')) {
    /**
     * Format nomor telepon untuk ditampilkan.
     * Input: 6281234567890 → Output: +62 812-3456-7890
     */
    function format_phone_display(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '62') && strlen($phone) >= 11) {
            $rest = substr($phone, 2);
            return '+62 ' . substr($rest, 0, 3) . '-' . substr($rest, 3, 4) . '-' . substr($rest, 7);
        }

        return $phone;
    }
}
