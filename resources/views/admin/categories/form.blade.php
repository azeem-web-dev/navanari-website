@extends('layouts.admin')
@section('title', $category->exists ? 'Edit Category' : 'New Category')
@section('heading', $category->exists ? 'Edit Category' : 'New Category')

@section('content')
<form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf
    @if($category->exists) @method('PUT') @endif

    <div class="card p-6 space-y-5">
        <div>
            <label class="label">Name <span class="text-rose-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="input">
        </div>

        <div>
            <label class="label">Description</label>
            <textarea name="description" rows="3" class="input">{{ old('description', $category->description) }}</textarea>
        </div>

        <div>
            <label class="label">Parent Category</label>
            <select name="parent_id" class="input">
                <option value="">— None (top level) —</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Image URL</label>
                <input type="text" name="image" value="{{ old('image', $category->image) }}" placeholder="https://…" class="input">
            </div>
            <div>
                <label class="label">…or upload image</label>
                <input type="file" name="image_file" accept="image/*" class="input !py-2">
            </div>
        </div>

        @if($category->image)
            <img src="{{ \Illuminate\Support\Str::startsWith($category->image,['http']) ? $category->image : \Illuminate\Support\Facades\Storage::url($category->image) }}" class="h-24 w-24 rounded-xl object-cover" alt="">
        @endif

        <div class="grid sm:grid-cols-3 gap-4 items-end">
            <div>
                <label class="label">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input">
            </div>
            <label class="flex items-center gap-2.5 text-sm pb-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                Active (visible)
            </label>
            <label class="flex items-center gap-2.5 text-sm pb-3">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $category->is_featured) ? 'checked' : '' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                Featured on home
            </label>
        </div>
    </div>

    <div class="flex gap-3 mt-6">
        <button class="btn-primary">{{ $category->exists ? 'Update Category' : 'Create Category' }}</button>
        <a href="{{ route('admin.categories.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
