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

        $reviews = $product->approvedReviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        $userReview = null;
        if (auth()->check()) {
            $userReview = $product->reviews()->where('user_id', auth()->id())->first();
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
