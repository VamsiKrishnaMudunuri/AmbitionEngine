@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.member.card', array('following' => $following, 'auth_member' => $auth_member, 'profile_member' => $member, 'members' => $followers->pluck('followings'), 'last_id' => $last_follower_id))

@endsection