<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::withCount('products')->latest()->paginate(15);

        return view('admin.partners.index', compact('partners'));
    }

    public function updateStatus(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,suspended'],
        ]);

        $partner->update($data);

        return back()->with('status', "파트너 상태가 '{$data['status']}'(으)로 변경되었습니다.");
    }
}
