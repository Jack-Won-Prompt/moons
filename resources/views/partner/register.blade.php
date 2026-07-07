<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>입점 신청 · MOONS Partner</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>
<div class="auth" style="background:linear-gradient(135deg,#3a2a12,#1c1810)">
    <div class="auth__card" style="max-width:460px">
        <div style="text-align:center"><span class="auth__role auth__role--partner">PARTNER · 입점신청</span></div>
        <div class="auth__logo">MOO<b>N</b>S</div>
        <p class="auth__tag">MOONS와 함께 성장할 파트너를 모집합니다</p>

        @if($errors->any())<div class="alert alert--err">{{ $errors->first() }}</div>@endif

        <form action="{{ route('partner.register') }}" method="POST">
            @csrf
            <div class="field">
                <label>상호(회사명) *</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="(주)럭셔리컴퍼니" required>
            </div>
            <div class="field">
                <label>담당자명 *</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="홍길동" required>
            </div>
            <div class="field">
                <label>이메일 *</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="partner@company.com" required>
            </div>
            <div class="field">
                <label>연락처</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="02-0000-0000">
            </div>
            <div class="field">
                <label>대표 취급 브랜드</label>
                <input type="text" name="brand" value="{{ old('brand') }}" placeholder="GUCCI, PRADA ...">
            </div>
            <div class="field">
                <label>비밀번호 *</label>
                <input type="password" name="password" placeholder="6자 이상" required>
            </div>
            <div class="field">
                <label>비밀번호 확인 *</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn--primary btn--block">입점 신청하기</button>
        </form>

        <p class="auth__alt">이미 파트너이신가요? <a href="{{ route('partner.login') }}">로그인</a></p>
    </div>
</div>
</body>
</html>
