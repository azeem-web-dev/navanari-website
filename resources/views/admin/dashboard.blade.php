@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
@if($pendingUpdates > 0)
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-amber-50 to-rose-50 px-5 py-4 ring-1 ring-amber-200">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-amber-600"><x-icon name="sparkles" class="h-5 w-5" /></span>
            <div>
                <p class="font-semibold text-ink">New updates are ready to apply</p>
                <p class="text-sm text-ink/60">A new feature was deployed. Click apply to finish setting it up — safe and instant.</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.system.update') }}">
            @csrf
            <button class="btn-primary shrink-0">Apply Updates</button>
        </form>
    </div>
@endif

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
            <a href="{{ route('admin.products.create') }}" class="btn-primary w-full !justify-start"><x-icon name="plus" class="h-4 w-4" /> Add Product</a>
            <a href="{{ route('admin.categories.create') }}" class="btn-outline w-full !justify-start"><x-icon name="plus" class="h-4 w-4" /> Add Category</a>
            <a href="{{ route('admin.promotions.create') }}" class="btn-outline w-full !justify-start"><x-icon name="plus" class="h-4 w-4" /> Add Promotion</a>
            <a href="{{ route('admin.settings.edit') }}" class="btn-outline w-full !justify-start"><x-icon name="settings" class="h-4 w-4" /> Site Settings</a>
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
                <p class="flex items-center gap-2 text-sm text-ink/50"><x-icon name="check-circle" class="h-4 w-4 text-green-500" /> No pending reviews</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
