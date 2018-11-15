@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.company.card', array('repo' => $repo, 'auth_member' => $member, 'companies' => $repos->pluck('entity'), 'last_id' => $last_id))

@endsection