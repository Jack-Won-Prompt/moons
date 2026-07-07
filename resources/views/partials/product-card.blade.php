@php
    $grad = $product->color ?: '#232526,#414345';
@endphp
<article class="card">
    <a href="{{ route('catalog.product', $product) }}">
        <div class="card__thumb">
            <div class="card__badges">
                @if($product->is_new)<span class="badge badge--new">NEW</span>@endif
                @if($product->is_best)<span class="badge badge--best">BEST</span>@endif
                @if($product->discount_rate)<span class="badge badge--sale">{{ $product->discount_rate }}%</span>@endif
            </div>
            <button class="wish" type="button" aria-label="위시리스트">♡</button>
            @if($product->image)
                <img class="ph" src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="ph" style="background:linear-gradient(135deg,{{ $grad }})">
                    <span class="plabel">{{ $product->brand }}</span>
                </div>
            @endif
        </div>
        <div class="card__brand">{{ $product->brand }}</div>
        <div class="card__name">{{ $product->name }}</div>
        <div class="card__price">
            @if($product->discount_rate)
                <span class="rate">{{ $product->discount_rate }}%</span>
                <span class="now">{{ number_format($product->final_price) }}원</span>
                <span class="was">{{ number_format($product->price) }}원</span>
            @else
                <span class="now">{{ number_format($product->final_price) }}원</span>
            @endif
        </div>
    </a>
</article>
