<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class PageController extends Controller
{
    public function howToBuy()
    {
        return view('how-to-buy');
    }

    public function faq()
    {
        $faqs = Faq::where('is_active', true)->orderBy('sort_order')->get();
        return view('faq', compact('faqs'));
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function terms()
    {
        return view('terms');
    }

    public function about()
    {
        return view('about');
    }
}
