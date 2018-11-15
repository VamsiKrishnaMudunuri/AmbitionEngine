@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.member.card', array('following' => $following, 'auth_member' => $auth_member, 'profile_member' => $member, 'members' => $followings->pluck('followers'), 'last_id' => $last_following_id))

@endsection