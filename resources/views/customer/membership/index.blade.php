@extends('layouts.storefront')
@section('title', '멤버십 · MOONS')

@php $gi = $user->gradeInfo(); @endphp

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="listing__head" style="margin-top:28px"><h1>멤버십</h1></div>

    {{-- 등급 카드 --}}
    <div class="panel-card" style="background:linear-gradient(135deg,{{ $gi[3] }}22,#fff);border-color:{{ $gi[3] }}55">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px">
            <div>
                <div style="font-size:12px;color:var(--muted)">{{ $user->name }}님의 등급</div>
                <div style="font-size:30px;font-weight:800;color:{{ $gi[3] }}">{{ $gi[0] }}</div>
                <div style="font-size:13px;color:var(--muted)">적립률 {{ $gi[2]*100 }}% · 누적구매 {{ number_format($user->total_spent) }}원</div>
            </div>
            <div style="text-align:right">
                <div style="font-size:12px;color:var(--muted)">보유 포인트</div>
                <div style="font-size:30px;font-weight:800">{{ number_format($user->points) }}<small style="font-size:14px">P</small></div>
            </div>
        </div>
        {{-- 등급 진행 --}}
        <div style="display:flex;gap:6px;margin-top:18px">
            @foreach(\App\Models\User::GRADES as $k=>$g)
                <div style="flex:1;text-align:center">
                    <div style="height:6px;border-radius:3px;background:{{ $user->total_spent >= $g[1] ? $g[3] : '#eee' }}"></div>
                    <div style="font-size:11px;margin-top:5px;color:{{ $user->grade===$k ? 'var(--ink)' : 'var(--muted)' }};font-weight:{{ $user->grade===$k?'800':'500' }}">{{ $g[0] }}</div>
                    <div style="font-size:10px;color:var(--muted)">{{ $g[1]>0 ? number_format($g[1]/10000).'만' : '0' }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 받을 수 있는 쿠폰 --}}
    @if($claimable->count())
    <div class="panel-card">
        <h3>🎟️ 받을 수 있는 쿠폰</h3>
        @foreach($claimable as $c)
            <div class="coupon-row">
                <div><b>{{ $c->name }}</b> <span class="pill pill--gray">{{ $c->label }}</span>
                    <div style="font-size:12px;color:var(--muted)">{{ $c->min_order>0 ? number_format($c->min_order).'원 이상' : '금액 무관' }}
                        @if($c->expires_at) · ~{{ $c->expires_at->format('Y.m.d') }}@endif</div></div>
                <form action="{{ route('membership.claim', $c) }}" method="POST">@csrf<button class="btn" style="padding:8px 16px">받기</button></form>
            </div>
        @endforeach
    </div>
    @endif

    {{-- 보유 쿠폰 --}}
    <div class="panel-card">
        <h3>보유 쿠폰</h3>
        @forelse($user->coupons->filter(fn($uc)=>$uc->coupon) as $uc)
            <div class="coupon-row {{ $uc->used_at ? 'used' : '' }}">
                <div><b>{{ $uc->coupon->name }}</b> <span class="pill pill--{{ $uc->used_at?'gray':'green' }}">{{ $uc->coupon->label }}</span>
                    <div style="font-size:12px;color:var(--muted)">{{ $uc->used_at ? '사용완료 '.$uc->used_at->format('Y.m.d') : '사용 가능' }}</div></div>
            </div>
        @empty
            <p style="color:var(--muted)">보유한 쿠폰이 없습니다.</p>
        @endforelse
    </div>

    {{-- 포인트 내역 --}}
    <div class="panel-card">
        <h3>포인트 내역</h3>
        @forelse($user->pointTransactions->take(15) as $pt)
            <div style="display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid var(--line-soft);font-size:14px">
                <span>{{ $pt->reason }}<div style="font-size:11px;color:var(--muted)">{{ $pt->created_at->format('Y.m.d H:i') }}</div></span>
                <span style="font-weight:800;color:{{ $pt->amount>0?'#12b76a':'#ff2d55' }}">{{ $pt->amount>0?'+':'' }}{{ number_format($pt->amount) }}P</span>
            </div>
        @empty
            <p style="color:var(--muted)">포인트 내역이 없습니다.</p>
        @endforelse
    </div>
</div>
<style>.coupon-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid var(--line-soft)}.coupon-row.used{opacity:.5}</style>
@endsection
