@extends('layouts.partner')
@section('title', '재고 관리')
@section('subtitle', '우리 지점 재고 조회 · 위치 관리')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>내 재고 ({{ $inventories->count() }})</h2>
        <div style="display:flex;gap:8px">
            <a class="pbtn pbtn--sm" href="{{ route('partner.inventory.stores') }}">타 지점 재고 조회</a>
            <a class="pbtn pbtn--sm pbtn--primary" href="{{ route('partner.inventory.transfers') }}">이동 요청/현황</a>
        </div>
    </div>
    <table class="table">
        <thead><tr><th>상품</th><th>수량</th><th>위치</th><th>업데이트</th></tr></thead>
        <tbody>
        @forelse($inventories as $inv)
            <tr>
                <td><div class="tprod">
                    @if($inv->product?->image)<img class="swatch" src="{{ $inv->product->image_url }}" alt="">@endif
                    <div><div class="name">{{ $inv->product?->name }}</div><div class="brand">{{ $inv->product?->brand }}</div></div></div></td>
                <td><b>{{ $inv->quantity }}</b>개</td>
                <td><span class="pill pill--gray">{{ $inv->location ?? '-' }}</span></td>
                <td>{{ $inv->updated_at->diffForHumans() }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-row">보유 재고가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
