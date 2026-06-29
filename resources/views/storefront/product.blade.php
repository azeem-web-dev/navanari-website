@extends('layouts.storefront')

@section('title', $product->name.' — '.setting('site_name','Navanari'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->short_description ?: $product->description), 150))

@section('content')
@php
    $images = $product->images->pluck('url')->all();
    if (empty($images)) { $images = ['https://via.placeholder.com/800x1000?text=Navanari']; }

    $hasVariants = $product->variants->isNotEmpty();
    $variantData = $product->variants->map(fn ($v) => [
        'id' => $v->id,
        'size' => $v->size,
        'color' => $v->color,
        'price' => (float) $v->price,
        'eff' => (float) $v->effective_price,
        'onSale' => $v->is_on_sale,
        'stock' => $v->stock_status,
        'image' => $v->image_url,
    ])->values()->all();

    // Option lists: from variants when present, else from the simple JSON fields.
    $sizes = $hasVariants ? $product->variants->pluck('size')->filter()->unique()->values()->all() : ($product->sizes ?? []);
    $colors = $hasVariants ? $product->variants->pluck('color')->filter()->unique()->values()->all() : ($product->colors ?? []);

    $wishData = [
        'id'=>$product->id,'name'=>$product->name,'slug'=>$product->slug,
        'image'=>$images[0],'url'=>route('product.show',$product),
        'price'=>$product->price_visible ? money($product->price_from) : null,
    ];
@endphp

