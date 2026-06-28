@extends('layouts.admin')
@section('title', 'Settings')
@section('heading', 'Site Settings')

@section('content')
@php
    $s = fn($k, $d='') => old($k, $settings[$k] ?? $d);
@endphp

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
      x-data="{ tab: 'branding' }" class="max-w-4xl">
    @csrf
    @method('PUT')

    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach(['branding'=>'Branding','hero'=>'Hero & Home','contact'=>'Contact & Social','content'=>'Content','pricing'=>'Pricing'] as $val=>$lbl)
            <button type="button" @click="tab='{{ $val }}'" :class="tab==='{{ $val }}' ? 'bg-rose-600 text-white' : 'bg-white text-ink/60 ring-1 ring-rose-100 hover:bg-rose-50'"
                    class="rounded-full px-5 py-2.5 text-sm font-medium transition">{{ $lbl }}</button>
        @endforeach
    </div>

    {{-- Branding --}}
    <div x-show="tab==='branding'" class="card p-6 space-y-5">
        <h2 class="font-semibold text-ink">Branding</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Site Name <span class="text-rose-500">*</span></label>
                <input type="text" name="site_name" value="{{ $s('site_name','Navanari') }}" required class="input">
            </div>
            <div>
                <label class="label">Tagline</label>
                <input type="text" name="site_tagline" value="{{ $s('site_tagline') }}" class="input">
            </div>
        </div>
        <div>
            <label class="label">Site Description <span class="text-ink/40 text-xs">(SEO / footer)</span></label>
            <textarea name="site_description" rows="2" class="input">{{ $s('site_description') }}</textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Logo URL</label>
                <input type="text" name="logo" value="{{ $s('logo') }}" placeholder="https://… (leave blank for text logo)" class="input">
            </div>
            <div>
                <label class="label">…or upload logo</label>
                <input type="file" name="logo_file" accept="image/*" class="input !py-2">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Primary Colour</label>
                <div class="flex items-center gap-3" x-data="{ c: '{{ $s('primary_color','#be185d') }}' }">
                    <input type="color" name="primary_color" x-model="c" class="h-11 w-16 rounded-lg border-rose-200">
                    <input type="text" x-model="c" class="input" readonly>
                </div>
            </div>
            <div>
                <label class="label">Accent Colour</label>
                <input type="color" name="accent_color" value="{{ $s('accent_color','#d4af37') }}" class="h-11 w-16 rounded-lg border-rose-200">
            </div>
        </div>
    </div>

    {{-- Hero --}}
    <div x-show="tab==='hero'" x-cloak class="card p-6 space-y-5">
        <h2 class="font-semibold text-ink">Hero & Homepage</h2>
        <div>
            <label class="label">Hero Heading</label>
            <input type="text" name="hero_heading" value="{{ $s('hero_heading') }}" class="input">
        </div>
        <div>
            <label class="label">Hero Subheading</label>
            <textarea name="hero_subheading" rows="2" class="input">{{ $s('hero_subheading') }}</textarea>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Hero Button Text</label>
                <input type="text" name="hero_cta_text" value="{{ $s('hero_cta_text') }}" class="input">
            </div>
            <div>
                <label class="label">Hero Background Image URL</label>
                <input type="text" name="hero_image" value="{{ $s('hero_image') }}" class="input">
            </div>
        </div>
        <div>
            <label class="label">Announcement Bar <span class="text-ink/40 text-xs">(leave blank to hide)</span></label>
            <input type="text" name="announcement" value="{{ $s('announcement') }}" class="input">
        </div>
        <div>
            <label class="label">Shipping Note</label>
            <input type="text" name="shipping_note" value="{{ $s('shipping_note') }}" class="input">
        </div>
    </div>

    {{-- Contact & Social --}}
    <div x-show="tab==='contact'" x-cloak class="card p-6 space-y-5">
        <h2 class="font-semibold text-ink">Contact & Social</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">WhatsApp Number <span class="text-ink/40 text-xs">(intl, no +)</span></label>
                <input type="text" name="whatsapp_number" value="{{ $s('whatsapp_number') }}" placeholder="919999999999" class="input">
            </div>
            <div>
                <label class="label">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ $s('contact_phone') }}" class="input">
            </div>
            <div>
                <label class="label">Contact Email</label>
                <input type="email" name="contact_email" value="{{ $s('contact_email') }}" class="input">
            </div>
            <div>
                <label class="label">Address</label>
                <input type="text" name="contact_address" value="{{ $s('contact_address') }}" class="input">
            </div>
            <div>
                <label class="label">Instagram URL</label>
                <input type="text" name="instagram_url" value="{{ $s('instagram_url') }}" class="input">
            </div>
            <div>
                <label class="label">Facebook URL</label>
                <input type="text" name="facebook_url" value="{{ $s('facebook_url') }}" class="input">
            </div>
            <div>
                <label class="label">YouTube URL</label>
                <input type="text" name="youtube_url" value="{{ $s('youtube_url') }}" class="input">
            </div>
            <div>
                <label class="label">Pinterest URL</label>
                <input type="text" name="pinterest_url" value="{{ $s('pinterest_url') }}" class="input">
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div x-show="tab==='content'" x-cloak class="card p-6 space-y-5">
        <h2 class="font-semibold text-ink">Page Content</h2>
        <div>
            <label class="label">About Text</label>
            <textarea name="about_text" rows="4" class="input">{{ $s('about_text') }}</textarea>
        </div>
        <div>
            <label class="label">Footer Note</label>
            <input type="text" name="footer_note" value="{{ $s('footer_note') }}" class="input">
        </div>
    </div>

    {{-- Pricing --}}
    <div x-show="tab==='pricing'" x-cloak class="card p-6 space-y-5">
        <h2 class="font-semibold text-ink">Pricing & Currency</h2>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Currency Symbol</label>
                <input type="text" name="currency_symbol" value="{{ $s('currency_symbol','₹') }}" maxlength="4" class="input">
            </div>
        </div>
        <label class="flex items-start gap-3 rounded-xl bg-rose-50/60 p-4">
            <input type="checkbox" name="show_prices" value="1" {{ ($settings['show_prices'] ?? '1') == '1' ? 'checked':'' }} class="mt-0.5 rounded border-rose-300 text-rose-600 focus:ring-rose-300">
            <span>
                <span class="block font-medium text-ink">Show prices across the site</span>
                <span class="block text-sm text-ink/50">Master switch. When off, all products show “Enquire for price”. You can also hide price per product.</span>
            </span>
        </label>
    </div>

    <div class="flex gap-3 mt-6 sticky bottom-4">
        <button class="btn-primary shadow-glow">Save Settings</button>
        <a href="{{ route('home') }}" target="_blank" class="btn-outline">Preview Site</a>
    </div>
</form>
@endsection
