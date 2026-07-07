<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>파트너 로그인 · MOONS</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth" style="background:linear-gradient(135deg,#3a2a12,#1c1810)">
    <div class="auth__card">
        <div style="text-align:center"><span class="auth__role auth__role--partner">PARTNER · 파트너</span></div>
        <div class="auth__logo">MOO<b>N</b>S</div>
        <p class="auth__tag">입점 파트너 센터에 로그인합니다</p>

        @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif
        @if(session('status'))<div class="alert alert--ok">{{ session('status') }}</div>@endif

        <form action="{{ route('partner.login') }}" method="POST">
            @csrf
            <div class="field">
                <label>파트너 이메일</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="partner@moons.com" required autofocus>
            </div>
            <div class="field">
                <label>비밀번호</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-row">
                <label><input type="checkbox" name="remember"> 로그인 유지</label>
            </div>
            <button type="submit" class="btn btn--primary btn--block">파트너 로그인</button>
        </form>

        <p class="auth__alt">아직 입점 파트너가 아니신가요? <a href="{{ route('partner.register') }}">입점 신청</a></p>

        <div style="margin-top:18px;padding:12px 14px;background:var(--bg-alt);border-radius:11px;font-size:12px;color:var(--muted);text-align:center">
            데모 계정 — partner@moons.com / password
        </div>

        <div class="auth__switch">
            <a href="{{ route('home') }}">스토어</a>
            <a href="{{ route('login') }}">고객 로그인</a>
            <a href="{{ route('admin.login') }}">관리자 로그인</a>
        </div>
    </div>
</div>
</body>
</html>
