@extends('layouts.storefront')

@section('content')

{{-- ============ HERO ============ --}}
<section class="container-x pt-6">
    @php($slides = $heroSlides->count() ? $heroSlides : collect([null]))
    <div x-data="{ active: 0, total: {{ max($slides->count(),1) }}, timer: null,
                   start(){ this.timer = setInterval(()=>this.next(), 6000) },
                   next(){ this.active = (this.active+1)%this.total },
                   go(i){ this.active=i; clearInterval(this.timer); this.start() } }"
         x-init="start()"
         class="relative overflow-hidden rounded-[2.5rem] shadow-glow">

        <div class="relative h-[460px] sm:h-[560px] lg:h-[640px]">
            @foreach($slides as $i => $slide)
                @php($bg = $slide?->image_url ?: setting('hero_image'))
                <div x-show="active === {{ $i }}" x-transition:enter="transition duration-700 ease-out" x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                     class="absolute inset-0">
                    <img src="{{ $bg }}" alt="" class="absolute inset-0 h-full w-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-ink/70 via-ink/40 to-transparent"></div>

                    <div class="relative h-full container-x flex items-center">
                        <div class="max-w-xl text-white">
                            <p class="heading-script text-3xl sm:text-4xl text-gold-light mb-3 animate-fade-up">{{ setting('site_tagline', 'Where Every Woman Shines') }}</p>
                            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight text-balance animate-fade-up" style="animation-delay:.1s">
                                {{ $slide->title ?? setting('hero_heading', 'Drape Yourself in Elegance') }}
                            </h1>
                            <p class="mt-5 text-rose-50/90 text-base sm:text-lg max-w-md animate-fade-up" style="animation-delay:.2s">
                                {{ $slide->subtitle ?? setting('hero_subheading') }}
                            </p>
                            <div class="mt-8 flex flex-wrap gap-3 animate-fade-up" style="animation-delay:.3s">
                                <a href="{{ $slide->link ?? route('shop') }}" class="btn-gold">{{ $slide->button_text ?? setting('hero_cta_text', 'Explore Collection') }}</a>
                                <a href="{{ route('shop', ['sale'=>1]) }}" class="btn-outline !bg-white/10 !text-white !border-white/40 hover:!bg-white hover:!text-rose-700">View Offers</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Controls --}}
        @if($slides->count() > 1)
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2.5">
                @foreach($slides as $i => $s)
                    <button @click="go({{ $i }})" :class="active==={{ $i }} ? 'w-8 bg-gold' : 'w-2.5 bg-white/60'" class="h-2.5 rounded-full transition-all duration-300" aria-label="Slide {{ $i+1 }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ============ USP BAR ============ --}}
<section class="container-x mt-8">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach([
            ['✨','Curated Quality','Handpicked premium pieces'],
            ['🚚','Pan-India Delivery', setting('shipping_note','Fast & safe shipping')],
            ['💬','WhatsApp Support','Personal styling help'],
            ['🔁','Easy Exchanges','Hassle-free returns'],
        ] as $i => $usp)
            <div data-reveal data-reveal-delay="{{ $i*80 }}" class="card flex items-center gap-3 p-4">
                <span class="text-2xl">{{ $usp[0] }}</span>
                <span>
                    <span class="block text-sm font-semibold text-ink">{{ $usp[1] }}</span>
                    <span class="block text-xs text-ink/50 line-clamp-1">{{ $usp[2] }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ CATEGORIES ============ --}}
@if($featuredCategories->count())
<section class="container-x mt-24">
    <div class="text-center mb-12" data-reveal>
        <div class="divider-fancy mb-3"><span class="text-sm uppercase tracking-[0.3em]">Shop by</span></div>
        <h2 class="font-serif text-3xl sm:text-4xl font-semibold text-ink">Our Collections</h2>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-5">
        @foreach($featuredCategories as $i => $cat)
            <a href="{{ route('shop', ['category'=>$cat->slug]) }}" data-reveal data-reveal-delay="{{ $i*70 }}"
               class="group relative aspect-[5/6] overflow-hidden rounded-3xl shadow-soft">
                <img src="{{ $cat->image }}" alt="{{ $cat->name }}" class="absolute inset-0 h-full w-full object-cover hover-zoom-img">
                <div class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 p-5 text-white">
                    <h3 class="font-serif text-xl sm:text-2xl font-semibold">{{ $cat->name }}</h3>
                    <span class="inline-flex items-center gap-1 text-sm text-gold-light mt-1 opacity-0 -translate-x-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-x-0">
                        Explore <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14m-6-6l6 6-6 6"/></svg>
                    </span>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ============ FEATURED PRODUCTS ============ --}}
