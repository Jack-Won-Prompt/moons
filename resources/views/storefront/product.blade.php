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
            @php $avg = $product->rating_avg; $rc = $product->reviews->count(); @endphp
            @if($rc)
                <div style="color:var(--gold);font-size:15px;letter-spacing:2px;margin:-10px 0 16px">
                    {!! str_repeat('★', round($avg)) . str_repeat('☆', 5-round($avg)) !!}
                    <span style="color:var(--ink-soft);font-size:13px;letter-spacing:0"> {{ $avg }} · 후기 {{ $rc }}건</span>
                </div>
            @endif

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
            <div style="display:flex;gap:12px;margin-top:12px">
                <button class="btn wish-btn {{ $wished ? 'on' : '' }}" type="button" data-product-id="{{ $product->id }}" style="flex:1">
                    <span class="wtxt">{{ $wished ? '♥ 관심상품 담김' : '♡ 관심상품' }}</span>
                </button>
                <form action="{{ route('chat.start') }}" method="POST" style="flex:1">@csrf
                    <input type="hidden" name="type" value="product">
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button class="btn btn--block" type="submit">💬 상품 문의</button>
                </form>
            </div>
            @else
            <div class="pd__cta">
                <a class="btn btn--lg btn--block" href="{{ route('login') }}">🛒 장바구니</a>
                <a class="btn btn--lg btn--primary btn--block" href="{{ route('login') }}">바로 구매하기</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- 리뷰 --}}
    <section class="section" id="reviews">
        <div class="section__head"><div class="section__title">상품 후기 <small>{{ $product->reviews->count() }}건 · 평점 {{ $product->rating_avg ?: '-' }}</small></div></div>

        @if($canReview)
        <div class="panel-card">
            <h3>후기 작성</h3>
            <form action="{{ route('reviews.store', $product) }}" method="POST" enctype="multipart/form-data">@csrf
                <div class="star-input" style="font-size:26px;color:var(--gold);cursor:pointer;margin-bottom:10px">
                    @for($i=1;$i<=5;$i++)<span data-v="{{ $i }}">★</span>@endfor
                    <input type="hidden" name="rating" value="5">
                </div>
                <div class="field"><textarea name="body" rows="3" placeholder="상품은 어떠셨나요?"></textarea></div>
                <div class="field"><label>사진 첨부 (최대 5장)</label><input type="file" name="photos[]" accept="image/*" multiple></div>
                <button class="btn btn--primary" type="submit">후기 등록</button>
            </form>
        </div>
        @endif

        @forelse($product->reviews as $rv)
            <div class="panel-card" style="padding:18px 22px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <div><span style="color:var(--gold);letter-spacing:2px">{!! str_repeat('★',$rv->rating).str_repeat('☆',5-$rv->rating) !!}</span>
                        <b style="margin-left:8px">{{ \Illuminate\Support\Str::mask($rv->user?->name ?? '고객','*',1) }}</b></div>
                    <span style="font-size:12px;color:var(--muted)">{{ $rv->created_at->format('Y.m.d') }}</span>
                </div>
                @if($rv->body)<p style="margin:10px 0 0;font-size:14px;color:var(--ink-soft)">{{ $rv->body }}</p>@endif
                @if($rv->photos && count($rv->photos))
                    <div style="display:flex;gap:8px;margin-top:10px;flex-wrap:wrap">
                        @foreach($rv->photos as $ph)<img src="{{ \Illuminate\Support\Str::startsWith($ph,'http')?$ph:asset($ph) }}" style="width:90px;height:90px;object-fit:cover;border-radius:9px">@endforeach
                    </div>
                @endif
            </div>
        @empty
            <p style="color:var(--muted)">아직 후기가 없습니다. 첫 후기를 남겨보세요!</p>
        @endforelse
    </section>

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
    // 별점 입력
    document.querySelectorAll('.star-input span').forEach(function(s){
        s.addEventListener('click', function(){
            var v=+s.dataset.v; s.parentNode.querySelector('input').value=v;
            s.parentNode.querySelectorAll('span').forEach(function(x){ x.textContent=(+x.dataset.v<=v)?'★':'☆'; });
        });
    });
</script>

<script>
    function pdSwap(btn, src) {
        var main = document.getElementById('pdMain');
        if (main) main.src = src;
        document.querySelectorAll('.pd__thumb').forEach(function (t) { t.classList.remove('on'); });
        btn.classList.add('on');
    }
</script>
@endsection
