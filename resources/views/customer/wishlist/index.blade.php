@extends('layouts.storefront')
@section('title', '관심상품 · MOONS')

@section('content')
<div class="wrap">
    <div class="listing__head" style="margin-top:28px"><h1>❤️ 관심상품</h1><span class="count">{{ $items->count() }}개</span></div>
    @if($items->isEmpty())
        <div class="empty"><div class="big">🤍</div><p>관심상품이 없습니다.</p>
            <a class="btn btn--primary" href="{{ route('catalog.all') }}" style="display:inline-block;margin-top:14px">쇼핑하러 가기</a></div>
    @else
        <div class="grid">
            @foreach($items as $w)
                @php $product = $w->product; @endphp
                @include('partials.product-card')
            @endforeach
        </div>
    @endif
</div>
@endsection
