<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShopStatusMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $status = setting('shop_status', 'open');

        if ($status === 'open') {
            return $next($request);
        }

        // Allow auth & admin routes always
        if ($request->is('login', 'logout', 'register', 'admin/*', 'payment/notification')) {
            return $next($request);
        }

        // Admin can still browse but sees a warning banner (via session flash)
        if (auth()->check() && auth()->user()->isAdmin()) {
            $labels = ['maintenance' => 'Maintenance', 'closed' => 'Tutup'];
            $label = $labels[$status] ?? $status;
            session()->flash('shop_status_warning', "Toko sedang dalam mode \"{$label}\". Pengunjung biasa tidak bisa mengakses situs. Ubah di Pengaturan > Pengaturan Toko.");
            return $next($request);
        }

        $message = setting('maintenance_message', 'Toko sedang dalam perbaikan. Silakan kembali lagi nanti.');

        if ($status === 'closed') {
            $message = setting('maintenance_message', 'Toko sedang tutup. Silakan kembali lagi nanti.');
        }

        return response()->view('maintenance', compact('message', 'status'), 503);
    }
}
