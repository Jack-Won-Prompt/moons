<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입 · MOONS</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth">
    <div class="auth__card">
        <div style="text-align:center"><span class="auth__role auth__role--customer">CUSTOMER · 고객</span></div>
        <a href="{{ route('home') }}" class="auth__logo">MOO<b>N</b>S</a>
        <p class="auth__tag">지금 가입하고 다양한 혜택을 받아보세요</p>

        @if($errors->any())
            <div class="alert alert--err">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="field">
                <label>이름</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="홍길동" required autofocus>
            </div>
            <div class="field">
                <label>이메일</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
            </div>
            <div class="field">
                <label>비밀번호</label>
                <input type="password" name="password" placeholder="6자 이상" required>
            </div>
            <div class="field">
                <label>비밀번호 확인</label>
                <input type="password" name="password_confirmation" placeholder="비밀번호 재입력" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block">회원가입</button>
        </form>

        <p class="auth__alt">이미 계정이 있으신가요? <a href="{{ route('login') }}">로그인</a></p>
    </div>
</div>
</body>
</html>
