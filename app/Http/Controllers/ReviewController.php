<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Simpan review produk.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this product
        $existing = Review::where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]);
            $message = 'Review berhasil diperbarui!';
        } else {
            Review::create([
                'product_id' => $product->id,
                'user_id'    => Auth::id(),
                'rating'     => $request->rating,
                'comment'    => $request->comment,
            ]);
            $message = 'Review berhasil ditambahkan!';
        }

        return back()->with('success', $message);
    }

    /**
     * Hapus review.
     */
    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }

        $review->delete();
        return back()->with('success', 'Review berhasil dihapus.');
    }
}
