<?php

namespace App\Http\Middleware;

use App\Http\Controllers\InstallController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Until the app is installed, funnel every request to the web installer.
 * Once installed, auto-apply any migrations shipped in a new deploy so the
 * site stays working without SSH or manual steps (shared-hosting friendly).
 */
class EnsureInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! InstallController::isInstalled()) {
            if (! $request->is('install', 'install/*')) {
                return redirect('/install');
            }
            return $next($request);
        }

        $this->applyPendingMigrationsOnce();

        return $next($request);
    }

    /**
     * Run outstanding migrations at most once per deploy. The cache flag lives
     * in storage/, which hosts like Hostinger wipe on deploy — so a fresh
     * deploy re-checks and applies any new migrations on the first request.
     */
    protected function applyPendingMigrationsOnce(): void
    {
        try {
            if (Cache::get('schema_up_to_date')) {
                return;
            }

            $files = count(glob(database_path('migrations/*.php')));
            $ran = DB::table('migrations')->count();

            if ($files > $ran) {
                Artisan::call('migrate', ['--force' => true]);
                try {
                    Artisan::call('storage:link');
                } catch (\Throwable $e) {
                    // link already exists — fine
                }
            }

            Cache::put('schema_up_to_date', true, now()->addHours(6));
        } catch (\Throwable $e) {
            // Never block a request because of this best-effort step.
        }
    }
}
