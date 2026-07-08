@extends('layouts.storefront')
@section('title', '자주 묻는 질문 · MOONS')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="listing__head" style="margin-top:28px"><h1>자주 묻는 질문</h1></div>
    @forelse($faqs as $category => $items)
        <div class="panel-card">
            <h3>{{ $category }}</h3>
            @foreach($items as $faq)
                <details class="faq">
                    <summary>Q. {{ $faq->question }}</summary>
                    <div class="faq-a">{!! nl2br(e($faq->answer)) !!}</div>
                </details>
            @endforeach
        </div>
    @empty
        <div class="empty"><div class="big">❓</div><p>등록된 FAQ가 없습니다.</p></div>
    @endforelse
</div>
<style>
    .faq{border-bottom:1px solid var(--line-soft);padding:4px 0}
    .faq summary{padding:14px 4px;font-weight:600;cursor:pointer;list-style:none;display:flex;justify-content:space-between}
    .faq summary::after{content:"+";color:var(--muted)} .faq[open] summary::after{content:"−"}
    .faq-a{padding:0 4px 16px;color:var(--ink-soft);font-size:14px;line-height:1.8}
</style>
@endsection
