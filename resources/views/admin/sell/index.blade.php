@extends('layouts.admin')
@section('title', '감정 · 접수 관리')
@section('subtitle', '위탁 판매 접수와 감정 진행 현황')

@section('content')
<div class="stats">
    @foreach(['received'=>'접수','appraising'=>'감정중','auctioning'=>'경매중','quoted'=>'견적완료','customer_approved'=>'고객승인','inbound'=>'입고','settled'=>'정산완료'] as $st=>$label)
        <a class="stat" href="{{ route('admin.sell.index', ['status'=>$st]) }}" style="text-decoration:none">
            <div class="k">{{ $label }}</div><div class="v">{{ $counts[$st] ?? 0 }}</div>
        </a>
    @endforeach
</div>

<div class="panel">
    <div class="panel__head">
        <h2>판매 접수 목록</h2>
        @if(request('status'))<a class="pbtn pbtn--sm" href="{{ route('admin.sell.index') }}">전체 보기</a>@endif
    </div>
    <table class="table">
        <thead><tr><th>접수번호</th><th>상품</th><th>고객</th><th>대상</th><th>방식</th><th>견적</th><th>상태</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @forelse($requests as $sr)
            <tr>
                <td><b>{{ $sr->code }}</b></td>
                <td><div class="tprod"><div><div class="name">{{ $sr->title }}</div><div class="brand">{{ $sr->brand }}</div></div></div></td>
                <td>{{ $sr->customer->name }}</td>
                <td>{{ $sr->target_label }}</td>
                <td>{{ $sr->method==='auction'?'경매':'일반' }}</td>
                <td>{{ $sr->quote_price ? number_format($sr->quote_price).'원' : '-' }}</td>
                <td><span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span></td>
                <td style="text-align:right"><a class="pbtn pbtn--sm" href="{{ route('admin.sell.show', $sr) }}">상세</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty-row">접수 내역이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $requests->links() }}</div>
</div>
@endsection
