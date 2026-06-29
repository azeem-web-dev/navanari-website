<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    /** Number of migration files not yet recorded as run. */
    public static function pendingUpdates(): int
    {
        try {
            $ran = DB::table('migrations')->count();
            $files = count(glob(database_path('migrations/*.php')));
            return max(0, $files - $ran);
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /** Run pending migrations + refresh caches after a redeploy (admin one-click). */
    public function update()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            // storage:link throws if the link already exists — ignore.
        }

        try {
            Artisan::call('view:clear');
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
        }

        return back()->with('status', 'Database updates applied successfully.');
    }
}
