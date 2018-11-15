@extends('layouts.plain')

@section('content')

    @foreach($jobs as $job)

        @include('templates.widget.social_media.job.dashboard', array('job' => $job))

    @endforeach

@endsection