@extends('layouts.admin')
@section('title', $promotion->exists ? 'Edit Promotion' : 'New Promotion')
@section('heading', $promotion->exists ? 'Edit Promotion' : 'New Promotion')

@section('content')
<form method="POST" action="{{ $promotion->exists ? route('admin.promotions.update', $promotion) : route('admin.promotions.store') }}" enctype="multipart/form-data" class="max-w-3xl">
    @csrf
    @if($promotion->exists) @method('PUT') @endif

    <div class="card p-6 space-y-5">
        <div>
            <label class="label">Title <span class="text-rose-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $promotion->title) }}" required class="input">
        </div>
        <div>
            <label class="label">Subtitle</label>
            <input type="text" name="subtitle" value="{{ old('subtitle', $promotion->subtitle) }}" class="input">
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Position</label>
                <select name="position" class="input">
                    @foreach(['hero'=>'Hero Slider','strip'=>'Mid-page Strip','topbar'=>'Announcement Bar'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('position', $promotion->position) === $val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $promotion->sort_order ?? 0) }}" class="input">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Button Text</label>
                <input type="text" name="button_text" value="{{ old('button_text', $promotion->button_text) }}" placeholder="Shop Now" class="input">
            </div>
            <div>
                <label class="label">Link / URL</label>
                <input type="text" name="link" value="{{ old('link', $promotion->link) }}" placeholder="/shop?category=sarees" class="input">
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="label">Image URL</label>
                <input type="text" name="image" value="{{ old('image', $promotion->image) }}" placeholder="https://…" class="input">
            </div>
            <div>
                <label class="label">…or upload image</label>
                <input type="file" name="image_file" accept="image/*" class="input !py-2">
            </div>
        </div>
        @if($promotion->image_url)
            <img src="{{ $promotion->image_url }}" class="h-28 rounded-xl object-cover" alt="">
        @endif
        <label class="flex items-center gap-2.5 text-sm">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $promotion->is_active ?? true) ? 'checked':'' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
            Active
        </label>
    </div>

    <div class="flex gap-3 mt-6">
        <button class="btn-primary">{{ $promotion->exists ? 'Update' : 'Create' }} Promotion</button>
        <a href="{{ route('admin.promotions.index') }}" class="btn-outline">Cancel</a>
    </div>
</form>
@endsection
