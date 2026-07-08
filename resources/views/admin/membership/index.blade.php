@extends('layouts.admin')
@section('title', '멤버십 · 쿠폰')
@section('subtitle', '등급 정책 · 포인트 · 쿠폰 관리')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">👥 회원</div><div class="v">{{ number_format($stats['members']) }}</div></div>
    <div class="stat"><div class="k">⭐ 총 보유포인트</div><div class="v">{{ number_format($stats['points_total']) }}</div></div>
    <div class="stat"><div class="k">💰 누적 적립</div><div class="v">{{ number_format($stats['points_issued']) }}</div></div>
    <div class="stat"><div class="k">🎟️ 쿠폰</div><div class="v">{{ $stats['coupons'] }}</div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">
    {{-- 등급 정책 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 14px;font-size:16px">등급 정책 · 분포</h2>
        <table class="table" style="margin:0 -22px">
            <thead><tr><th>등급</th><th>기준(누적)</th><th>적립률</th><th>회원수</th></tr></thead>
            <tbody>
            @foreach(\App\Models\User::GRADES as $k=>$g)
                <tr>
                    <td><span class="pill" style="background:{{ $g[3] }}22;color:{{ $g[3] }}">{{ $g[0] }}</span></td>
                    <td>{{ number_format($g[1]) }}원</td>
                    <td>{{ $g[2]*100 }}%</td>
                    <td>{{ $gradeDist[$k]->c ?? 0 }}명</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div></div>

    {{-- 쿠폰 발급 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 14px;font-size:16px">쿠폰 생성 · 발급</h2>
        <form action="{{ route('admin.membership.coupons.store') }}" method="POST">@csrf
            <div class="pfield" style="margin-bottom:10px"><label>쿠폰명</label><input type="text" name="name" placeholder="신규가입 15만원 할인" required></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="pfield"><label>유형</label><select name="type"><option value="fixed">정액(원)</option><option value="percent">정률(%)</option></select></div>
                <div class="pfield"><label>값</label><input type="number" name="value" min="1" required></div>
                <div class="pfield"><label>최소주문(원)</label><input type="number" name="min_order" min="0" value="0"></div>
                <div class="pfield"><label>최대할인(원)</label><input type="number" name="max_discount" min="0" placeholder="정률 상한"></div>
            </div>
            <div class="pfield" style="margin:10px 0"><label>만료일</label><input type="date" name="expires_at"></div>
            <label style="display:flex;gap:8px;align-items:center;font-size:14px;margin-bottom:12px"><input type="checkbox" name="issue_all" value="1"> 생성 즉시 전 회원에게 발급 + 알림</label>
            <button class="pbtn pbtn--primary" type="submit">쿠폰 생성</button>
        </form>
    </div></div>
</div>

<div class="panel">
    <div class="panel__head"><h2>쿠폰 목록</h2></div>
    <table class="table">
        <thead><tr><th>코드</th><th>쿠폰명</th><th>혜택</th><th>최소주문</th><th>발급수</th><th>만료</th><th>상태</th><th style="text-align:right">관리</th></tr></thead>
        <tbody>
        @forelse($coupons as $c)
            <tr>
                <td style="font-family:monospace">{{ $c->code }}</td>
                <td><b>{{ $c->name }}</b></td>
                <td>{{ $c->label }}</td>
                <td>{{ $c->min_order>0 ? number_format($c->min_order).'원' : '-' }}</td>
                <td>{{ $c->user_coupons_count }}</td>
                <td>{{ $c->expires_at?->format('Y.m.d') ?? '무기한' }}</td>
                <td><span class="pill pill--{{ $c->is_active?'green':'gray' }}">{{ $c->is_active?'활성':'중지' }}</span></td>
                <td style="text-align:right"><form action="{{ route('admin.membership.coupons.toggle', $c) }}" method="POST" style="display:inline">@csrf<button class="pbtn pbtn--sm">{{ $c->is_active?'중지':'활성' }}</button></form></td>
            </tr>
        @empty
            <tr><td colspan="8" class="empty-row">쿠폰이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
