@extends('layouts.storefront')
@section('title', $order->code . ' 주문 상세 · MOONS')

@php
    $steps = ['결제 완료'=>'paid','상품 준비'=>'preparing','배송 중'=>'shipping','배송 완료'=>'delivered'];
    $order_flow = ['paid'=>0,'preparing'=>1,'shipping'=>2,'delivered'=>3];
    $cur = $order_flow[$order->status] ?? 0;
    $cancelled = $order->status === 'cancelled';
@endphp

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="crumb" style="margin-top:22px"><a href="{{ route('orders.index') }}">주문 조회</a> / <span>{{ $order->code }}</span></div>
    <div class="listing__head"><h1 style="font-size:24px">주문 {{ $order->code }}</h1>
        <span class="pill pill--{{ $order->status_color }}" style="margin-left:8px">{{ $order->status_label }}</span></div>

    @if($cancelled)
        <div class="alert alert--err">주문이 취소되었습니다.</div>
    @else
    <div class="panel-card">
        <div class="timeline">
            @foreach($steps as $label=>$key)
                @php $i = $loop->index; @endphp
                <div class="step {{ $i < $cur ? 'done' : ($i === $cur ? 'current' : '') }}">
                    <div class="dot">{{ $i <= $cur ? '✓' : $i+1 }}</div>{{ $label }}
                </div>
            @endforeach
        </div>
        @if($order->tracking_no)<p style="text-align:center;margin:14px 0 0;font-size:14px">📦 운송장 번호: <b>{{ $order->tracking_no }}</b></p>@endif
    </div>
    @endif

    <div class="panel-card">
        <h3>주문 상품</h3>
        @foreach($order->items as $it)
            <div style="display:flex;gap:14px;align-items:center;padding:10px 0;border-bottom:1px solid var(--line-soft)">
                <div style="width:60px;height:60px;border-radius:9px;overflow:hidden;background:var(--bg-alt);flex:none">
                    @if($it->image_url)<img src="{{ $it->image_url }}" style="width:100%;height:100%;object-fit:cover">@endif</div>
                <div style="flex:1"><b>{{ $it->brand }}</b><div style="font-size:13px;color:var(--ink-soft)">{{ $it->name }}</div></div>
                <div style="text-align:right"><b>{{ number_format($it->price) }}원</b><div style="font-size:12px;color:var(--muted)">수량 {{ $it->quantity }}</div></div>
            </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
        <div class="panel-card"><h3>배송지</h3>
            <dl class="dl-grid" style="grid-template-columns:80px 1fr">
                <dt>받는분</dt><dd>{{ $order->receiver_name }}</dd>
                <dt>연락처</dt><dd>{{ $order->phone }}</dd>
                <dt>주소</dt><dd>{{ $order->address }} {{ $order->address_detail }}</dd>
                @if($order->memo)<dt>메모</dt><dd>{{ $order->memo }}</dd>@endif
            </dl>
        </div>
        <div class="panel-card"><h3>결제 정보</h3>
            <dl class="dl-grid" style="grid-template-columns:90px 1fr">
                <dt>결제수단</dt><dd>{{ $order->payment?->method_label ?? '-' }}</dd>
                <dt>거래번호</dt><dd style="font-family:monospace;font-size:12px">{{ $order->payment?->pg_tid ?? '-' }}</dd>
                <dt>상품금액</dt><dd>{{ number_format($order->subtotal) }}원</dd>
                <dt>배송비</dt><dd>무료</dd>
                <dt>총 결제</dt><dd><b style="font-size:18px">{{ number_format($order->total) }}원</b></dd>
            </dl>
        </div>
    </div>
</div>
@endsection
