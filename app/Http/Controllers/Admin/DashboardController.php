<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'categories' => Category::count(),
            'enquiries' => Enquiry::count(),
            'new_enquiries' => Enquiry::where('status', 'new')->count(),
            'pending_reviews' => Review::where('is_approved', false)->count(),
        ];

        $recentEnquiries = Enquiry::with('product')->latest()->take(6)->get();
        $pendingReviews = Review::with('product')->where('is_approved', false)->latest()->take(5)->get();
        $topProducts = Product::orderByDesc('views')->take(5)->get();

        // Enquiries over the last 7 days for the mini chart.
        $chart = collect(range(6, 0))->map(function ($daysAgo) {
            $date = now()->subDays($daysAgo);
            return [
                'label' => $date->format('D'),
                'count' => Enquiry::whereDate('created_at', $date->toDateString())->count(),
            ];
        });

        return view('admin.dashboard', compact('stats', 'recentEnquiries', 'pendingReviews', 'topProducts', 'chart'));
    }
}
