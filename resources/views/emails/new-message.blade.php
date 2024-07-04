@extends('layouts.mail')

@section('title')
    You have a new message
@endsection

@section('subtitle')
    You have a new message from <span>{{ $receiver }}</span>
@endsection

@section('send-by')
    sent by <span>{{ $sender }}</span> on <span>{{ $messageTime }}</span>
@endsection

@section('content')
    To view and respond to the full message, please click button bellow or visit your messages section on our platform.
@endsection