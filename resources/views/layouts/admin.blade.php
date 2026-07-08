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
            <a class="side__link {{ str_starts_with($r,'admin.partners') ? 'on' : '' }}" href="{{ route('admin.partners.index') }}"><span class="i">🤝</span>지점(파트너) 관리</a>
            <div class="grouplabel">감정 · 거래</div>
            <a class="side__link {{ str_starts_with($r,'admin.sell') ? 'on' : '' }}" href="{{ route('admin.sell.index') }}"><span class="i">🔍</span>감정 · 접수 관리</a>
            <a class="side__link {{ str_starts_with($r,'admin.certificates') ? 'on' : '' }}" href="{{ route('admin.certificates.index') }}"><span class="i">🎖️</span>블록체인 감정서</a>
            <div class="grouplabel">주문 · 상담</div>
            <a class="side__link {{ str_starts_with($r,'admin.orders') ? 'on' : '' }}" href="{{ route('admin.orders.index') }}"><span class="i">🧾</span>주문 관리</a>
            <a class="side__link {{ str_starts_with($r,'admin.settlements') ? 'on' : '' }}" href="{{ route('admin.settlements.index') }}"><span class="i">💵</span>정산 관리</a>
            <a class="side__link {{ str_starts_with($r,'admin.chat') ? 'on' : '' }}" href="{{ route('admin.chat.index') }}"><span class="i">💬</span>상담 모니터링</a>
            <a class="side__link {{ str_starts_with($r,'admin.notifications') ? 'on' : '' }}" href="{{ route('admin.notifications.index') }}"><span class="i">🔔</span>알림 <span class="side-badge" id="notiBadge" style="display:none"></span></a>
            <div class="grouplabel">운영 · 마케팅</div>
            <a class="side__link {{ str_starts_with($r,'admin.inventory') ? 'on' : '' }}" href="{{ route('admin.inventory.index') }}"><span class="i">📦</span>멀티지점 재고</a>
            <a class="side__link {{ str_starts_with($r,'admin.membership') ? 'on' : '' }}" href="{{ route('admin.membership.index') }}"><span class="i">⭐</span>멤버십 · 쿠폰</a>
            <a class="side__link {{ str_starts_with($r,'admin.content') ? 'on' : '' }}" href="{{ route('admin.content.index') }}"><span class="i">🖼️</span>콘텐츠 관리</a>
            <a class="side__link {{ str_starts_with($r,'admin.stats') ? 'on' : '' }}" href="{{ route('admin.stats.index') }}"><span class="i">📈</span>통계</a>
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
<style>.side-badge{background:#ff2d55;color:#fff;font-size:10px;font-weight:800;border-radius:999px;padding:1px 7px;margin-left:auto}</style>
<script>
(function(){
    var badge=document.getElementById('notiBadge');
    function refresh(){ fetch("{{ route('admin.notifications.unread') }}",{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(function(d){
        if(d.count>0){ badge.textContent=d.count>99?'99+':d.count; badge.style.display=''; } else badge.style.display='none'; }).catch(function(){}); }
    refresh(); setInterval(refresh,15000);
})();
</script>
</body>
</html>
