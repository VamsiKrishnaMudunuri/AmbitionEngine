@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.job.dashboard', array('job' => $job))

@endsection