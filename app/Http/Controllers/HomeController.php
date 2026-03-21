<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::active()->ordered()->withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

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
        $popularProducts = Product::with('category')
            ->active()
            ->popular()
            ->inStock()
            ->limit(4)
            ->get();

        // Stats
        $stats = [
            'products'   => Product::active()->count(),
            'categories' => Category::active()->count(),
            'orders'     => \App\Models\Order::count(),
        ];

        // Banners
        $banners = Banner::active()->ordered()->get();

        // Latest articles
        $latestArticles = Article::latestPublished()->limit(3)->get();

        // Flash sale products
        $flashSaleProducts = Product::with('category')
            ->active()
            ->flashSale()
            ->inStock()
            ->limit(8)
            ->get();

        return view('home', compact(
            'categories',
            'products',
            'popularProducts',
            'stats',
            'banners',
            'latestArticles',
            'flashSaleProducts',
        ));
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q', '');

        if (strlen($keyword) < 2) {
            return response()->json(['results' => [], 'total' => 0]);
        }

        $products = Product::with('category')
            ->active()
            ->search($keyword)
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'price'         => $p->formatted_price,
                'category'      => $p->category?->name,
                'icon'          => $p->category?->icon ?? 'fas fa-box',
                'color'         => $p->category?->color ?? '#6c63ff',
                'badge'         => $p->badge_label,
                'is_in_stock'   => $p->is_in_stock,
                'url'           => route('product.show', $p->slug),
            ]);

        return response()->json([
            'results' => $products,
            'total'   => $products->count(),
            'keyword' => $keyword,
        ]);
    }
}
