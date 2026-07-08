@extends('layouts.admin')
@section('title', '접수 ' . $sr->code)
@section('subtitle', $sr->brand . ' · ' . $sr->title)

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">
    {{-- 접수/사진/경매 --}}
    <div>
        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 14px;font-size:16px">접수 정보 <span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span></h2>
            <dl class="dl-grid" style="font-size:13px">
                <dt>접수번호</dt><dd>{{ $sr->code }}</dd>
                <dt>고객</dt><dd>{{ $sr->customer->name }} · {{ $sr->customer->email }}</dd>
                <dt>판매 대상</dt><dd>{{ $sr->target_label }}</dd>
                <dt>방식</dt><dd>{{ $sr->method==='auction'?'경매 견적':'일반 견적' }} · {{ $sr->delivery_method==='visit'?'방문':'택배' }}</dd>
                <dt>희망가</dt><dd>{{ $sr->desired_price ? number_format($sr->desired_price).'원' : '-' }}</dd>
                <dt>접수일</dt><dd>{{ $sr->created_at->format('Y.m.d H:i') }}</dd>
            </dl>
            @if($sr->description)<p style="font-size:13px;color:var(--p-muted);margin-top:12px">{{ $sr->description }}</p>@endif
            @if($sr->photos && count($sr->photos))
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:14px">
                    @foreach($sr->photos as $p)<img src="{{ asset($p) }}" style="width:88px;height:88px;object-fit:cover;border-radius:9px;border:1px solid var(--p-line)">@endforeach
                </div>
            @endif
        </div></div>

        @if($sr->method === 'auction' && $sr->bids->count())
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 12px;font-size:16px">경매 입찰 현황</h2>
                @foreach($sr->bids as $bid)
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--p-line);font-size:14px">
                        <span>{{ $bid->store->company_name }} {!! $bid->status==='won'?'<span class="pill pill--green">낙찰</span>':'' !!}</span>
                        <b>{{ number_format($bid->bid_price) }}원</b>
                    </div>
                @endforeach
            </div></div>
        @endif
    </div>

    {{-- 감정 / 감정서 발급 --}}
    <div>
        {{-- 본사 직접 감정 (target=head_office, 아직 견적 전) --}}
        @if($sr->target_type === 'head_office' && in_array($sr->status, ['received','appraising','photo_requested','quoting']))
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 16px;font-size:16px">본사 정품 감정 · 견적</h2>
                <form action="{{ route('admin.sell.appraise', $sr) }}" method="POST">@csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 14px;margin-bottom:14px">
                        @foreach(\App\Models\SellRequest::CHECKLIST as $k=>$label)
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px">
                                <label style="font-size:13px">{{ $label }}</label>
                                <select name="checklist[{{ $k }}]" style="width:auto;padding:6px 10px;border:1px solid var(--p-line);border-radius:8px">
                                    <option value="">-</option><option value="ok">정상</option><option value="fail">이상</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="pfield" style="margin-bottom:12px"><label>판정 *</label>
                        <select name="appraisal_result" required><option value="authentic">정품</option><option value="uncertain">판정보류</option><option value="fake">가품(반려)</option></select>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div class="pfield"><label>감정사 *</label><input type="text" name="appraiser" placeholder="감정사명" required></div>
                        <div class="pfield"><label>견적/매입가</label><input type="number" name="quote_price" min="0"></div>
                    </div>
                    <div class="pfield" style="margin-top:12px"><label>메모</label><textarea name="memo" rows="2"></textarea></div>
                    <button class="pbtn pbtn--primary" type="submit" style="margin-top:14px">감정·견적 등록</button>
                </form>
            </div></div>
        @endif

        {{-- 감정 결과 요약 --}}
        @if($sr->appraisal_result !== 'pending')
            <div class="panel"><div class="panel__body">
                <h2 style="margin:0 0 12px;font-size:16px">감정 결과</h2>
                <dl class="dl-grid" style="font-size:13px">
                    <dt>판정</dt><dd><span class="pill pill--{{ $sr->appraisal_result==='authentic'?'green':($sr->appraisal_result==='fake'?'red':'amber') }}">
                        {{ ['authentic'=>'정품','fake'=>'가품','uncertain'=>'판정보류'][$sr->appraisal_result] }}</span></dd>
                    <dt>감정사</dt><dd>{{ $sr->appraiser }}</dd>
                    <dt>견적</dt><dd>{{ $sr->quote_price ? number_format($sr->quote_price).'원' : '-' }}</dd>
                </dl>
            </div></div>
        @endif

        {{-- 감정서 발급 --}}
        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 12px;font-size:16px">🎖️ 블록체인 감정서 · DPP</h2>
            @if($sr->certificate)
                <p style="font-size:14px">발급 완료 · <b>{{ $sr->certificate->code }}</b></p>
                <a class="pbtn pbtn--primary pbtn--sm" href="{{ route('verify.show', $sr->certificate->code) }}" target="_blank">감정서 조회</a>
            @elseif(in_array($sr->status, ['customer_approved','inbound']) && $sr->appraisal_result !== 'fake')
                <p style="font-size:13px;color:var(--p-muted);margin:0 0 12px">입고 확인 후 감정서를 발급하면 블록체인 해시와 DPP가 생성됩니다.</p>
                <form action="{{ route('admin.sell.certificate', $sr) }}" method="POST">@csrf
                    <button class="pbtn pbtn--primary" type="submit">감정서 발급 + DPP 생성</button>
                </form>
            @else
                <p style="font-size:13px;color:var(--p-muted)">고객 승인·입고 완료 후 발급 가능합니다.</p>
            @endif
        </div></div>

        <a class="pbtn" href="{{ route('admin.sell.index') }}">← 목록으로</a>
    </div>
</div>
@endsection
