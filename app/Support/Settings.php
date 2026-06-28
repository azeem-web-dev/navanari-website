<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Cached accessor for site-wide settings (branding, contact, toggles).
 * Powers the fully-customizable admin panel without code changes.
 */
class Settings
{
    protected const CACHE_KEY = 'site_settings';

    /** @var array<string,string>|null */
    protected static ?array $cache = null;

    /** Load all settings as an associative array (cached). */
    public static function all(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        try {
            static::$cache = Cache::rememberForever(static::CACHE_KEY, function () {
                return Setting::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            // DB not migrated yet (e.g. during install) — fall back to defaults.
            static::$cache = [];
        }

        return static::$cache;
    }

    public static function get(string $key, $default = null)
    {
        $value = static::all()[$key] ?? null;
        return $value === null || $value === '' ? $default : $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $value = static::all()[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    public static function set(string $key, $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        static::flush();
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        static::flush();
    }

    public static function flush(): void
    {
        static::$cache = null;
        Cache::forget(static::CACHE_KEY);
    }

    /** Format a price using the configured currency symbol. */
    public static function money($amount): string
    {
        $symbol = static::get('currency_symbol', '₹');
        return $symbol.number_format((float) $amount, 0);
    }
}
