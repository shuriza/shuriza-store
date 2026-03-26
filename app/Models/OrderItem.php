<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
        'delivery_data',
        'delivery_type',
        'delivered_at',
        'delivery_status',
        'delivery_attempts',
        'last_delivery_error',
        'delivery_meta',
    ];

    protected $casts = [
        'price'        => 'integer',
        'subtotal'     => 'integer',
        'quantity'     => 'integer',
        'delivered_at' => 'datetime',
        'delivery_attempts' => 'integer',
        'delivery_meta' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name'  => $this->product_name,
            'price' => $this->price,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getDeliveryStatusLabelAttribute(): string
    {
        return match($this->delivery_status) {
            'delivered' => 'Terkirim',
            'failed'    => 'Gagal',
            default     => 'Menunggu',
        };
    }

    public function getDeliveryTypeLabelAttribute(): string
    {
        return match($this->delivery_type) {
            'account' => 'Akun',
            'link'    => 'Link Download',
            'code'    => 'Kode/Voucher',
            'other'   => 'Lainnya',
            default   => '-',
        };
    }

    public function needsDelivery(): bool
    {
        return $this->delivery_status !== 'delivered';
    }
}
