<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index()
    {
        $promotions = Promotion::orderBy('position')->orderBy('sort_order')->get();

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.form', ['promotion' => new Promotion(['is_active' => true, 'position' => 'hero'])]);
    }

    public function store(Request $request)
    {
        Promotion::create($this->validateData($request));

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion created.');
    }

    public function edit(Promotion $promotion)
    {
        return view('admin.promotions.form', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $promotion->update($this->validateData($request));

        return redirect()->route('admin.promotions.index')->with('status', 'Promotion updated.');
    }

    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

        return back()->with('status', 'Promotion deleted.');
    }

    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'subtitle' => ['nullable', 'string', 'max:200'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'link' => ['nullable', 'string', 'max:300'],
            'button_text' => ['nullable', 'string', 'max:60'],
            'position' => ['required', 'in:hero,topbar,strip'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('promotions', 'public');
        }
        unset($data['image_file']);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) $request->get('sort_order', 0);

        return $data;
    }
}
