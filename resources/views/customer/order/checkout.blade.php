@extends('layouts.storefront')
@section('title', '주문/결제 · MOONS')

@section('content')
<div class="wrap" style="max-width:900px">
    <div class="listing__head" style="margin-top:28px"><h1>주문 / 결제</h1></div>
    @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif

    <form action="{{ route('orders.place') }}" method="POST" style="display:grid;grid-template-columns:1.4fr 1fr;gap:20px;align-items:start">@csrf
        <div>
            {{-- 배송지 --}}
            <div class="panel-card">
                <h3>배송지 정보</h3>
                <div class="form-2col">
                    <div class="field"><label>받는 분 *</label><input type="text" name="receiver_name" value="{{ old('receiver_name', $user->name) }}" required></div>
                    <div class="field"><label>연락처 *</label><input type="text" name="phone" value="{{ old('phone') }}" placeholder="010-0000-0000" required></div>
                </div>
                <div class="field"><label>우편번호</label><input type="text" name="zipcode" value="{{ old('zipcode') }}" style="max-width:160px"></div>
                <div class="field"><label>주소 *</label><input type="text" name="address" value="{{ old('address') }}" placeholder="도로명 주소" required></div>
                <div class="field"><label>상세주소</label><input type="text" name="address_detail" value="{{ old('address_detail') }}"></div>
                <div class="field"><label>배송 메모</label><input type="text" name="memo" value="{{ old('memo') }}" placeholder="예: 부재 시 문 앞"></div>
            </div>

            {{-- 결제수단 --}}
            <div class="panel-card">
                <h3>결제 수단</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    @foreach(['card'=>'💳 신용카드','kakao'=>'💛 카카오페이','naver'=>'💚 네이버페이','vbank'=>'🏦 가상계좌'] as $m=>$label)
                        <label class="pay-pick"><input type="radio" name="method" value="{{ $m }}" @checked($loop->first)> {{ $label }}</label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 주문 요약 --}}
        <div class="panel-card" style="position:sticky;top:150px">
            <h3>주문 상품 ({{ $items->count() }})</h3>
            @foreach($items as $it)
                <div style="display:flex;gap:10px;align-items:center;padding:8px 0;border-bottom:1px solid var(--line-soft)">
                    <div style="width:44px;height:44px;border-radius:8px;overflow:hidden;background:var(--bg-alt);flex:none">
                        @if($it->product->image)<img src="{{ $it->product->image_url }}" style="width:100%;height:100%;object-fit:cover">@endif</div>
                    <div style="flex:1;font-size:13px"><b>{{ $it->product->brand }}</b><br>{{ Str::limit($it->product->name,20) }} · {{ $it->quantity }}개</div>
                    <div style="font-weight:600;font-size:13px">{{ number_format($it->line_total) }}원</div>
                </div>
            @endforeach
            {{-- 쿠폰 --}}
            <div style="margin:16px 0 10px">
                <label style="font-size:12px;color:var(--muted)">쿠폰</label>
                <select name="user_coupon_id" id="couponSel" onchange="calcTotal()" style="width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:10px;margin-top:5px">
                    <option value="" data-d="0">쿠폰 미사용</option>
                    @foreach($coupons as $uc)
                        <option value="{{ $uc->id }}" data-d="{{ $uc->coupon->discountFor($subtotal) }}">{{ $uc->coupon->name }} ({{ $uc->coupon->label }})</option>
                    @endforeach
                </select>
            </div>
            {{-- 포인트 --}}
            <div style="margin-bottom:12px">
                <label style="font-size:12px;color:var(--muted)">포인트 사용 (보유 {{ number_format($user->points) }}P)</label>
                <div style="display:flex;gap:8px;margin-top:5px">
                    <input type="number" name="point_used" id="pointInput" value="0" min="0" max="{{ $user->points }}" oninput="calcTotal()" style="flex:1;padding:10px 12px;border:1px solid var(--line);border-radius:10px">
                    <button type="button" class="btn" style="padding:8px 14px" onclick="document.getElementById('pointInput').value={{ min($user->points,$subtotal) }};calcTotal()">전액</button>
                </div>
            </div>

            <div style="display:flex;justify-content:space-between;margin:8px 0 4px"><span>상품금액</span><span>{{ number_format($subtotal) }}원</span></div>
            <div style="display:flex;justify-content:space-between;color:var(--sale);font-size:14px"><span>쿠폰 할인</span><span id="dCoupon">0원</span></div>
            <div style="display:flex;justify-content:space-between;color:var(--sale);font-size:14px"><span>포인트 사용</span><span id="dPoint">0원</span></div>
            <div style="display:flex;justify-content:space-between;color:var(--muted);font-size:14px"><span>배송비</span><span>무료</span></div>
            <div style="display:flex;justify-content:space-between;align-items:baseline;margin-top:14px;padding-top:14px;border-top:1px solid var(--line)"><b>총 결제금액</b><b style="font-size:24px" id="totalOut">{{ number_format($subtotal) }}원</b></div>
            <div style="font-size:12px;color:var(--muted);margin-top:6px;text-align:right">결제 시 <b id="earnOut">{{ number_format((int)floor($subtotal*$user->pointRate())) }}</b>P 적립 예정</div>
            <button type="submit" class="btn btn--primary btn--block btn--lg" style="margin-top:14px">결제하기</button>
            <script>
                var SUBTOTAL={{ $subtotal }}, RATE={{ $user->pointRate() }}, MAXPOINT={{ $user->points }};
                function won(n){ return n.toLocaleString()+'원'; }
                function calcTotal(){
                    var sel=document.getElementById('couponSel'); var cd=parseInt(sel.options[sel.selectedIndex].dataset.d||0);
                    var afterCoupon=Math.max(0,SUBTOTAL-cd);
                    var pi=document.getElementById('pointInput'); var pu=Math.min(parseInt(pi.value||0),MAXPOINT,afterCoupon); if(pu<0)pu=0; pi.value=pu;
                    var total=Math.max(0,afterCoupon-pu);
                    document.getElementById('dCoupon').textContent='-'+won(cd);
                    document.getElementById('dPoint').textContent='-'+won(pu);
                    document.getElementById('totalOut').textContent=won(total);
                    document.getElementById('earnOut').textContent=Math.floor(total*RATE).toLocaleString();
                }
                calcTotal();
            </script>
        </div>
    </form>
</div>
<style>
    .form-2col{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
    .pay-pick{display:flex;align-items:center;gap:8px;padding:14px;border:1px solid var(--line);border-radius:11px;cursor:pointer;font-weight:600;font-size:14px}
    .pay-pick:has(input:checked){border-color:var(--ink);background:var(--bg-alt)}
    @media(max-width:760px){ form{grid-template-columns:1fr!important} .form-2col{grid-template-columns:1fr} }
</style>
@endsection
