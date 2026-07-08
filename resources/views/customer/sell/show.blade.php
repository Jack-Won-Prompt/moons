@extends('layouts.storefront')
@section('title', $sr->code . ' 판매 상세 · MOONS')

@php
    $steps = ['접수','감정','견적','승인','입고','정산완료'];
    $idx = ['received'=>0,'appraising'=>1,'photo_requested'=>1,'quoting'=>2,'auctioning'=>2,
            'quoted'=>2,'customer_approved'=>3,'inbound'=>4,'settled'=>5][$sr->status] ?? 0;
    $rejected = $sr->status === 'rejected';
@endphp

@section('content')
<div class="wrap" style="max-width:920px">
    <div class="crumb" style="margin-top:22px"><a href="{{ route('sell.history') }}">판매 진행현황</a> / <span>{{ $sr->code }}</span></div>
    <div class="listing__head"><h1 style="font-size:24px">{{ $sr->brand }} · {{ $sr->title }}</h1>
        <span class="pill pill--{{ $sr->status_color }}" style="margin-left:8px">{{ $sr->status_label }}</span>
        <form action="{{ route('chat.start') }}" method="POST" style="margin-left:auto">@csrf
            <input type="hidden" name="type" value="quote"><input type="hidden" name="sell_request_id" value="{{ $sr->id }}">
            <button class="btn" style="padding:10px 16px">💬 견적 상담</button>
        </form>
    </div>

    {{-- 진행 타임라인 --}}
    @unless($rejected)
    <div class="panel-card">
        <div class="timeline">
            @foreach($steps as $i => $label)
                <div class="step {{ $i < $idx ? 'done' : ($i === $idx ? 'current' : '') }}">
                    <div class="dot">{{ $i < $idx ? '✓' : $i+1 }}</div>{{ $label }}
                </div>
            @endforeach
        </div>
    </div>
    @else
        <div class="alert alert--err">감정 결과 <b>가품/판정불가</b> 등으로 접수가 반려되었습니다. @if($sr->memo)<br>사유: {{ $sr->memo }}@endif</div>
    @endunless

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">
        {{-- 접수 정보 --}}
        <div class="panel-card">
            <h3>접수 정보</h3>
            <dl class="dl-grid">
                <dt>접수번호</dt><dd>{{ $sr->code }}</dd>
                <dt>판매 방식</dt><dd>{{ $sr->method === 'auction' ? '경매 견적' : '일반 견적' }}</dd>
                <dt>판매 대상</dt><dd>{{ $sr->target_label }}</dd>
                <dt>수령 방법</dt><dd>{{ $sr->delivery_method === 'visit' ? '방문 예약' : '택배 접수' }}
                    @if($sr->visit_at) · {{ $sr->visit_at->format('Y.m.d H:i') }}@endif</dd>
                <dt>희망가</dt><dd>{{ $sr->desired_price ? number_format($sr->desired_price).'원' : '-' }}</dd>
                <dt>접수일</dt><dd>{{ $sr->created_at->format('Y.m.d H:i') }}</dd>
            </dl>
            @if($sr->description)<p style="margin-top:14px;font-size:14px;color:var(--ink-soft)">{{ $sr->description }}</p>@endif
        </div>

        {{-- 감정 결과 --}}
        <div class="panel-card">
            <h3>감정 결과</h3>
            @if($sr->appraisal_result === 'pending')
                <p style="color:var(--muted)">감정 대기 중입니다.</p>
            @else
                <dl class="dl-grid">
                    <dt>판정</dt><dd>
                        @php $r=['authentic'=>['정품','green'],'fake'=>['가품','red'],'uncertain'=>['판정보류','amber']][$sr->appraisal_result] ?? ['-','gray']; @endphp
                        <span class="pill pill--{{ $r[1] }}">{{ $r[0] }}</span></dd>
                    <dt>감정사</dt><dd>{{ $sr->appraiser }}</dd>
                </dl>
                @if($sr->appraisal)
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px">
                        @foreach(\App\Models\SellRequest::CHECKLIST as $k=>$label)
                            @php $v = $sr->appraisal[$k] ?? null; @endphp
                            <span class="chip" style="font-size:12px">{{ $label }} {{ $v==='ok'?'✅':($v==='fail'?'❌':'—') }}</span>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- 사진 --}}
    @if($sr->photos && count($sr->photos))
    <div class="panel-card">
        <h3>등록 사진 ({{ count($sr->photos) }})</h3>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
            @foreach($sr->photos as $p)<img src="{{ asset($p) }}" alt="" style="width:110px;height:110px;object-fit:cover;border-radius:10px;border:1px solid var(--line)">@endforeach
        </div>
    </div>
    @endif

    {{-- 견적 / 경매 --}}
    @if($sr->method === 'auction')
        <div class="panel-card">
            <h3>경매 입찰 현황 <small style="color:var(--muted);font-weight:500">· 참여 지점 {{ $sr->bids->count() }}곳</small></h3>
            @forelse($sr->bids as $bid)
                <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid var(--line-soft)">
                    <div style="flex:1">
                        <b>{{ $bid->store->company_name }}</b> {!! $bid->status==='won' ? '<span class="pill pill--green">낙찰</span>' : '' !!}
                        @if($bid->message)<div style="font-size:13px;color:var(--muted)">{{ $bid->message }}</div>@endif
                    </div>
                    <div style="font-size:18px;font-weight:800">{{ number_format($bid->bid_price) }}원</div>
                    @if($sr->status === 'auctioning')
                        <form action="{{ route('sell.approve', $sr) }}" method="POST">@csrf
                            <input type="hidden" name="bid_id" value="{{ $bid->id }}">
                            <button class="btn btn--primary" style="padding:9px 16px" type="submit">이 지점 낙찰</button>
                        </form>
                    @endif
                </div>
            @empty
                <p style="color:var(--muted)">아직 입찰이 없습니다. 지점들의 입찰을 기다리고 있습니다.</p>
            @endforelse
        </div>
    @else
        @if($sr->quote_price)
        <div class="panel-card" style="border-color:var(--ink)">
            <h3>견적 금액</h3>
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
                <div style="font-size:30px;font-weight:800">{{ number_format($sr->quote_price) }}원 <small style="font-size:14px;color:var(--muted)">by {{ $sr->target_label }}</small></div>
                @if($sr->status === 'quoted')
                    <form action="{{ route('sell.approve', $sr) }}" method="POST">@csrf
                        <button class="btn btn--primary btn--lg" type="submit">견적 승인하기</button>
                    </form>
                @elseif(in_array($sr->status, ['customer_approved','inbound','settled']))
                    <span class="pill pill--green" style="font-size:14px;padding:8px 14px">승인 완료</span>
                @endif
            </div>
        </div>
        @endif
    @endif

    {{-- 감정서 --}}
    @if($sr->certificate)
    <div class="panel-card" style="background:linear-gradient(135deg,#faf6ee,#fff);border-color:var(--gold)">
        <h3>🎖️ 블록체인 감정서 발급 완료</h3>
        <p style="margin:0 0 14px;font-size:14px;color:var(--ink-soft)">감정서 번호 <b>{{ $sr->certificate->code }}</b> · Digital Product Passport가 생성되었습니다.</p>
        <a class="btn btn--primary" href="{{ route('verify.show', $sr->certificate->code) }}" target="_blank">감정서 · DPP 조회하기</a>
    </div>
    @endif
</div>
@endsection
