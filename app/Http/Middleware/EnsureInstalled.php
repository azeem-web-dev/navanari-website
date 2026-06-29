<?php

namespace App\Http\Middleware;

use App\Http\Controllers\InstallController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Until the app has been installed (no lock file yet), funnel every request to
 * the web installer so a fresh shared-hosting deploy is set up with one click.
 */
class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! InstallController::isInstalled() && ! $request->is('install', 'install/*')) {
            return redirect('/install');
        }

        return $next($request);
    }
}
