@extends('layouts.admin')
@section('title', '정산 관리')
@section('subtitle', '지점 매입·판매 정산 · 수수료')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">💰 총 거래액</div><div class="v">{{ number_format($stats['gross']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">🏦 수수료 수익</div><div class="v">{{ number_format($stats['commission']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">⏳ 미지급</div><div class="v">{{ number_format($stats['pending']) }}<small style="font-size:13px">원</small></div></div>
    <div class="stat"><div class="k">✅ 지급완료</div><div class="v">{{ number_format($stats['paid']) }}<small style="font-size:13px">원</small></div></div>
</div>

<div class="panel">
    <div class="panel__head"><h2>지점별 정산</h2></div>
    <table class="table">
        <thead><tr><th>지점</th><th>판매액</th><th>수수료</th><th>정산액</th><th>미지급</th><th style="text-align:right">지급</th></tr></thead>
        <tbody>
        @forelse($byStore as $row)
            <tr>
                <td><b>{{ $row->store?->company_name }}</b></td>
                <td>{{ number_format($row->gross) }}원</td>
                <td>{{ number_format($row->commission) }}원</td>
                <td>{{ number_format($row->net) }}원</td>
                <td><b style="color:var(--p-amber)">{{ number_format($row->pending) }}원</b></td>
                <td style="text-align:right">
                    @if($row->pending > 0)
                        <form action="{{ route('admin.settlements.pay', $row->store_id) }}" method="POST" onsubmit="return confirm('정산 지급?')">@csrf
                            <button class="pbtn pbtn--sm pbtn--primary">정산 지급</button></form>
                    @else <span class="pill pill--green">완료</span> @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">정산 내역이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <div class="panel__head"><h2>전체 정산 내역</h2></div>
    <table class="table">
        <thead><tr><th>주문</th><th>지점</th><th>상품</th><th>판매액</th><th>정산액</th><th>상태</th></tr></thead>
        <tbody>
        @forelse($settlements as $s)
            <tr>
                <td>{{ $s->order?->code }}</td>
                <td>{{ $s->store?->company_name }}</td>
                <td>{{ Str::limit($s->product?->name ?? '-',20) }}</td>
                <td>{{ number_format($s->gross_amount) }}원</td>
                <td><b>{{ number_format($s->net_amount) }}원</b></td>
                <td><span class="pill pill--{{ $s->status==='paid'?'green':'amber' }}">{{ $s->status==='paid'?'완료':'대기' }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">-</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $settlements->links() }}</div>
</div>
@endsection
