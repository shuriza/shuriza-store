<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Halaman wishlist.
     */
    public function index()
    {
        $wishlistItems = Auth::user()->wishlistedProducts()
            ->with('category')
            ->active()
            ->latest('wishlists.created_at')
            ->paginate(12);

        return view('wishlist', compact('wishlistItems'));
    }

    /**
     * Toggle wishlist (add/remove).
     */
    public function toggle(Request $request, Product $product)
    {
        $user = Auth::user();
        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $added = false;
            $message = 'Produk dihapus dari wishlist.';
        } else {
            Wishlist::create([
                'user_id'    => $user->id,
                'product_id' => $product->id,
            ]);
            $added = true;
            $message = 'Produk ditambahkan ke wishlist!';
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'added'   => $added,
                'message' => $message,
                'count'   => $user->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }
}
