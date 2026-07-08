<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('customer', 'payment')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()->paginate(15)->withQueryString();

        $counts = Order::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');
        $revenue = Order::whereIn('status', ['paid', 'preparing', 'shipping', 'delivered'])->sum('total');

        return view('admin.orders.index', compact('orders', 'counts', 'revenue'));
    }

    public function show(Order $order)
    {
        $order->load('items', 'payment', 'customer');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status'      => ['required', 'in:paid,preparing,shipping,delivered,cancelled'],
            'tracking_no' => ['nullable', 'string', 'max:60'],
        ]);
        $order->update($data);

        $label = Order::STATUSES[$data['status']][0] ?? $data['status'];
        NotificationService::notify('customer', $order->customer_id, 'order', '📦 주문 상태 변경',
            "주문 {$order->code} · {$label}" . ($order->tracking_no ? " (송장 {$order->tracking_no})" : ''),
            route('orders.show', $order), ['in_app', 'kakao'], '📦');

        return back()->with('status', "주문 상태를 '{$label}'(으)로 변경했습니다.");
    }
}
