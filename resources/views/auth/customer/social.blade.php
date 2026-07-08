<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $provider_name }} 로그인 · MOONS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
@php $bg = ['kakao'=>'#FEE500','naver'=>'#03C75A','google'=>'#fff'][$provider] ?? '#eee';
     $fg = ['kakao'=>'#191600','naver'=>'#fff','google'=>'#111'][$provider] ?? '#111'; @endphp
<div class="auth">
    <div class="auth__card" style="text-align:center">
        <div class="auth__logo">MOO<b>N</b>S</div>
        <div style="width:64px;height:64px;border-radius:16px;margin:18px auto 12px;display:grid;place-items:center;font-size:28px;font-weight:800;background:{{ $bg }};color:{{ $fg }};border:1px solid var(--line)">
            {{ ['kakao'=>'K','naver'=>'N','google'=>'G'][$provider] ?? '?' }}</div>
        <h2 style="margin:0 0 4px">{{ $provider_name }} 계정으로 로그인</h2>
        <p class="auth__tag">{{ $provider_name }}에 로그인되어 있는 계정으로<br>MOONS 이용에 동의하고 계속합니다. (데모)</p>

        <form action="{{ route('social.callback', $provider) }}" method="POST" style="margin-top:8px">@csrf
            <div class="field" style="text-align:left"><label>이름 (데모용)</label>
                <input type="text" name="name" value="{{ $provider_name }} 데모회원" required></div>
            <button type="submit" class="btn btn--primary btn--block" style="background:{{ $bg }};color:{{ $fg }};border-color:{{ $bg }}">
                {{ $provider_name }} 계정으로 계속하기</button>
        </form>
        <p class="auth__alt"><a href="{{ route('login') }}">← 다른 방법으로 로그인</a></p>
        <div style="margin-top:14px;padding:10px 12px;background:var(--bg-alt);border-radius:10px;font-size:11px;color:var(--muted)">
            실제 서비스에서는 {{ $provider_name }} OAuth(Socialite)로 연결됩니다. 현재는 데모 시뮬레이션입니다.</div>
    </div>
</div>
</body>
</html>
