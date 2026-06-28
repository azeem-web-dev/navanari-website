<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** Text-based setting keys editable from the admin panel. */
    protected array $keys = [
        'site_name', 'site_tagline', 'site_description', 'currency_symbol',
        'whatsapp_number', 'contact_phone', 'contact_email', 'contact_address',
        'instagram_url', 'facebook_url', 'youtube_url', 'pinterest_url',
        'primary_color', 'accent_color',
        'hero_heading', 'hero_subheading', 'hero_cta_text', 'hero_image',
        'about_text', 'footer_note', 'shipping_note', 'announcement',
    ];

    public function edit()
    {
        return view('admin.settings.edit', ['settings' => Settings::all()]);
    }

    public function update(Request $request)
    {
        $values = [];
        foreach ($this->keys as $key) {
            $values[$key] = (string) $request->get($key, '');
        }

        // Boolean toggle: master price visibility.
        $values['show_prices'] = $request->boolean('show_prices') ? '1' : '0';

        // Logo upload (or external URL pasted into `logo` field).
        if ($request->hasFile('logo_file')) {
            $values['logo'] = $request->file('logo_file')->store('branding', 'public');
        } elseif ($request->filled('logo')) {
            $values['logo'] = $request->get('logo');
        }

        $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'logo_file' => ['nullable', 'image', 'max:2048'],
            'contact_email' => ['nullable', 'email'],
            'primary_color' => ['nullable', 'string', 'max:20'],
        ]);

        Settings::setMany($values);

        return back()->with('status', 'Settings saved successfully.');
    }
}
