@extends('layouts.plain')

@section('content')

    @foreach($members as $member)

        @include('templates.widget.social_media.businessopportunity.talent', array('member' => $member))

    @endforeach

@endsection