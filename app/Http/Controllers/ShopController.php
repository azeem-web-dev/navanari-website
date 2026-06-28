<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['images', 'category']);

        // Keyword search across name, brand and description.
        if ($term = trim((string) $request->get('q'))) {
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('brand', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%");
            });
        }

        // Category filter (by slug).
        $activeCategory = null;
        if ($slug = $request->get('category')) {
            $activeCategory = Category::where('slug', $slug)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        // Price range filter.
        if ($request->filled('min')) {
            $query->where('price', '>=', (float) $request->get('min'));
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', (float) $request->get('max'));
        }

        // On-sale only.
        if ($request->boolean('sale')) {
            $query->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price');
        }

        // Sorting.
        switch ($request->get('sort')) {
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, price) asc');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, price) desc');
                break;
            case 'name':
                $query->orderBy('name');
                break;
            case 'popular':
                $query->orderByDesc('views');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::active()->whereNull('parent_id')->withCount(['products' => fn ($q) => $q->active()])->orderBy('sort_order')->get();

        return view('storefront.shop', compact('products', 'categories', 'activeCategory'));
    }
}
