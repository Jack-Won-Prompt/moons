<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 찾기 · MOONS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth">
    <div class="auth__card">
        <div class="auth__logo">MOO<b>N</b>S</div>
        <p class="auth__tag">가입한 이메일로 재설정 링크를 보내드립니다</p>
        @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif
        @if(session('status'))<div class="alert alert--ok">{{ session('status') }}</div>@endif
        @if(session('reset_link'))
            <div style="margin-bottom:14px;padding:12px;background:var(--bg-alt);border-radius:10px;font-size:12px">
                🔧 데모 링크: <a href="{{ session('reset_link') }}" style="color:var(--ink);font-weight:600;word-break:break-all">{{ Str::limit(session('reset_link'),50) }}</a></div>
        @endif
        <form action="{{ route('password.email') }}" method="POST">@csrf
            <div class="field"><label>이메일</label><input type="email" name="email" value="{{ old('email') }}" required autofocus></div>
            <button class="btn btn--primary btn--block" type="submit">재설정 링크 받기</button>
        </form>
        <p class="auth__alt"><a href="{{ route('login') }}">← 로그인으로 돌아가기</a></p>
    </div>
</div>
</body>
</html>
