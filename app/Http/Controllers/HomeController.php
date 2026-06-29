<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $heroSlides = Promotion::active()->where('position', 'hero')->orderBy('sort_order')->get();
        $stripPromo = Promotion::active()->where('position', 'strip')->orderBy('sort_order')->first();

        $featuredCategories = Category::active()->where('is_featured', true)->orderBy('sort_order')->get();

        // Amazon-style scrollable rows: one carousel per category.
        $categoryRows = Category::active()
            ->orderBy('sort_order')
            ->with(['products' => fn ($q) => $q->active()->with('images', 'variants')->latest()->take(12)])
            ->get()
            ->filter(fn ($c) => $c->products->isNotEmpty())
            ->take(4);

        $featuredProducts = Product::active()->featured()->with('images', 'variants')->latest()->take(8)->get();
        $newArrivals = Product::active()->with('images', 'variants')->latest()->take(8)->get();

        $topReviews = Review::approved()->with('product')->where('rating', '>=', 5)->latest()->take(6)->get();

        $stats = [
            'products' => Product::active()->count(),
            'categories' => Category::active()->count(),
            'happy' => max(500, Product::sum('views')),
        ];

        return view('storefront.home', compact(
            'heroSlides', 'stripPromo', 'featuredCategories', 'categoryRows',
            'featuredProducts', 'newArrivals', 'topReviews', 'stats'
        ));
    }
}
