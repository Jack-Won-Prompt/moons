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
            <a href="{{ auth('web')->check() ? route('chat.index') : route('login') }}" class="iconbtn" onclick="return cwOpen()"><span class="i">💬</span>상담</a>
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

{{-- 실시간 상담 플로팅 위젯 (팝업) · 폴링 기반, 추후 Pusher 전환 예정 --}}
<div id="chatWidget" aria-live="polite">
    <button id="cwToggle" class="cw-toggle" type="button" aria-label="실시간 상담" onclick="cwOpen()">
        <span class="cw-ico">💬</span><span class="cw-dot" id="cwDot" style="display:none"></span>
    </button>
    <div id="cwPanel" class="cw-panel" role="dialog" aria-label="실시간 상담">
        <div class="cw-head">
            <div>
                <div class="cw-title">MOONS 실시간 상담</div>
                <div class="cw-sub">보통 몇 분 내에 답변드려요</div>
            </div>
            <button class="cw-x" type="button" aria-label="닫기" onclick="cwClose()">✕</button>
        </div>
        <div class="cw-body" id="cwBody"></div>
        <form class="cw-input" id="cwForm" style="display:none">
            <input type="text" id="cwText" placeholder="메시지를 입력하세요" autocomplete="off">
            <button type="submit">전송</button>
        </form>
    </div>
