@extends('layouts.storefront')
@section('title', '포토 후기 · MOONS')

@section('content')
<div class="wrap">
    <div class="listing__head" style="margin-top:28px"><h1>📸 포토 후기</h1><span class="count">{{ $reviews->total() }}개</span></div>
    @if($reviews->count())
        <div class="review-grid">
            @foreach($reviews as $r)
                <a href="{{ $r->product ? route('catalog.product', $r->product) : '#' }}" class="review-card">
                    <div class="review-photo"><img src="{{ \Illuminate\Support\Str::startsWith($r->photos[0],'http') ? $r->photos[0] : asset($r->photos[0]) }}" alt=""></div>
                    <div class="review-body">
                        <div class="stars">{!! str_repeat('★', $r->rating) . str_repeat('☆', 5-$r->rating) !!}</div>
                        <div class="review-brand">{{ $r->product?->brand }}</div>
                        <p>{{ \Illuminate\Support\Str::limit($r->body, 50) }}</p>
                        <div class="review-user">{{ \Illuminate\Support\Str::mask($r->user?->name ?? '고객', '*', 1) }} · {{ $r->created_at->format('Y.m.d') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $reviews->links() }}</div>
    @else
        <div class="empty"><div class="big">📸</div><p>등록된 포토 후기가 없습니다.</p></div>
    @endif
</div>
<style>
    .review-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
    .review-card{background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden}
    .review-photo{aspect-ratio:1;background:var(--bg-alt)} .review-photo img{width:100%;height:100%;object-fit:cover}
    .review-body{padding:14px} .stars{color:var(--gold);font-size:14px;letter-spacing:2px}
    .review-brand{font-weight:800;font-size:14px;margin:5px 0 3px} .review-body p{font-size:13px;color:var(--ink-soft);margin:0 0 8px;min-height:34px}
    .review-user{font-size:12px;color:var(--muted)}
    @media(max-width:900px){.review-grid{grid-template-columns:repeat(2,1fr)}}
</style>
@endsection
