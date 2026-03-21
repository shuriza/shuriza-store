<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Statistik Utama ──────────────────────────────────────────────────
        $totalProducts   = Product::count();
        $activeProducts  = Product::active()->count();
        $totalCategories = Category::count();
        $totalUsers      = User::where('role', 'user')->count();

        // ── Statistik Order ──────────────────────────────────────────────────
        $totalOrders     = Order::count();
        $pendingOrders   = Order::pending()->count();
        $processingOrders = Order::processing()->count();
        $completedOrders = Order::completed()->count();
        $cancelledOrders = Order::cancelled()->count();

        // ── Pendapatan ───────────────────────────────────────────────────────
        $totalRevenue = Order::completed()->sum('total');
        $monthRevenue = Order::completed()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $todayRevenue = Order::completed()
            ->whereDate('created_at', today())
            ->sum('total');

        // ── Grafik Order 7 Hari Terakhir ──────────────────────────────────────
        $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
            $date   = Carbon::today()->subDays($daysAgo);
            $orders = Order::whereDate('created_at', $date)->count();
            $revenue = Order::completed()->whereDate('created_at', $date)->sum('total');
            return [
                'date'    => $date->format('d M'),
                'orders'  => $orders,
                'revenue' => (float) $revenue,
            ];
        });

        // ── Order Terbaru ─────────────────────────────────────────────────────
        $recentOrders = Order::with('items')
            ->latest()
            ->limit(10)
            ->get();

        // ── Produk Stok Hampir Habis ──────────────────────────────────────────
        $lowStockProducts = Product::with('category')
            ->active()
            ->where('stock', '>', 0)
            ->where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // ── Produk Stok Habis ─────────────────────────────────────────────────
        $outOfStockProducts = Product::active()
            ->where('stock', 0)
            ->count();

        // ── Produk Terlaris ───────────────────────────────────────────────────
        $topProducts = Product::with('category')
            ->withCount(['orderItems as total_sold' => function ($q) {
                $q->whereHas('order', fn($o) => $o->whereIn('status', ['processing', 'completed']));
            }])
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // ── Kategori Terpopuler ───────────────────────────────────────────────
        $topCategories = Category::withCount(['products as total_products' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['products as total_orders' => function ($q) {
                $q->whereHas('orderItems');
            }])
            ->orderBy('total_orders', 'desc')
            ->limit(5)
            ->get();

        // ── User Baru Hari Ini ─────────────────────────────────────────────────
        $newUsersToday = User::where('role', 'user')
            ->whereDate('created_at', today())
            ->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'activeProducts',
            'totalCategories',
            'totalUsers',
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'totalRevenue',
            'monthRevenue',
            'todayRevenue',
            'last7Days',
            'recentOrders',
            'lowStockProducts',
            'outOfStockProducts',
            'topProducts',
            'topCategories',
            'newUsersToday',
        ));
    }
}
