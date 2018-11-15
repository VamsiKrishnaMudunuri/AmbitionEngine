@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.member.card', array('repo' => $repo, 'auth_member' => $member, 'members' => $repos->pluck('entity'), 'last_id' => $last_id))

@endsection