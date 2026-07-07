@extends('layouts.partner')
@section('title', '파트너 대시보드')
@section('subtitle', $partner->company_name . ' 판매 현황')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">📦 등록 상품</div><div class="v">{{ number_format($stats['products']) }}</div></div>
    <div class="stat"><div class="k">✅ 판매중</div><div class="v">{{ number_format($stats['active']) }}</div></div>
    <div class="stat"><div class="k">👁️ 총 조회수</div><div class="v">{{ number_format($stats['views']) }}</div></div>
    <div class="stat">
        <div class="k">🏷️ 계정 상태</div>
        <div class="v" style="font-size:20px;margin-top:12px"><span class="pill pill--green">승인 완료</span></div>
    </div>
</div>

<div class="panel">
    <div class="panel__head"><h2>최근 등록 상품</h2><a class="pbtn pbtn--primary" href="{{ route('partner.products.create') }}">+ 상품 등록</a></div>
    <table class="table">
        <thead><tr><th>상품</th><th>카테고리</th><th>판매가</th><th>조회수</th><th>상태</th></tr></thead>
        <tbody>
        @forelse($recent as $product)
            <tr>
                <td>
                    <div class="tprod">
                        @if($product->image)<img class="swatch" src="{{ $product->image_url }}" alt="">@else<span class="swatch" style="background:linear-gradient(135deg,{{ $product->color ?: '#333,#555' }})"></span>@endif
                        <div><div class="name">{{ $product->name }}</div><div class="brand">{{ $product->brand }}</div></div>
                    </div>
                </td>
                <td>{{ $product->category?->name }}</td>
                <td><b>{{ number_format($product->final_price) }}원</b></td>
                <td>{{ number_format($product->view_count) }}</td>
                <td>{!! $product->is_active ? '<span class="pill pill--green">판매중</span>' : '<span class="pill pill--gray">비활성</span>' !!}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty-row">아직 등록한 상품이 없습니다. 첫 상품을 등록해보세요!</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
