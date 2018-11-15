@extends('layouts.plain')

@section('content')

    @foreach($notifications as $notification)


        @include('templates.widget.social_media.notification.list', array('notification' => $notification))


    @endforeach

@endsection