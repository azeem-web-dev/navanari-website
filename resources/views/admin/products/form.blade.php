@extends('layouts.admin')
@section('title', $product->exists ? 'Edit Product' : 'New Product')
@section('heading', $product->exists ? 'Edit Product' : 'New Product')

@section('content')
<form method="POST" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data"
      class="grid lg:grid-cols-3 gap-6">
    @csrf
    @if($product->exists) @method('PUT') @endif

    {{-- Main column --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="card p-6 space-y-5">
            <h2 class="font-semibold text-ink">Basic Details</h2>
            <div>
                <label class="label">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Category</label>
                    <select name="category_id" class="input">
                        <option value="">— Select —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected':'' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="input">
                </div>
            </div>
            <div>
                <label class="label">Short Description</label>
                <input type="text" name="short_description" value="{{ old('short_description', $product->short_description) }}" maxlength="500" class="input">
            </div>
            <div>
                <label class="label">Full Description <span class="text-ink/40 text-xs">(basic HTML allowed)</span></label>
                <textarea name="description" rows="6" class="input">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>

        {{-- Images --}}
        <div class="card p-6 space-y-5">
            <h2 class="font-semibold text-ink">Images</h2>

            @if($product->exists && $product->images->count())
                <div class="grid grid-cols-4 sm:grid-cols-6 gap-3">
                    @foreach($product->images as $image)
                        <div class="relative group">
                            <img src="{{ $image->url }}" class="h-20 w-full rounded-xl object-cover ring-1 ring-rose-100" alt="">
                            @if($image->is_primary)<span class="absolute top-1 left-1 chip !px-1.5 !py-0.5 !text-[9px]">Main</span>@endif
                            <button type="button" onclick="if(confirm('Remove image?')){document.getElementById('del-img-{{ $image->id }}').submit()}"
                                    class="absolute -top-1.5 -right-1.5 h-5 w-5 rounded-full bg-rose-600 text-white text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
                        </div>
                    @endforeach
                </div>
            @endif

            <div>
                <label class="label">Upload Images <span class="text-ink/40 text-xs">(multiple allowed)</span></label>
                <input type="file" name="images[]" multiple accept="image/*" class="input !py-2">
            </div>
            <div>
                <label class="label">…or paste image URLs <span class="text-ink/40 text-xs">(one per line)</span></label>
                <textarea name="image_urls" rows="2" placeholder="https://…" class="input"></textarea>
            </div>
        </div>

        {{-- Variant pricing (Amazon-style) --}}
        @php
            $variantRows = $product->relationLoaded('variants')
                ? $product->variants->map(fn ($v) => [
                    '_k' => $v->id,
                    'size' => $v->size,
                    'color' => $v->color,
                    'price' => $v->price,
                    'sale_price' => $v->sale_price,
                    'stock_status' => $v->stock_status,
                    'sku' => $v->sku,
                    'image' => $v->image,
                    'note' => $v->note,
                ])->values()->all()
                : [];
        @endphp
        <div class="card p-6 space-y-4"
             x-data="{
                rows: {{ \Illuminate\Support\Js::from($variantRows) }},
                add() { this.rows.push({ _k: Math.random(), size: '', color: '', price: '', sale_price: '', stock_status: 'in_stock', sku: '', image: '', note: '' }); },
                remove(i) { this.rows.splice(i, 1); }
             }">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-ink">Variant Pricing</h2>
                    <p class="text-xs text-ink/50 mt-0.5">Optional — give each size/colour its own price, sale, stock & image (like Amazon). Leave empty to use the base price.</p>
                </div>
                <button type="button" @click="add()" class="btn-outline !py-2 !px-4 text-xs shrink-0"><x-icon name="plus" class="h-4 w-4" /> Add</button>
            </div>

            <div class="space-y-3" x-show="rows.length">
                <template x-for="(v, i) in rows" :key="v._k">
                    <div class="rounded-2xl bg-rose-50/40 ring-1 ring-rose-100 p-4">
                        <div class="grid grid-cols-2 lg:grid-cols-12 gap-3">
                            <div class="lg:col-span-2">
                                <label class="label !text-xs">Size</label>
                                <input :name="`variants[${i}][size]`" x-model="v.size" placeholder="M" class="input !py-2 text-sm">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="label !text-xs">Colour</label>
                                <input :name="`variants[${i}][color]`" x-model="v.color" placeholder="Maroon" class="input !py-2 text-sm">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="label !text-xs">Price ₹</label>
                                <input type="number" step="0.01" :name="`variants[${i}][price]`" x-model="v.price" placeholder="0" class="input !py-2 text-sm">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="label !text-xs">Sale ₹</label>
                                <input type="number" step="0.01" :name="`variants[${i}][sale_price]`" x-model="v.sale_price" placeholder="—" class="input !py-2 text-sm">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="label !text-xs">Stock</label>
                                <select :name="`variants[${i}][stock_status]`" x-model="v.stock_status" class="input !py-2 text-sm">
                                    <option value="in_stock">In stock</option>
                                    <option value="out_of_stock">Out</option>
                                    <option value="made_to_order">To order</option>
                                </select>
                            </div>
                            <div class="lg:col-span-2 flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="label !text-xs">SKU</label>
                                    <input :name="`variants[${i}][sku]`" x-model="v.sku" placeholder="opt." class="input !py-2 text-sm">
                                </div>
                                <button type="button" @click="remove(i)" class="mb-1 flex h-9 w-9 items-center justify-center rounded-lg text-rose-500 hover:bg-rose-100" aria-label="Remove variant">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>
                            <div class="lg:col-span-12">
                                <input :name="`variants[${i}][image]`" x-model="v.image" placeholder="Variant image URL (optional)" class="input !py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <p x-show="!rows.length" class="text-sm text-ink/40 py-2">No variants yet. Click “Add” to create size/colour pricing.</p>
        </div>
    </div>

    {{-- Sidebar column --}}
    <div class="space-y-6">
        <div class="card p-6 space-y-5">
            <h2 class="font-semibold text-ink">Pricing</h2>
            <div>
                <label class="label">Price (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required class="input">
            </div>
            <div>
                <label class="label">Sale Price (₹) <span class="text-ink/40 text-xs">(optional)</span></label>
                <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="input">
            </div>
            <label class="flex items-center gap-2.5 text-sm">
                <input type="checkbox" name="show_price" value="1" {{ old('show_price', $product->show_price ?? true) ? 'checked':'' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                Show price on this product
            </label>
        </div>

        <div class="card p-6 space-y-5">
            <h2 class="font-semibold text-ink">Variants</h2>
            <div>
                <label class="label">Sizes <span class="text-ink/40 text-xs">(comma separated)</span></label>
                <input type="text" name="sizes" value="{{ old('sizes', is_array($product->sizes) ? implode(', ', $product->sizes) : '') }}" placeholder="S, M, L, XL" class="input">
            </div>
            <div>
                <label class="label">Colours <span class="text-ink/40 text-xs">(comma separated)</span></label>
                <input type="text" name="colors" value="{{ old('colors', is_array($product->colors) ? implode(', ', $product->colors) : '') }}" placeholder="Red, Blue, Gold" class="input">
            </div>
            <div>
                <label class="label">Material</label>
                <input type="text" name="material" value="{{ old('material', $product->material) }}" class="input">
            </div>
        </div>

        <div class="card p-6 space-y-5">
            <h2 class="font-semibold text-ink">Organisation</h2>
            <div>
                <label class="label">SKU</label>
                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="input">
            </div>
            <div>
                <label class="label">Stock Status</label>
                <select name="stock_status" class="input">
                    @foreach(['in_stock'=>'In Stock','out_of_stock'=>'Out of Stock','made_to_order'=>'Made to Order'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('stock_status', $product->stock_status) === $val ? 'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Sort Order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="input">
            </div>
            <label class="flex items-center gap-2.5 text-sm">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active ?? true) ? 'checked':'' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                Active (visible on site)
            </label>
            <label class="flex items-center gap-2.5 text-sm">
                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked':'' }} class="rounded border-rose-300 text-rose-600 focus:ring-rose-300">
                Featured product
            </label>
        </div>

        <div class="flex flex-col gap-3">
            <button class="btn-primary w-full">{{ $product->exists ? 'Update Product' : 'Create Product' }}</button>
            <a href="{{ route('admin.products.index') }}" class="btn-outline w-full">Cancel</a>
        </div>
    </div>
</form>

{{-- Hidden image-delete forms --}}
@if($product->exists)
    @foreach($product->images as $image)
        <form id="del-img-{{ $image->id }}" method="POST" action="{{ route('admin.product-images.destroy', $image) }}" class="hidden">
            @csrf @method('DELETE')
        </form>
    @endforeach
@endif
@endsection
