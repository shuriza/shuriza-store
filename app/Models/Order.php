<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'name',
        'phone',
        'email',
        'total',
        'status',
        'notes',
        'coupon_code',
        'discount_amount',
        'admin_notes',
        'whatsapp_sent_at',
        'payment_method',
        'payment_token',
        'payment_url',
        'payment_gateway_id',
        'paid_at',
    ];

    protected $casts = [
        'total'             => 'integer',
        'discount_amount'   => 'integer',
        'whatsapp_sent_at'  => 'datetime',
        'paid_at'           => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Menunggu',
            'processing' => 'Diproses',
            'completed'  => 'Selesai',
            'cancelled'  => 'Dibatalkan',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'completed'  => 'green',
            'cancelled'  => 'red',
            default      => 'gray',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'pending'    => 'bg-yellow-100 text-yellow-800',
            'processing' => 'bg-blue-100 text-blue-800',
            'completed'  => 'bg-green-100 text-green-800',
            'cancelled'  => 'bg-red-100 text-red-800',
        ];

        $class = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';

        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class . '">'
            . $this->status_label
            . '</span>';
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Non-cancelled orders (pending, processing, completed).
     */
    public function scopeNotCancelled($query)
    {
        return $query->whereIn('status', ['pending', 'processing', 'completed']);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'SHR-' . strtoupper(now()->format('ymd')) . '-' . strtoupper(substr(uniqid(), -5));
        } while (self::where('order_number', $number)->exists());

        return $number;
    }

    public function buildWhatsAppMessage(): string
    {
        $template = setting('order_wa_template', '');

        // Jika ada template custom dari admin, gunakan itu
        if (!empty($template)) {
            $items = $this->items->map(function ($item) {
                return "• {$item->product_name} x{$item->quantity} = Rp " . number_format($item->subtotal, 0, ',', '.');
            })->join("\n");

            return str_replace(
                ['{order_number}', '{name}', '{phone}', '{email}', '{total}', '{items}', '{notes}', '{store_name}'],
                [
                    $this->order_number,
                    $this->name,
                    $this->phone,
                    $this->email ?? '-',
                    $this->formatted_total,
                    $items,
                    $this->notes ?? '-',
                    setting('store_name', 'Shuriza Store'),
                ],
                $template
            );
        }

        // Fallback: format bawaan
        $items = $this->items->map(function ($item) {
            return "- {$item->product_name} x{$item->quantity} = " . 'Rp ' . number_format($item->subtotal, 0, ',', '.');
        })->join("\n");

        $storeName = setting('store_name', 'Shuriza Store');
        $message = "*Order Baru - {$storeName}*\n\n";
        $message .= "*No. Order:* {$this->order_number}\n";
        $message .= "*Nama:* {$this->name}\n";
        $message .= "*HP:* {$this->phone}\n";
        if ($this->email) {
            $message .= "*Email:* {$this->email}\n";
        }
        $message .= "\n*Detail Pesanan:*\n{$items}\n\n";
        $message .= "*Total: {$this->formatted_total}*\n";
        if ($this->notes) {
            $message .= "\n*Catatan:* {$this->notes}";
        }

        return $message;
    }

    public function getWhatsAppUrl(): string
    {
        $adminPhone = setting('whatsapp_number');
        $message    = rawurlencode($this->buildWhatsAppMessage());

        return "https://wa.me/{$adminPhone}?text={$message}";
    }
}
