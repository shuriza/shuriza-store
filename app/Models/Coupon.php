<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order', 'max_discount',
        'usage_limit', 'used_count', 'usage_per_user', 'first_order_only',
        'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value'            => 'integer',
        'min_order'        => 'integer',
        'max_discount'     => 'integer',
        'usage_limit'      => 'integer',
        'used_count'       => 'integer',
        'usage_per_user'   => 'integer',
        'first_order_only' => 'boolean',
        'is_active'        => 'boolean',
        'starts_at'        => 'datetime',
        'expires_at'       => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ─── Validation ───────────────────────────────────────────────────────────

    /**
     * Check if coupon is valid for given subtotal.
     *
     * @param  int         $subtotal
     * @param  int|null    $userId        Authenticated user ID (if any)
     * @param  string|null $userEmail     Customer email (for guest dedup)
     * @param  bool        $isFirstOrder  Whether this is the customer's first order
     */
    public function isValid(int $subtotal = 0, ?int $userId = null, ?string $userEmail = null, bool $isFirstOrder = true): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($subtotal < $this->min_order) return false;

        // First-order-only rule
        if ($this->first_order_only && !$isFirstOrder) return false;

        // Per-user usage limit
        if ($this->usage_per_user) {
            $count = $this->getUserUsageCount($userId, $userEmail);
            if ($count >= $this->usage_per_user) return false;
        }

        return true;
    }

    /**
     * Count how many times this coupon has been used by a specific user/email.
     */
    public function getUserUsageCount(?int $userId, ?string $userEmail): int
    {
        $query = $this->usages();

        if ($userId) {
            return $query->where('user_id', $userId)->count();
        }

        if ($userEmail) {
            return $query->where('user_email', $userEmail)->count();
        }

        return 0;
    }

    /**
     * Calculate discount amount for given subtotal.
     * Caller must ensure the coupon is valid before calling this method.
     * Use isValid() to check eligibility prior to calling calculateDiscount().
     */
    public function calculateDiscount(int $subtotal): int
    {
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
