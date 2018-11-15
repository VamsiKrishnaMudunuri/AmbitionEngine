@extends('layouts.plain')

@section('content')

    @foreach($companies as $company)

        @include('templates.widget.social_media.businessopportunity.company', array('company' => $company))

    @endforeach

@endsection