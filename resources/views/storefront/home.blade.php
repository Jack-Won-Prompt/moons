@extends('layouts.storefront')
@section('title', 'MOONS · 럭셔리 셀렉트샵')

@section('content')
<div class="wrap">

    {{-- Hero --}}
    <section class="hero">
        <div class="hero__grid">
            @php $useBanners = $heroBanners->count() > 0; $count = $useBanners ? $heroBanners->count() : $slides->count(); @endphp
            @if($count)
                <div class="hero__slider" id="heroSlider" data-count="{{ $count }}">
                    <div class="hero__track">
                        @if($useBanners)
                            @foreach($heroBanners as $b)
                                <a href="{{ $b->link ?: '#' }}" class="hero__slide" style="background:linear-gradient(135deg,{{ $b->gradient ?: '#1a1a2e,#4b1248' }})">
                                    @if($b->image)<img src="{{ $b->image_url }}" alt="{{ $b->title }}">@endif
                                    <div class="hero__slide-cap">
                                        @if($b->eyebrow)<div class="eyebrow">{{ $b->eyebrow }}</div>@endif
                                        <h2>{{ $b->title }}</h2>
                                        @if($b->subtitle)<p class="hero__slide-price"><span class="now" style="font-size:15px;font-weight:500">{{ $b->subtitle }}</span></p>@endif
                                    </div>
                                </a>
                            @endforeach
                        @else
                            @foreach($slides as $s)
                                <a href="{{ route('catalog.product', $s) }}" class="hero__slide">
                                    <img src="{{ $s->image_url }}" alt="{{ $s->name }}" {{ $loop->first ? '' : 'loading=lazy' }}>
                                    <div class="hero__slide-cap">
                                        <div class="eyebrow">MOONS EXCLUSIVE · {{ $s->brand }}</div>
                                        <h2>{{ \Illuminate\Support\Str::limit($s->name, 40) }}</h2>
                                        <p class="hero__slide-price">
                                            @if($s->discount_rate)<span class="rate">{{ $s->discount_rate }}%</span>@endif
                                            <span class="now">{{ number_format($s->final_price) }}원</span>
                                            @if($s->discount_rate)<span class="was">{{ number_format($s->price) }}원</span>@endif
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                    <button class="hero__nav prev" type="button" aria-label="이전">‹</button>
                    <button class="hero__nav next" type="button" aria-label="다음">›</button>
                    <div class="hero__dots">
                        @for($i = 0; $i < $count; $i++)
                            <button type="button" class="dot {{ $i === 0 ? 'on' : '' }}" data-i="{{ $i }}" aria-label="{{ $i + 1 }}번 슬라이드"></button>
                        @endfor
                    </div>
                </div>
            @else
                <a href="{{ route('catalog.all', ['sort'=>'discount']) }}" class="hero__card"
                   style="background:linear-gradient(135deg,#1a1a2e,#4b1248)">
                    <div>
                        <div class="eyebrow">MOONS Exclusive</div>
                        <h2>이번 시즌 럭셔리<br>최대 45% 특가</h2>
                        <p>전 세계 명품을 가장 합리적인 가격으로 만나보세요</p>
                    </div>
                </a>
            @endif

            <div class="hero__stack">
                <a href="{{ route('catalog.category', 'handbags') }}" class="hero__card hero__card--sm"
                   style="background:linear-gradient(135deg,#603813,#b29f94)">
                    @if($bagFeature)<img class="hero__card-bg" src="{{ $bagFeature->image_url }}" alt="" loading="lazy">@endif
                    <div><div class="eyebrow">Handbags</div><h2>잇백 컬렉션</h2></div>
                </a>
                <a href="{{ route('catalog.category', 'womens-shoes') }}" class="hero__card hero__card--sm"
                   style="background:linear-gradient(135deg,#2b5876,#4e4376)">
                    @if($shoeFeature)<img class="hero__card-bg" src="{{ $shoeFeature->image_url }}" alt="" loading="lazy">@endif
                    <div><div class="eyebrow">Shoes</div><h2>슈즈 컬렉션</h2></div>
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

    {{-- 기획전 --}}
    @if($promotions->count())
    <section class="section">
        <div class="section__head"><div class="section__title">기획전 <small>MOONS가 준비한 특별 기획</small></div></div>
        <div class="promo-grid">
            @foreach($promotions as $promo)
                <a href="{{ route('content.promotion', $promo) }}" class="promo-card" style="background:linear-gradient(135deg,{{ $promo->gradient ?: '#1a1a2e,#4b1248' }})">
                    <div class="eyebrow">기획전</div>
                    <h3>{{ $promo->title }}</h3>
                    @if($promo->subtitle)<p>{{ $promo->subtitle }}</p>@endif
                </a>
            @endforeach
        </div>
    </section>
    <style>
        .promo-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
        .promo-card{border-radius:14px;padding:28px 24px;color:#fff;min-height:150px;display:flex;flex-direction:column;justify-content:flex-end}
        .promo-card .eyebrow{font-size:11px;letter-spacing:.14em;text-transform:uppercase;opacity:.85}
        .promo-card h3{font-size:20px;font-weight:800;margin:6px 0 4px} .promo-card p{font-size:13px;opacity:.9;margin:0}
        @media(max-width:720px){ .promo-grid{grid-template-columns:1fr} }
    </style>
    @endif

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

<script>
    (function () {
        var slider = document.getElementById('heroSlider');
        if (!slider) return;
        var track = slider.querySelector('.hero__track');
        var dots  = Array.prototype.slice.call(slider.querySelectorAll('.dot'));
        var count = parseInt(slider.getAttribute('data-count'), 10) || 1;
        var i = 0, timer;

        function go(n) {
            i = (n + count) % count;
            track.style.transform = 'translateX(-' + (i * 100) + '%)';
            dots.forEach(function (d, k) { d.classList.toggle('on', k === i); });
        }
        function next() { go(i + 1); }
        function start() { stop(); timer = setInterval(next, 4500); }
        function stop() { if (timer) clearInterval(timer); }

        slider.querySelector('.next').addEventListener('click', function (e) { e.preventDefault(); next(); start(); });
        slider.querySelector('.prev').addEventListener('click', function (e) { e.preventDefault(); go(i - 1); start(); });
        dots.forEach(function (d) {
            d.addEventListener('click', function (e) { e.preventDefault(); go(parseInt(d.getAttribute('data-i'), 10)); start(); });
        });
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);
        start();
    })();
</script>
@endsection
