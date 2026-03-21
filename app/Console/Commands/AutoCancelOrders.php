<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AutoCancelOrders extends Command
{
    protected $signature = 'orders:auto-cancel';
    protected $description = 'Otomatis batalkan order pending yang melebihi batas waktu';

    public function handle()
    {
        $days = (int) setting('auto_cancel_days', 0);

        if ($days <= 0) {
            $this->info('Auto-cancel dinonaktifkan (0 hari).');
            return 0;
        }

        $cutoff = now()->subDays($days);

        $orders = Order::where('status', 'pending')
            ->whereNull('paid_at')
            ->where('created_at', '<', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Tidak ada order yang perlu dibatalkan.');
            return 0;
        }

        $count = 0;
        foreach ($orders as $order) {
            // Kembalikan stok
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'notes'  => ($order->notes ? $order->notes . "\n" : '') . "Otomatis dibatalkan setelah {$days} hari belum dibayar.",
            ]);

            $count++;
        }

        $this->info("Berhasil membatalkan {$count} order.");
        return 0;
    }
}
