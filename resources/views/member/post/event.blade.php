@extends('layouts.plain')

@section('content')

    @foreach($posts as $post)

        @include('templates.widget.social_media.event', array('post' => $post, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox))

    @endforeach

@endsection