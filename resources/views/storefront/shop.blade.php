@extends('layouts.storefront')

@section('title', ($activeCategory->name ?? 'Shop').' — '.setting('site_name','Navanari'))

@section('content')
<div class="container-x py-10">

    {{-- Page header --}}
    <div class="mb-8" data-reveal>
        <nav class="text-xs text-ink/50 mb-3 flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-rose-700">Home</a><span>/</span>
            <a href="{{ route('shop') }}" class="hover:text-rose-700">Shop</a>
            @if($activeCategory)<span>/</span><span class="text-rose-700">{{ $activeCategory->name }}</span>@endif
        </nav>
        <h1 class="font-serif text-4xl font-semibold text-ink">{{ $activeCategory->name ?? (request('q') ? 'Search Results' : 'All Products') }}</h1>
        @if(request('q'))
            <p class="mt-2 text-ink/60">Showing results for “<span class="font-medium text-rose-700">{{ request('q') }}</span>”</p>
        @elseif($activeCategory?->description)
            <p class="mt-2 text-ink/60 max-w-2xl">{{ $activeCategory->description }}</p>
        @endif
    </div>

    <div class="grid lg:grid-cols-[260px,1fr] gap-8">

        {{-- ===== Filters sidebar ===== --}}
        <aside class="lg:sticky lg:top-28 h-max" x-data="{ open:false }">
            <button @click="open=!open" class="lg:hidden btn-outline w-full mb-4">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 6h18M6 12h12M10 18h4"/></svg>
                Filters & Sort
            </button>
            <form method="GET" action="{{ route('shop') }}" x-show="open || window.innerWidth >= 1024" x-cloak class="card p-5 space-y-6">
                @if(request('q'))<input type="hidden" name="q" value="{{ request('q') }}">@endif

                <div>
                    <h3 class="font-semibold text-ink mb-3">Categories</h3>
                    <div class="space-y-1.5 text-sm">
                        <a href="{{ route('shop', array_merge(request()->except('category','page'))) }}"
                           class="flex items-center justify-between rounded-lg px-3 py-2 {{ !$activeCategory ? 'bg-rose-600 text-white' : 'text-ink/70 hover:bg-rose-50' }}">
                            <span>All Products</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('shop', array_merge(request()->except('page'), ['category'=>$cat->slug])) }}"
                               class="flex items-center justify-between rounded-lg px-3 py-2 {{ $activeCategory?->id === $cat->id ? 'bg-rose-600 text-white' : 'text-ink/70 hover:bg-rose-50' }}">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs opacity-70">{{ $cat->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-ink mb-3">Price Range</h3>
                    <div class="flex items-center gap-2">
                        <input type="number" name="min" value="{{ request('min') }}" placeholder="Min" class="input !py-2 text-sm">
                        <span class="text-ink/40">–</span>
                        <input type="number" name="max" value="{{ request('max') }}" placeholder="Max" class="input !py-2 text-sm">
                    </div>
                </div>

                <label class="flex items-center gap-2.5 text-sm text-ink/80">
                    <input type="checkbox" name="sale" value="1" {{ request('sale') ? 'checked' : '' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                    On Sale only
                </label>

                <div>
                    <h3 class="font-semibold text-ink mb-3">Sort By</h3>
                    <select name="sort" class="input !py-2.5 text-sm">
                        @foreach(['newest'=>'Newest','popular'=>'Most Popular','price_low'=>'Price: Low to High','price_high'=>'Price: High to Low','name'=>'Name A–Z'] as $val=>$lbl)
                            <option value="{{ $val }}" {{ request('sort')===$val ? 'selected':'' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button class="btn-primary flex-1 !py-2.5">Apply</button>
                    <a href="{{ route('shop') }}" class="btn-outline !py-2.5">Reset</a>
                </div>
            </form>
        </aside>

        {{-- ===== Product grid ===== --}}
        <div>
            <div class="flex items-center justify-between mb-5 text-sm text-ink/60">
                <span>{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</span>
            </div>

            @if($products->count())
                <div class="grid grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($products as $i => $product)
                        <x-product-card :product="$product" :delay="($i%6)*50" />
                    @endforeach
                </div>
                <div class="mt-10">{{ $products->links() }}</div>
            @else
                <div class="card text-center py-20 px-6" data-reveal>
                    <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 text-rose-500"><x-icon name="bag" class="h-8 w-8" /></span>
                    <h3 class="font-serif text-2xl text-ink mb-2">No products found</h3>
                    <p class="text-ink/60 mb-6">Try adjusting your filters or search for something else.</p>
                    <a href="{{ route('shop') }}" class="btn-primary">View All Products</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
