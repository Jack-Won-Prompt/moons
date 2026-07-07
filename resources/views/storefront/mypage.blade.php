@extends('layouts.storefront')
@section('title', '마이페이지 · MOONS')

@section('content')
<div class="wrap">
    <div class="listing__head" style="margin-top:28px"><h1>마이페이지</h1></div>
    <div class="pd__meta" style="max-width:520px">
        <dl>
            <dt>이름</dt><dd>{{ auth('web')->user()->name }}</dd>
            <dt>이메일</dt><dd>{{ auth('web')->user()->email }}</dd>
            <dt>가입일</dt><dd>{{ auth('web')->user()->created_at?->format('Y.m.d') }}</dd>
        </dl>
    </div>
    <div style="margin-top:24px;display:flex;gap:12px">
        <a href="{{ route('catalog.all') }}" class="btn btn--primary">쇼핑 계속하기</a>
        <form action="{{ route('logout') }}" method="POST">@csrf<button class="btn" type="submit">로그아웃</button></form>
    </div>
</div>
@endsection
