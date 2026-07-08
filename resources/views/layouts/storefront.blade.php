<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth('web')<meta name="wished-ids" content="{{ \App\Models\Wishlist::where('user_id', auth('web')->id())->pluck('product_id')->implode(',') }}">@endauth
    <title>@yield('title', 'MOONS · 럭셔리 셀렉트샵')</title>
    <meta name="description" content="MOONS — 전 세계 명품을 한 곳에서. 최신 트렌드의 럭셔리 셀렉트샵.">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" as="style" crossorigin
          href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body>

@php $me = auth('web')->user(); @endphp

<div class="topbar">
    <div class="wrap">
        @auth('web')
            <span>{{ $me->name }}님</span>
            <span class="sep">·</span>
            <a href="{{ route('orders.index') }}">주문조회</a>
            <span class="sep">·</span>
            <a href="{{ route('sell.history') }}">판매현황</a>
            <span class="sep">·</span>
            <a href="{{ route('membership.index') }}">멤버십</a>
            <span class="sep">·</span>
            <a href="{{ route('mypage') }}">마이페이지</a>
            <span class="sep">·</span>
            <form action="{{ route('logout') }}" method="POST" style="display:inline">@csrf
                <button type="submit" style="background:none;border:0;color:#cfcfcf;font-size:12px;padding:0">로그아웃</button>
            </form>
        @else
            <a href="{{ route('login') }}">로그인</a>
            <span class="sep">·</span>
            <a href="{{ route('register') }}">회원가입</a>
        @endauth
        <span class="sep">·</span>
        <a href="{{ route('partner.login') }}">파트너센터</a>
        <span class="sep">·</span>
        <a href="{{ route('admin.login') }}">관리자</a>
    </div>
</div>

