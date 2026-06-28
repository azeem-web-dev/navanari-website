<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php($siteName = setting('site_name', 'Navanari'))
    <title>@yield('title', $siteName.' — '.setting('site_tagline', 'Elegance for Every Woman'))</title>
    <meta name="description" content="@yield('meta_description', setting('site_description'))">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Live brand colours from admin settings --}}
    <style>
        :root {
            --brand: {{ setting('primary_color', '#be185d') }};
            --accent: {{ setting('accent_color', '#d4af37') }};
        }
        [data-site-header] { transition: background .35s ease, box-shadow .35s ease, padding .35s ease; }
        [data-site-header].is-scrolled { background: rgba(255,255,255,.92); backdrop-filter: blur(14px); box-shadow: 0 10px 40px -12px rgba(190,24,93,.22); }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen font-sans">

    {{-- Announcement bar --}}
    @if($a = setting('announcement'))
        <div class="bg-gradient-to-r from-rose-700 via-rose-600 to-rose-700 text-white text-center text-xs sm:text-sm py-2 px-4 tracking-wide">
            <span class="inline-flex items-center gap-2">{{ $a }}</span>
        </div>
    @endif

    @include('partials.storefront.header')

    <main>
        @yield('content')
    </main>

    @include('partials.storefront.footer')

    {{-- Floating WhatsApp button --}}
    <a href="{{ whatsapp_link('Hello '.$siteName.'! I would like to know more about your collection.') }}"
       target="_blank" rel="noopener"
       class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25d366] text-white shadow-glow animate-float hover:scale-110 transition"
       aria-label="Chat on WhatsApp">
        <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.518 5.26l-.999 3.648 3.741-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.71.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413z"/></svg>
    </a>

    @stack('scripts')
</body>
</html>
