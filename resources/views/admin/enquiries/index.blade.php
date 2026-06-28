@extends('layouts.admin')
@section('title', 'Enquiries')
@section('heading', 'Enquiries')

@section('content')
{{-- Filters --}}
<div class="flex flex-wrap items-center gap-2 mb-6 text-sm">
    @php($statuses = ['' => 'All', 'new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed'])
    @foreach($statuses as $val => $lbl)
        <a href="{{ route('admin.enquiries.index', array_filter(['status'=>$val])) }}"
           class="rounded-full px-4 py-2 transition {{ request('status', '') === $val ? 'bg-rose-600 text-white' : 'bg-white text-ink/60 ring-1 ring-rose-100 hover:bg-rose-50' }}">{{ $lbl }}</a>
    @endforeach
</div>

<div class="space-y-3">
    @forelse($enquiries as $enquiry)
        <div class="card p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="font-medium text-ink">{{ $enquiry->product_name ?? $enquiry->product?->name ?? 'General enquiry' }}</p>
                        <span class="chip">{{ ucfirst($enquiry->source) }}</span>
                        @if($enquiry->variant)
                            @foreach($enquiry->variant as $k => $v)
                                <span class="chip !bg-rose-50">{{ ucfirst($k) }}: {{ $v }}</span>
                            @endforeach
                        @endif
                    </div>
                    @if($enquiry->customer_name || $enquiry->customer_phone || $enquiry->customer_email)
                        <p class="mt-1 text-sm text-ink/60">
                            {{ $enquiry->customer_name }}
                            @if($enquiry->customer_phone) · 📞 {{ $enquiry->customer_phone }} @endif
                            @if($enquiry->customer_email) · ✉️ {{ $enquiry->customer_email }} @endif
                        </p>
                    @endif
                    @if($enquiry->message)<p class="mt-2 text-sm text-ink/70 bg-rose-50/50 rounded-xl px-3 py-2">{{ $enquiry->message }}</p>@endif
                    <p class="mt-2 text-xs text-ink/40">{{ $enquiry->created_at->format('d M Y, g:i A') }} · {{ $enquiry->created_at->diffForHumans() }}</p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="input !py-2 text-sm">
                            @foreach(['new'=>'New','contacted'=>'Contacted','closed'=>'Closed'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ $enquiry->status===$val ? 'selected':'' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </form>
                    @if($enquiry->product)
                        <a href="{{ route('product.show', $enquiry->product) }}" target="_blank" class="btn-outline !py-2 !px-3 text-xs">View</a>
                    @endif
                    <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}" onsubmit="return confirm('Delete this enquiry?')">
                        @csrf @method('DELETE')
                        <button class="text-ink/40 hover:text-rose-600 text-sm px-2">✕</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-12 text-center text-ink/50">No enquiries yet.</div>
    @endforelse
</div>

<div class="mt-6">{{ $enquiries->links() }}</div>
@endsection
