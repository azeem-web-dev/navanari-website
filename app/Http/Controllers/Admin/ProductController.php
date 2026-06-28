<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category', 'images')
            ->when($request->get('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->get('category'), fn ($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true, 'show_price' => true, 'stock_status' => 'in_stock']),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $product = Product::create($data);

        $this->syncImages($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function edit(Product $product)
    {
        return view('admin.products.form', [
            'product' => $product->load('images'),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateData($request);
        $product->update($data);

        $this->syncImages($request, $product);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            $this->deleteStoredImage($image->path);
        }
        $product->delete();

        return back()->with('status', 'Product deleted.');
    }

    public function destroyImage(ProductImage $image)
    {
        $this->deleteStoredImage($image->path);
        $image->delete();

        return back()->with('status', 'Image removed.');
    }

    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:60'],
            'brand' => ['nullable', 'string', 'max:80'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'material' => ['nullable', 'string', 'max:120'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock,made_to_order'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Variant lists arrive as comma-separated strings.
        $validated['sizes'] = $this->splitList($request->get('sizes'));
        $validated['colors'] = $this->splitList($request->get('colors'));
        $validated['show_price'] = $request->boolean('show_price');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = (int) $request->get('sort_order', 0);

        return $validated;
    }

    protected function splitList(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->values()
            ->all();
    }

    protected function syncImages(Request $request, Product $product): void
    {
        // External image URLs (textarea, one per line).
        if ($urls = trim((string) $request->get('image_urls'))) {
            foreach (preg_split('/\r\n|\r|\n/', $urls) as $url) {
                $url = trim($url);
                if ($url && Str::startsWith($url, ['http://', 'https://'])) {
                    $product->images()->create([
                        'path' => $url,
                        'is_primary' => $product->images()->count() === 0,
                    ]);
                }
            }
        }

        // Uploaded files.
        foreach ((array) $request->file('images', []) as $file) {
            if (! $file) {
                continue;
            }
            $path = $file->store('products', 'public');
            $product->images()->create([
                'path' => $path,
                'is_primary' => $product->images()->count() === 0,
            ]);
        }
    }

    protected function deleteStoredImage(?string $path): void
    {
        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
