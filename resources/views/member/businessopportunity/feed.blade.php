@extends('layouts.plain')

@section('content')

    @foreach($business_opportunities as $business_opportunity)

        @include('templates.widget.social_media.businessopportunity.dashboard', array('business_opportunity' => $business_opportunity))

    @endforeach

@endsection