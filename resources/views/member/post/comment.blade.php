@extends('layouts.plain')

@section('content')

    @foreach($comments as $comment)

        @include('templates.widget.social_media.comment', array('comment' => $comment, 'like' => $like))

    @endforeach

@endsection