<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\LowStockAlert;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StockAlertController extends Controller
{
    /**
     * Tampilkan halaman stok menipis.
     */
    public function index(Request $request)
    {
        $threshold = (int) config('app.low_stock_threshold', 5);

        $query = Product::with('category')->active();

        // Filter: low stock or out of stock
        $filter = $request->string('filter', 'all')->toString();
        if ($filter === 'out') {
            $query->where('stock', 0);
        } elseif ($filter === 'low') {
            $query->where('stock', '>', 0)->where('stock', '<=', $threshold);
        } else {
            $query->where('stock', '<=', $threshold);
        }

        // Search
        if ($request->filled('search')) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhereHas('category', fn($c) => $c->where('name', 'like', "%{$keyword}%"));
            });
        }

        $products = $query->orderBy('stock')->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'low_stock'    => Product::active()->where('stock', '>', 0)->where('stock', '<=', $threshold)->count(),
            'out_of_stock' => Product::active()->where('stock', 0)->count(),
            'threshold'    => $threshold,
        ];

        return view('admin.stock-alerts', compact('products', 'stats', 'filter'));
    }

    /**
     * Kirim email alert ke semua admin.
     */
    public function sendAlert()
    {
        $threshold = (int) config('app.low_stock_threshold', 5);

        $products = Product::with('category')
            ->active()
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->get();

        if ($products->isEmpty()) {
            return back()->with('success', 'Semua stok aman, tidak ada alert yang perlu dikirim.');
        }

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new LowStockAlert($products));
        }

        return back()->with('success', "Email alert stok menipis berhasil dikirim ke {$admins->count()} admin.");
    }
}
