@extends('layouts.storefront')
@section('title', $c->subject . ' · 상담')

@section('content')
<div class="wrap" style="max-width:820px">
    <div class="crumb" style="margin-top:22px"><a href="{{ route('chat.index') }}">실시간 상담</a> / <span>{{ $c->code }}</span></div>
    @include('partials.chat-room', ['c'=>$c,'meRole'=>'customer','sendUrl'=>route('chat.send',$c),'pollUrl'=>route('chat.poll',$c)])
</div>
@endsection
