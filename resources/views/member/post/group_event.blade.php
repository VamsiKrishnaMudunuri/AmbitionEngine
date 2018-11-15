@extends('layouts.plain')

@section('content')

    @foreach($posts as $post)

        @include('templates.member.post.event.single_group_event', array('post' => $post))

    @endforeach

@endsection