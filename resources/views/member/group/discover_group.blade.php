@extends('layouts.plain')

@section('content')

    @foreach($groups as $group)

        @include('templates.widget.social_media.group.dashboard', array('group' => $group, 'join' => $join))

    @endforeach

@endsection