@extends('layouts.partner')
@section('title', '판매 정산')
@section('subtitle', '지점 판매 상품의 정산 내역')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">💰 총 판매액</div><div class="v">{{ number_format($summary['gross']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">➖ 수수료</div><div class="v">{{ number_format($summary['commission']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">⏳ 정산 대기</div><div class="v">{{ number_format($summary['pending_net']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">✅ 정산 완료</div><div class="v">{{ number_format($summary['paid_net']) }}<small style="font-size:13px">원</small></div></div>
</div>

<div class="panel">
    <div class="panel__head"><h2>정산 내역</h2></div>
    <table class="table">
        <thead><tr><th>주문</th><th>상품</th><th>판매액</th><th>수수료</th><th>정산액</th><th>상태</th><th>일자</th></tr></thead>
        <tbody>
        @forelse($settlements as $s)
            <tr>
                <td><b>{{ $s->order?->code }}</b></td>
                <td>{{ Str::limit($s->product?->name ?? '-', 24) }}</td>
                <td>{{ number_format($s->gross_amount) }}원</td>
                <td>{{ number_format($s->commission) }}원 ({{ $s->commission_rate }}%)</td>
                <td><b>{{ number_format($s->net_amount) }}원</b></td>
                <td><span class="pill pill--{{ $s->status==='paid'?'green':'amber' }}">{{ $s->status==='paid'?'정산완료':'대기' }}</span></td>
                <td>{{ optional($s->paid_at ?? $s->created_at)->format('Y.m.d') }}</td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-row">정산 내역이 없습니다. (지점 소유 상품 판매 시 생성)</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $settlements->links() }}</div>
</div>
@endsection
