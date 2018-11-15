@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.comment_editor', array('route' => array('member::post::post-edit-comment', $comment->getKey()), 'member' => $comment->user, 'post' => $comment->post, 'comment' => $comment, 'is_edit' => true))

@endsection