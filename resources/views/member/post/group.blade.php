@extends('layouts.plain')

@section('content')

    @foreach($posts as $post)

        @include('templates.widget.social_media.feed', array('post' => $post, 'join' => $join))

    @endforeach

@endsection