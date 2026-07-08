@extends('layouts.storefront')
@section('title', $promotion->title . ' · MOONS 기획전')

@section('content')
<div class="wrap">
    <div class="promo-hero" style="background:linear-gradient(135deg,{{ $promotion->gradient ?: '#1a1a2e,#4b1248' }})">
        <div class="eyebrow">MOONS 기획전</div>
        <h1>{{ $promotion->title }}</h1>
        @if($promotion->subtitle)<p>{{ $promotion->subtitle }}</p>@endif
    </div>
    @if($promotion->description)<p style="color:var(--ink-soft);margin:20px 0">{{ $promotion->description }}</p>@endif

    @if($products->count())
        <div class="grid">
            @foreach($products as $product)@include('partials.product-card')@endforeach
        </div>
        <div class="pagination-wrap">{{ $products->links() }}</div>
    @else
        <div class="empty"><div class="big">🛍️</div><p>조건에 맞는 상품이 없습니다.</p></div>
    @endif
</div>
<style>
    .promo-hero{border-radius:16px;padding:50px 40px;color:#fff;margin-top:24px}
    .promo-hero .eyebrow{font-size:12px;letter-spacing:.16em;text-transform:uppercase;opacity:.85}
    .promo-hero h1{font-size:32px;font-weight:800;margin:10px 0 6px}
    .promo-hero p{opacity:.9;margin:0}
</style>
@endsection
