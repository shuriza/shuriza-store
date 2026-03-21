<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ["name", "email", "phone", "password", "role"];

    protected $hidden = ["password", "remember_token"];

    protected function casts(): array
    {
        return [
            "email_verified_at" => "datetime",
            "password" => "hashed",
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === "admin";
    }

    public function isUser(): bool
    {
        return $this->role === "user";
    }

    public function getCartCountAttribute(): int
    {
        return $this->cartItems()->sum("quantity");
    }

    public function getCartTotalAttribute(): int
    {
        return $this->cartItems()
            ->with("product")
            ->get()
            ->sum(fn($item) => $item->quantity * ($item->product?->price ?? 0));
    }

    public function getFormattedCartTotalAttribute(): string
    {
        return "Rp " . number_format($this->cart_total, 0, ",", ".");
    }
}
