@php
    $isPanel = in_array($role, ['admin','store']);
    $layout  = $role==='admin' ? 'layouts.admin' : ($role==='store' ? 'layouts.partner' : 'layouts.storefront');
    $rp      = $role==='admin' ? 'admin.' : ($role==='store' ? 'partner.' : '');
@endphp
@extends($layout)
@section('title', '알림')
@section('subtitle', '알림 센터')

@section('content')
<div class="{{ $isPanel ? '' : 'wrap' }}" style="{{ $isPanel ? '' : 'max-width:720px;' }}">
    @unless($isPanel)<div class="listing__head" style="margin-top:28px"><h1>🔔 알림</h1>
        <form action="{{ route($rp.'notifications.readAll') }}" method="POST" style="margin-left:auto">@csrf
            <button class="btn" style="padding:9px 16px">모두 읽음</button></form></div>
    @else
        <div style="display:flex;justify-content:flex-end;margin-bottom:14px"><form action="{{ route($rp.'notifications.readAll') }}" method="POST">@csrf<button class="pbtn">모두 읽음</button></form></div>
    @endunless

    <div class="{{ $isPanel ? 'panel' : '' }}">
        @forelse($notifications as $n)
            <form action="{{ route($rp.'notifications.read', $n) }}" method="POST" class="noti-item {{ $n->read_at ? 'read' : 'unread' }}">@csrf
                <button type="submit" class="noti-btn">
                    <span class="noti-ico">{{ $n->icon }}</span>
                    <span class="noti-main">
                        <span class="noti-title">{{ $n->title }}</span>
                        @if($n->body)<span class="noti-body">{{ $n->body }}</span>@endif
                        <span class="noti-meta">{{ $n->created_at->diffForHumans() }}
                            @foreach($n->channels ?? [] as $ch)<span class="noti-ch">{{ ['in_app'=>'인앱','email'=>'메일','sms'=>'SMS','kakao'=>'알림톡','push'=>'푸시'][$ch] ?? $ch }}</span>@endforeach
                        </span>
                    </span>
                    @unless($n->read_at)<span class="noti-dot"></span>@endunless
                </button>
            </form>
        @empty
            <div class="empty" style="padding:60px 20px"><div class="big">🔔</div><p>알림이 없습니다.</p></div>
        @endforelse
    </div>
    <div style="margin-top:16px">{{ $notifications->links() }}</div>
</div>

<style>
    .noti-item{border-bottom:1px solid #f0f0f0} .noti-item:last-child{border-bottom:0}
    .noti-btn{width:100%;display:flex;align-items:flex-start;gap:14px;padding:16px 18px;background:none;border:0;text-align:left;cursor:pointer}
    .noti-item.unread .noti-btn{background:#fbfaf6}
    .noti-ico{font-size:22px;flex:none;line-height:1.3}
    .noti-main{flex:1;display:flex;flex-direction:column;gap:3px}
    .noti-title{font-weight:700;font-size:14px}
    .noti-body{font-size:13px;color:#555}
    .noti-meta{font-size:11px;color:#999;display:flex;gap:6px;align-items:center;margin-top:2px}
    .noti-ch{background:#eef0f3;color:#6b7080;border-radius:4px;padding:1px 6px}
    .noti-dot{width:8px;height:8px;border-radius:50%;background:#ff2d55;flex:none;margin-top:6px}
    .noti-btn:hover{background:#f6f6f6}
</style>
@endsection
