@extends('layouts.admin')
@section('title', '주문 ' . $order->code)
@section('subtitle', $order->customer->name . ' · ' . number_format($order->total) . '원')

@section('content')
<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:18px;align-items:start">
    <div>
        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 14px;font-size:16px">주문 상품</h2>
            @foreach($order->items as $it)
                <div style="display:flex;gap:12px;align-items:center;padding:8px 0;border-bottom:1px solid var(--p-line)">
                    <div style="width:52px;height:52px;border-radius:8px;overflow:hidden;background:#f2f2f2;flex:none">
                        @if($it->image_url)<img src="{{ $it->image_url }}" style="width:100%;height:100%;object-fit:cover">@endif</div>
                    <div style="flex:1"><b>{{ $it->brand }}</b><div style="font-size:13px;color:var(--p-muted)">{{ $it->name }}</div></div>
                    <div style="text-align:right"><b>{{ number_format($it->price) }}원</b><div style="font-size:12px;color:var(--p-muted)">×{{ $it->quantity }}</div></div>
                </div>
            @endforeach
            <div style="display:flex;justify-content:space-between;margin-top:14px;padding-top:12px;border-top:1px solid var(--p-line)"><b>총 결제금액</b><b style="font-size:18px">{{ number_format($order->total) }}원</b></div>
        </div></div>

        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 12px;font-size:16px">배송지 · 결제</h2>
            <dl class="dl-grid" style="grid-template-columns:90px 1fr;font-size:13px">
                <dt>받는분</dt><dd>{{ $order->receiver_name }} · {{ $order->phone }}</dd>
                <dt>주소</dt><dd>{{ $order->address }} {{ $order->address_detail }}</dd>
                <dt>메모</dt><dd>{{ $order->memo ?: '-' }}</dd>
                <dt>결제수단</dt><dd>{{ $order->payment?->method_label }} · <span style="font-family:monospace">{{ $order->payment?->pg_tid }}</span></dd>
            </dl>
        </div></div>
    </div>

    <div>
        <div class="panel"><div class="panel__body">
            <h2 style="margin:0 0 12px;font-size:16px">주문 처리 <span class="pill pill--{{ $order->status_color }}">{{ $order->status_label }}</span></h2>
            <form action="{{ route('admin.orders.status', $order) }}" method="POST">@csrf @method('PATCH')
                <div class="pfield" style="margin-bottom:12px"><label>상태 변경</label>
                    <select name="status">
                        @foreach(['paid'=>'결제완료','preparing'=>'상품준비','shipping'=>'배송중','delivered'=>'배송완료','cancelled'=>'취소'] as $st=>$label)
                            <option value="{{ $st }}" @selected($order->status==$st)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pfield" style="margin-bottom:12px"><label>운송장 번호</label><input type="text" name="tracking_no" value="{{ $order->tracking_no }}" placeholder="배송중일 때 입력"></div>
                <button class="pbtn pbtn--primary" type="submit">상태 저장 + 고객 알림</button>
            </form>
        </div></div>
        <a class="pbtn" href="{{ route('admin.orders.index') }}">← 목록으로</a>
    </div>
</div>
@endsection
