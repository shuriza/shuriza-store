<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Article;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Cache::remember('home_categories', 600, function () {
            return Category::active()->ordered()->withCount(['products' => function ($q) {
                $q->where('is_active', true);
            }])->get();
        });

        $query = Product::with('category')->active();

        // Filter by category
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->byCategory($request->kategori);
        }

        // Search
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        // Hide out of stock
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sort
        $query->sorted($request->get('sort', 'default'));

        $products = $query->paginate((int) setting('products_per_page', 12))->withQueryString();

        // Popular products for hero/featured
        $popularProducts = Cache::remember('home_popular', 600, function () {
            return Product::with('category')
                ->active()
                ->popular()
                ->inStock()
                ->limit(4)
                ->get();
        });

        // Stats (cached 10 menit)
        $stats = Cache::remember('home_stats', 600, function () {
            return [
                'products'   => Product::active()->count(),
                'categories' => Category::active()->count(),
                'orders'     => \App\Models\Order::whereIn('status', ['processing', 'completed'])->count(),
            ];
        });

        // Banners
        $banners = Cache::remember('home_banners', 600, function () {
            return Banner::active()->ordered()->get();
        });

        // Latest articles
        $latestArticles = Article::latestPublished()->limit(3)->get();

        // Flash sale products
        $flashSaleProducts = Product::with('category')
            ->active()
            ->flashSale()
            ->inStock()
            ->limit(8)
            ->get();

        // Testimoni dinamis dari review (approved, rating >= 4)
        $testimonials = Review::with(['user', 'product'])
            ->approved()
            ->where('rating', '>=', 4)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->latest()
            ->limit(6)
            ->get();

        return view('home', compact(
            'categories',
            'products',
            'popularProducts',
            'stats',
            'banners',
            'latestArticles',
            'flashSaleProducts',
            'testimonials',
        ));
    }

    /**
     * API: Recent orders untuk social proof popup.
     */
    public function recentOrders()
    {
        $orders = Cache::remember('social_proof_orders', 300, function () {
            return \App\Models\Order::with('items')
                ->whereIn('status', ['processing', 'completed'])
                ->where('created_at', '>=', now()->subDays(7))
                ->latest()
                ->limit(10)
                ->get()
                ->map(function ($order) {
                    $firstItem = $order->items->first();
                    // Anonimkan nama: "Andi Surya" -> "Andi S."
                    $nameParts = explode(' ', $order->name);
                    $anonName = $nameParts[0];
                    if (count($nameParts) > 1) {
                        $anonName .= ' ' . strtoupper(substr($nameParts[1], 0, 1)) . '.';
                    }
                    return [
                        'name'    => $anonName,
                        'product' => $firstItem?->product_name ?? 'Produk Digital',
                        'time'    => $order->created_at->diffForHumans(),
                    ];
                });
        });

        return response()->json($orders);
    }

    public function search(Request $request)
    {
        $keyword = substr($request->get('q', ''), 0, 100);

        if (strlen($keyword) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $results = collect();

        // Cek apakah keyword adalah order number (format SHR-XXXXXX-XXXXX)
        if (preg_match('/^SHR-/i', $keyword)) {
            $order = \App\Models\Order::where('order_number', 'like', "%{$keyword}%")
                ->limit(3)
                ->get()
                ->map(fn($o) => [
                    'type'     => 'order',
                    'name'     => "Order #{$o->order_number}",
                    'subtitle' => $o->status_label . ' · ' . $o->formatted_total,
                    'icon'     => 'fas fa-receipt',
                    'color'    => '#6c63ff',
                    'url'      => route('order.track') . '?order=' . $o->order_number,
                ]);
            $results = $results->concat($order);
        }

        // Cari produk
        $products = Product::with('category')
            ->active()
            ->search($keyword)
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'type'          => 'product',
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'price'         => $p->formatted_price,
                'subtitle'      => ($p->category?->name ?? '') . ($p->badge_label ? ' · ' . $p->badge_label : '') . (!$p->is_in_stock ? ' · Stok Habis' : ''),
                'category'      => $p->category?->name,
                'icon'          => $p->category?->icon ?? 'fas fa-box',
                'color'         => $p->category?->color ?? '#6c63ff',
                'badge'         => $p->badge_label,
                'is_in_stock'   => $p->is_in_stock,
                'url'           => route('products.show', $p->slug),
            ]);

        $results = $results->concat($products);

        return response()->json([
            'results' => $results->values(),
            'total'   => $results->count(),
            'keyword' => $keyword,
        ]);
    }
}
