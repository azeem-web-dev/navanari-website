@props(['product', 'delay' => 0])

@php
    $img = $product->primary_image ?: 'https://via.placeholder.com/600x750?text=Navanari';
    $wishData = [
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'image' => $img,
        'url' => route('product.show', $product),
        'price' => $product->price_visible ? money($product->effective_price) : null,
    ];
@endphp

<div data-reveal data-reveal-delay="{{ $delay }}"
     x-data="{ p: {{ \Illuminate\Support\Js::from($wishData) }} }"
     class="group card overflow-hidden hover:-translate-y-1.5 hover:shadow-glow">
    <div class="relative overflow-hidden">
        <a href="{{ route('product.show', $product) }}" class="block aspect-[4/5] overflow-hidden bg-rose-50">
            <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy"
                 class="h-full w-full object-cover hover-zoom-img">
        </a>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
            @if($product->is_on_sale)
                <span class="rounded-full bg-rose-600 px-2.5 py-1 text-[11px] font-bold text-white shadow">-{{ $product->discount_percent }}%</span>
            @endif
            @if($product->is_featured)
                <span class="rounded-full bg-gold px-2.5 py-1 text-[11px] font-bold text-ink shadow">★ Featured</span>
            @endif
            @if($product->stock_status === 'out_of_stock')
                <span class="rounded-full bg-ink/80 px-2.5 py-1 text-[11px] font-semibold text-white">Sold Out</span>
            @endif
        </div>

        {{-- Wishlist --}}
        <button @click="$store.wishlist.toggle(p)"
                class="absolute top-3 right-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-rose-600 shadow-soft transition hover:scale-110"
                aria-label="Add to wishlist">
            <svg class="h-5 w-5" :fill="$store.wishlist.has(p.id) ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
        </button>

        {{-- Quick enquire on hover --}}
        <div class="absolute inset-x-3 bottom-3 translate-y-4 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
            <a href="{{ route('product.enquire', $product) }}" target="_blank" rel="noopener" class="btn-whatsapp w-full !py-2.5 text-xs">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
                Enquire Now
            </a>
        </div>
    </div>

    <div class="p-4">
        @if($product->category)
            <p class="text-[11px] uppercase tracking-wider text-rose-400 mb-1">{{ $product->category->name }}</p>
        @endif
        <a href="{{ route('product.show', $product) }}" class="block font-medium text-ink line-clamp-1 hover:text-rose-700 transition">{{ $product->name }}</a>

        <div class="mt-2 flex items-center justify-between">
            @if($product->price_visible)
                <div class="flex items-baseline gap-2">
                    <span class="font-serif text-lg font-semibold text-rose-700">{{ money($product->effective_price) }}</span>
                    @if($product->is_on_sale)
                        <span class="text-sm text-ink/40 line-through">{{ money($product->price) }}</span>
                    @endif
                </div>
            @else
                <span class="text-sm font-medium text-ink/50">Enquire for price</span>
            @endif

            @if($product->approvedReviews->count())
                <span class="inline-flex items-center gap-1 text-xs text-gold-dark">
                    <svg class="h-3.5 w-3.5 fill-gold" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.49 6.91l6.572-.955L10 0l2.938 5.955 6.572.955-4.755 4.635 1.123 6.545z"/></svg>
                    {{ number_format($product->average_rating, 1) }}
                </span>
            @endif
        </div>
    </div>
</div>
