<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:80'],
            'author_email' => ['nullable', 'email', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:1500'],
        ]);

        $data['is_approved'] = false; // Awaits admin moderation.
        $product->reviews()->create($data);

        return back()->with('status', 'Thank you! Your review has been submitted and will appear once approved.');
    }
}
