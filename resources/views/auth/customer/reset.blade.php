<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 재설정 · MOONS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth">
    <div class="auth__card">
        <div class="auth__logo">MOO<b>N</b>S</div>
        <p class="auth__tag">새 비밀번호를 설정하세요</p>
        @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif
        <form action="{{ route('password.update') }}" method="POST">@csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field"><label>이메일</label><input type="email" name="email" value="{{ $email }}" required></div>
            <div class="field"><label>새 비밀번호</label><input type="password" name="password" placeholder="6자 이상" required></div>
            <div class="field"><label>비밀번호 확인</label><input type="password" name="password_confirmation" required></div>
            <button class="btn btn--primary btn--block" type="submit">비밀번호 변경</button>
        </form>
    </div>
</div>
</body>
</html>
