<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '관리자') · MOONS Admin</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/panel.css') }}">
</head>
<body>
@php $admin = auth('admin')->user(); $r = Route::currentRouteName(); @endphp
<div class="shell">
    <aside class="side">
        <div class="side__brand">MOO<b>N</b>S <span class="side__role side__role--admin">ADMIN</span></div>
        <nav class="side__nav">
            <div class="grouplabel">운영</div>
            <a class="side__link {{ $r==='admin.dashboard' ? 'on' : '' }}" href="{{ route('admin.dashboard') }}"><span class="i">📊</span>대시보드</a>
            <a class="side__link {{ str_starts_with($r,'admin.products') ? 'on' : '' }}" href="{{ route('admin.products.index') }}"><span class="i">📦</span>상품 관리</a>
            <a class="side__link {{ str_starts_with($r,'admin.partners') ? 'on' : '' }}" href="{{ route('admin.partners.index') }}"><span class="i">🤝</span>파트너 관리</a>
            <div class="grouplabel">바로가기</div>
            <a class="side__link" href="{{ route('home') }}" target="_blank"><span class="i">🛍️</span>쇼핑몰 보기</a>
        </nav>
        <div class="side__foot">
            <form action="{{ route('admin.logout') }}" method="POST">@csrf
                <button class="side__logout" type="submit">로그아웃</button>
            </form>
            <a href="{{ route('home') }}" class="side__home">← 스토어로 돌아가기</a>
        </div>
    </aside>

    <main class="main">
        <div class="topline">
            <div>
                <h1>@yield('title', '관리자')</h1>
                <div class="sub">@yield('subtitle', '')</div>
            </div>
            <div class="whoami">👤 <b>{{ $admin->name }}</b> · {{ $admin->email }}</div>
        </div>

        @if(session('status'))<div class="palert palert--ok">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="palert palert--err">{{ $errors->first() }}</div>@endif

        @yield('content')
    </main>
</div>
</body>
</html>
