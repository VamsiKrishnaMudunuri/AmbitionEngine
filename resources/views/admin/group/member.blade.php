@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.member.list', array('following' => $following, 'auth_member' => $auth_member, 'members' => $edges->pluck('user'), 'last_id' => $last_id, 'type' => 'group', 'group' => $group))


@endsection