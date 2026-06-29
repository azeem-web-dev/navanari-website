@extends('layouts.storefront')

@section('title', 'Contact Us — '.setting('site_name','Navanari'))

@section('content')
<div class="container-x py-12">
    <div class="text-center mb-12" data-reveal>
        <p class="heading-script text-2xl mb-1">We'd love to hear from you</p>
        <h1 class="font-serif text-4xl font-semibold text-ink">Get in Touch</h1>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        {{-- Info + WhatsApp --}}
        <div class="space-y-4" data-reveal="left">
            <div class="card p-7">
                <h2 class="font-serif text-2xl font-semibold text-ink mb-5">Reach us directly</h2>
                <ul class="space-y-4 text-ink/75">
                    @if($p = setting('contact_phone'))
                        <li class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700"><x-icon name="phone" class="h-5 w-5" /></span>{{ $p }}</li>
                    @endif
                    @if($e = setting('contact_email'))
                        <li class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700"><x-icon name="mail" class="h-5 w-5" /></span><a href="mailto:{{ $e }}" class="hover:text-rose-700">{{ $e }}</a></li>
                    @endif
                    @if($a = setting('contact_address'))
                        <li class="flex items-center gap-3"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-700"><x-icon name="pin" class="h-5 w-5" /></span>{{ $a }}</li>
                    @endif
                </ul>
                <a href="{{ whatsapp_link('Hello '.setting('site_name','Navanari').'! I have a question.') }}" target="_blank" rel="noopener" class="btn-whatsapp w-full mt-6">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.945C.16 5.335 5.495 0 12.05 0a11.817 11.817 0 018.413 3.488 11.824 11.824 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>

        {{-- Form --}}
        <div class="card p-7" data-reveal="right">
            <h2 class="font-serif text-2xl font-semibold text-ink mb-5">Send us a message</h2>
            @if(session('status'))
                <div class="mb-4 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}" required class="input">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="input">
                    </div>
                    <div>
                        <label class="label">Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="input">
                    </div>
                </div>
                <div>
                    <label class="label">Message</label>
                    <textarea name="message" rows="5" required class="input">{{ old('message') }}</textarea>
                </div>
                <button class="btn-primary w-full">Send Message</button>
            </form>
        </div>
    </div>
</div>
@endsection
