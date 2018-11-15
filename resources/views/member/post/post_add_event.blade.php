@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.event', array('post' => $post, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox))

@endsection