<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order', 'max_discount',
        'usage_limit', 'usage_limit_per_user', 'used_count', 'starts_at', 'expires_at', 'is_active',
        'min_total_items', 'allowed_category_ids', 'campaign_name',
    ];

    protected $casts = [
        'value'        => 'integer',
        'min_order'    => 'integer',
        'max_discount' => 'integer',
        'usage_limit'  => 'integer',
        'usage_limit_per_user' => 'integer',
        'used_count'   => 'integer',
        'min_total_items' => 'integer',
        'allowed_category_ids' => 'array',
        'is_active'    => 'boolean',
        'starts_at'    => 'datetime',
        'expires_at'   => 'datetime',
    ];

    /**
     * Check if coupon is valid for given subtotal.
     */
    public function isValid(int $subtotal = 0, ?Collection $cartItems = null, ?int $userId = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($subtotal < $this->min_order) return false;
        if ($cartItems && $cartItems->sum('quantity') < max(1, $this->min_total_items ?? 1)) return false;
        if ($cartItems && !$this->passesCategoryWhitelist($cartItems)) return false;
        if ($userId && !$this->passesPerUserLimit($userId)) return false;

        return true;
    }

    public function getInvalidReason(int $subtotal = 0, ?Collection $cartItems = null, ?int $userId = null): ?string
    {
        if (!$this->is_active) return 'Kupon sedang nonaktif.';
        if ($this->starts_at && now()->lt($this->starts_at)) return 'Kupon belum mulai berlaku.';
        if ($this->expires_at && now()->gt($this->expires_at)) return 'Kupon sudah kedaluwarsa.';
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return 'Kupon sudah habis digunakan.';
        if ($subtotal < $this->min_order) {
            return 'Minimum order Rp ' . number_format($this->min_order, 0, ',', '.') . ' untuk menggunakan kupon ini.';
        }

        $minItems = max(1, $this->min_total_items ?? 1);
        if ($cartItems && $cartItems->sum('quantity') < $minItems) {
            return "Minimal {$minItems} item untuk memakai kupon ini.";
        }

        if ($cartItems && !$this->passesCategoryWhitelist($cartItems)) {
            return 'Kupon hanya berlaku untuk kategori produk tertentu.';
        }

        if ($userId && !$this->passesPerUserLimit($userId)) {
            return 'Batas penggunaan kupon per akun sudah tercapai.';
        }

        return null;
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

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function passesPerUserLimit(int $userId): bool
    {
        if (!$this->usage_limit_per_user) {
            return true;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->count();

        return $userUsageCount < $this->usage_limit_per_user;
    }

    public function passesCategoryWhitelist(Collection $cartItems): bool
    {
        $allowedCategories = collect($this->allowed_category_ids)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($allowedCategories->isEmpty()) {
            return true;
        }

        return $cartItems->contains(function ($item) use ($allowedCategories) {
            $categoryId = $item->product?->category_id;
            return $categoryId && $allowedCategories->contains((int) $categoryId);
        });
    }
}
