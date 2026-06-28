<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        $enquiries = Enquiry::with('product')
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->get('source'), fn ($q, $s) => $q->where('source', $s))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', compact('enquiries'));
    }

    public function update(Request $request, Enquiry $enquiry)
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,closed'],
        ]);

        $enquiry->update($data);

        return back()->with('status', 'Enquiry updated.');
    }

    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return back()->with('status', 'Enquiry deleted.');
    }
}
