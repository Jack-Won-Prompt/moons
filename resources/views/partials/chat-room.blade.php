{{-- 공유 채팅방 · 변수: $c, $meRole, $sendUrl, $pollUrl, $canReply(기본 true) --}}
@php $canReply = $canReply ?? true; @endphp
<div class="chatroom">
    <div class="chatroom__head">
        <div>
            <div class="chatroom__title">{{ $c->subject }}</div>
            <div class="chatroom__sub">{{ $c->type_label }} · {{ $meRole==='customer' ? $c->staff_label : $c->customer->name }}
                @if($c->product) · <a href="{{ route('catalog.product', $c->product) }}" target="_blank" style="color:inherit;text-decoration:underline">상품보기</a>@endif
            </div>
        </div>
        <span class="pill pill--{{ $c->status==='open'?'green':'gray' }}">{{ $c->status==='open'?'상담중':'종료' }}</span>
    </div>

    <div class="chatroom__body" id="chatBody">
        @foreach($c->messages as $m)
            @if($m->sender_role === 'system')
                <div class="chat-sys">{{ $m->body }}</div>
            @else
                <div class="chat-msg {{ $m->sender_role === $meRole ? 'mine' : 'theirs' }}">
                    @if($m->sender_role !== $meRole)<div class="chat-who">{{ $m->sender_name }}</div>@endif
                    <div class="chat-bubble">
                        @if($m->attachment)
                            @php $src = str_starts_with($m->attachment,'http') ? $m->attachment : asset($m->attachment); @endphp
                            @if($m->attachment_type === 'image')<a href="{{ $src }}" target="_blank"><img src="{{ $src }}" class="chat-img"></a>
                            @else<a href="{{ $src }}" target="_blank" class="chat-file">📎 첨부파일</a>@endif
                        @endif
                        @if($m->body)<div>{!! nl2br(e($m->body)) !!}</div>@endif
                    </div>
                    <div class="chat-time">{{ $m->created_at->format('H:i') }}</div>
                </div>
            @endif
        @endforeach
    </div>

    @if($canReply)
    <form class="chatroom__input" id="chatForm" action="{{ $sendUrl }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="chat-attach" title="파일 첨부">📎<input type="file" name="attachment" accept="image/*,application/pdf" hidden onchange="this.form.querySelector('.chat-fname').textContent=this.files[0]?.name||''"></label>
        <input type="text" name="body" placeholder="메시지를 입력하세요" autocomplete="off">
        <span class="chat-fname"></span>
        <button type="submit">전송</button>
    </form>
    @else
        <div class="chatroom__input" style="justify-content:center;color:var(--muted,#888);font-size:13px">👁️ 모니터링 모드 · 지점 담당 상담은 읽기 전용입니다</div>
    @endif
</div>

<style>
    .chatroom { display:flex;flex-direction:column;height:70vh;min-height:460px;background:#fff;border:1px solid #ececec;border-radius:16px;overflow:hidden }
    .chatroom__head { display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #ececec }
    .chatroom__title { font-weight:800 }
    .chatroom__sub { font-size:12px;color:#8a8a8f;margin-top:2px }
    .chatroom__body { flex:1;overflow-y:auto;padding:20px;background:#fafafa;display:flex;flex-direction:column;gap:12px }
    .chat-sys { align-self:center;font-size:12px;color:#8a8a8f;background:#fff;border:1px solid #ececec;border-radius:999px;padding:5px 14px }
    .chat-msg { max-width:72%;display:flex;flex-direction:column }
    .chat-msg.mine { align-self:flex-end;align-items:flex-end }
    .chat-msg.theirs { align-self:flex-start;align-items:flex-start }
    .chat-who { font-size:11px;color:#8a8a8f;margin:0 4px 4px }
    .chat-bubble { padding:11px 15px;border-radius:14px;font-size:14px;line-height:1.5;word-break:break-word }
    .chat-msg.mine .chat-bubble { background:#111;color:#fff;border-bottom-right-radius:4px }
    .chat-msg.theirs .chat-bubble { background:#fff;border:1px solid #ececec;border-bottom-left-radius:4px }
    .chat-time { font-size:10px;color:#b0b0b5;margin:3px 5px 0 }
    .chat-img { max-width:200px;border-radius:9px;display:block;margin-bottom:4px }
    .chat-file { color:inherit }
    .chatroom__input { display:flex;align-items:center;gap:10px;padding:12px 16px;border-top:1px solid #ececec }
    .chatroom__input input[type=text] { flex:1;border:1px solid #ececec;border-radius:999px;padding:11px 18px;font-size:14px;outline:0;font-family:inherit }
    .chatroom__input input[type=text]:focus { border-color:#111 }
    .chatroom__input button { border:0;background:#111;color:#fff;border-radius:999px;padding:11px 22px;font-weight:700;cursor:pointer }
    .chat-attach { font-size:20px;cursor:pointer;user-select:none }
    .chat-fname { font-size:11px;color:#8a8a8f;max-width:90px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis }
</style>
<script>
(function(){
    var body=document.getElementById('chatBody'), form=document.getElementById('chatForm');
    var meRole=@json($meRole);
    var pollUrl=@json($pollUrl), sendUrl=@json($sendUrl);
    var token=document.querySelector('meta[name=csrf-token]')?.content || (form && form.querySelector('input[name=_token]')?.value);
    var last={{ $c->messages->max('id') ?? 0 }};
    function scroll(){ body.scrollTop=body.scrollHeight; }
    scroll();
    function esc(s){ return (s||'').replace(/[&<>]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;'}[c];}); }
    function render(m){
        if(m.role==='system'){ var d=document.createElement('div'); d.className='chat-sys'; d.textContent=m.body; body.appendChild(d); return; }
        var mine=m.role===meRole;
        var wrap=document.createElement('div'); wrap.className='chat-msg '+(mine?'mine':'theirs');
        var html='';
        if(!mine) html+='<div class="chat-who">'+esc(m.name)+'</div>';
        html+='<div class="chat-bubble">';
        if(m.attachment){ html+= m.type==='image' ? '<a href="'+m.attachment+'" target="_blank"><img src="'+m.attachment+'" class="chat-img"></a>' : '<a href="'+m.attachment+'" target="_blank" class="chat-file">📎 첨부파일</a>'; }
        if(m.body) html+='<div>'+esc(m.body).replace(/\n/g,'<br>')+'</div>';
        html+='</div><div class="chat-time">'+m.at+'</div>';
        wrap.innerHTML=html; body.appendChild(wrap);
    }
    function poll(){
        fetch(pollUrl+'?after='+last,{headers:{'X-Requested-With':'XMLHttpRequest'}}).then(r=>r.json()).then(function(list){
            if(list.length){ list.forEach(function(m){ if(m.id>last){ render(m); last=m.id; } }); scroll(); }
        }).catch(function(){});
    }
    setInterval(poll, 3000);
    if(form) form.addEventListener('submit', function(e){
        e.preventDefault();
        var fd=new FormData(form);
        if(!fd.get('body') && !(fd.get('attachment') && fd.get('attachment').size)) return;
        fetch(sendUrl,{method:'POST',headers:{'X-Requested-With':'XMLHttpRequest'},body:fd})
          .then(r=>r.json()).then(function(){ form.reset(); form.querySelector('.chat-fname').textContent=''; poll(); }).catch(function(){ form.submit(); });
    });
})();
</script>
