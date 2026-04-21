<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Hide out of stock
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sort
        $sort = $request->get('sort', 'default');
        $query->sorted($sort);

        $products   = $query->paginate((int) setting('products_per_page', 12))->withQueryString();
        $categories = Category::active()->ordered()->withCount('activeProducts')->get();

        return view('products.index', compact('products', 'categories', 'sort'));
    }

    public function show(Product $product)
    {
        abort_if(! $product->is_active, 404);

        $product->incrementViews();
        $product->load('category');

        $reviews = collect();
        $userReview = null;

        if (setting('review_enabled', '1') === '1') {
            $reviews = $product->approvedReviews()
                ->with('user')
                ->latest()
                ->paginate(10);

            if (auth()->check()) {
                $userReview = $product->reviews()->where('user_id', auth()->id())->first();
            }
        }

        $related = Product::with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'related', 'reviews', 'userReview'));
    }

    /**
     * Halaman promo - semua produk yang sedang diskon/flash sale.
     */
    public function promo(Request $request)
    {
        $query = Product::with('category')->active();

        // Produk yang punya diskon (original_price > price) ATAU sedang flash sale
        $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->whereNotNull('original_price')
                    ->whereColumn('price', '<', 'original_price');
            })->orWhere(function ($sub) {
                $sub->whereNotNull('flash_sale_price')
                    ->where('flash_sale_start', '<=', now())
                    ->where('flash_sale_end', '>=', now());
            });
        });

        $query->sorted($request->get('sort', 'default'));

        $products = $query->paginate(24)->withQueryString();

        // Flash sale products (terpisah untuk section khusus)
        $flashSaleProducts = Product::with('category')
            ->active()
            ->flashSale()
            ->inStock()
            ->limit(8)
            ->get();

        return view('products.promo', compact('products', 'flashSaleProducts'));
    }

    /**
     * Halaman kategori dedicated.
     */
    public function category(Category $category, Request $request)
    {
        abort_if(!$category->is_active, 404);

        $query = Product::with('category')->active()->where('category_id', $category->id);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        $query->sorted($request->get('sort', 'default'));

        $products = $query->paginate((int) setting('products_per_page', 12))->withQueryString();

        // Produk populer di kategori ini
        $popularInCategory = Product::with('category')
            ->active()
            ->where('category_id', $category->id)
            ->popular()
            ->inStock()
            ->limit(4)
            ->get();

        // Kategori lain untuk navigasi
        $otherCategories = Category::active()
            ->ordered()
            ->where('id', '!=', $category->id)
            ->withCount('activeProducts')
            ->get();

        return view('products.category', compact('category', 'products', 'popularInCategory', 'otherCategories'));
    }

    public function search(Request $request)
    {
        $keyword = $request->get('q', '');

        $products = Product::with('category')
            ->active()
            ->search($keyword)
            ->limit(8)
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'products' => $products->map(function ($p) {
                    return [
                        'id'          => $p->id,
                        'name'        => $p->name,
                        'slug'        => $p->slug,
                        'price'       => $p->formatted_price,
                        'category'    => $p->category?->name,
                        'image'       => $p->image_url,
                        'is_in_stock' => $p->is_in_stock,
                        'url'         => route('products.show', $p->slug),
                    ];
                }),
            ]);
        }

        $categories = Category::active()->ordered()->withCount('activeProducts')->get();
        return view('products.index', compact('products', 'categories', 'keyword'));
    }
}
