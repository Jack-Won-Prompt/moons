<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 로그인 · MOONS</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth" style="background:#0f1115">
    <div class="auth__card">
        <div style="text-align:center"><span class="auth__role auth__role--admin">ADMIN · 관리자</span></div>
        <div class="auth__logo">MOO<b>N</b>S</div>
        <p class="auth__tag">관리자 전용 콘솔에 로그인합니다</p>

        @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="field">
                <label>관리자 이메일</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@moons.com" required autofocus>
            </div>
            <div class="field">
                <label>비밀번호</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-row">
                <label><input type="checkbox" name="remember"> 로그인 유지</label>
            </div>
            <button type="submit" class="btn btn--primary btn--block">관리자 로그인</button>
        </form>

        <div style="margin-top:18px;padding:12px 14px;background:var(--bg-alt);border-radius:11px;font-size:12px;color:var(--muted);text-align:center">
            데모 계정 — admin@moons.com / password
        </div>

        <div class="auth__switch">
            <a href="{{ route('home') }}">스토어</a>
            <a href="{{ route('login') }}">고객 로그인</a>
            <a href="{{ route('partner.login') }}">파트너 로그인</a>
        </div>
    </div>
</div>
</body>
</html>
