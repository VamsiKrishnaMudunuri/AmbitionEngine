@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.feed', array('post' => $post, 'comment' => $comment, 'like' => $like))

@endsection