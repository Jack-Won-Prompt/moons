@extends('layouts.admin')
@section('title', '콘텐츠 관리')
@section('subtitle', '배너 · 기획전 · 공지 · FAQ')

@php $del = fn($type,$id) => '<form action="'.route('admin.content.destroy',[$type,$id]).'" method="POST" style="display:inline" onsubmit="return confirm(\'삭제?\')">'.csrf_field().method_field('DELETE').'<button class="pbtn pbtn--danger pbtn--sm">삭제</button></form>'; @endphp

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">
    {{-- 배너 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 12px;font-size:16px">🖼️ 히어로 배너</h2>
        <form action="{{ route('admin.content.banners.store') }}" method="POST" style="margin-bottom:14px">@csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <input name="eyebrow" placeholder="EYEBROW" class="pfin"><input name="title" placeholder="제목*" required class="pfin">
                <input name="subtitle" placeholder="부제" class="pfin"><input name="gradient" placeholder="#1a1a2e,#4b1248" class="pfin">
                <input name="link" placeholder="링크(/category/bags)" class="pfin"><select name="position" class="pfin"><option value="hero">히어로</option><option value="strip">스트립</option></select>
            </div>
            <button class="pbtn pbtn--primary pbtn--sm" style="margin-top:10px">배너 추가</button>
        </form>
        @foreach($banners as $b)
            <div class="crow"><span><b>{{ $b->title }}</b> <span class="pill pill--gray">{{ $b->position }}</span></span>{!! $del('banner',$b->id) !!}</div>
        @endforeach
    </div></div>

    {{-- 기획전 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 12px;font-size:16px">🎯 기획전</h2>
        <form action="{{ route('admin.content.promotions.store') }}" method="POST" style="margin-bottom:14px">@csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <input name="title" placeholder="제목*" required class="pfin"><input name="subtitle" placeholder="부제" class="pfin">
                <input name="brand" placeholder="브랜드 필터(예: GUCCI)" class="pfin"><input name="min_discount" type="number" placeholder="최소 할인율%" class="pfin">
                <input name="gradient" placeholder="#603813,#b29f94" class="pfin">
            </div>
            <button class="pbtn pbtn--primary pbtn--sm" style="margin-top:10px">기획전 추가</button>
        </form>
        @foreach($promotions as $p)
            <div class="crow"><span><b>{{ $p->title }}</b> <a href="{{ route('content.promotion',$p) }}" target="_blank" style="font-size:12px;color:var(--p-gold)">보기</a></span>{!! $del('promotion',$p->id) !!}</div>
        @endforeach
    </div></div>

    {{-- 공지 --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 12px;font-size:16px">📢 공지사항</h2>
        <form action="{{ route('admin.content.notices.store') }}" method="POST" style="margin-bottom:14px">@csrf
            <div style="display:flex;gap:10px;margin-bottom:8px"><select name="category" class="pfin"><option value="notice">공지</option><option value="event">이벤트</option></select>
                <input name="title" placeholder="제목*" required class="pfin" style="flex:1"></div>
            <textarea name="body" placeholder="내용" class="pfin" rows="2"></textarea>
            <label style="font-size:13px;display:flex;gap:6px;margin:8px 0"><input type="checkbox" name="is_pinned" value="1">상단 고정</label>
            <button class="pbtn pbtn--primary pbtn--sm">공지 등록</button>
        </form>
        @foreach($notices as $n)
            <div class="crow"><span>@if($n->is_pinned)📌 @endif{{ Str::limit($n->title,30) }}</span>{!! $del('notice',$n->id) !!}</div>
        @endforeach
    </div></div>

    {{-- FAQ --}}
    <div class="panel"><div class="panel__body">
        <h2 style="margin:0 0 12px;font-size:16px">❓ FAQ</h2>
        <form action="{{ route('admin.content.faqs.store') }}" method="POST" style="margin-bottom:14px">@csrf
            <div style="display:flex;gap:10px;margin-bottom:8px"><input name="category" placeholder="분류(예: 결제)" value="일반" class="pfin" style="width:120px">
                <input name="question" placeholder="질문*" required class="pfin" style="flex:1"></div>
            <textarea name="answer" placeholder="답변*" class="pfin" rows="2" required></textarea>
            <button class="pbtn pbtn--primary pbtn--sm" style="margin-top:8px">FAQ 등록</button>
        </form>
        @foreach($faqs as $f)
            <div class="crow"><span><span class="pill pill--gray">{{ $f->category }}</span> {{ Str::limit($f->question,28) }}</span>{!! $del('faq',$f->id) !!}</div>
        @endforeach
    </div></div>
</div>
<style>
    .pfin{width:100%;padding:9px 12px;border:1px solid var(--p-line);border-radius:9px;font-size:13px;font-family:inherit}
    .crow{display:flex;justify-content:space-between;align-items:center;padding:9px 0;border-bottom:1px solid var(--p-line);font-size:13px}
</style>
@endsection
