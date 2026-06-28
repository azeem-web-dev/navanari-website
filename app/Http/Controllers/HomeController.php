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

        $featuredProducts = Product::active()->featured()->with('images')->latest()->take(8)->get();
        $newArrivals = Product::active()->with('images')->latest()->take(8)->get();

        $topReviews = Review::approved()->with('product')->where('rating', '>=', 5)->latest()->take(6)->get();

        $stats = [
            'products' => Product::active()->count(),
            'categories' => Category::active()->count(),
            'happy' => max(500, Product::sum('views')),
        ];

        return view('storefront.home', compact(
            'heroSlides', 'stripPromo', 'featuredCategories',
            'featuredProducts', 'newArrivals', 'topReviews', 'stats'
        ));
    }
}
