@extends('layouts.storefront')
@section('title', '감정서 · DPP 조회 · MOONS')

@section('content')
<div class="wrap" style="max-width:560px">
    <div class="listing__head" style="margin-top:40px;justify-content:center"><h1>🎖️ 감정서 · 정품 검증</h1></div>
    <p style="text-align:center;color:var(--muted);margin:-8px 0 26px">감정서 번호 또는 QR 코드로 정품 감정 결과와<br>상품 생애 이력(DPP)을 확인하세요.</p>

    <form action="{{ route('verify.index') }}" method="GET" class="panel-card" style="text-align:center">
        <div class="field" style="text-align:left">
            <label>감정서 번호</label>
            <input type="text" name="code" placeholder="MOONS-2026-XXXXXX" required autofocus style="text-align:center;font-size:16px;letter-spacing:.05em">
        </div>
        <button class="btn btn--primary btn--block" type="submit">조회하기</button>
    </form>
</div>
@endsection
