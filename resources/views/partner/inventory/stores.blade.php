@extends('layouts.partner')
@section('title', '타 지점 재고 조회')
@section('subtitle', '다른 지점의 재고를 조회하고 이동을 요청합니다')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>타 지점 재고 ({{ $others->count() }})</h2></div>
    <table class="table">
        <thead><tr><th>상품</th><th>보유 지점</th><th>수량</th><th>위치</th><th style="text-align:right">이동 요청</th></tr></thead>
        <tbody>
        @forelse($others as $inv)
            <tr>
                <td><div class="tprod">
                    @if($inv->product?->image)<img class="swatch" src="{{ $inv->product->image_url }}" alt="">@endif
                    <div><div class="name">{{ $inv->product?->name }}</div><div class="brand">{{ $inv->product?->brand }}</div></div></div></td>
                <td><b>{{ $inv->store?->company_name }}</b></td>
                <td>{{ $inv->quantity }}개</td>
                <td>{{ $inv->location ?? '-' }}</td>
                <td style="text-align:right">
                    <form action="{{ route('partner.inventory.request') }}" method="POST" style="display:flex;gap:6px;justify-content:flex-end;align-items:center">@csrf
                        <input type="hidden" name="inventory_id" value="{{ $inv->id }}">
                        <input type="number" name="quantity" value="1" min="1" max="{{ $inv->quantity }}" style="width:60px;padding:6px;border:1px solid var(--p-line);border-radius:8px">
                        <label style="font-size:12px;color:var(--p-muted);display:flex;gap:4px;align-items:center"><input type="checkbox" name="customer_wish" value="1">고객희망</label>
                        <button class="pbtn pbtn--sm pbtn--primary" type="submit">요청</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="empty-row">타 지점 재고가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
