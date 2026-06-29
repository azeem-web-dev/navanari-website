@extends('layouts.storefront')

@section('title', 'About Us — '.setting('site_name','Navanari'))

@section('content')
<div class="container-x py-12">
    {{-- Hero --}}
    <div class="relative overflow-hidden rounded-[2.5rem] shadow-glow mb-16" data-reveal="zoom">
        <img src="{{ setting('hero_image') }}" alt="" class="h-72 sm:h-96 w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-ink/70 to-ink/20 flex items-center">
            <div class="p-8 sm:p-16 text-white max-w-xl">
                <p class="heading-script text-3xl text-gold-light mb-2">Our Story</p>
                <h1 class="font-serif text-4xl sm:text-5xl font-semibold">About {{ setting('site_name','Navanari') }}</h1>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div data-reveal="left">
            <div class="divider-fancy !mx-0 justify-start mb-4"><span class="text-sm uppercase tracking-[0.3em]">Who we are</span></div>
            <h2 class="font-serif text-3xl font-semibold text-ink mb-4">Elegance, crafted with care</h2>
            <p class="text-ink/70 leading-relaxed">{{ setting('about_text') }}</p>
            <p class="mt-4 text-ink/70 leading-relaxed">{{ setting('site_description') }}</p>
            <a href="{{ route('shop') }}" class="btn-primary mt-7">Explore the Collection</a>
        </div>
        <div class="grid grid-cols-2 gap-4" data-reveal="right">
            <img src="https://images.unsplash.com/photo-1610030469983-98e550d6193c?q=80&w=600&auto=format&fit=crop" class="rounded-3xl shadow-soft h-56 w-full object-cover animate-float" alt="">
            <img src="https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?q=80&w=600&auto=format&fit=crop" class="rounded-3xl shadow-soft h-56 w-full object-cover mt-8" alt="">
            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?q=80&w=600&auto=format&fit=crop" class="rounded-3xl shadow-soft h-56 w-full object-cover" alt="">
            <img src="https://images.unsplash.com/photo-1543163521-1bf539c55dd2?q=80&w=600&auto=format&fit=crop" class="rounded-3xl shadow-soft h-56 w-full object-cover mt-8" alt="">
        </div>
    </div>

    {{-- Values --}}
    <div class="grid sm:grid-cols-3 gap-5 mt-20">
        @foreach([
            ['heart','Curated with Love','Every piece is handpicked for quality, comfort and timeless style.'],
            ['chat','Personal Service','Chat with us on WhatsApp for styling advice and quick answers.'],
            ['gem','Authentic Craft','Supporting artisans and authentic Indian craftsmanship.'],
        ] as $i => $v)
            <div data-reveal data-reveal-delay="{{ $i*100 }}" class="card p-7 text-center">
                <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-rose-600 to-rose-500 text-white shadow-soft">
                    <x-icon :name="$v[0]" class="h-6 w-6" :stroke="1.6" />
                </span>
                <h3 class="font-serif text-xl font-semibold text-ink mb-2">{{ $v[1] }}</h3>
                <p class="text-sm text-ink/60 leading-relaxed">{{ $v[2] }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
