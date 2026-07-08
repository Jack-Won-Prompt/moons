@extends('layouts.storefront')
@section('title', '실시간 상담 · MOONS')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="listing__head" style="margin-top:28px"><h1>💬 실시간 상담</h1>
        <form action="{{ route('chat.start') }}" method="POST" style="margin-left:auto">@csrf
            <input type="hidden" name="type" value="support"><input type="hidden" name="subject" value="1:1 고객 상담">
            <button class="btn btn--primary" style="padding:10px 18px">+ 새 상담</button>
        </form>
    </div>

    @forelse($conversations as $c)
        @php $unread = $c->unreadFor('customer'); @endphp
        <a href="{{ route('chat.show', $c) }}" class="sr-row">
            <div class="sr-thumb"><span>{{ ['quote'=>'💰','product'=>'🛍️','support'=>'💬'][$c->type] ?? '💬' }}</span></div>
            <div class="sr-main">
                <div class="sr-code">{{ $c->type_label }} · {{ $c->staff_label }}</div>
                <div class="sr-title">{{ $c->subject }}</div>
                <div class="sr-sub">{{ optional($c->last_message_at)->diffForHumans() ?? '대화 없음' }}</div>
            </div>
            @if($unread)<span class="pill pill--red">{{ $unread }}</span>@else<span class="pill pill--gray">{{ $c->status==='open'?'상담중':'종료' }}</span>@endif
        </a>
    @empty
        <div class="empty"><div class="big">💬</div><p>진행 중인 상담이 없습니다.<br>상품 상세 또는 판매 상세에서 상담을 시작할 수 있습니다.</p></div>
    @endforelse
</div>
<style>
    .sr-row{display:flex;align-items:center;gap:16px;background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px;margin-bottom:12px;transition:box-shadow .15s}
    .sr-row:hover{box-shadow:var(--shadow)} .sr-thumb{width:52px;height:52px;border-radius:12px;background:var(--bg-alt);display:grid;place-items:center;font-size:24px;flex:none}
    .sr-code{font-size:12px;color:var(--muted)} .sr-title{font-weight:700;margin:2px 0 3px} .sr-sub{font-size:13px;color:var(--ink-soft)}
</style>
@endsection
