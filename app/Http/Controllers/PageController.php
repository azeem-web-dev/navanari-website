<?php

namespace App\Http\Controllers;

use App\Models\Enquiry;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('storefront.about');
    }

    public function contact()
    {
        return view('storefront.contact');
    }

    public function submitContact(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:80'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $data['source'] = 'contact_form';
        $data['status'] = 'new';
        Enquiry::create($data);

        return back()->with('status', 'Thank you for reaching out! Our team will get back to you shortly.');
    }

    public function wishlist()
    {
        // Wishlist lives in the browser (localStorage) — no login required.
        return view('storefront.wishlist');
    }
}