</div>
<style>
    #chatWidget { position: fixed; right: 22px; bottom: 22px; z-index: 90; }
    .cw-toggle { width: 60px; height: 60px; border-radius: 50%; border: 0; background: var(--ink); color: #fff;
        font-size: 26px; cursor: pointer; box-shadow: 0 8px 24px rgba(0,0,0,.28); display: grid; place-items: center;
        transition: transform .18s; position: relative; }
    .cw-toggle:hover { transform: scale(1.06); }
    .cw-dot { position: absolute; top: 10px; right: 12px; width: 11px; height: 11px; border-radius: 50%; background: var(--sale); border: 2px solid var(--ink); }
    .cw-panel { position: absolute; right: 0; bottom: 74px; width: 360px; max-width: calc(100vw - 44px); height: 520px; max-height: calc(100vh - 120px);
        background: #fff; border: 1px solid var(--line); border-radius: 18px; box-shadow: var(--shadow-lg);
        display: none; flex-direction: column; overflow: hidden; }
    #chatWidget.open .cw-panel { display: flex; animation: cwUp .18s ease; }
    @keyframes cwUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    .cw-head { background: var(--ink); color: #fff; padding: 18px 20px; display: flex; justify-content: space-between; align-items: center; }
    .cw-title { font-weight: 700; font-size: 15px; }
    .cw-sub { font-size: 12px; opacity: .7; margin-top: 2px; }
    .cw-x { background: none; border: 0; color: #fff; font-size: 16px; cursor: pointer; opacity: .8; }
    .cw-body { flex: 1; overflow-y: auto; padding: 16px; background: var(--bg-alt); display: flex; flex-direction: column; gap: 10px; }
    .cw-msg { max-width: 80%; padding: 10px 13px; border-radius: 13px; font-size: 13.5px; line-height: 1.5; word-break: break-word; }
    .cw-msg.me { align-self: flex-end; background: var(--ink); color: #fff; border-bottom-right-radius: 4px; }
    .cw-msg.them { align-self: flex-start; background: #fff; border: 1px solid var(--line); border-bottom-left-radius: 4px; }
    .cw-who { font-size: 11px; color: var(--muted); margin: 0 4px 3px; }
    .cw-time { font-size: 10px; color: #b5b5ba; margin: 2px 5px 0; }
    .cw-note { text-align: center; color: var(--muted); font-size: 13px; margin: auto; padding: 20px; }
    .cw-note .btn { display: inline-block; margin-top: 12px; }
    .cw-input { display: flex; gap: 8px; padding: 12px; border-top: 1px solid var(--line); background: #fff; }
    .cw-input input { flex: 1; border: 1px solid var(--line); border-radius: 999px; padding: 10px 16px; font-size: 14px; outline: 0; font-family: inherit; }
    .cw-input input:focus { border-color: var(--ink); }
    .cw-input button { border: 0; background: var(--ink); color: #fff; border-radius: 999px; padding: 10px 18px; font-weight: 700; cursor: pointer; }
    @media (max-width: 480px) { .cw-panel { width: calc(100vw - 24px); right: -6px; } #chatWidget { right: 14px; bottom: 14px; } }
</style>
<script>
(function () {
    var W = {
        authed: @json(auth('web')->check()),
        login: "{{ route('login') }}",
        widget: "{{ route('chat.widget') }}",
        token: document.querySelector('meta[name=csrf-token]').content,
        loaded: false, sendUrl: null, pollUrl: null, last: 0, timer: null
    };
    var root = document.getElementById('chatWidget'),
        body = document.getElementById('cwBody'),
        form = document.getElementById('cwForm'),
        text = document.getElementById('cwText');

    function esc(s){ return (s||'').replace(/[&<>]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c];}); }
    function scroll(){ body.scrollTop = body.scrollHeight; }

    function render(m){
        if (m.role === 'system') { var d=document.createElement('div'); d.className='cw-note'; d.textContent=m.body; body.appendChild(d); return; }
        var me = m.role === 'customer';
        var wrap = document.createElement('div'); wrap.style.display='flex'; wrap.style.flexDirection='column';
        wrap.style.alignItems = me ? 'flex-end' : 'flex-start';
        var html = me ? '' : '<div class="cw-who">'+esc(m.name)+'</div>';
        html += '<div class="cw-msg '+(me?'me':'them')+'">';
        if (m.attachment) html += m.type==='image' ? '<a href="'+m.attachment+'" target="_blank"><img src="'+m.attachment+'" style="max-width:160px;border-radius:8px"></a>' : '<a href="'+m.attachment+'" target="_blank">📎 첨부</a>';
        if (m.body) html += esc(m.body).replace(/\n/g,'<br>');
        html += '</div><div class="cw-time">'+m.at+'</div>';
        wrap.innerHTML = html; body.appendChild(wrap);
    }

    function poll(){
        if (!W.pollUrl) return;
        fetch(W.pollUrl + '?after=' + W.last, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
            .then(function(r){ return r.json(); })
            .then(function(list){ if (list.length){ list.forEach(function(m){ if(m.id>W.last){ render(m); W.last=m.id; } }); scroll(); } })
            .catch(function(){});
    }

    function load(){
        if (W.loaded) return;
        body.innerHTML = '<div class="cw-note">불러오는 중…</div>';
        fetch(W.widget, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
            .then(function(r){ return r.json(); })
            .then(function(d){
                W.loaded = true; W.sendUrl = d.send_url; W.pollUrl = d.poll_url;
                body.innerHTML = '';
                var intro = document.createElement('div'); intro.className='cw-note';
                intro.textContent = '무엇을 도와드릴까요? 상품·주문·감정 등 편하게 문의하세요.';
                body.appendChild(intro);
                (d.messages||[]).forEach(function(m){ render(m); W.last = Math.max(W.last, m.id); });
                scroll(); form.style.display = 'flex'; text.focus();
                if (W.timer) clearInterval(W.timer); W.timer = setInterval(poll, 3000);
            });
    }

    window.cwOpen = function(){
        root.classList.add('open');
        document.getElementById('cwDot').style.display = 'none';
        if (!W.authed) {
            body.innerHTML = '<div class="cw-note">실시간 상담은 로그인 후 이용하실 수 있어요.<br><a class="btn btn--primary" href="'+W.login+'">로그인하기</a></div>';
            form.style.display = 'none';
        } else { load(); }
        return false;
    };
    window.cwClose = function(){ root.classList.remove('open'); };

    if (form) form.addEventListener('submit', function(e){
        e.preventDefault();
        var v = text.value.trim(); if (!v || !W.sendUrl) return;
        text.value = '';
        var fd = new FormData(); fd.append('_token', W.token); fd.append('body', v);
        fetch(W.sendUrl, { method:'POST', headers:{ 'X-Requested-With':'XMLHttpRequest' }, body: fd })
            .then(function(){ poll(); }).catch(function(){});
    });
})();
</script>

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
