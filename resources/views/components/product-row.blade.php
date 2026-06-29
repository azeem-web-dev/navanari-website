@props(['title', 'products', 'link' => null, 'subtitle' => null])

<section class="container-x mt-20"
         x-data="{
            scrollBy(dir) {
                const el = $refs.track;
                el.scrollBy({ left: dir * Math.max(280, el.clientWidth * 0.8), behavior: 'smooth' });
            }
         }">
    <div class="flex items-end justify-between gap-4 mb-6" data-reveal>
        <div>
            @if($subtitle)<p class="heading-script text-xl sm:text-2xl mb-0.5">{{ $subtitle }}</p>@endif
            <h2 class="font-serif text-2xl sm:text-3xl font-semibold text-ink">{{ $title }}</h2>
        </div>
        <div class="flex items-center gap-2">
            @if($link)
                <a href="{{ $link }}" class="hidden sm:inline-flex btn-outline !py-2 !px-4 text-xs">
                    View all <x-icon name="arrow-right" class="h-4 w-4" />
                </a>
            @endif
            <button type="button" @click="scrollBy(-1)" class="hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-white text-rose-700 ring-1 ring-rose-200 shadow-soft hover:bg-rose-600 hover:text-white transition" aria-label="Scroll left">
                <x-icon name="chevron-left" class="h-5 w-5" />
            </button>
            <button type="button" @click="scrollBy(1)" class="hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-white text-rose-700 ring-1 ring-rose-200 shadow-soft hover:bg-rose-600 hover:text-white transition" aria-label="Scroll right">
                <x-icon name="chevron-right" class="h-5 w-5" />
            </button>
        </div>
    </div>

    <div x-ref="track" class="flex gap-5 overflow-x-auto snap-x snap-mandatory pb-4 -mx-4 px-4 scrollbar-hide">
        @foreach($products as $product)
            <div class="snap-start shrink-0 w-[230px] sm:w-[260px]">
                <x-product-card :product="$product" :reveal="false" />
            </div>
        @endforeach
    </div>
</section>
