@extends('layouts.admin')
@section('title', '상담 모니터링')
@section('subtitle', '전체 상담 현황과 통계')

@section('content')
<div class="stats">
    <div class="stat"><div class="k">💬 전체 상담</div><div class="v">{{ $stats['total'] }}</div></div>
    <div class="stat"><div class="k">🟢 진행중</div><div class="v">{{ $stats['open'] }}</div></div>
    <div class="stat"><div class="k">✉️ 총 메시지</div><div class="v">{{ number_format($stats['messages']) }}</div></div>
    <div class="stat"><div class="k">🏢 본사 담당</div><div class="v">{{ $stats['to_hq'] }}</div></div>
</div>

<div class="panel">
    <div class="panel__head"><h2>상담 목록</h2></div>
    <table class="table">
        <thead><tr><th>유형</th><th>제목</th><th>고객</th><th>담당</th><th>최근</th><th style="text-align:right">보기</th></tr></thead>
        <tbody>
        @forelse($conversations as $c)
            <tr>
                <td>{{ $c->type_label }}</td>
                <td><b>{{ Str::limit($c->subject, 38) }}</b></td>
                <td>{{ $c->customer->name }}</td>
                <td>{!! $c->store_id ? $c->store->company_name : '<span class="pill pill--amber">본사</span>' !!}</td>
                <td>{{ optional($c->last_message_at)->diffForHumans() ?? '-' }}</td>
                <td style="text-align:right"><a class="pbtn pbtn--sm {{ is_null($c->store_id)?'pbtn--primary':'' }}" href="{{ route('admin.chat.show', $c) }}">{{ is_null($c->store_id)?'답변':'모니터' }}</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">상담이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $conversations->links() }}</div>
</div>
@endsection
