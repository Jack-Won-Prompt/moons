@extends('layouts.storefront')
@section('title', '주문 조회 · MOONS')

@section('content')
<div class="wrap" style="max-width:860px">
    <div class="listing__head" style="margin-top:28px"><h1>주문 조회 / 배송</h1></div>

    @forelse($orders as $order)
        <a href="{{ route('orders.show', $order) }}" class="sr-row">
            <div class="sr-thumb">
                @php $first = $order->items->first(); @endphp
                @if($first && $first->image_url)<img src="{{ $first->image_url }}" alt="">@else<span>📦</span>@endif
            </div>
            <div class="sr-main">
                <div class="sr-code">{{ $order->code }} · {{ $order->created_at->format('Y.m.d') }}</div>
                <div class="sr-title">{{ optional($order->items->first())->brand }} {{ optional($order->items->first())->name ? Str::limit($order->items->first()->name,24) : '' }}
                    @if($order->items->count()>1) 외 {{ $order->items->count()-1 }}건 @endif</div>
                <div class="sr-sub"><b>{{ number_format($order->total) }}원</b> · {{ $order->payment?->method_label }}</div>
            </div>
            <span class="pill pill--{{ $order->status_color }}">{{ $order->status_label }}</span>
        </a>
    @empty
        <div class="empty"><div class="big">📦</div><p>주문 내역이 없습니다.</p></div>
    @endforelse
    <div class="pagination-wrap">{{ $orders->links() }}</div>
</div>
<style>
    .sr-row{display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:12px;transition:box-shadow .15s}
    .sr-row:hover{box-shadow:var(--shadow)} .sr-thumb{width:64px;height:64px;border-radius:10px;background:var(--bg-alt);display:grid;place-items:center;overflow:hidden;font-size:26px;flex:none}
    .sr-thumb img{width:100%;height:100%;object-fit:cover}
    .sr-code{font-size:12px;color:var(--muted)} .sr-title{font-weight:700;margin:2px 0 3px} .sr-sub{font-size:13px;color:var(--ink-soft)}
</style>
@endsection
