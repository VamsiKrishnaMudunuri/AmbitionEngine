@extends('layouts.plain')

@section('content')

    @include('templates.widget.social_media.businessopportunity.dashboard', array('business_opportunity' => $business_opportunity))

@endsection