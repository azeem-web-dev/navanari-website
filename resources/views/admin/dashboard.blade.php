@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @php
        $cards = [
            ['label'=>'Products','value'=>$stats['products'],'sub'=>$stats['active_products'].' active','color'=>'from-rose-500 to-rose-700','route'=>route('admin.products.index')],
            ['label'=>'Categories','value'=>$stats['categories'],'sub'=>'collections','color'=>'from-fuchsia-500 to-purple-600','route'=>route('admin.categories.index')],
            ['label'=>'Enquiries','value'=>$stats['enquiries'],'sub'=>$stats['new_enquiries'].' new','color'=>'from-amber-500 to-orange-600','route'=>route('admin.enquiries.index')],
            ['label'=>'Pending Reviews','value'=>$stats['pending_reviews'],'sub'=>'to moderate','color'=>'from-emerald-500 to-teal-600','route'=>route('admin.reviews.index')],
        ];
    @endphp
    @foreach($cards as $card)
        <a href="{{ $card['route'] }}" class="card p-5 hover:-translate-y-1 transition group">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-ink/50">{{ $card['label'] }}</p>
                    <p class="mt-1 font-serif text-3xl font-bold text-ink">{{ number_format($card['value']) }}</p>
                    <p class="mt-1 text-xs text-rose-500">{{ $card['sub'] }}</p>
                </div>
                <span class="h-11 w-11 rounded-2xl bg-gradient-to-br {{ $card['color'] }} opacity-90 group-hover:scale-110 transition"></span>
            </div>
        </a>
    @endforeach
</div>

<div class="grid lg:grid-cols-3 gap-6 mt-6">
    {{-- Enquiries chart --}}
    <div class="lg:col-span-2 card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-semibold text-ink">Enquiries · Last 7 days</h2>
            <a href="{{ route('admin.enquiries.index') }}" class="text-sm text-rose-600 hover:underline">View all</a>
        </div>
        @php($max = max(1, $chart->max('count')))
        <div class="flex items-end justify-between gap-3 h-44">
            @foreach($chart as $day)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full rounded-t-xl bg-gradient-to-t from-rose-600 to-rose-400 transition-all hover:from-rose-700"
                         style="height: {{ max(6, ($day['count']/$max)*100) }}%" title="{{ $day['count'] }} enquiries"></div>
                    <span class="text-xs text-ink/50">{{ $day['label'] }}</span>
                    <span class="text-xs font-semibold text-ink">{{ $day['count'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="card p-6">
        <h2 class="font-semibold text-ink mb-4">Quick Actions</h2>
        <div class="space-y-2.5">
            <a href="{{ route('admin.products.create') }}" class="btn-primary w-full !justify-start">＋ Add Product</a>
            <a href="{{ route('admin.categories.create') }}" class="btn-outline w-full !justify-start">＋ Add Category</a>
            <a href="{{ route('admin.promotions.create') }}" class="btn-outline w-full !justify-start">＋ Add Promotion</a>
            <a href="{{ route('admin.settings.edit') }}" class="btn-outline w-full !justify-start">⚙ Site Settings</a>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mt-6">
    {{-- Recent enquiries --}}
    <div class="card p-6">
        <h2 class="font-semibold text-ink mb-4">Recent Enquiries</h2>
        <div class="space-y-3">
            @forelse($recentEnquiries as $e)
                <div class="flex items-center justify-between rounded-xl bg-rose-50/60 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-ink line-clamp-1">{{ $e->product_name ?? $e->product?->name ?? 'General enquiry' }}</p>
                        <p class="text-xs text-ink/50">{{ ucfirst($e->source) }} · {{ $e->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="chip">{{ ucfirst($e->status) }}</span>
                </div>
            @empty
                <p class="text-sm text-ink/50">No enquiries yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Pending reviews --}}
    <div class="card p-6">
        <h2 class="font-semibold text-ink mb-4">Reviews Awaiting Approval</h2>
        <div class="space-y-3">
            @forelse($pendingReviews as $r)
                <div class="flex items-center justify-between rounded-xl bg-rose-50/60 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-ink">{{ $r->author_name }} · <span class="text-gold-dark">{{ str_repeat('★', $r->rating) }}</span></p>
                        <p class="text-xs text-ink/50 line-clamp-1">{{ $r->product?->name }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">
                        @csrf @method('PATCH')
                        <button class="text-xs font-semibold text-green-600 hover:underline">Approve</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-ink/50">No pending reviews 🎉</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
