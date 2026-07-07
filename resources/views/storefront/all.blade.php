@extends('layouts.storefront')
@section('title', ($keyword ? $keyword.' 검색결과' : '전체 상품') . ' · MOONS')

@section('content')
<div class="wrap">
    <div class="crumb"><a href="{{ route('home') }}">홈</a> / <span>{{ $keyword ? '검색' : '전체 상품' }}</span></div>

    <div class="listing__head">
        <h1>{{ $keyword ? '“'.$keyword.'” 검색결과' : '전체 상품' }}</h1>
        <span class="count">{{ number_format($products->total()) }}개 상품</span>
    </div>

    <div class="toolbar">
        <div class="chips"><span class="chip on">MOONS 전체 컬렉션</span></div>
        <div class="sortbar">
            @foreach(['new'=>'신상품순','popular'=>'인기순','price_low'=>'낮은가격','price_high'=>'높은가격','discount'=>'할인율순'] as $key=>$label)
                @php $cur = request('sort', 'new'); @endphp
                <a class="{{ $cur===$key ? 'on' : '' }}"
                   href="{{ route('catalog.all', array_merge(request()->except('page'), ['sort'=>$key])) }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    @if($products->count())
        <div class="grid">
            @foreach($products as $product)
                @include('partials.product-card')
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $products->links() }}</div>
    @else
        <div class="empty">
            <div class="big">🔍</div>
            <p>검색 결과가 없습니다. 다른 키워드로 검색해보세요.</p>
        </div>
    @endif
</div>
@endsection
