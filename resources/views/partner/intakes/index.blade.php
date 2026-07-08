@extends('layouts.partner')
@section('title', '판매 접수')
@section('subtitle', '우리 지점 지정 건과 참여 가능한 경매를 확인합니다')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>🏬 우리 지점 지정 접수 ({{ $direct->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>접수번호</th><th>상품</th><th>고객</th><th>희망가</th><th>상태</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @forelse($direct as $sr)
            <tr>
                <td><b>{{ $sr->code }}</b></td>
                <td><div class="tprod"><div><div class="name">{{ $sr->title }}</div><div class="brand">{{ $sr->brand }}</div></div></div></td>
                <td>{{ $sr->customer->name }}</td>
                <td>{{ $sr->desired_price ? number_format($sr->desired_price).'원' : '-' }}</td>
                <td><span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span></td>
                <td style="text-align:right"><a class="pbtn pbtn--sm" href="{{ route('partner.intakes.show', $sr) }}">감정/견적</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">지정된 접수가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <div class="panel__head"><h2>🔨 경매 참여 ({{ $auctions->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>접수번호</th><th>상품</th><th>최고 입찰</th><th>참여</th><th>상태</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @forelse($auctions as $sr)
            @php $top = $sr->bids->max('bid_price'); $mine = $sr->bids->firstWhere('store_id', $me); @endphp
            <tr>
                <td><b>{{ $sr->code }}</b></td>
                <td><div class="tprod"><div><div class="name">{{ $sr->title }}</div><div class="brand">{{ $sr->brand }}</div></div></div></td>
                <td>{{ $top ? number_format($top).'원' : '-' }}</td>
                <td>{{ $sr->bids->count() }}곳 {!! $mine ? '· <b style="color:var(--p-gold)">내 입찰 '.number_format($mine->bid_price).'원</b>' : '' !!}</td>
                <td><span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span></td>
                <td style="text-align:right"><a class="pbtn pbtn--sm {{ $sr->status==='auctioning'?'pbtn--primary':'' }}" href="{{ route('partner.intakes.show', $sr) }}">{{ $sr->status==='auctioning' ? '입찰하기' : '보기' }}</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">참여 가능한 경매가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
