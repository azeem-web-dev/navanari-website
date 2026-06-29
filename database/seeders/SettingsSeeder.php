<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Navanari',
            'site_tagline' => 'Where Every Woman Shines',
            'site_description' => 'Navanari is your destination for elegant dresses, exquisite sarees, fine jewellery and curated essentials — handpicked for the modern woman.',
            'logo' => '',
            'currency_symbol' => '₹',
            'show_prices' => '1',
            'whatsapp_number' => env('WHATSAPP_NUMBER', '919999999999'),
            'contact_phone' => '+91 99999 99999',
            'contact_email' => 'hello@navanari.com',
            'contact_address' => 'Boutique No. 9, Fashion Street, Mumbai, India',
            'instagram_url' => 'https://instagram.com/',
            'facebook_url' => 'https://facebook.com/',
            'youtube_url' => '',
            'pinterest_url' => '',
            'primary_color' => '#be185d',
            'accent_color' => '#d4af37',
            'hero_heading' => 'Drape Yourself in Elegance',
            'hero_subheading' => 'Discover sarees, dresses & jewellery curated for the woman who owns every room she walks into.',
            'hero_cta_text' => 'Explore Collection',
            'hero_image' => 'https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=1600&auto=format&fit=crop',
            'about_text' => 'Born from a love of timeless craftsmanship, Navanari brings together India\'s finest weaves, contemporary silhouettes and statement jewellery. Every piece is chosen to make you feel effortlessly beautiful.',
            'footer_note' => 'Handpicked elegance, delivered with love.',
            'shipping_note' => 'Free shipping across India on orders above ₹2999.',
            'announcement' => '✦ Monsoon Edit is live — up to 40% off on festive wear ✦',
        ];

        foreach ($defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
