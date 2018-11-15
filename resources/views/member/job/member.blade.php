@extends('layouts.plain')

@section('content')

    @foreach($members as $member)

        @include('templates.widget.social_media.job.talent', array('member' => $member))

    @endforeach

@endsection