@extends('layouts.storefront')
@section('title', 'MOONS · 럭셔리 셀렉트샵')

@section('content')
<div class="wrap">

    {{-- Hero --}}
    <section class="hero">
        <div class="hero__grid">
            <a href="{{ route('catalog.all', ['sort'=>'discount']) }}" class="hero__card"
               style="background:linear-gradient(135deg,#1a1a2e,#4b1248)">
                <div>
                    <div class="eyebrow">MOONS Exclusive</div>
                    <h2>이번 시즌 럭셔리<br>최대 45% 특가</h2>
                    <p>전 세계 명품을 가장 합리적인 가격으로 만나보세요</p>
                </div>
            </a>
            <div class="hero__stack">
                <a href="{{ route('catalog.category', 'bags') }}" class="hero__card hero__card--sm"
                   style="background:linear-gradient(135deg,#603813,#b29f94)">
                    <div><div class="eyebrow">Bags</div><h2>잇백 컬렉션</h2></div>
                </a>
                <a href="{{ route('catalog.category', 'shoes') }}" class="hero__card hero__card--sm"
                   style="background:linear-gradient(135deg,#2b5876,#4e4376)">
                    <div><div class="eyebrow">Shoes</div><h2>슈즈 신상</h2></div>
                </a>
            </div>
        </div>
    </section>

    {{-- Category quick access --}}
    <section class="section" style="margin-top:44px">
        <div class="cat-quick">
            @foreach($rootCategories as $cat)
                <a href="{{ route('catalog.category', $cat) }}">
                    <span class="bubble">{{ $cat->icon }}</span>
                    <span>{{ $cat->name }}</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- New arrivals --}}
    @if($newArrivals->count())
    <section class="section">
        <div class="section__head">
            <div class="section__title">신상품 <small>방금 입고된 따끈한 신상</small></div>
            <a class="section__more" href="{{ route('catalog.all') }}">전체보기 →</a>
        </div>
        <div class="grid">
            @foreach($newArrivals as $product)
                @include('partials.product-card')
            @endforeach
        </div>
    </section>
    @endif

    {{-- Best --}}
    @if($best->count())
    <section class="section">
        <div class="section__head">
            <div class="section__title">베스트 <small>지금 가장 인기있는 상품</small></div>
            <a class="section__more" href="{{ route('catalog.all', ['sort'=>'popular']) }}">전체보기 →</a>
        </div>
        <div class="grid">
            @foreach($best as $product)
                @include('partials.product-card')
            @endforeach
        </div>
    </section>
    @endif

    {{-- Sale --}}
    @if($sale->count())
    <section class="section">
        <div class="section__head">
            <div class="section__title" style="color:var(--sale)">🔥 특가 세일 <small style="color:var(--muted)">놓치면 후회하는 할인가</small></div>
            <a class="section__more" href="{{ route('catalog.all', ['sort'=>'discount']) }}">전체보기 →</a>
        </div>
        <div class="grid">
            @foreach($sale as $product)
                @include('partials.product-card')
            @endforeach
        </div>
    </section>
    @endif

</div>
@endsection
