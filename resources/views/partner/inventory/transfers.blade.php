@extends('layouts.partner')
@section('title', '재고 이동')
@section('subtitle', '받은 요청 승인 · 보낸 요청 현황 · 이동 이력')

@section('content')
@php
    $actBtn = function($t, $action, $label, $cls='') {
        return '<form action="'.route('partner.inventory.act',$t).'" method="POST" style="display:inline">'.csrf_field().
            '<input type="hidden" name="action" value="'.$action.'"><button class="pbtn pbtn--sm '.$cls.'" type="submit">'.$label.'</button></form>';
    };
@endphp

{{-- 받은 요청 (내가 보유 지점) --}}
<div class="panel">
    <div class="panel__head"><h2>📥 받은 이동 요청 ({{ $incoming->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>이동번호</th><th>상품</th><th>요청 지점</th><th>수량</th><th>상태</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @forelse($incoming as $t)
            <tr>
                <td><b>{{ $t->code }}</b>@if($t->customer_wish)<span class="pill pill--amber" style="margin-left:4px">고객희망</span>@endif</td>
                <td>{{ $t->product?->brand }} {{ Str::limit($t->product?->name,20) }}</td>
                <td>{{ $t->toStore?->company_name }}</td>
                <td>{{ $t->quantity }}개</td>
                <td><span class="pill pill--{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                <td style="text-align:right;white-space:nowrap">
                    @if($t->status==='requested'){!! $actBtn($t,'approve','승인','pbtn--primary').' '.$actBtn($t,'reject','반려','pbtn--danger') !!}
                    @elseif($t->status==='approved'){!! $actBtn($t,'ship','발송','pbtn--primary') !!}
                    @else - @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">받은 요청이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- 보낸 요청 (내가 요청 지점) --}}
<div class="panel">
    <div class="panel__head"><h2>📤 보낸 이동 요청 ({{ $outgoing->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>이동번호</th><th>상품</th><th>보유 지점</th><th>수량</th><th>상태</th><th style="text-align:right">처리</th></tr></thead>
        <tbody>
        @forelse($outgoing as $t)
            <tr>
                <td><b>{{ $t->code }}</b></td>
                <td>{{ $t->product?->brand }} {{ Str::limit($t->product?->name,20) }}</td>
                <td>{{ $t->fromStore?->company_name }}</td>
                <td>{{ $t->quantity }}개</td>
                <td><span class="pill pill--{{ $t->status_color }}">{{ $t->status_label }}</span></td>
                <td style="text-align:right">
                    @if($t->status==='shipping'){!! $actBtn($t,'complete','수령 확인','pbtn--primary') !!}@else - @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty-row">보낸 요청이 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
