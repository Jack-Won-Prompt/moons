@extends('layouts.storefront')
@section('title', $category->name . ' · MOONS')

@section('content')
<div class="wrap">
    <div class="crumb">
        <a href="{{ route('home') }}">홈</a>
        @if($category->parent) / <a href="{{ route('catalog.category', $category->parent) }}">{{ $category->parent->name }}</a>@endif
        / <span>{{ $category->name }}</span>
    </div>

    <div class="listing__head">
        <h1>{{ $category->icon }} {{ $category->name }}</h1>
        <span class="count">{{ number_format($products->total()) }}개 상품</span>
    </div>

    {{-- Subcategory chips --}}
    @if($category->children->count())
        <div class="chips" style="margin-bottom:18px">
            @foreach($category->children as $child)
                <a class="chip" href="{{ route('catalog.category', $child) }}">{{ $child->name }}</a>
            @endforeach
        </div>
    @endif

    {{-- Toolbar: brand filter + sort --}}
    <div class="toolbar">
        <div class="chips">
            <a class="chip {{ !request('brand') ? 'on' : '' }}" href="{{ route('catalog.category', [$category, 'sort'=>request('sort')]) }}">전체 브랜드</a>
            @foreach($brands->take(8) as $brand)
                <a class="chip {{ request('brand')===$brand ? 'on' : '' }}"
                   href="{{ route('catalog.category', [$category, 'brand'=>$brand, 'sort'=>request('sort')]) }}">{{ $brand }}</a>
            @endforeach
        </div>
        <div class="sortbar">
            @foreach(['new'=>'신상품순','popular'=>'인기순','price_low'=>'낮은가격','price_high'=>'높은가격','discount'=>'할인율순'] as $key=>$label)
                @php $cur = request('sort', 'new'); @endphp
                <a class="{{ $cur===$key ? 'on' : '' }}"
                   href="{{ route('catalog.category', array_merge([$category], request()->except('page'), ['sort'=>$key])) }}">{{ $label }}</a>
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
            <div class="big">🛍️</div>
            <p>해당 조건의 상품이 아직 없습니다.</p>
        </div>
    @endif
</div>
@endsection
