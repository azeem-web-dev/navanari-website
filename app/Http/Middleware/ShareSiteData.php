<?php

namespace App\Http\Middleware;

use App\Models\Category;
use App\Models\Promotion;
use App\Support\Settings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shares settings, navigation categories and the announcement bar with all
 * front-end views so Blade templates stay clean.
 */
class ShareSiteData
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only compute for non-admin web pages to keep the admin panel snappy.
        if (! $request->is('admin*')) {
            try {
                $navCategories = Category::active()
                    ->whereNull('parent_id')
                    ->with(['children' => fn ($q) => $q->active()])
                    ->orderBy('sort_order')
                    ->get();

                $topbar = Promotion::active()->where('position', 'topbar')->orderBy('sort_order')->first();
            } catch (\Throwable $e) {
                $navCategories = collect();
                $topbar = null;
            }

            View::share('navCategories', $navCategories);
            View::share('topbarPromo', $topbar);
            View::share('settings', Settings::all());
        }

        return $next($request);
    }
}
