@extends('layouts.storefront')
@section('title', '판매 진행현황 · MOONS')

@section('content')
<div class="wrap" style="max-width:960px">
    <div class="listing__head" style="margin-top:28px">
        <h1>판매 진행현황</h1>
        <a class="btn btn--primary" href="{{ route('sell.create') }}" style="margin-left:auto;padding:10px 18px">+ 판매하기</a>
    </div>

    @forelse($requests as $sr)
        <a href="{{ route('sell.show', $sr) }}" class="sr-row">
            <div class="sr-thumb">
                @if($sr->photos && count($sr->photos))<img src="{{ asset($sr->photos[0]) }}" alt="">@else<span>📦</span>@endif
            </div>
            <div class="sr-main">
                <div class="sr-code">{{ $sr->code }} · {{ $sr->method === 'auction' ? '경매' : '일반견적' }} · {{ $sr->target_label }}</div>
                <div class="sr-title">{{ $sr->brand }} · {{ $sr->title }}</div>
                <div class="sr-sub">{{ $sr->created_at->format('Y.m.d') }} 접수
                    @if($sr->quote_price) · 견적 <b>{{ number_format($sr->quote_price) }}원</b>@endif
                    @if($sr->certificate) · 감정서 {{ $sr->certificate->code }}@endif
                </div>
            </div>
            <span class="pill pill--{{ $sr->status_color }}">{{ $sr->status_label }}</span>
        </a>
    @empty
        <div class="empty"><div class="big">💰</div><p>아직 판매 접수 내역이 없습니다.</p>
            <a class="btn btn--primary" href="{{ route('sell.create') }}" style="display:inline-block;margin-top:14px">첫 상품 판매하기</a></div>
    @endforelse

    <div class="pagination-wrap">{{ $requests->links() }}</div>
</div>

<style>
    .sr-row { display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:12px;transition:box-shadow .15s }
    .sr-row:hover { box-shadow:var(--shadow) }
    .sr-thumb { width:64px;height:64px;border-radius:10px;background:var(--bg-alt);display:grid;place-items:center;overflow:hidden;flex:none;font-size:26px }
    .sr-thumb img { width:100%;height:100%;object-fit:cover }
    .sr-code { font-size:12px;color:var(--muted) }
    .sr-title { font-weight:700;margin:2px 0 3px }
    .sr-sub { font-size:13px;color:var(--ink-soft) }
    .pill { font-size:12px;font-weight:700;padding:5px 11px;border-radius:999px;white-space:nowrap }
    .pill--green{background:#e7f8ef;color:#12b76a}.pill--amber{background:#fef3e6;color:#f79009}
    .pill--red{background:#fdecef;color:#ff2d55}.pill--gray{background:#eef0f3;color:#6b7080}
</style>
@endsection
