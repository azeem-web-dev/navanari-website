<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * One-time web installer for shared hosting (no SSH/Composer needed on server).
 * Collects the database credentials, runs migrations + seeders, creates the
 * admin account, links storage, then writes a lock file to disable itself.
 */
class InstallController extends Controller
{
    public static function lockPath(): string
    {
        return storage_path('installed.lock');
    }

    public static function isInstalled(): bool
    {
        // Fast path: the lock file written during install.
        if (file_exists(self::lockPath())) {
            return true;
        }

        // Self-healing path: the lock file lives in storage/ and some hosts wipe
        // untracked files on every deploy. The database survives, so if the core
        // tables are already set up we ARE installed — recreate the lock and move on.
        try {
            if (Schema::hasTable('settings') && Schema::hasTable('users') && Setting::query()->exists()) {
                @file_put_contents(self::lockPath(), 'Detected existing install at '.date('c'));
                return true;
            }
        } catch (\Throwable $e) {
            // Database not reachable / not migrated yet — treat as not installed.
        }

        return false;
    }

    public function show()
    {
        if (self::isInstalled()) {
            return redirect()->route('login')->with('status', 'Navanari is already installed.');
        }

        return view('install', [
            'defaults' => [
                'db_host' => config('database.connections.mysql.host', 'localhost'),
                'db_port' => config('database.connections.mysql.port', '3306'),
                'db_database' => config('database.connections.mysql.database', ''),
                'db_username' => config('database.connections.mysql.username', ''),
                'db_password' => config('database.connections.mysql.password', ''),
            ],
        ]);
    }

    public function run(Request $request)
    {
        if (self::isInstalled()) {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'db_host' => ['required', 'string'],
            'db_port' => ['nullable', 'string'],
            'db_database' => ['required', 'string'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
            'admin_name' => ['required', 'string', 'max:80'],
            'admin_email' => ['required', 'email'],
            'admin_password' => ['required', 'string', 'min:6'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'with_demo' => ['nullable'],
        ]);

        $port = $data['db_port'] ?: '3306';

        // 1) Test the database connection up-front for a friendly error.
        try {
            new \PDO(
                "mysql:host={$data['db_host']};port={$port};dbname={$data['db_database']}",
                $data['db_username'],
                $data['db_password'] ?? '',
                [\PDO::ATTR_TIMEOUT => 5]
            );
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'db' => 'Could not connect to the database. Check the details and that the database exists. ('.$e->getMessage().')',
            ]);
        }

        // 2) Point the live connection at these credentials for migrating.
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => $data['db_host'],
            'database.connections.mysql.port' => $port,
            'database.connections.mysql.database' => $data['db_database'],
            'database.connections.mysql.username' => $data['db_username'],
            'database.connections.mysql.password' => $data['db_password'] ?? '',
        ]);
        DB::purge('mysql');

        // 3) Migrate + seed.
        try {
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\SettingsSeeder', '--force' => true]);

            if ($request->boolean('with_demo')) {
                Artisan::call('db:seed', ['--class' => 'Database\\Seeders\\DemoSeeder', '--force' => true]);
            }
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors([
                'db' => 'Setup failed while creating tables: '.$e->getMessage(),
            ]);
        }

        // 4) Admin account + WhatsApp number.
        User::updateOrCreate(
            ['email' => $data['admin_email']],
            [
                'name' => $data['admin_name'],
                'password' => Hash::make($data['admin_password']),
                'is_admin' => true,
            ],
        );

        if (! empty($data['whatsapp_number'])) {
            \App\Models\Setting::updateOrCreate(['key' => 'whatsapp_number'], ['value' => $data['whatsapp_number']]);
        }

        // 5) Persist credentials to .env and lock production settings.
        $this->writeEnv([
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => $port,
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
            'APP_ENV' => 'production',
            'APP_DEBUG' => 'false',
            'APP_URL' => $request->getSchemeAndHttpHost(),
            'FILESYSTEM_DISK' => 'public',
        ]);

        // 6) Public storage symlink for uploaded images (best effort).
        try {
            Artisan::call('storage:link');
        } catch (\Throwable $e) {
            // Fallback: create the symlink directly.
            @symlink(storage_path('app/public'), public_path('storage'));
        }

        // 7) Clear caches and lock the installer.
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
        } catch (\Throwable $e) {
        }

        @file_put_contents(self::lockPath(), 'Installed at '.date('c'));

        return redirect()->route('login')->with('status', 'Installation complete! Sign in with your new admin account.');
    }

    /** Update or append keys in the project's .env file. */
    protected function writeEnv(array $values): void
    {
        $path = base_path('.env');
        if (! file_exists($path)) {
            @copy(base_path('.env.example'), $path);
        }
        if (! file_exists($path) || ! is_writable($path)) {
            return;
        }

        $env = file_get_contents($path);

        foreach ($values as $key => $value) {
            // Quote values containing spaces or special characters.
            if (preg_match('/\s|#|"|\'/', (string) $value)) {
                $value = '"'.addslashes((string) $value).'"';
            }
            $line = $key.'='.$value;

            if (preg_match("/^{$key}=.*/m", $env)) {
                $env = preg_replace("/^{$key}=.*/m", $line, $env);
            } else {
                $env .= PHP_EOL.$line;
            }
        }

        file_put_contents($path, $env);
    }
}
