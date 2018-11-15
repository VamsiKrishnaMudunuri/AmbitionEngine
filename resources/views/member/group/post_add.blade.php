@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.group.dashboard', array('group' => $group, 'join' => $join))

@endsection