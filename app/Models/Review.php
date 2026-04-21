<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'is_approved'];

    protected $casts = [
        'rating'      => 'integer',
        'is_approved' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeLatest($query)
    {
        return $query->orderByDesc('created_at');
    }

    // ─── Accessor: nama reviewer berdasarkan setting admin ───────────────

    /**
     * Nama yang ditampilkan di ulasan.
     * Mode: 'name' = nama user, 'phone' = nomor WA disensor, 'anonymous' = "Pelanggan"
     */
    public function getReviewerDisplayNameAttribute(): string
    {
        $mode = setting('review_display_mode', 'name');

        return match ($mode) {
            'phone' => $this->getPhoneDisplay(),
            'anonymous' => 'Pelanggan',
            default => $this->user?->name ?? 'Pelanggan', // 'name'
        };
    }

    /**
     * Inisial untuk avatar berdasarkan mode.
     */
    public function getReviewerInitialAttribute(): string
    {
        $mode = setting('review_display_mode', 'name');

        return match ($mode) {
            'phone' => '#',
            'anonymous' => 'P',
            default => strtoupper(substr($this->user?->name ?? 'P', 0, 1)),
        };
    }

    /**
     * Ambil nomor WA user yang disensor.
     */
    private function getPhoneDisplay(): string
    {
        $phone = $this->user?->phone;

        if (!$phone) {
            // Fallback ke nama jika user tidak punya nomor HP
            return $this->user?->name ?? 'Pelanggan';
        }

        return censor_phone($phone);
    }
}
