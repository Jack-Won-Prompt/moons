@extends('layouts.storefront')
@section('title', $notice->title . ' · MOONS')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="crumb" style="margin-top:22px"><a href="{{ route('content.notices') }}">공지사항</a> / <span>{{ Str::limit($notice->title,20) }}</span></div>
    <div class="panel-card">
        <div style="display:flex;gap:10px;align-items:center;margin-bottom:8px">
            <span class="pill pill--{{ $notice->category==='event'?'amber':'gray' }}">{{ $notice->category==='event'?'이벤트':'공지' }}</span>
            <span style="font-size:13px;color:var(--muted)">{{ $notice->created_at->format('Y.m.d') }}</span>
        </div>
        <h1 style="font-size:24px;margin:0 0 20px">{{ $notice->title }}</h1>
        <div style="font-size:15px;line-height:1.9;color:var(--ink-soft)">{!! nl2br(e($notice->body)) !!}</div>
    </div>
    <a class="btn" href="{{ route('content.notices') }}">← 목록으로</a>
</div>
@endsection
