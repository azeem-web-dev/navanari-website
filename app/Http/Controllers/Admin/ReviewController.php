<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('product')
            ->when($request->get('status') === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when($request->get('status') === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);

        return back()->with('status', $review->is_approved ? 'Review approved.' : 'Review unpublished.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
