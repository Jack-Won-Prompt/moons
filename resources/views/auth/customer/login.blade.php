<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 · MOONS</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth">
    <div class="auth__card">
        <div style="text-align:center"><span class="auth__role auth__role--customer">CUSTOMER · 고객</span></div>
        <a href="{{ route('home') }}" class="auth__logo">MOO<b>N</b>S</a>
        <p class="auth__tag">로그인하고 나만의 럭셔리를 만나보세요</p>

        @if($errors->any())
            <div class="alert alert--err">{{ $errors->first() }}</div>
        @endif
        @if(session('status'))
            <div class="alert alert--ok">{{ session('status') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="field">
                <label>이메일</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="customer@moons.com" required autofocus>
            </div>
            <div class="field">
                <label>비밀번호</label>
                <input type="password" name="password" placeholder="비밀번호" required>
            </div>
            <div class="form-row">
                <label><input type="checkbox" name="remember"> 로그인 상태 유지</label>
                <a href="{{ route('password.request') }}" style="color:var(--muted)">비밀번호 찾기</a>
            </div>
            <button type="submit" class="btn btn--primary btn--block">로그인</button>
        </form>

        <div class="sns-login">
            <div class="sns-sep"><span>SNS 계정으로 로그인</span></div>
            <div class="sns-btns">
                <a href="{{ route('social.redirect', 'kakao') }}" class="sns-btn" style="background:#FEE500;color:#191600">카카오</a>
                <a href="{{ route('social.redirect', 'naver') }}" class="sns-btn" style="background:#03C75A;color:#fff">네이버</a>
                <a href="{{ route('social.redirect', 'google') }}" class="sns-btn" style="background:#fff;color:#111;border:1px solid var(--line)">구글</a>
            </div>
        </div>

        <p class="auth__alt">아직 회원이 아니신가요? <a href="{{ route('register') }}">회원가입</a></p>
        <style>
            .sns-login{margin-top:22px}
            .sns-sep{display:flex;align-items:center;gap:12px;color:var(--muted);font-size:12px;margin-bottom:14px}
            .sns-sep::before,.sns-sep::after{content:"";flex:1;height:1px;background:var(--line)}
            .sns-btns{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px}
            .sns-btn{padding:12px 0;border-radius:11px;text-align:center;font-weight:600;font-size:14px}
        </style>

        <div style="margin-top:18px;padding:12px 14px;background:var(--bg-alt);border-radius:11px;font-size:12px;color:var(--muted);text-align:center">
            데모 계정 — customer@moons.com / password
        </div>

        <div class="auth__switch">
            <a href="{{ route('partner.login') }}">파트너 로그인</a>
            <a href="{{ route('admin.login') }}">관리자 로그인</a>
        </div>
    </div>
</div>
</body>
</html>
