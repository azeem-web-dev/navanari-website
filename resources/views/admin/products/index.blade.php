@extends('layouts.admin')
@section('title', 'Products')
@section('heading', 'Products')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <form method="GET" class="flex gap-2 flex-1 max-w-md">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products…" class="input !py-2.5">
        <select name="category" class="input !py-2.5 max-w-[10rem]" onchange="this.form.submit()">
            <option value="">All categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category')==$cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="btn-outline !py-2.5">Go</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn-primary shrink-0"><x-icon name="plus" class="h-4 w-4" /> Add Product</a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-rose-50/60 text-left text-ink/60">
            <tr>
                <th class="px-5 py-3 font-medium">Product</th>
                <th class="px-5 py-3 font-medium">Category</th>
                <th class="px-5 py-3 font-medium">Price</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-50">
            @forelse($products as $product)
                <tr class="hover:bg-rose-50/40">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="h-12 w-12 rounded-xl bg-rose-100 overflow-hidden shrink-0">
                                @if($product->primary_image)<img src="{{ $product->primary_image }}" class="h-full w-full object-cover" alt="">@endif
                            </span>
                            <div>
                                <p class="font-medium text-ink line-clamp-1">{{ $product->name }}</p>
                                <p class="text-xs text-ink/40">{{ $product->sku }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-ink/70">{{ $product->category?->name ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="font-medium text-rose-700">{{ money($product->effective_price) }}</span>
                        @if($product->is_on_sale)<span class="block text-xs text-ink/40 line-through">{{ money($product->price) }}</span>@endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="chip {{ $product->is_active ? '!bg-green-50 !text-green-700 !ring-green-100' : '!bg-gray-100 !text-gray-500 !ring-gray-200' }}">{{ $product->is_active ? 'Active' : 'Hidden' }}</span>
                        @if($product->is_featured)<span class="chip !bg-gold/10 !text-gold-dark !ring-gold/20">★</span>@endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('product.show', $product) }}" target="_blank" class="text-ink/40 hover:text-rose-600">View</a>
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-rose-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                @csrf @method('DELETE')
                                <button class="text-ink/40 hover:text-rose-600">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-ink/50">No products found. <a href="{{ route('admin.products.create') }}" class="text-rose-600">Add one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $products->links() }}</div>
@endsection
