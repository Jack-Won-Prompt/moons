@extends('layouts.admin')
@section('title', '통계')
@section('subtitle', '매출 · 주문 · 감정 · 재고 · 멤버십')

@php $maxDay = max(1, $bars->max('value')); @endphp

@section('content')
<div style="display:flex;gap:8px;margin-bottom:18px">
    @foreach([7=>'최근 7일',30=>'최근 30일',90=>'최근 90일'] as $d=>$label)
        <a class="pbtn pbtn--sm {{ $days===$d ? 'pbtn--primary' : '' }}" href="{{ route('admin.stats.index', ['days'=>$d]) }}">{{ $label }}</a>
    @endforeach
</div>

<div class="stats">
    <div class="stat"><div class="k">💰 매출 ({{ $days }}일)</div><div class="v">{{ number_format($kpi['revenue']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">🧾 주문 ({{ $days }}일)</div><div class="v">{{ number_format($kpi['orders']) }}</div></div>
    <div class="stat"><div class="k">📊 평균 객단가</div><div class="v">{{ number_format($kpi['avg_order']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">👥 회원</div><div class="v">{{ number_format($kpi['members']) }}</div></div>
    <div class="stat"><div class="k">🔍 감정</div><div class="v">{{ number_format($kpi['appraisals']) }}</div></div>
    <div class="stat"><div class="k">🎖️ 감정서</div><div class="v">{{ number_format($kpi['certs']) }}</div></div>
</div>

{{-- 7일 매출 --}}
<div class="panel"><div class="panel__body">
    <h2 style="margin:0 0 18px;font-size:16px">매출 추이 ({{ $days<=30 ? '일별' : '주별' }})</h2>
    <div class="bars">
        @foreach($bars as $d)
            <div class="bar-col">
                <div class="bar-val">{{ $d['value']>0 ? number_format($d['value']/10000).'만' : '' }}</div>
                <div class="bar" style="height:{{ max(4, round($d['value']/$maxDay*140)) }}px"></div>
                <div class="bar-lbl">{{ $d['label'] }}</div>
            </div>
        @endforeach
    </div>
</div></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">
    {{-- 회원 등급 분포 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 16px;font-size:16px">회원 등급 분포</h2>
        @php $gmax = max(1, collect(\App\Models\User::GRADES)->keys()->map(fn($k)=>$grades[$k]??0)->max()); @endphp
        @foreach(\App\Models\User::GRADES as $k=>$g)
            <div class="hbar-row"><span class="hbar-lbl" style="color:{{ $g[3] }}">{{ $g[0] }}</span>
                <div class="hbar"><div style="width:{{ round(($grades[$k]??0)/$gmax*100) }}%;background:{{ $g[3] }}"></div></div>
                <span class="hbar-num">{{ $grades[$k]??0 }}</span></div>
        @endforeach
    </div></div>

    {{-- 감정 결과 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 16px;font-size:16px">감정 결과 분포</h2>
        @php $amap=['authentic'=>['정품','#12b76a'],'fake'=>['가품','#ff2d55'],'uncertain'=>['보류','#f79009'],'pending'=>['대기','#9ca3af']]; $amax=max(1,$appraisal->max()); @endphp
        @foreach($amap as $k=>$m)
            <div class="hbar-row"><span class="hbar-lbl">{{ $m[0] }}</span>
                <div class="hbar"><div style="width:{{ round(($appraisal[$k]??0)/$amax*100) }}%;background:{{ $m[1] }}"></div></div>
                <span class="hbar-num">{{ $appraisal[$k]??0 }}</span></div>
        @endforeach
    </div></div>

    {{-- 브랜드 TOP --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 16px;font-size:16px">브랜드 TOP 8 (상품 수)</h2>
        @php $bmax=max(1,$topBrands->max('c')); @endphp
        @foreach($topBrands as $b)
            <div class="hbar-row"><span class="hbar-lbl" style="width:110px">{{ Str::limit($b->brand,12) }}</span>
                <div class="hbar"><div style="width:{{ round($b->c/$bmax*100) }}%;background:#111"></div></div>
                <span class="hbar-num">{{ $b->c }}</span></div>
        @endforeach
    </div></div>

    {{-- 재고 이동 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 16px;font-size:16px">재고 이동 현황</h2>
        @php $tmap=\App\Models\StockTransfer::STATUSES; @endphp
        @forelse($transfers as $st=>$c)
            <div class="hbar-row"><span class="hbar-lbl">{{ $tmap[$st][0] ?? $st }}</span>
                <div class="hbar"><div style="width:{{ round($c/max(1,$transfers->max())*100) }}%;background:#b48a4a"></div></div>
                <span class="hbar-num">{{ $c }}</span></div>
        @empty
            <p style="color:var(--p-muted)">이동 내역이 없습니다.</p>
        @endforelse
    </div></div>
</div>
<style>
    .bars{display:flex;align-items:flex-end;gap:14px;height:180px;padding-top:10px}
    .bar-col{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%}
    .bar{width:70%;max-width:52px;background:linear-gradient(180deg,#111,#444);border-radius:6px 6px 0 0}
    .bar-val{font-size:11px;color:var(--p-muted);margin-bottom:5px} .bar-lbl{font-size:12px;color:var(--p-muted);margin-top:8px}
    .hbar-row{display:flex;align-items:center;gap:12px;margin-bottom:11px}
    .hbar-lbl{width:70px;font-size:13px;font-weight:600;flex:none} .hbar-num{font-size:13px;font-weight:700;width:40px;text-align:right}
    .hbar{flex:1;height:12px;background:#f0f0f2;border-radius:6px;overflow:hidden} .hbar>div{height:100%;border-radius:6px;min-width:2px}
</style>
@endsection
