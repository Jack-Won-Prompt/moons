@extends('layouts.admin')
@section('title', '대시보드')
@section('subtitle', 'MOONS 운영 현황을 한눈에')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">📦 전체 상품</div><div class="v">{{ number_format($stats['products']) }}</div></div>
    <div class="stat"><div class="k">🗂️ 카테고리</div><div class="v">{{ number_format($stats['categories']) }}</div></div>
    <div class="stat"><div class="k">🤝 파트너</div><div class="v">{{ number_format($stats['partners']) }}</div></div>
    <div class="stat"><div class="k">👥 고객</div><div class="v">{{ number_format($stats['customers']) }}</div></div>
    <div class="stat">
        <div class="k">⏳ 승인 대기 @if($stats['pending'])<span class="chip chip--red">{{ $stats['pending'] }}</span>@endif</div>
        <div class="v">{{ number_format($stats['pending']) }}</div>
    </div>
</div>

@if($pendingPartners->count())
<div class="panel">
    <div class="panel__head"><h2>⏳ 승인 대기 중인 파트너</h2><a class="pbtn pbtn--sm" href="{{ route('admin.partners.index') }}">전체 보기</a></div>
    <table class="table">
        <thead><tr><th>상호</th><th>담당자</th><th>이메일</th><th>브랜드</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @foreach($pendingPartners as $p)
            <tr>
                <td><b>{{ $p->company_name }}</b></td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->brand ?: '-' }}</td>
                <td style="text-align:right">
                    <form action="{{ route('admin.partners.status', $p) }}" method="POST" style="display:inline">
                        @csrf @method('PATCH')<input type="hidden" name="status" value="approved">
                        <button class="pbtn pbtn--primary pbtn--sm" type="submit">승인</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="panel">
    <div class="panel__head"><h2>🆕 최근 등록 상품</h2><a class="pbtn pbtn--sm" href="{{ route('admin.products.index') }}">상품 관리</a></div>
    <table class="table">
        <thead><tr><th>상품</th><th>가격</th><th>판매자</th><th>상태</th></tr></thead>
        <tbody>
        @forelse($recentProducts as $product)
            <tr>
                <td>
                    <div class="tprod">
                        @if($product->image)<img class="swatch" src="{{ $product->image_url }}" alt="">@else<span class="swatch" style="background:linear-gradient(135deg,{{ $product->color ?: '#333,#555' }})"></span>@endif
                        <div><div class="name">{{ $product->name }}</div><div class="brand">{{ $product->brand }}</div></div>
                    </div>
                </td>
                <td><b>{{ number_format($product->final_price) }}원</b></td>
                <td>{{ $product->partner?->company_name ?? 'MOONS 공식' }}</td>
                <td>{!! $product->is_active ? '<span class="pill pill--green">판매중</span>' : '<span class="pill pill--gray">비활성</span>' !!}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="empty-row">등록된 상품이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
