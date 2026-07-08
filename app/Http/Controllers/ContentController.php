<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Notice;
use App\Models\Promotion;

class ContentController extends Controller
{
    public function notices()
    {
        $notices = Notice::orderByDesc('is_pinned')->latest()->paginate(15);

        return view('content.notices', compact('notices'));
    }

    public function notice(Notice $notice)
    {
        return view('content.notice', compact('notice'));
    }

    public function faqs()
    {
        $faqs = Faq::orderBy('category')->orderBy('sort')->get()->groupBy('category');

        return view('content.faqs', compact('faqs'));
    }

    public function promotion(Promotion $promotion)
    {
        abort_unless($promotion->is_active, 404);
        $products = $promotion->products()->latest()->paginate(12);

        return view('content.promotion', compact('promotion', 'products'));
    }
}
