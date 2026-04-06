<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'quantity',
        'price_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute(): int
    {
        // Gunakan price_snapshot jika ada, jika tidak gunakan harga produk saat ini
        $price = $this->price_snapshot ?? ($this->product ? $this->product->effective_price : 0);
        return $this->quantity * $price;
    }

    /**
     * Harga efektif item ini (snapshot atau current price)
     */
    public function getEffectivePriceAttribute(): int
    {
        return $this->price_snapshot ?? ($this->product ? $this->product->effective_price : 0);
    }

    /**
     * Format harga efektif
     */
    public function getFormattedEffectivePriceAttribute(): string
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}
