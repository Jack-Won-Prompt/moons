@extends('layouts.admin')
@section('title', '블록체인 감정서')
@section('subtitle', '발급된 감정서와 DPP 관리')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>발급 감정서 ({{ $certificates->total() }})</h2></div>
    <table class="table">
        <thead><tr><th>감정서 번호</th><th>상품</th><th>판정</th><th>발급처</th><th>블록체인 해시</th><th>발급일</th><th style="text-align:right">검증</th></tr></thead>
        <tbody>
        @forelse($certificates as $c)
            <tr>
                <td><b>{{ $c->code }}</b></td>
                <td><div class="tprod"><div><div class="name">{{ $c->model }}</div><div class="brand">{{ $c->brand }}</div></div></div></td>
                <td><span class="pill pill--{{ $c->result==='authentic'?'green':($c->result==='fake'?'red':'amber') }}">{{ $c->result_label }}</span></td>
                <td>{{ $c->issuer }}</td>
                <td style="font-family:monospace;font-size:11px;max-width:180px;overflow:hidden;text-overflow:ellipsis">{{ Str::limit($c->blockchain_hash, 20) }}
                    {!! $c->isValid() ? '<span class="pill pill--green">✓</span>' : '<span class="pill pill--red">위변조</span>' !!}</td>
                <td>{{ optional($c->issued_at)->format('Y.m.d') }}</td>
                <td style="text-align:right"><a class="pbtn pbtn--sm" href="{{ route('verify.show', $c->code) }}" target="_blank">조회</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-row">발급된 감정서가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $certificates->links() }}</div>
</div>
@endsection
