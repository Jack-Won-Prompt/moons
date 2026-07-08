@extends('layouts.storefront')
@section('title', '이메일 인증 · MOONS')

@section('content')
<div class="wrap" style="max-width:520px">
    <div class="panel-card" style="margin-top:40px;text-align:center">
        <div style="font-size:44px">📧</div>
        <h1 style="font-size:22px;margin:10px 0 6px">이메일 인증</h1>
        <p style="color:var(--muted)">가입하신 이메일로 인증 링크를 발송했습니다.<br>메일함에서 인증을 완료해 주세요.</p>

        @if(session('status'))<div class="alert alert--ok">{{ session('status') }}</div>@endif

        @php $demoLink = session('verify_link') ?? ($link ?? null); @endphp
        @if($demoLink)
            <div style="margin:18px 0;padding:14px;background:var(--bg-alt);border-radius:11px;text-align:left">
                <div style="font-size:12px;color:var(--muted);margin-bottom:8px">🔧 데모: 메일 발송 대신 아래 링크로 바로 인증</div>
                <a href="{{ $demoLink }}" class="btn btn--primary btn--block">이메일 인증 완료하기</a>
            </div>
        @endif

        <form action="{{ route('verification.send') }}" method="POST" style="margin-top:12px">@csrf
            <button class="btn btn--block" type="submit">인증 메일 재발송</button>
        </form>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">@csrf
            <button class="btn btn--block" type="submit" style="border:0;background:none;color:var(--muted)">로그아웃</button>
        </form>
    </div>
</div>
@endsection
