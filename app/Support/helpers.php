<?php

use App\Support\Settings;

if (! function_exists('setting')) {
    /** Get a site setting value with a fallback default. */
    function setting(string $key, $default = null)
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('setting_bool')) {
    function setting_bool(string $key, bool $default = false): bool
    {
        return Settings::bool($key, $default);
    }
}

if (! function_exists('money')) {
    /** Format an amount with the configured currency symbol. */
    function money($amount): string
    {
        return Settings::money($amount);
    }
}

if (! function_exists('whatsapp_link')) {
    /**
     * Build a wa.me deep link with a pre-filled message.
     */
    function whatsapp_link(string $message = ''): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) Settings::get('whatsapp_number', ''));
        $base = 'https://wa.me/'.$number;
        return $message === '' ? $base : $base.'?text='.rawurlencode($message);
    }
}
