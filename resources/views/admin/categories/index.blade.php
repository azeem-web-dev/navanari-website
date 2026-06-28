@extends('layouts.admin')
@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
<div class="flex items-center justify-between mb-6">
    <p class="text-ink/60 text-sm">{{ $categories->total() }} categories</p>
    <a href="{{ route('admin.categories.create') }}" class="btn-primary">＋ Add Category</a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-rose-50/60 text-left text-ink/60">
            <tr>
                <th class="px-5 py-3 font-medium">Category</th>
                <th class="px-5 py-3 font-medium hidden sm:table-cell">Products</th>
                <th class="px-5 py-3 font-medium hidden md:table-cell">Status</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-rose-50">
            @forelse($categories as $cat)
                <tr class="hover:bg-rose-50/40">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <span class="h-10 w-10 rounded-xl bg-rose-100 overflow-hidden shrink-0">
                                @if($cat->image)<img src="{{ \Illuminate\Support\Str::startsWith($cat->image,['http']) ? $cat->image : \Illuminate\Support\Facades\Storage::url($cat->image) }}" class="h-full w-full object-cover" alt="">@endif
                            </span>
                            <div>
                                <p class="font-medium text-ink">{{ $cat->name }}</p>
                                <p class="text-xs text-ink/40">/{{ $cat->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 hidden sm:table-cell">{{ $cat->products_count }}</td>
                    <td class="px-5 py-3 hidden md:table-cell">
                        <span class="chip {{ $cat->is_active ? '!bg-green-50 !text-green-700 !ring-green-100' : '!bg-gray-100 !text-gray-500 !ring-gray-200' }}">{{ $cat->is_active ? 'Active' : 'Hidden' }}</span>
                        @if($cat->is_featured)<span class="chip !bg-gold/10 !text-gold-dark !ring-gold/20">Featured</span>@endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.categories.edit', $cat) }}" class="text-rose-600 hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')">
                                @csrf @method('DELETE')
                                <button class="text-ink/40 hover:text-rose-600">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-ink/50">No categories yet. <a href="{{ route('admin.categories.create') }}" class="text-rose-600">Add one</a>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $categories->links() }}</div>
@endsection
