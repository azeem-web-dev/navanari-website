<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'category', 'approvedReviews', 'variants']);
        $product->increment('views');

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->with('images', 'variants')
            ->inRandomOrder()
            ->take(4)
            ->get();

        if ($related->count() < 4) {
            $related = Product::active()->where('id', '!=', $product->id)
                ->with('images', 'variants')->latest()->take(4)->get();
        }

        return view('storefront.product', compact('product', 'related'));
    }
}
