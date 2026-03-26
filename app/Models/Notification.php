<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'message', 'link', 'icon', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getIsReadAttribute(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Create a notification for order status change.
     */
    public static function orderStatusChanged(Order $order, string $oldStatus): self
    {
        $statusLabels = [
            'pending' => 'Menunggu', 'processing' => 'Diproses',
            'completed' => 'Selesai', 'cancelled' => 'Dibatalkan',
        ];
        $icons = [
            'pending' => 'fas fa-clock text-yellow-400',
            'processing' => 'fas fa-spinner text-blue-400',
            'completed' => 'fas fa-check-circle text-green-400',
            'cancelled' => 'fas fa-times-circle text-red-400',
        ];

        return static::create([
            'user_id' => $order->user_id,
            'type'    => 'order_status',
            'title'   => "Pesanan #{$order->order_number} {$statusLabels[$order->status]}",
            'message' => "Status pesanan berubah dari \"{$statusLabels[$oldStatus]}\" menjadi \"{$statusLabels[$order->status]}\".",
            'link'    => route('order.show', $order->order_number),
            'icon'    => $icons[$order->status] ?? 'fas fa-bell text-peri',
        ]);
    }

    /**
     * Create a notification for digital delivery.
     */
    public static function digitalDelivery(Order $order, OrderItem $item, string $status = 'delivered'): self
    {
        $statusMessage = $status === 'retry'
            ? 'Pengiriman ulang data produk digital berhasil. Cek detail pesanan.'
            : 'Produk digital kamu sudah tersedia. Cek detail pesanan untuk melihat data akun/link.';

        return static::create([
            'user_id' => $order->user_id,
            'type'    => 'delivery',
            'title'   => "Item {$item->product_name} untuk Order #{$order->order_number} Terkirim",
            'message' => $statusMessage,
            'link'    => route('order.show', $order->order_number),
            'icon'    => 'fas fa-gift text-green-400',
        ]);
    }
}