@if($featuredProducts->count())
<section class="container-x mt-24">
    <div class="flex items-end justify-between mb-10" data-reveal>
        <div>
            <p class="heading-script text-2xl mb-1">Bestsellers</p>
            <h2 class="font-serif text-3xl sm:text-4xl font-semibold text-ink">Loved by Our Customers</h2>
        </div>
        <a href="{{ route('shop') }}" class="hidden sm:inline-flex btn-outline">View All</a>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($featuredProducts as $i => $product)
            <x-product-card :product="$product" :delay="$i*60" />
        @endforeach
    </div>
</section>
@endif

{{-- ============ PROMO STRIP ============ --}}
@if($stripPromo)
<section class="container-x mt-24">
    <a href="{{ $stripPromo->link ?? route('shop') }}" data-reveal="zoom" class="group relative block overflow-hidden rounded-[2.5rem] shadow-glow">
        <img src="{{ $stripPromo->image_url }}" alt="{{ $stripPromo->title }}" class="h-72 sm:h-96 w-full object-cover hover-zoom-img">
        <div class="absolute inset-0 bg-gradient-to-r from-ink/70 to-transparent flex items-center">
            <div class="p-8 sm:p-16 text-white max-w-lg">
                <h3 class="font-serif text-3xl sm:text-5xl font-semibold leading-tight">{{ $stripPromo->title }}</h3>
                <p class="mt-3 text-rose-50/90">{{ $stripPromo->subtitle }}</p>
                <span class="btn-gold mt-6">{{ $stripPromo->button_text ?? 'Shop Now' }}</span>
            </div>
        </div>
    </a>
</section>
@endif

{{-- ============ STATS ============ --}}
<section class="container-x mt-24">
    <div class="grid grid-cols-3 gap-4 text-center">
        @foreach([
            ['data'=>$stats['products'],'suffix'=>'+','label'=>'Curated Products'],
            ['data'=>$stats['categories'],'suffix'=>'+','label'=>'Collections'],
            ['data'=>$stats['happy'],'suffix'=>'+','label'=>'Happy Women'],
        ] as $stat)
            <div data-reveal class="card py-8">
                <p class="font-serif text-3xl sm:text-5xl font-bold text-gradient" data-counter="{{ $stat['data'] }}" data-suffix="{{ $stat['suffix'] }}">0</p>
                <p class="mt-1 text-sm text-ink/60">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ NEW ARRIVALS ============ --}}
@if($newArrivals->count())
<section class="container-x mt-24">
    <div class="text-center mb-10" data-reveal>
        <p class="heading-script text-2xl mb-1">Just In</p>
        <h2 class="font-serif text-3xl sm:text-4xl font-semibold text-ink">New Arrivals</h2>
    </div>
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($newArrivals as $i => $product)
            <x-product-card :product="$product" :delay="$i*60" />
        @endforeach
    </div>
    <div class="text-center mt-10" data-reveal>
        <a href="{{ route('shop') }}" class="btn-primary">Browse Full Collection</a>
    </div>
</section>
@endif

{{-- ============ TESTIMONIALS ============ --}}
@if($topReviews->count())
<section class="mt-24 overflow-hidden py-16 bg-gradient-to-b from-transparent to-rose-50/60">
    <div class="container-x text-center mb-10" data-reveal>
        <div class="divider-fancy mb-3"><span class="text-sm uppercase tracking-[0.3em]">Kind words</span></div>
        <h2 class="font-serif text-3xl sm:text-4xl font-semibold text-ink">What Women Say</h2>
    </div>
    <div class="flex gap-5 w-max animate-marquee hover:[animation-play-state:paused]">
        @foreach($topReviews->concat($topReviews) as $review)
            <div class="card w-80 shrink-0 p-6">
                <div class="flex gap-0.5 text-gold mb-3">
                    @for($s=0;$s<5;$s++)<svg class="h-4 w-4 {{ $s < $review->rating ? 'fill-gold' : 'fill-rose-100' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.49 6.91l6.572-.955L10 0l2.938 5.955 6.572.955-4.755 4.635 1.123 6.545z"/></svg>@endfor
                </div>
                <p class="text-sm text-ink/70 leading-relaxed line-clamp-4">“{{ $review->body }}”</p>
                <div class="mt-4 flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-semibold text-sm">{{ strtoupper(substr($review->author_name,0,1)) }}</span>
                    <span>
                        <span class="block text-sm font-semibold text-ink">{{ $review->author_name }}</span>
                        <span class="block text-xs text-ink/40">on {{ $review->product?->name }}</span>
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
