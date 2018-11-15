@extends('layouts.plain')

@section('content')

    @include('templates.member.post.event.single_group_event', array('post' => $post))

@endsection