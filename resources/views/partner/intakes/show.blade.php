@extends('layouts.partner')
@section('title', '접수 ' . $sr->code)
@section('subtitle', $sr->brand . ' · ' . $sr->title)

@section('content')
@php $isDirect = $sr->target_type === 'store' && $sr->target_store_id === $me; @endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">
    {{-- 좌: 접수/사진 --}}
    <div>
        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 14px;font-size:16px">접수 정보 <span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span></h2>
            <dl class="dl-grid" style="font-size:13px">
                <dt>접수번호</dt><dd>{{ $sr->code }}</dd>
                <dt>고객</dt><dd>{{ $sr->customer->name }}</dd>
                <dt>방식</dt><dd>{{ $sr->method==='auction'?'경매':'일반견적' }} · {{ $sr->delivery_method==='visit'?'방문':'택배' }}</dd>
                <dt>희망가</dt><dd>{{ $sr->desired_price ? number_format($sr->desired_price).'원' : '-' }}</dd>
            </dl>
            @if($sr->description)<p style="font-size:13px;color:var(--p-muted);margin-top:12px">{{ $sr->description }}</p>@endif
            @if($sr->photos && count($sr->photos))
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px">
                    @foreach($sr->photos as $p)<img src="{{ asset($p) }}" style="width:88px;height:88px;object-fit:cover;border-radius:9px;border:1px solid var(--p-line)">@endforeach
                </div>
            @endif
        </div></div>
    </div>

    {{-- 우: 감정/견적 또는 입찰 --}}
    <div>
        @if($isDirect)
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 16px;font-size:16px">정품 감정 · 견적 등록</h2>
                <form action="{{ route('partner.intakes.appraise', $sr) }}" method="POST">@csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 14px;margin-bottom:14px">
                        @foreach(\App\Models\SellRequest::CHECKLIST as $k=>$label)
                            <div class="pfield" style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                                <label style="margin:0;font-size:13px">{{ $label }}</label>
                                <select name="checklist[{{ $k }}]" style="width:auto;padding:6px 10px">
                                    @php $v=$sr->appraisal[$k]??''; @endphp
                                    <option value="">-</option>
                                    <option value="ok" @selected($v==='ok')>정상</option>
                                    <option value="fail" @selected($v==='fail')>이상</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="pfield" style="margin-bottom:12px"><label>감정 판정 *</label>
                        <select name="appraisal_result" required>
                            <option value="authentic" @selected($sr->appraisal_result==='authentic')>정품</option>
                            <option value="uncertain" @selected($sr->appraisal_result==='uncertain')>판정보류</option>
                            <option value="fake" @selected($sr->appraisal_result==='fake')>가품(반려)</option>
                        </select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div class="pfield"><label>감정사 *</label><input type="text" name="appraiser" value="{{ $sr->appraiser }}" placeholder="담당 감정사" required></div>
                        <div class="pfield"><label>견적/매입가 (원)</label><input type="number" name="quote_price" value="{{ $sr->quote_price }}" min="0"></div>
                    </div>
                    <div class="pfield" style="margin-top:12px"><label>메모</label><textarea name="memo" rows="2">{{ $sr->memo }}</textarea></div>
                    <button class="pbtn pbtn--primary" type="submit" style="margin-top:14px">감정·견적 등록</button>
                </form>
            </div></div>
        @endif

        @if($sr->method === 'auction')
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 6px;font-size:16px">경매 입찰</h2>
                <p style="font-size:13px;color:var(--p-muted);margin:0 0 14px">현재 최고 입찰: <b>{{ $sr->bids->max('bid_price') ? number_format($sr->bids->max('bid_price')).'원' : '없음' }}</b> · 참여 {{ $sr->bids->count() }}곳</p>
                @if($sr->status === 'auctioning')
                    <form action="{{ route('partner.intakes.bid', $sr) }}" method="POST">@csrf
                        <div class="pfield"><label>입찰가 (원) *</label><input type="number" name="bid_price" value="{{ $myBid->bid_price ?? '' }}" min="1000" required></div>
                        <div class="pfield" style="margin-top:10px"><label>메시지</label><input type="text" name="message" value="{{ $myBid->message ?? '' }}" placeholder="고객에게 전할 코멘트"></div>
                        <button class="pbtn pbtn--primary" type="submit" style="margin-top:12px">{{ $myBid ? '입찰 수정' : '입찰하기' }}</button>
                    </form>
                @else
                    <p class="pill pill--{{ $sr->winning_store_id===$me?'green':'gray' }}">{{ $sr->winning_store_id===$me ? '낙찰 (우리 지점)' : '경매 종료' }}</p>
                @endif
            </div></div>
        @endif

        {{-- 입고 처리 --}}
        @if($sr->status === 'customer_approved' && ($sr->winning_store_id === $me || $sr->target_store_id === $me))
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 8px;font-size:16px">입고 처리</h2>
                <p style="font-size:13px;color:var(--p-muted);margin:0 0 12px">고객이 견적을 승인했습니다. 상품 입고 후 확인 처리하세요.</p>
                <form action="{{ route('partner.intakes.inbound', $sr) }}" method="POST">@csrf
                    <button class="pbtn pbtn--primary" type="submit">입고 확인</button>
                </form>
            </div></div>
        @elseif(in_array($sr->status, ['inbound','settled']))
            <div class="panel"><div class="panel__body"><p class="pill pill--green">입고 완료 {{ $sr->status==='settled' ? '· 정산완료' : '· 본사 감정서 발급 대기' }}</p></div></div>
        @endif

        <a class="pbtn" href="{{ route('partner.intakes.index') }}">← 목록으로</a>
    </div>
</div>
@endsection
