@extends('layouts.admin')
@section('title', '상품 관리')
@section('subtitle', '전체 상품을 등록하고 관리합니다')

@section('content')
<div class="panel">
    <div class="panel__head">
        <form method="GET" style="display:flex;gap:8px;margin:0">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="상품/브랜드 검색"
                   style="padding:9px 14px;border:1px solid var(--p-line);border-radius:10px;font-size:13px;font-family:inherit">
            <button class="pbtn pbtn--sm" type="submit">검색</button>
        </form>
        <a class="pbtn pbtn--primary" href="{{ route('admin.products.create') }}">+ 상품 등록</a>
    </div>
    <table class="table">
        <thead><tr><th>상품</th><th>카테고리</th><th>정가</th><th>판매가</th><th>재고</th><th>판매자</th><th>상태</th><th style="text-align:right">관리</th></tr></thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>
                    <div class="tprod">
                        @if($product->image)<img class="swatch" src="{{ $product->image_url }}" alt="">@else<span class="swatch" style="background:linear-gradient(135deg,{{ $product->color ?: '#333,#555' }})"></span>@endif
                        <div><div class="name">{{ $product->name }}</div><div class="brand">{{ $product->brand }}</div></div>
                    </div>
                </td>
                <td>{{ $product->category?->name }}</td>
                <td>{{ number_format($product->price) }}원</td>
                <td>{!! $product->sale_price ? '<b style="color:var(--p-red)">'.number_format($product->sale_price).'원</b>' : '-' !!}</td>
                <td>{{ $product->stock }}</td>
                <td>{{ $product->partner?->company_name ?? 'MOONS' }}</td>
                <td>{!! $product->is_active ? '<span class="pill pill--green">판매중</span>' : '<span class="pill pill--gray">비활성</span>' !!}</td>
                <td style="text-align:right;white-space:nowrap">
                    <a class="pbtn pbtn--sm" href="{{ route('admin.products.edit', $product) }}">수정</a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display:inline"
                          onsubmit="return confirm('삭제하시겠습니까?')">
                        @csrf @method('DELETE')<button class="pbtn pbtn--danger pbtn--sm" type="submit">삭제</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty-row">상품이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $products->links() }}</div>
</div>
@endsection
