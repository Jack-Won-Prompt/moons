@extends('layouts.storefront')
@section('title', $product->brand . ' ' . $product->name . ' · MOONS')

@section('content')
<div class="wrap">
    <div class="crumb">
        <a href="{{ route('home') }}">홈</a>
        / <a href="{{ route('catalog.category', $product->category) }}">{{ $product->category->name }}</a>
        / <span>{{ $product->brand }}</span>
    </div>

    <div class="pd">
        <div>
            <div class="pd__media">
                @if($product->image)
                    <img id="pdMain" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                @else
                    <div class="ph" style="background:linear-gradient(135deg,{{ $product->color ?: '#232526,#414345' }})">
                        <span class="plabel" style="font-size:26px;font-weight:800;letter-spacing:.04em">{{ $product->brand }}</span>
                    </div>
                @endif
            </div>
            @if(is_array($product->gallery) && count($product->gallery))
                <div class="pd__gallery">
                    @if($product->image)
                        <button type="button" class="pd__thumb on" onclick="pdSwap(this,'{{ $product->image_url }}')">
                            <img src="{{ $product->image_url }}" alt="">
                        </button>
                    @endif
                    @foreach($product->gallery as $g)
                        @php $gsrc = \Illuminate\Support\Str::startsWith($g, 'http') ? $g : asset($g); @endphp
                        <button type="button" class="pd__thumb" onclick="pdSwap(this,'{{ $gsrc }}')">
                            <img src="{{ $gsrc }}" alt="" loading="lazy">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="pd__info">
            @if($product->is_new || $product->is_best)
                <div class="card__badges" style="position:static;margin-bottom:12px">
                    @if($product->is_new)<span class="badge badge--new">NEW</span>@endif
                    @if($product->is_best)<span class="badge badge--best">BEST</span>@endif
                </div>
            @endif
            <div class="pd__brand">{{ $product->brand }}</div>
            <div class="pd__name">{{ $product->name }}</div>

            <div class="pd__price">
                @if($product->discount_rate)
                    <span class="rate">{{ $product->discount_rate }}%</span>
                    <span class="now">{{ number_format($product->final_price) }}원</span>
                    <span class="was">{{ number_format($product->price) }}원</span>
                @else
                    <span class="now">{{ number_format($product->final_price) }}원</span>
                @endif
            </div>

            <div class="pd__meta">
                <dl>
                    <dt>카테고리</dt><dd>{{ $product->category->parent?->name }} · {{ $product->category->name }}</dd>
                    <dt>판매자</dt><dd>{{ $product->seller_type === 'store' ? '🏬' : '🏢' }} <b>{{ $product->seller_label }}</b> · 정품 감정 완료</dd>
                    <dt>배송</dt><dd>무료배송 · 정품 보증서 동봉</dd>
                    <dt>재고</dt><dd>{{ $product->stock > 0 ? '재고 있음 ('.$product->stock.'개)' : '품절' }}</dd>
                </dl>
            </div>

            <p class="pd__desc">{{ $product->description }}</p>

            @auth('web')
            <div class="pd__cta">
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn--lg btn--block" type="submit">🛒 장바구니</button>
                </form>
                <form action="{{ route('cart.add') }}" method="POST" style="flex:1">@csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="buy_now" value="1">
                    <button class="btn btn--lg btn--primary btn--block" type="submit">바로 구매하기</button>
                </form>
            </div>
            <form action="{{ route('chat.start') }}" method="POST" style="margin-top:12px">@csrf
                <input type="hidden" name="type" value="product">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button class="btn btn--block" type="submit">💬 상품 문의 (실시간 상담)</button>
            </form>
            @else
            <div class="pd__cta">
                <a class="btn btn--lg btn--block" href="{{ route('login') }}">🛒 장바구니</a>
                <a class="btn btn--lg btn--primary btn--block" href="{{ route('login') }}">바로 구매하기</a>
            </div>
            @endauth
        </div>
    </div>

    @if($related->count())
    <section class="section">
        <div class="section__head"><div class="section__title">함께 보면 좋은 상품</div></div>
        <div class="grid">
            @foreach($related as $product)
                @include('partials.product-card')
            @endforeach
        </div>
    </section>
    @endif
</div>

<script>
    function pdSwap(btn, src) {
        var main = document.getElementById('pdMain');
        if (main) main.src = src;
        document.querySelectorAll('.pd__thumb').forEach(function (t) { t.classList.remove('on'); });
        btn.classList.add('on');
    }
</script>
@endsection
