<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '파트너') · MOONS Partner</title>
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/panel.css') }}">
</head>
<body>
@php $partner = auth('partner')->user(); $r = Route::currentRouteName(); @endphp
<div class="shell">
    <aside class="side">
        <div class="side__brand">MOO<b>N</b>S <span class="side__role side__role--partner">PARTNER</span></div>
        <nav class="side__nav">
            <div class="grouplabel">파트너센터</div>
            <a class="side__link {{ $r==='partner.dashboard' ? 'on' : '' }}" href="{{ route('partner.dashboard') }}"><span class="i">📊</span>대시보드</a>
            <a class="side__link {{ str_starts_with($r,'partner.products') ? 'on' : '' }}" href="{{ route('partner.products.index') }}"><span class="i">📦</span>내 상품</a>
            <div class="grouplabel">감정 · 매입</div>
            <a class="side__link {{ str_starts_with($r,'partner.intakes') ? 'on' : '' }}" href="{{ route('partner.intakes.index') }}"><span class="i">🔍</span>판매 접수 · 감정</a>
            <a class="side__link {{ str_starts_with($r,'partner.inventory') ? 'on' : '' }}" href="{{ route('partner.inventory.index') }}"><span class="i">📦</span>재고 · 지점이동</a>
            <div class="grouplabel">상담 · 알림</div>
            <a class="side__link {{ str_starts_with($r,'partner.chat') ? 'on' : '' }}" href="{{ route('partner.chat.index') }}"><span class="i">💬</span>고객 상담</a>
            <a class="side__link {{ str_starts_with($r,'partner.notifications') ? 'on' : '' }}" href="{{ route('partner.notifications.index') }}"><span class="i">🔔</span>알림 <span class="side-badge" id="notiBadge" style="display:none"></span></a>
            <div class="grouplabel">바로가기</div>
            <a class="side__link" href="{{ route('home') }}" target="_blank"><span class="i">🛍️</span>쇼핑몰 보기</a>
        </nav>
        <div class="side__foot">
            <form action="{{ route('partner.logout') }}" method="POST">@csrf
                <button class="side__logout" type="submit">로그아웃</button>
            </form>
            <a href="{{ route('home') }}" class="side__home">← 스토어로 돌아가기</a>
        </div>
    </aside>

    <main class="main">
        <div class="topline">
            <div>
                <h1>@yield('title', '파트너')</h1>
                <div class="sub">@yield('subtitle', '')</div>
            </div>
            <div class="whoami">🏢 <b>{{ $partner->company_name }}</b> · {{ $partner->name }}</div>
        </div>

        @if(session('status'))<div class="palert palert--ok">{{ session('status') }}</div>@endif
        @if($errors->any())<div class="palert palert--err">{{ $errors->first() }}</div>@endif

        @yield('content')
    </main>
</div>
<style>.side-badge{background:#ff2d55;color:#fff;font-size:10px;font-weight:800;border-radius:999px;padding:1px 7px;margin-left:auto}</style>
<script>
(function(){
    var badge=document.getElementById('notiBadge');
    function refresh(){ fetch("{{ route('partner.notifications.unread') }}",{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(function(d){
        if(d.count>0){ badge.textContent=d.count>99?'99+':d.count; badge.style.display=''; } else badge.style.display='none'; }).catch(function(){}); }
    refresh(); setInterval(refresh,15000);
})();
</script>
</body>
</html>
