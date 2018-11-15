@extends('layouts.page')
@section('title', sprintf('Locations - %s - %s', $country_name, $state_name))
@section('link')
   @parent
@endsection
@section('styles')
    @parent
    {{ Html::skin('app/modules/page/locations/all.css') }}
@endsection

{{--@section('breadcrumb')--}}
    {{--{{--}}

        {{--Html::breadcrumb(array(--}}
            {{--['page::index', Translator::transSmart('app.All Offices', 'All Offices'), ['slug' => 'locations'], ['title' => Translator::transSmart('app.All Offices', 'All offices')]],--}}
            {{--['page::location::country::state::office-state', $state_name, ['country' => $country, 'state' => $state], ['title' => $state_name]],--}}
        {{--))--}}

    {{--}}--}}
{{--@endsection--}}
@section('container', 'container-fluid')

@section('content')

    <div class="page-locations page-location-state location">

            @if($properties->isEmpty())

                <div class="content-container">

                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 empty">

                                <h1>{{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}</h1>

                            </div>
                        </div>
                    </div>

                </div>

            @else

                <div class="content-container">

                    <div class="container">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="page-header b-b-none">
                                    <h2 class="text-green">
                                        <b>
                                            {{ strtoupper($state_name) }}
                                        </b>
                                    </h2>
                                    <div class="text-muted">
                                        {{Translator::transSmart('app.There are more than %s coworking spaces in %s', sprintf('There are more than %s coworking spaces in %s', $properties->count(),  $state_name), false, ['count' => $properties->count(), 'state' => $state_name])}}

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-9 col-md-9 col-lg-9 large">
                                <ul class="office row-flex">
                                    @foreach($properties as $property)
                                        <li class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="thumb">

                                                @php

                                                    $image = $property->profilesSandboxWithQuery->first();
                                                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
                                                    $mimes = join(',', $config['mimes']);
                                                    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

                                                @endphp
                                                <div class="image-frame">
                                                    @if($property->coming_soon)
                                                        <div class="layer"></div>
                                                        <h3>
                                                            <b>
                                                                {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                                                            </b>
                                                        </h3>
                                                        {{ Html::skin('coming-soon.png', array('class' => 'coming-soon')) }}
                                                    @endif
                                                    <div class="image-frame-office">
                                                        {{ $sandbox::s3()->link($image, $property, $config, $dimension)}}
                                                    </div>
                                                </div>

                                                <div class="info">
                                                    <div class="caption">
                                                        <h5 style="color: #ccc">
                                                            {{$state_name}}, {{$property->country_slug_name}}
                                                        </h5>
                                                        <h3 class="text-green">
                                                            {{$property->place}}
                                                        </h3>
                                                    </div>

                                                    @if ($property->coming_soon)
                                                        Coming Soon...
                                                    @else

                                                        <br/>
                                                        <div class="feature">
                                                        @if($property->facilities->count() > 0)
                                                            <table class="table discount">
                                                                @foreach($property->facilities as $facility)
                                                                    <tr>
                                                                        <td>
                                                                            {{Utility::constant(sprintf('facility_category.%s.name', $facility->category))}}
                                                                        </td>
                                                                        <td>
                                                                            &nbsp;
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                               $selling_price = CLDR::showPrice($facility->min_spot_price, $property->currency, Config::get('money.precision'))
                                                                            @endphp

                                                                            <div class="price">
                                                                                {{$selling_price}}/{{Translator::transSmart('app.mo', 'mo')}}
                                                                            </div>

                                                                            <div class="condition">

                                                                            </div>

                                                                        </td>
                                                                    </tr>

                                                                @endforeach

                                                            </table>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="action">
                                                    @php
                                                        $moreInfo = Translator::transSmart("app.MORE INFO", "MORE INFO");
                                                    @endphp
                                                    @if(is_null($property->metaWithQuery))
                                                        <a href="javascript:void(0);" class="btn btn-green" title="{{$moreInfo}}">{{$moreInfo}}</a>
                                                    @else
                                                        {{Html::link($property->metaWithQuery->full_url, $moreInfo, ['class' => 'btn btn-green', 'title' => $moreInfo])}}
                                                    @endif

                                                </div>
                                            </div>
                                        </li>
                                    @endforeach


                                </ul>

                                <div class="pagination-container">
                                    @php
                                        $query_search_param = Utility::parseQueryParams();
                                    @endphp
                                    {!! $properties->appends($query_search_param)->render() !!}
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

                            </div>
                        </div>
                    </div>

                </div>

                <div class="map-container">
                    <div>

                        @php

                            foreach($properties as $key => $property){
                                if($key == 0){
                                    $map = Mapper::map($property->latitude, $property->longitude, ['zoom' => 9,
                                        'cluster' => false,
                                        'center' => true,
                                        'fullscreenControl' => false,
                                        'scrollWheelZoom' => false
                                        //'eventAfterLoad' => 'var latLng = new google.maps.LatLng(3.152109, 101.666041); maps[0].map.panTo(latLng);'
                                    ]);
                                }

                               $map->informationWindow($property->latitude, $property->longitude, (Utility::hasString($property->building) ? $property->building : $property->place), ['open' => true]);
                            }

                        @endphp



                        {!! $map->render() !!}


                    </div>
                </div>
            @endif


    </div>


@endsection

