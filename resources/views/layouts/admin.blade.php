<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ setting('site_name', 'Navanari') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-rose-50/40 font-sans text-ink" x-data="{ sidebar:false }">

    @php
        $nav = [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h14V10'],
            ['route' => 'admin.products.index', 'label' => 'Products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4'],
            ['route' => 'admin.categories.index', 'label' => 'Categories', 'icon' => 'M4 6h16M4 12h16M4 18h16'],
            ['route' => 'admin.promotions.index', 'label' => 'Promotions', 'icon' => 'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
            ['route' => 'admin.enquiries.index', 'label' => 'Enquiries', 'icon' => 'M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z'],
            ['route' => 'admin.reviews.index', 'label' => 'Reviews', 'icon' => 'M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01z'],
            ['route' => 'admin.settings.edit', 'label' => 'Settings', 'icon' => 'M12 15a3 3 0 100-6 3 3 0 000 6zM19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z'],
        ];
    @endphp

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-40 w-64 transform bg-ink text-white transition-transform lg:translate-x-0"
           :class="sidebar ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center gap-2 px-6 border-b border-white/10">
            <span class="text-xl font-serif font-bold text-white">{{ setting('site_name', 'Navanari') }}</span>
            <span class="chip !bg-white/10 !text-rose-200 !ring-white/10">Admin</span>
        </div>
        <nav class="p-4 space-y-1">
            @foreach($nav as $item)
                @php($active = request()->routeIs($item['route']) || ($item['route'] !== 'admin.dashboard' && request()->routeIs(str_replace('index','*',$item['route']))))
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ $active ? 'bg-rose-600 text-white shadow-soft' : 'text-rose-100/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/></svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
        <div class="absolute bottom-0 inset-x-0 p-4 border-t border-white/10">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm text-rose-100/70 hover:bg-white/10 hover:text-white transition">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                View Live Site
            </a>
        </div>
    </aside>

    <div x-show="sidebar" x-cloak @click="sidebar=false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"></div>

    {{-- Main --}}
    <div class="lg:pl-64">
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-rose-100 bg-white/80 backdrop-blur px-4 sm:px-6">
            <button @click="sidebar=!sidebar" class="lg:hidden p-2 text-rose-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            <h1 class="font-serif text-xl font-semibold text-ink">@yield('heading', 'Dashboard')</h1>
            <div class="flex items-center gap-3" x-data="{ open:false }">
                <span class="hidden sm:block text-sm text-ink/60">{{ auth()->user()->name }}</span>
                <button @click="open=!open" class="flex h-9 w-9 items-center justify-center rounded-full bg-rose-600 text-white font-semibold text-sm">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</button>
                <div x-show="open" x-cloak @click.outside="open=false" x-transition class="absolute right-4 top-14 w-44 card p-2">
                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-rose-50">My Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left rounded-lg px-3 py-2 text-sm text-rose-700 hover:bg-rose-50">Log out</button>
                    </form>
                </div>
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            @if(session('status'))
                <div x-data="{ show:true }" x-show="show" x-init="setTimeout(()=>show=false, 4000)"
                     class="mb-6 flex items-center justify-between rounded-2xl bg-green-50 px-5 py-4 text-sm text-green-700 ring-1 ring-green-100">
                    <span>✓ {{ session('status') }}</span>
                    <button @click="show=false" class="text-green-700/60 hover:text-green-700">✕</button>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-2xl bg-rose-50 px-5 py-4 text-sm text-rose-700 ring-1 ring-rose-100">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <style>[x-cloak]{display:none!important}</style>
    @stack('scripts')
</body>
</html>
