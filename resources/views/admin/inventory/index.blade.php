@extends('layouts.admin')
@section('title', '멀티지점 재고')
@section('subtitle', '전체 재고 · 지점간 이동 · 물류 현황')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">📦 취급 SKU</div><div class="v">{{ number_format($stats['skus']) }}</div></div>
    <div class="stat"><div class="k">🔢 총 수량</div><div class="v">{{ number_format($stats['units']) }}</div></div>
    <div class="stat"><div class="k">🏬 재고 지점</div><div class="v">{{ $stats['stores'] }}</div></div>
    <div class="stat"><div class="k">🚚 진행중 이동</div><div class="v">{{ $stats['transfers'] }}</div></div>
</div>

<div class="panel">
    <div class="panel__head"><h2>지점별 재고 현황</h2></div>
    <table class="table">
        <thead><tr><th>지점</th><th>SKU</th><th>총 수량</th></tr></thead>
        <tbody>
        @forelse($byStore as $row)
            <tr><td><b>{{ $row->store?->company_name }}</b></td><td>{{ $row->skus }}</td><td>{{ number_format($row->units) }}개</td></tr>
        @empty
            <tr><td colspan="3" class="empty-row">재고 데이터가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <div class="panel__head"><h2>이동 현황 · 물류</h2></div>
    <table class="table">
        <thead><tr><th>이동번호</th><th>상품</th><th>출발</th><th>도착</th><th>수량</th><th>상태</th></tr></thead>
        <tbody>
        @forelse($transfers as $t)
            <tr>
                <td><b>{{ $t->code }}</b></td>
                <td>{{ $t->product?->brand }} {{ Str::limit($t->product?->name,18) }}</td>
                <td>{{ $t->fromStore?->company_name }}</td>
                <td>{{ $t->toStore?->company_name }}</td>
                <td>{{ $t->quantity }}</td>
                <td><span class="pill pill--{{ $t->status_color }}">{{ $t->status_label }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">이동 내역이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $transfers->links() }}</div>
</div>
@endsection
