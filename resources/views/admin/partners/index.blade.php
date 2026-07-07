@extends('layouts.admin')
@section('title', '파트너 관리')
@section('subtitle', '입점 파트너를 승인하고 관리합니다')

@section('content')
<div class="panel">
    <div class="panel__head"><h2>전체 파트너 ({{ $partners->total() }})</h2></div>
    <table class="table">
        <thead><tr><th>상호</th><th>담당자</th><th>이메일</th><th>브랜드</th><th>상품수</th><th>상태</th><th style="text-align:right">상태 변경</th></tr></thead>
        <tbody>
        @php $labels = ['pending'=>['승인대기','amber'],'approved'=>['승인','green'],'suspended'=>['정지','red']]; @endphp
        @forelse($partners as $p)
            <tr>
                <td><b>{{ $p->company_name }}</b></td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->email }}</td>
                <td>{{ $p->brand ?: '-' }}</td>
                <td>{{ $p->products_count }}</td>
                <td><span class="pill pill--{{ $labels[$p->status][1] }}">{{ $labels[$p->status][0] }}</span></td>
                <td style="text-align:right;white-space:nowrap">
                    @foreach(['approved'=>'승인','pending'=>'대기','suspended'=>'정지'] as $st=>$lbl)
                        @if($p->status !== $st)
                            <form action="{{ route('admin.partners.status', $p) }}" method="POST" style="display:inline">
                                @csrf @method('PATCH')<input type="hidden" name="status" value="{{ $st }}">
                                <button class="pbtn pbtn--sm {{ $st==='approved'?'pbtn--primary':($st==='suspended'?'pbtn--danger':'') }}" type="submit">{{ $lbl }}</button>
                            </form>
                        @endif
                    @endforeach
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty-row">파트너가 없습니다.</td></tr>
        @endforelse
        </tbody>
    </table>
    <div class="ppage">{{ $partners->links() }}</div>
</div>
@endsection
