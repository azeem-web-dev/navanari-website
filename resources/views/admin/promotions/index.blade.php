@extends('layouts.admin')
@section('title', 'Promotions')
@section('heading', 'Promotions & Banners')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-ink/60 text-sm">Hero sliders, announcement bar & promo strips</p>
    <a href="{{ route('admin.promotions.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add Promotion</a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($promotions as $promo)
        <div class="card overflow-hidden">
            <div class="relative h-36 bg-rose-100">
                @if($promo->image_url)<img src="{{ $promo->image_url }}" class="h-full w-full object-cover" alt="">@endif
                <span class="absolute top-2 left-2 chip !bg-white/90">{{ ucfirst($promo->position) }}</span>
                <span class="absolute top-2 right-2 chip {{ $promo->is_active ? '!bg-green-50 !text-green-700' : '!bg-gray-100 !text-gray-500' }}">{{ $promo->is_active ? 'Active':'Off' }}</span>
            </div>
            <div class="p-4">
                <p class="font-medium text-ink line-clamp-1">{{ $promo->title }}</p>
                <p class="text-xs text-ink/50 line-clamp-1">{{ $promo->subtitle }}</p>
                <div class="flex items-center gap-3 mt-3 text-sm">
                    <a href="{{ route('admin.promotions.edit', $promo) }}" class="text-rose-600 hover:underline">Edit</a>
                    <form method="POST" action="{{ route('admin.promotions.destroy', $promo) }}" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="text-ink/40 hover:text-rose-600">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-10 text-center text-ink/50 col-span-full">No promotions yet. <a href="{{ route('admin.promotions.create') }}" class="text-rose-600">Add one</a>.</div>
    @endforelse
</div>
@endsection
