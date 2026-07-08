@extends('layouts.partner')
@section('title', '고객 상담')
@section('subtitle', '우리 지점으로 접수된 상담을 확인합니다')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>상담 목록 ({{ $conversations->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>유형</th><th>제목</th><th>고객</th><th>최근</th><th>미읽음</th><th style="text-align:right">열기</th></tr></thead>
        <tbody>
        @forelse($conversations as $c)
            @php $u = $c->unreadFor('staff'); @endphp
            <tr>
                <td>{{ $c->type_label }}</td>
                <td><b>{{ Str::limit($c->subject, 40) }}</b></td>
                <td>{{ $c->customer->name }}</td>
                <td>{{ optional($c->last_message_at)->diffForHumans() ?? '-' }}</td>
                <td>{!! $u ? '<span class="pill pill--red">'.$u.'</span>' : '<span class="pill pill--gray">0</span>' !!}</td>
                <td style="text-align:right"><a class="pbtn pbtn--sm pbtn--primary" href="{{ route('partner.chat.show', $c) }}">상담</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">상담이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
