@php($siteName = setting('site_name', 'Navanari'))

{{-- Newsletter / WhatsApp strip --}}
<section class="container-x mt-20">
    <div class="relative overflow-hidden rounded-[2.5rem] bg-gradient-to-br from-rose-700 via-rose-600 to-rose-800 px-6 py-14 sm:px-14 text-center text-white shadow-glow" data-reveal="zoom">
        <div class="pointer-events-none absolute -top-16 -right-10 h-56 w-56 rounded-full bg-gold/20 blur-3xl animate-float"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-10 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
        <p class="heading-script text-2xl text-gold-light mb-2">Stay in touch</p>
        <h3 class="font-serif text-3xl sm:text-4xl font-semibold">Be the first to see new arrivals</h3>
        <p class="mt-3 text-rose-100 max-w-xl mx-auto">Message us on WhatsApp to get personalised recommendations, festive lookbooks and early access to offers.</p>
        <a href="{{ whatsapp_link('Hi '.$siteName.'! Please add me for new arrival updates.') }}" target="_blank" rel="noopener" class="btn-gold mt-7">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
            Chat with us
        </a>
    </div>
</section>

<footer class="mt-20 border-t border-rose-100 bg-white/60">
    <div class="container-x py-14 grid gap-10 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <span class="text-2xl font-serif font-bold text-gradient">{{ $siteName }}</span>
            <p class="mt-3 text-sm text-ink/60 leading-relaxed">{{ setting('footer_note', 'Handpicked elegance, delivered with love.') }}</p>
            <div class="mt-5 flex gap-2.5">
                @foreach(['instagram'=>'instagram_url','facebook'=>'facebook_url','youtube'=>'youtube_url','pinterest'=>'pinterest_url'] as $name => $key)
                    @if($url = setting($key))
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="group flex h-10 w-10 items-center justify-center rounded-full bg-rose-50 text-rose-700 ring-1 ring-rose-100 hover:bg-rose-600 hover:text-white hover:-translate-y-0.5 hover:shadow-soft transition-all" aria-label="{{ $name }}">
                            <x-icon :name="$name" class="h-[18px] w-[18px]" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="font-semibold text-ink mb-4">Shop</h4>
            <ul class="space-y-2.5 text-sm text-ink/60">
                @foreach(($navCategories ?? collect())->take(6) as $cat)
                    <li><a href="{{ route('shop', ['category'=>$cat->slug]) }}" class="hover:text-rose-700 transition">{{ $cat->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h4 class="font-semibold text-ink mb-4">Company</h4>
            <ul class="space-y-2.5 text-sm text-ink/60">
                <li><a href="{{ route('about') }}" class="hover:text-rose-700 transition">About Us</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-rose-700 transition">Contact</a></li>
                <li><a href="{{ route('shop') }}" class="hover:text-rose-700 transition">All Products</a></li>
                <li><a href="{{ route('shop', ['sale'=>1]) }}" class="hover:text-rose-700 transition">Offers</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-semibold text-ink mb-4">Get in touch</h4>
            <ul class="space-y-3 text-sm text-ink/60">
                @if($p = setting('contact_phone'))<li class="flex items-center gap-2.5"><x-icon name="phone" class="h-4 w-4 text-rose-500 shrink-0" />{{ $p }}</li>@endif
                @if($e = setting('contact_email'))<li class="flex items-center gap-2.5"><x-icon name="mail" class="h-4 w-4 text-rose-500 shrink-0" /><a href="mailto:{{ $e }}" class="hover:text-rose-700">{{ $e }}</a></li>@endif
                @if($ad = setting('contact_address'))<li class="flex items-start gap-2.5"><x-icon name="pin" class="h-4 w-4 text-rose-500 shrink-0 mt-0.5" /><span>{{ $ad }}</span></li>@endif
            </ul>
        </div>
    </div>

    <div class="border-t border-rose-100">
        <div class="container-x py-5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-ink/50">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</p>
            <p>Crafted with <span class="text-rose-600">♥</span> for the modern woman.</p>
        </div>
    </div>
</footer>
