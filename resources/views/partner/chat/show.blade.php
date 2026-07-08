@extends('layouts.partner')
@section('title', '상담 · ' . $c->code)
@section('subtitle', $c->customer->name . ' 고객')

@section('content')
<div style="max-width:820px">
    @include('partials.chat-room', ['c'=>$c,'meRole'=>'store','sendUrl'=>route('partner.chat.send',$c),'pollUrl'=>route('partner.chat.poll',$c)])
    <a class="pbtn" href="{{ route('partner.chat.index') }}" style="margin-top:14px">← 목록으로</a>
</div>
@endsection
