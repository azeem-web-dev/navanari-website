@extends('layouts.storefront')

@section('title', 'My Wishlist — '.setting('site_name','Navanari'))

@section('content')
<div class="container-x py-12" x-data>
    <div class="text-center mb-10" data-reveal>
        <p class="heading-script text-2xl mb-1">Your favourites</p>
        <h1 class="font-serif text-4xl font-semibold text-ink">My Wishlist</h1>
        <p class="mt-2 text-ink/60">Saved on this device — no account needed.</p>
    </div>

    {{-- Empty state --}}
    <div x-show="$store.wishlist.count === 0" class="card text-center py-20 px-6 max-w-xl mx-auto">
        <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 text-rose-500"><x-icon name="heart" class="h-8 w-8" /></span>
        <h3 class="font-serif text-2xl text-ink mb-2">Your wishlist is empty</h3>
        <p class="text-ink/60 mb-6">Tap the heart on any product to save it here.</p>
        <a href="{{ route('shop') }}" class="btn-primary">Start Shopping</a>
    </div>

    {{-- Items --}}
    <div x-show="$store.wishlist.count > 0" x-cloak class="grid grid-cols-2 lg:grid-cols-4 gap-5">
        <template x-for="item in $store.wishlist.items" :key="item.id">
            <div class="card overflow-hidden group">
                <a :href="item.url" class="block aspect-[4/5] overflow-hidden bg-rose-50">
                    <img :src="item.image" :alt="item.name" class="h-full w-full object-cover hover-zoom-img">
                </a>
                <div class="p-4">
                    <a :href="item.url" class="block font-medium text-ink line-clamp-1 hover:text-rose-700" x-text="item.name"></a>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="font-serif text-lg font-semibold text-rose-700" x-text="item.price || 'Enquire'"></span>
                        <button @click="$store.wishlist.remove(item.id)" class="text-xs text-ink/40 hover:text-rose-600 transition">Remove</button>
                    </div>
                    <a :href="item.url" class="btn-outline w-full mt-3 !py-2 text-xs">View Product</a>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection
