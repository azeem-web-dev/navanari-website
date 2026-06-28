@extends('layouts.admin')
@section('title', 'Reviews')
@section('heading', 'Reviews')

@section('content')
<div class="flex flex-wrap items-center gap-2 mb-6 text-sm">
    @php($statuses = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved'])
    @foreach($statuses as $val => $lbl)
        <a href="{{ route('admin.reviews.index', array_filter(['status'=>$val])) }}"
           class="rounded-full px-4 py-2 transition {{ request('status', '') === $val ? 'bg-rose-600 text-white' : 'bg-white text-ink/60 ring-1 ring-rose-100 hover:bg-rose-50' }}">{{ $lbl }}</a>
    @endforeach
</div>

<div class="space-y-3">
    @forelse($reviews as $review)
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-semibold text-sm">{{ strtoupper(substr($review->author_name,0,1)) }}</span>
                        <span class="font-medium text-ink">{{ $review->author_name }}</span>
                        <span class="text-gold-dark">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        <span class="chip {{ $review->is_approved ? '!bg-green-50 !text-green-700' : '!bg-amber-50 !text-amber-700' }}">{{ $review->is_approved ? 'Approved' : 'Pending' }}</span>
                    </div>
                    @if($review->title)<p class="mt-2 font-medium text-ink">{{ $review->title }}</p>@endif
                    <p class="mt-1 text-sm text-ink/70">{{ $review->body }}</p>
                    <p class="mt-2 text-xs text-ink/40">on <span class="text-rose-600">{{ $review->product?->name }}</span> · {{ $review->created_at->diffForHumans() }}</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                        @csrf @method('PATCH')
                        <button class="btn-outline !py-2 !px-4 text-xs {{ $review->is_approved ? '' : '!bg-green-600 !text-white !border-green-600 hover:!bg-green-700' }}">
                            {{ $review->is_approved ? 'Unpublish' : 'Approve' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review?')">
                        @csrf @method('DELETE')
                        <button class="text-ink/40 hover:text-rose-600 text-sm px-2">✕</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-12 text-center text-ink/50">No reviews yet.</div>
    @endforelse
</div>

<div class="mt-6">{{ $reviews->links() }}</div>
@endsection
