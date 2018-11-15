@extends('layouts.plain')

@section('content')

    @if($facility_price->exists)

         @include('shortcodes.cms.packages.price', array('package_price' => $facility_price, 'template' => "5"))

    @else

        [cms-package-price type="{{$facility_price->type}}" country="{{$property->exists ? $property->country : config('dns.default')}}" template="4" /]

    @endif

@endsection