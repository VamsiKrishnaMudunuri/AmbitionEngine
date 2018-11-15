@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.comment', array('comment' => $comment))

@endsection