<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user'])->latest();

        if ($request->filled('status')) {
            $query->where('is_approved', $request->status === 'approved');
        }

        $reviews = $query->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleApproval(Review $review)
    {
        $review->update(['is_approved' => !$review->is_approved]);

        $status = $review->is_approved ? 'disetujui' : 'disembunyikan';

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => "Review {$status}.", 'is_approved' => $review->is_approved]);
        }

        return back()->with('success', "Review {$status}.");
    }

    public function destroy(Review $review)
    {
        $review->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Review berhasil dihapus.']);
        }

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
