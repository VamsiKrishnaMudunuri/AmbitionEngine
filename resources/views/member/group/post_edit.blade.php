@extends('layouts.plain')

@section('content')

    @if(strcasecmp($view , 'single') == 0)
        @include('templates.widget.social_media.group.single', array('group' => $group, 'join' => $join))
    @else
        @include('templates.widget.social_media.group.dashboard', array('group' => $group, 'join' => $join))
    @endif
@endsection