<header class="header">
    <div class="wrap header__main">
        <a href="{{ route('home') }}" class="logo">MOO<b>N</b>S</a>
        <form class="search" action="{{ route('catalog.all') }}" method="GET">
            <button type="submit">🔍</button>
            <input type="text" name="q" placeholder="브랜드, 상품을 검색해보세요" value="{{ request('q') }}">
        </form>
        <div class="header__actions">
            <a href="{{ route('sell.create') }}" class="iconbtn"><span class="i">💰</span>판매하기</a>
            <a href="{{ auth('web')->check() ? route('chat.index') : route('login') }}" class="iconbtn"><span class="i">💬</span>상담</a>
            <a href="{{ auth('web')->check() ? route('wishlist.index') : route('login') }}" class="iconbtn"><span class="i">❤️</span>관심</a>
            <a href="{{ auth('web')->check() ? route('cart.index') : route('login') }}" class="iconbtn"><span class="i">🛒</span>장바구니</a>
            <a href="{{ auth('web')->check() ? route('notifications.index') : route('login') }}" class="iconbtn" style="position:relative">
                <span class="i">🔔</span>알림
                <span class="noti-badge" id="notiBadge" style="display:none">0</span>
            </a>
            <a href="{{ auth('web')->check() ? route('mypage') : route('login') }}" class="iconbtn"><span class="i">👤</span>마이</a>
        </div>
    </div>
    <nav class="catnav">
        <div class="wrap catnav__list">
            <div class="catnav__item">
                <a href="{{ route('catalog.all') }}" class="catnav__link"><span class="emoji">🔥</span>전체</a>
            </div>
            @foreach($navCategories as $cat)
                <div class="catnav__item">
                    <a href="{{ route('catalog.category', $cat) }}" class="catnav__link">
                        <span class="emoji">{{ $cat->icon }}</span>{{ $cat->name }}
                    </a>
                    @if($cat->children->count())
                        <div class="megamenu">
                            @foreach($cat->children as $child)
                                <a href="{{ route('catalog.category', $child) }}">{{ $child->name }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </nav>
</header>

@if(session('status'))
    <div class="wrap" style="margin-top:16px"><div class="alert alert--ok">{{ session('status') }}</div></div>
@endif

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="wrap">
        <div class="footer__grid">
            <div>
                <div class="footer__logo">MOO<b>N</b>S</div>
                <p class="footer__note">
                    전 세계 명품을 가장 합리적인 가격으로.<br>
                    MOONS는 정품만을 취급하는 럭셔리 셀렉트샵입니다.<br>
                    고객센터 1600-0000 · 평일 10:00–18:00
                </p>
            </div>
            <div>
                <h4>쇼핑</h4>
                <ul>
                    <li><a href="{{ route('catalog.all') }}">전체 상품</a></li>
                    <li><a href="{{ route('catalog.all', ['sort'=>'discount']) }}">특가 세일</a></li>
                    <li><a href="{{ route('catalog.all', ['sort'=>'popular']) }}">인기 상품</a></li>
                </ul>
            </div>
            <div>
                <h4>고객지원</h4>
                <ul>
                    <li><a href="{{ route('content.notices') }}">공지사항</a></li>
                    <li><a href="{{ route('content.faqs') }}">자주 묻는 질문</a></li>
                    <li><a href="{{ route('reviews.gallery') }}">포토 후기</a></li>
                    <li><a href="{{ route('verify.index') }}">정품 인증 조회</a></li>
                </ul>
            </div>
            <div>
                <h4>파트너 · 관리</h4>
                <ul>
                    <li><a href="{{ route('partner.login') }}">파트너 로그인</a></li>
                    <li><a href="{{ route('partner.register') }}">입점 신청</a></li>
                    <li><a href="{{ route('admin.login') }}">관리자 로그인</a></li>
                </ul>
            </div>
        </div>
        <div class="footer__bar">
            <span>© {{ date('Y') }} MOONS. All rights reserved.</span>
            <span>이용약관 · 개인정보처리방침</span>
        </div>
    </div>
</footer>

<script>
    // Wishlist (DB-backed)
    (function () {
        var meta = document.querySelector('meta[name=wished-ids]');
        var authed = !!meta;
        var wished = new Set((meta ? meta.content : '').split(',').filter(Boolean).map(Number));
        var token = document.querySelector('meta[name=csrf-token]').content;
        var LOGIN = "{{ route('login') }}", TOGGLE = "{{ route('wishlist.toggle') }}";

        function paint() {
            document.querySelectorAll('.wish[data-product-id]').forEach(function (w) {
                var on = wished.has(+w.dataset.productId);
                w.classList.toggle('on', on); w.textContent = on ? '♥' : '♡';
            });
            document.querySelectorAll('.wish-btn[data-product-id]').forEach(function (b) {
                var on = wished.has(+b.dataset.productId);
                b.classList.toggle('on', on);
                var t = b.querySelector('.wtxt'); if (t) t.textContent = on ? '♥ 관심상품 담김' : '♡ 관심상품';
            });
        }
        paint();

        document.addEventListener('click', function (e) {
            var el = e.target.closest('.wish[data-product-id], .wish-btn[data-product-id]');
            if (!el) return;
            e.preventDefault();
            if (!authed) { location.href = LOGIN; return; }
            var pid = +el.dataset.productId;
            fetch(TOGGLE, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                body: 'product_id=' + pid })
              .then(function (r) { return r.json(); })
              .then(function (d) { d.wished ? wished.add(pid) : wished.delete(pid); paint(); })
              .catch(function () {});
        });
    })();

    // Notification bell — poll unread count
    @auth('web')
    (function () {
        var badge = document.getElementById('notiBadge');
        function refresh() {
            fetch("{{ route('notifications.unread') }}", {headers:{'X-Requested-With':'XMLHttpRequest'}})
                .then(r => r.json()).then(function (d) {
                    if (d.count > 0) { badge.textContent = d.count > 99 ? '99+' : d.count; badge.style.display = ''; }
                    else { badge.style.display = 'none'; }
                }).catch(function(){});
        }
        refresh(); setInterval(refresh, 15000);
    })();
    @endauth
</script>
</body>
</html>
