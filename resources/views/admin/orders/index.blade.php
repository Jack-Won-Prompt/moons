@extends('layouts.admin')
@section('title', '주문 관리')
@section('subtitle', '전체 주문과 배송 현황')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">💰 총 매출</div><div class="v">{{ number_format($revenue) }}<small style="font-size:14px">원</small></div></div>
    @foreach(['paid'=>'결제완료','preparing'=>'상품준비','shipping'=>'배송중','delivered'=>'배송완료'] as $st=>$label)
        <a class="stat" href="{{ route('admin.orders.index', ['status'=>$st]) }}" style="text-decoration:none"><div class="k">{{ $label }}</div><div class="v">{{ $counts[$st] ?? 0 }}</div></a>
    @endforeach
</div>

<div class="panel">
    <div class="panel__head"><h2>주문 목록</h2>@if(request('status'))<a class="pbtn pbtn--sm" href="{{ route('admin.orders.index') }}">전체</a>@endif</div>
    <table class="table">
        <thead><tr><th>주문번호</th><th>고객</th><th>결제금액</th><th>결제수단</th><th>주문일</th><th>상태</th><th style="text-align:right">관리</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td><b>{{ $order->code }}</b></td>
                <td>{{ $order->customer->name }}</td>
                <td><b>{{ number_format($order->total) }}원</b></td>
                <td>{{ $order->payment?->method_label ?? '-' }}</td>
                <td>{{ $order->created_at->format('Y.m.d') }}</td>
                <td><span class="pill pill--{{ $order->status_color }}">{{ $order->status_label }}</span></td>
                <td style="text-align:right"><a class="pbtn pbtn--sm" href="{{ route('admin.orders.show', $order) }}">상세</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-row">주문이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $orders->links() }}</div>
</div>
@endsection