<div class="container-x py-10"
     x-data="{
        img: '{{ $images[0] }}',
        size: @js($sizes[0] ?? ''),
        color: @js($colors[0] ?? ''),
        qty: 1,
        p: {{ \Illuminate\Support\Js::from($wishData) }},
        hasVariants: {{ $hasVariants ? 'true' : 'false' }},
        variants: {{ \Illuminate\Support\Js::from($variantData) }},
        priceVisible: {{ $product->price_visible ? 'true' : 'false' }},
        currency: @js(setting('currency_symbol', '₹')),
        basePrice: {{ (float) $product->effective_price }},
        baseRegular: {{ (float) $product->price }},
        baseOnSale: {{ $product->is_on_sale ? 'true' : 'false' }},
        get currentVariant(){
            if(!this.hasVariants) return null;
            return this.variants.find(v => (!this.size || v.size===this.size) && (!this.color || v.color===this.color)) || null;
        },
        get displayPrice(){ const v=this.currentVariant; return v ? v.eff : this.basePrice; },
        get displayRegular(){ const v=this.currentVariant; return v ? v.price : this.baseRegular; },
        get displayOnSale(){ const v=this.currentVariant; return v ? v.onSale : this.baseOnSale; },
        get currentStock(){ const v=this.currentVariant; return v ? v.stock : @js($product->stock_status); },
        fmt(n){ return this.currency + Number(n).toLocaleString('en-IN'); },
        syncVariantImage(){ const v=this.currentVariant; if(v && v.image){ this.img = v.image; } },
        get enquireUrl(){
            const u = new URL('{{ route('product.enquire', $product) }}');
            if(this.size) u.searchParams.set('size', this.size);
            if(this.color) u.searchParams.set('color', this.color);
            if(this.currentVariant) u.searchParams.set('variant', this.currentVariant.id);
            u.searchParams.set('qty', this.qty);
            return u.toString();
        }
     }"
     x-effect="syncVariantImage()">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-ink/50 mb-6 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-rose-700">Home</a><span>/</span>
        <a href="{{ route('shop') }}" class="hover:text-rose-700">Shop</a>
        @if($product->category)<span>/</span><a href="{{ route('shop',['category'=>$product->category->slug]) }}" class="hover:text-rose-700">{{ $product->category->name }}</a>@endif
        <span>/</span><span class="text-rose-700 line-clamp-1">{{ $product->name }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-10">

        {{-- ===== Gallery ===== --}}
        <div data-reveal="left">
            <div class="relative overflow-hidden rounded-3xl shadow-soft bg-rose-50 aspect-[4/5]">
                <img :src="img" alt="{{ $product->name }}" class="h-full w-full object-cover transition-all duration-500">
                @if($product->is_on_sale)
                    <span class="absolute top-4 left-4 rounded-full bg-rose-600 px-3 py-1 text-xs font-bold text-white shadow">-{{ $product->discount_percent }}% OFF</span>
                @endif
            </div>
            @if(count($images) > 1)
                <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
                    @foreach($images as $im)
                        <button @click="img='{{ $im }}'" :class="img==='{{ $im }}' ? 'ring-2 ring-rose-600' : 'ring-1 ring-rose-100'"
                                class="h-20 w-16 shrink-0 overflow-hidden rounded-xl">
                            <img src="{{ $im }}" alt="" class="h-full w-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== Details ===== --}}
        <div data-reveal="right">
            @if($product->brand)<p class="text-xs uppercase tracking-[0.25em] text-rose-400 mb-2">{{ $product->brand }}</p>@endif
            <h1 class="font-serif text-3xl sm:text-4xl font-semibold text-ink leading-tight">{{ $product->name }}</h1>

            {{-- Rating --}}
            @if($product->approvedReviews->count())
                <div class="mt-3 flex items-center gap-2">
                    <div class="flex gap-0.5 text-gold">
                        @for($s=0;$s<5;$s++)<svg class="h-4 w-4 {{ $s < round($product->average_rating) ? 'fill-gold' : 'fill-rose-100' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.49 6.91l6.572-.955L10 0l2.938 5.955 6.572.955-4.755 4.635 1.123 6.545z"/></svg>@endfor
                    </div>
                    <span class="text-sm text-ink/50">{{ number_format($product->average_rating,1) }} ({{ $product->approvedReviews->count() }} reviews)</span>
                </div>
            @endif

            {{-- Price (reactive to the selected variant) --}}
            <div class="mt-5">
                @if($product->price_visible)
                    <div class="flex items-baseline gap-3 flex-wrap">
                        @if($product->has_price_range)
                            <span class="text-sm text-ink/40 mr-1">from</span>
                        @endif
                        <span class="font-serif text-3xl font-bold text-rose-700" x-text="fmt(displayPrice)">{{ money($product->price_from) }}</span>
                        <template x-if="displayOnSale">
                            <span class="text-lg text-ink/40 line-through" x-text="fmt(displayRegular)"></span>
                        </template>
                        <template x-if="displayOnSale">
                            <span class="chip !bg-rose-600 !text-white" x-text="'Save ' + fmt(displayRegular - displayPrice)"></span>
                        </template>
                    </div>
                @else
                    <span class="text-lg font-medium text-ink/60">Contact us for the best price</span>
                @endif
            </div>

            @if($product->short_description)
                <p class="mt-4 text-ink/70 leading-relaxed">{{ $product->short_description }}</p>
            @endif

            {{-- Variants --}}
            @if(count($sizes))
                <div class="mt-6">
                    <p class="label">Size <span class="text-rose-600 font-normal" x-text="size ? '· '+size : ''"></span></p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($sizes as $s)
                            <button type="button" @click="size='{{ $s }}'" :class="size==='{{ $s }}' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-ink/70 border-rose-200 hover:border-rose-400'"
                                    class="min-w-[3rem] rounded-xl border px-4 py-2 text-sm font-medium transition">{{ $s }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($colors))
                <div class="mt-5">
                    <p class="label">Colour <span class="text-rose-600 font-normal" x-text="color ? '· '+color : ''"></span></p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($colors as $c)
                            <button type="button" @click="color='{{ $c }}'" :class="color==='{{ $c }}' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-ink/70 border-rose-200 hover:border-rose-400'"
                                    class="rounded-xl border px-4 py-2 text-sm font-medium transition">{{ $c }}</button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Qty --}}
            <div class="mt-5">
                <p class="label">Quantity</p>
                <div class="inline-flex items-center rounded-xl border border-rose-200 bg-white">
                    <button type="button" @click="qty = Math.max(1, qty-1)" class="px-4 py-2 text-rose-700 text-lg">−</button>
                    <span class="w-10 text-center font-medium" x-text="qty"></span>
                    <button type="button" @click="qty++" class="px-4 py-2 text-rose-700 text-lg">+</button>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-3">
                <a :href="enquireUrl" target="_blank" rel="noopener" class="btn-whatsapp flex-1 !py-4 text-base">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
                    Enquire on WhatsApp
                </a>
                <button @click="$store.wishlist.toggle(p)" class="btn-outline !py-4 sm:w-auto">
                    <svg class="h-5 w-5" :fill="$store.wishlist.has(p.id) ? 'currentColor':'none'" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    <span x-text="$store.wishlist.has(p.id) ? 'Saved' : 'Wishlist'"></span>
                </button>
            </div>

            {{-- Meta --}}
            <div class="mt-7 grid grid-cols-2 gap-3 text-sm">
                @if($product->sku)<div class="flex gap-2"><span class="text-ink/40">SKU:</span><span class="font-medium">{{ $product->sku }}</span></div>@endif
                @if($product->material)<div class="flex gap-2"><span class="text-ink/40">Material:</span><span class="font-medium">{{ $product->material }}</span></div>@endif
                @if($product->category)<div class="flex gap-2"><span class="text-ink/40">Category:</span><a href="{{ route('shop',['category'=>$product->category->slug]) }}" class="font-medium text-rose-700">{{ $product->category->name }}</a></div>@endif
                <div class="flex gap-2"><span class="text-ink/40">Availability:</span>
                    <span class="font-medium" :class="currentStock==='out_of_stock' ? 'text-ink/50' : 'text-green-600'"
                          x-text="{'in_stock':'In Stock','out_of_stock':'Out of Stock','made_to_order':'Made to Order'}[currentStock] || 'In Stock'">{{ str($product->stock_status)->headline() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Description ===== --}}
    @if($product->description)
    <section class="mt-16" data-reveal>
        <div class="card p-8">
            <h2 class="font-serif text-2xl font-semibold text-ink mb-4">Product Details</h2>
            <div class="prose prose-rose max-w-none text-ink/75">{!! $product->description !!}</div>
        </div>
    </section>
    @endif

    {{-- ===== Reviews ===== --}}
    <section class="mt-12 grid lg:grid-cols-[1fr,380px] gap-8">
        <div data-reveal>
            <h2 class="font-serif text-2xl font-semibold text-ink mb-5">Customer Reviews</h2>
            @forelse($product->approvedReviews as $review)
                <div class="card p-5 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-semibold text-sm">{{ strtoupper(substr($review->author_name,0,1)) }}</span>
                            <div>
                                <p class="font-semibold text-ink text-sm">{{ $review->author_name }}</p>
                                <p class="text-xs text-ink/40">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex gap-0.5 text-gold">
                            @for($s=0;$s<5;$s++)<svg class="h-3.5 w-3.5 {{ $s < $review->rating ? 'fill-gold':'fill-rose-100' }}" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.49 6.91l6.572-.955L10 0l2.938 5.955 6.572.955-4.755 4.635 1.123 6.545z"/></svg>@endfor
                        </div>
                    </div>
                    @if($review->title)<p class="mt-3 font-medium text-ink">{{ $review->title }}</p>@endif
                    <p class="mt-1 text-sm text-ink/70 leading-relaxed">{{ $review->body }}</p>
                </div>
            @empty
                <div class="card p-8 text-center text-ink/60">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-rose-50 text-rose-400"><x-icon name="star-outline" class="h-6 w-6" /></span>
                    Be the first to review this product
                </div>
            @endforelse
        </div>

        {{-- Review form --}}
        <div class="lg:sticky lg:top-28 h-max" data-reveal="right">
            <div class="card p-6">
                <h3 class="font-semibold text-ink mb-1">Write a Review</h3>
                <p class="text-xs text-ink/50 mb-4">Your review appears after approval.</p>

                @if(session('status'))
                    <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('product.review', $product) }}" class="space-y-3" x-data="{ rating:5 }">
                    @csrf
                    <div>
                        <p class="label">Your Rating</p>
                        <div class="flex gap-1">
                            @for($s=1;$s<=5;$s++)
                                <button type="button" @click="rating={{ $s }}" class="transition hover:scale-110">
                                    <svg class="h-7 w-7" :class="rating>={{ $s }} ? 'fill-gold':'fill-rose-100'" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.49 6.91l6.572-.955L10 0l2.938 5.955 6.572.955-4.755 4.635 1.123 6.545z"/></svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <input type="text" name="author_name" placeholder="Your name" required class="input">
                    <input type="email" name="author_email" placeholder="Email (optional)" class="input">
                    <input type="text" name="title" placeholder="Review title (optional)" class="input">
                    <textarea name="body" rows="4" placeholder="Share your experience…" required class="input"></textarea>
                    <button class="btn-primary w-full">Submit Review</button>
                </form>
            </div>
        </div>
    </section>

    {{-- ===== Related ===== --}}
    @if($related->count())
    <section class="mt-20">
        <div class="text-center mb-10" data-reveal>
            <p class="heading-script text-2xl mb-1">You may also love</p>
            <h2 class="font-serif text-3xl font-semibold text-ink">Related Products</h2>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($related as $i => $rel)
                <x-product-card :product="$rel" :delay="$i*60" />
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
