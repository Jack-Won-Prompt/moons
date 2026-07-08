@extends('layouts.storefront')
@section('title', '장바구니 · MOONS')

@section('content')
<div class="wrap" style="max-width:900px">
    <div class="listing__head" style="margin-top:28px"><h1>🛒 장바구니</h1><span class="count">{{ $items->count() }}개 상품</span></div>

    @if($items->isEmpty())
        <div class="empty"><div class="big">🛒</div><p>장바구니가 비어 있습니다.</p>
            <a class="btn btn--primary" href="{{ route('catalog.all') }}" style="display:inline-block;margin-top:14px">쇼핑하러 가기</a></div>
    @else
        @foreach($items as $it)
            <div class="cart-row">
                <a href="{{ route('catalog.product', $it->product) }}" class="cart-thumb">
                    @if($it->product->image)<img src="{{ $it->product->image_url }}" alt="">@else<span>🛍️</span>@endif
                </a>
                <div class="cart-info">
                    <div class="cart-brand">{{ $it->product->brand }}</div>
                    <div class="cart-name">{{ $it->product->name }}</div>
                    <div class="cart-price">{{ number_format($it->product->final_price) }}원</div>
                </div>
                <form action="{{ route('cart.update', $it) }}" method="POST" class="cart-qty">@csrf @method('PATCH')
                    <select name="quantity" onchange="this.form.submit()">
                        @for($q=1;$q<=10;$q++)<option value="{{ $q }}" @selected($it->quantity==$q)>{{ $q }}</option>@endfor
                    </select>
                </form>
                <div class="cart-line">{{ number_format($it->line_total) }}원</div>
                <form action="{{ route('cart.remove', $it) }}" method="POST">@csrf @method('DELETE')
                    <button class="cart-del" title="삭제">✕</button></form>
            </div>
        @endforeach

        <div class="cart-summary">
            <div class="cart-total"><span>총 결제금액</span><b>{{ number_format($subtotal) }}원</b></div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:16px">무료배송 · 정품 보증서 동봉</div>
            <a class="btn btn--primary btn--block btn--lg" href="{{ route('checkout') }}">주문하기</a>
        </div>
    @endif
</div>
<style>
    .cart-row{display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;margin-bottom:12px}
    .cart-thumb{width:80px;height:80px;border-radius:10px;overflow:hidden;background:var(--bg-alt);display:grid;place-items:center;font-size:26px;flex:none}
    .cart-thumb img{width:100%;height:100%;object-fit:cover}
    .cart-info{flex:1} .cart-brand{font-weight:700;font-size:14px} .cart-name{font-size:13px;color:var(--ink-soft);margin:2px 0} .cart-price{font-size:13px;color:var(--muted)}
    .cart-qty select{padding:8px 10px;border:1px solid var(--line);border-radius:9px}
    .cart-line{font-weight:700;min-width:110px;text-align:right}
    .cart-del{border:0;background:none;color:var(--muted);font-size:16px;cursor:pointer}
    .cart-summary{background:#fff;border:1px solid var(--ink);border-radius:16px;padding:24px;margin-top:20px}
    .cart-total{display:flex;justify-content:space-between;align-items:baseline;font-size:16px;margin-bottom:6px} .cart-total b{font-size:26px}
    @media(max-width:640px){ .cart-name{display:none} .cart-line{min-width:80px} }
</style>
@endsection
