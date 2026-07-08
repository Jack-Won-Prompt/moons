@extends('layouts.admin')
@section('title', '상담 · ' . $c->code)
@section('subtitle', $c->customer->name . ' 고객 · ' . ($c->store_id ? $c->store->company_name : '본사 담당'))

@section('content')
<div style="max-width:820px">
    @include('partials.chat-room', ['c'=>$c,'meRole'=>'admin','sendUrl'=>route('admin.chat.send',$c),'pollUrl'=>route('admin.chat.poll',$c),'canReply'=>$canReply])
    <a class="pbtn" href="{{ route('admin.chat.index') }}" style="margin-top:14px">← 목록으로</a>
</div>
@endsection
