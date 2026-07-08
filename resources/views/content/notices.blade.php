@extends('layouts.storefront')
@section('title', '공지사항 · MOONS')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="listing__head" style="margin-top:28px"><h1>공지사항</h1></div>
    <div style="border-top:2px solid var(--ink)">
        @forelse($notices as $n)
            <a href="{{ route('content.notice', $n) }}" style="display:flex;gap:14px;align-items:center;padding:18px 6px;border-bottom:1px solid var(--line)">
                <span class="pill pill--{{ $n->category==='event'?'amber':'gray' }}">{{ $n->category==='event'?'이벤트':'공지' }}</span>
                <span style="flex:1;font-weight:600">@if($n->is_pinned)📌 @endif{{ $n->title }}</span>
                <span style="font-size:13px;color:var(--muted)">{{ $n->created_at->format('Y.m.d') }}</span>
            </a>
        @empty
            <div class="empty"><div class="big">📢</div><p>등록된 공지가 없습니다.</p></div>
        @endforelse
    </div>
    <div class="pagination-wrap">{{ $notices->links() }}</div>
</div>
@endsection
