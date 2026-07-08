@extends('layouts.storefront')
@section('title', '감정서 ' . $code . ' · MOONS')

@section('content')
<div class="wrap" style="max-width:760px">
    <div class="crumb" style="margin-top:22px"><a href="{{ route('verify.index') }}">감정서 조회</a> / <span>{{ $code }}</span></div>

    @if(! $certificate)
        <div class="panel-card" style="text-align:center;padding:60px 24px">
            <div style="font-size:44px">❌</div>
            <h3 style="margin:12px 0 6px">감정서를 찾을 수 없습니다</h3>
            <p style="color:var(--muted)">번호 <b>{{ $code }}</b> 에 해당하는 감정서가 없습니다. 번호를 다시 확인해 주세요.</p>
        </div>
    @else
        @php $valid = $certificate->isValid(); @endphp

        {{-- 감정서 헤더 --}}
        <div class="panel-card" style="background:linear-gradient(135deg,#faf6ee,#fff);border-color:var(--gold)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:20px;flex-wrap:wrap">
                <div style="flex:1;min-width:220px">
                    <div style="font-size:12px;letter-spacing:.14em;color:var(--gold);font-weight:700">MOONS CERTIFICATE OF AUTHENTICITY</div>
                    <h1 style="margin:8px 0 4px;font-size:26px">{{ $certificate->brand }}</h1>
                    <div style="color:var(--ink-soft)">{{ $certificate->model }}</div>
                    <div style="margin-top:14px">
                        <span class="pill pill--{{ $certificate->result==='authentic'?'green':($certificate->result==='fake'?'red':'amber') }}" style="font-size:14px;padding:8px 16px">
                            {{ $certificate->result==='authentic' ? '✅ 정품 인증' : $certificate->result_label }}
                        </span>
                    </div>
                </div>
                <div style="text-align:center">
                    <img src="{{ route('verify.qr', $certificate->code) }}" alt="QR" width="130" height="130"
                         style="border:1px solid var(--line);border-radius:10px;background:#fff;padding:6px">
                    <div style="font-size:11px;color:var(--muted);margin-top:6px">스캔하여 검증</div>
                </div>
            </div>
        </div>

        {{-- 검증 상태 --}}
        <div class="alert {{ $valid ? 'alert--ok' : 'alert--err' }}" style="display:flex;align-items:center;gap:10px">
            <span style="font-size:20px">{{ $valid ? '🔗' : '⚠️' }}</span>
            <div>
                <b>{{ $valid ? '블록체인 검증 통과' : '위·변조 감지: 해시 불일치' }}</b>
                <div style="font-size:12px;word-break:break-all;opacity:.85;font-family:monospace">{{ $certificate->blockchain_hash }}</div>
            </div>
        </div>

        {{-- 감정 정보 --}}
        <div class="panel-card">
            <h3>감정 정보</h3>
            <dl class="dl-grid">
                <dt>감정서 번호</dt><dd>{{ $certificate->code }}</dd>
                <dt>카테고리</dt><dd>{{ $certificate->category ?? '-' }}</dd>
                <dt>감정사</dt><dd>{{ $certificate->appraiser }}</dd>
                <dt>발급처</dt><dd>{{ $certificate->issuer }}</dd>
                <dt>발급일</dt><dd>{{ optional($certificate->issued_at)->format('Y.m.d H:i') }}</dd>
            </dl>
        </div>

        {{-- Digital Product Passport --}}
        <div class="panel-card">
            <h3>📘 Digital Product Passport <small style="color:var(--muted);font-weight:500">· 상품 생애 이력</small></h3>
            <ul class="dpp">
                @foreach($certificate->dpp ?? [] as $e)
                    @php $labels=['appraisal'=>'감정 이력','ownership'=>'소유권 변경','trade'=>'거래 이력','repair'=>'수리 이력','storage'=>'보관 이력']; @endphp
                    <li>
                        <span class="marker"></span>
                        <span class="t">{{ $labels[$e['type']] ?? $e['type'] }}</span> — {{ $e['note'] }}
                        <div class="m">{{ $e['at'] }} · {{ $e['by'] }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
