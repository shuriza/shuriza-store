<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order', 'max_discount',
        'usage_limit', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value'        => 'integer',
        'min_order'    => 'integer',
        'max_discount' => 'integer',
        'usage_limit'  => 'integer',
        'used_count'   => 'integer',
        'is_active'    => 'boolean',
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
    ];

    /**
     * Check if coupon is valid for given subtotal.
     */
    public function isValid(int $subtotal = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($subtotal < $this->min_order) return false;

        return true;
    }

    /**
     * Calculate discount amount for given subtotal.
     */
    public function calculateDiscount(int $subtotal): int
    {
        if (!$this->isValid($subtotal)) return 0;

        if ($this->type === 'percent') {
            $discount = (int) round($subtotal * $this->value / 100);
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            return $discount;
        }

        // Fixed discount (cannot exceed subtotal)
        return min($this->value, $subtotal);
    }

    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percent') {
            return $this->value . '%';
        }
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) return 'Nonaktif';
        if ($this->expires_at && now()->gt($this->expires_at)) return 'Kedaluwarsa';
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return 'Habis';
        return 'Aktif';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }
}
