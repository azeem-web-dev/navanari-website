<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.form', ['category' => new Category(), 'parents' => $parents]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['image'] = $this->handleImage($request, $data['image'] ?? null);

        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();

        return view('admin.categories.form', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validateData($request, $category);
        $data['image'] = $this->handleImage($request, $category->image);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back()->with('status', 'Category deleted.');
    }

    protected function validateData(Request $request, ?Category $category = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'image' => ['nullable'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) $request->get('sort_order', 0),
        ];
    }

    protected function handleImage(Request $request, ?string $current): ?string
    {
        if ($request->hasFile('image_file')) {
            return $request->file('image_file')->store('categories', 'public');
        }

        // Allow pasting an external image URL via the `image` text field.
        return $request->filled('image') ? $request->get('image') : $current;
    }
}
