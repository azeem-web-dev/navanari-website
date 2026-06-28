@php($siteName = setting('site_name', 'Navanari'))
@php($logo = setting('logo'))

<header data-site-header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md" x-data="{ mobile:false, search:false }">
    <div class="container-x">
        <div class="flex items-center justify-between gap-4 py-4">
            {{-- Mobile menu button --}}
            <button @click="mobile=true" class="lg:hidden -ml-1 p-2 text-rose-700" aria-label="Open menu">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                @if($logo)
                    <img src="{{ \Illuminate\Support\Str::startsWith($logo,['http']) ? $logo : \Illuminate\Support\Facades\Storage::url($logo) }}" alt="{{ $siteName }}" class="h-10 w-auto">
                @else
                    <span class="text-2xl sm:text-3xl font-serif font-bold text-gradient leading-none">{{ $siteName }}</span>
                @endif
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-7 text-sm font-medium text-ink/80">
                <a href="{{ route('home') }}" class="link-underline hover:text-rose-700">Home</a>
                <div class="relative" x-data="{ open:false }" @mouseenter="open=true" @mouseleave="open=false">
                    <a href="{{ route('shop') }}" class="link-underline hover:text-rose-700 inline-flex items-center gap-1">
                        Shop
                        <svg class="h-4 w-4 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                    </a>
                    {{-- Mega menu --}}
                    <div x-show="open" x-cloak x-transition.opacity
                         class="absolute left-1/2 top-full -translate-x-1/2 pt-4 w-[34rem]">
                        <div class="card p-6 grid grid-cols-2 gap-2">
                            @forelse($navCategories ?? [] as $cat)
                                <a href="{{ route('shop', ['category' => $cat->slug]) }}"
                                   class="group flex items-center gap-3 rounded-2xl p-3 hover:bg-rose-50 transition">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700 group-hover:bg-rose-600 group-hover:text-white transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6h16.5M3.75 12h16.5m-16.5 6h16.5"/></svg>
                                    </span>
                                    <span>
                                        <span class="block font-semibold text-ink">{{ $cat->name }}</span>
                                        <span class="block text-xs text-ink/50 line-clamp-1">{{ $cat->description }}</span>
                                    </span>
                                </a>
                            @empty
                                <span class="text-sm text-ink/50">No categories yet.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <a href="{{ route('shop', ['sale' => 1]) }}" class="link-underline hover:text-rose-700">Offers</a>
                <a href="{{ route('about') }}" class="link-underline hover:text-rose-700">About</a>
                <a href="{{ route('contact') }}" class="link-underline hover:text-rose-700">Contact</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-1 sm:gap-2">
                <button @click="search=!search" class="p-2.5 rounded-full hover:bg-rose-50 text-rose-700 transition" aria-label="Search">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </button>
                <a href="{{ route('wishlist') }}" class="relative p-2.5 rounded-full hover:bg-rose-50 text-rose-700 transition" aria-label="Wishlist">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    <span x-show="$store.wishlist.count > 0" x-text="$store.wishlist.count" x-cloak
                          class="absolute -top-0.5 -right-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white"></span>
                </a>
                <a href="{{ whatsapp_link('Hello '.$siteName.'!') }}" target="_blank" rel="noopener" class="hidden sm:inline-flex btn-primary !px-5 !py-2.5 text-xs">
                    Enquire
                </a>
            </div>
        </div>

        {{-- Slide-down search --}}
        <div x-show="search" x-cloak x-transition class="pb-4">
            <form action="{{ route('shop') }}" method="GET" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" autofocus
                       placeholder="Search sarees, dresses, jewellery…"
                       class="input !rounded-full !py-3.5 pl-12 pr-28">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-rose-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M21 21l-4.3-4.3m1.8-5.2a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button class="btn-primary absolute right-1.5 top-1/2 -translate-y-1/2 !py-2 !px-5">Search</button>
            </form>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div x-show="mobile" x-cloak class="fixed inset-0 z-50 lg:hidden">
        <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="mobile=false" x-transition.opacity></div>
        <div class="absolute left-0 top-0 h-full w-80 max-w-[85%] bg-cream shadow-2xl p-6 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
            <div class="flex items-center justify-between mb-6">
                <span class="text-2xl font-serif font-bold text-gradient">{{ $siteName }}</span>
                <button @click="mobile=false" class="p-2 text-rose-700"><svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/></svg></button>
            </div>
            <nav class="space-y-1 text-ink/80 font-medium">
                <a href="{{ route('home') }}" class="block rounded-xl px-4 py-3 hover:bg-rose-50">Home</a>
                <a href="{{ route('shop') }}" class="block rounded-xl px-4 py-3 hover:bg-rose-50">Shop All</a>
                <p class="px-4 pt-4 pb-1 text-xs uppercase tracking-wider text-rose-400">Categories</p>
                @foreach($navCategories ?? [] as $cat)
                    <a href="{{ route('shop', ['category' => $cat->slug]) }}" class="block rounded-xl px-4 py-2.5 hover:bg-rose-50">{{ $cat->name }}</a>
                @endforeach
                <a href="{{ route('shop', ['sale' => 1]) }}" class="block rounded-xl px-4 py-3 hover:bg-rose-50 mt-2">Offers</a>
                <a href="{{ route('about') }}" class="block rounded-xl px-4 py-3 hover:bg-rose-50">About</a>
                <a href="{{ route('contact') }}" class="block rounded-xl px-4 py-3 hover:bg-rose-50">Contact</a>
            </nav>
        </div>
    </div>
</header>
<style>[x-cloak]{display:none!important}</style>
