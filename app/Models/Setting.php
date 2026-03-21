<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'label'];

    /**
     * Get a setting value by key, with optional fallback.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('app_settings');
    }

    /**
     * Get all settings grouped by group name.
     */
    public static function allGrouped(): array
    {
        return static::orderBy('group')->orderBy('id')->get()->groupBy('group')->toArray();
    }

    /**
     * Get boolean value.
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $val = static::get($key);
        if ($val === null) return $default;
        return in_array($val, ['1', 'true', 'yes', true, 1], true);
    }
}
