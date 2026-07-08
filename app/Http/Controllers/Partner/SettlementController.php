<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    public function index()
    {
        $me = Auth::guard('partner')->id();

        $settlements = Settlement::with('order', 'product')
            ->where('store_id', $me)->latest()->paginate(15);

        $summary = [
            'pending_net' => (int) Settlement::where('store_id', $me)->where('status', 'pending')->sum('net_amount'),
            'paid_net'    => (int) Settlement::where('store_id', $me)->where('status', 'paid')->sum('net_amount'),
            'gross'       => (int) Settlement::where('store_id', $me)->sum('gross_amount'),
            'commission'  => (int) Settlement::where('store_id', $me)->sum('commission'),
        ];

        return view('partner.settlements.index', compact('settlements', 'summary'));
    }
}
