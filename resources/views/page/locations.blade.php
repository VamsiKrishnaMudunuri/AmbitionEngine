@extends('layouts.page')
@section('title', Translator::transSmart('app.Locations', 'Locations'))

@section('container', 'container')
@section('top_banner_image_url', URL::skin('packages/hot-desk/banner.jpg'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/locations/locations.css') }}
@endsection

@section('top_banner')
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="form-search location f-14">
                {{ Form::open(array('route' => 'page::location::search-office', 'class' => 'form-inline header-form')) }}
                    <div class="row">
                        <div class="col-md-8">
                            @php
                                $field = 'location';
                                $name = sprintf('%s', $field);
                                $translate = Translator::transSmart('app.Location', 'Location');
                            @endphp
                            {{
                                Form::select($field, $temp->getPropertyMenuCountrySortByOccupancy(Cms::landingCCTLDDomain()), null, array('id' => $name, 'class' => 'form-control input-select change-btn-state m-t-5', 'placeholder' => Translator::transSmart('app.Location', 'Location'), 'data-button-state' => '.btn-find-a-space'))
                            }}
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-theme btn-block btn-find-a-space" disabled>
                                {{ Translator::transSmart("Choose a Location", "Choose a Location") }}
                            </button>
                        </div>
                    </div>
                {{ Form::close() }}
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
@endsection

@section('content')

    <div class="page-locations">

        @php
            $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
            $mimes = join(',', $config['mimes']);
            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
        @endphp

        {{--<div class="row location-tab-container">--}}
            {{--<div class="col-md-3">--}}
            {{--</div>--}}
            {{--<div class="col-md-6" style="overflow: auto">--}}
                {{--<div>--}}
                    {{--<ul class="nav nav-tabs nav-justified location-tab">--}}
                        {{--@foreach ($location as $country)--}}
                            {{--@if ($country['active_status'])--}}
                                {{--<li class="{{ $loop->index == 0 ? 'active' : '' }}">--}}
                                    {{--<a data-toggle="tab" href="#{{ $country['name'] }}">{{ $country['name'] }}</a>--}}
                                {{--</li>--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                    {{--</ul>--}}
                    {{--<div class="tab-content">--}}
                        {{--@foreach($location as $country)--}}
                            {{--<div id="{{ $country['name'] }}"--}}
                                 {{--class="tab-pane in {{ $loop->index == 0 ? 'active' : '' }}">--}}
                                {{--<ul class="location-content-link">--}}
                                    {{--@foreach($country['states'] as $state)--}}
                                        {{--@if($state['state_model']->active_status)--}}
                                            {{--<li class="{{ $loop->index == 0 && $loop->parent->index == 0 ? 'active' : '' }}">--}}
                                                {{--@php--}}
                                                    {{--$title = $state['state_model']->convertFriendlyUrlToName($state['state_model']->state_slug);--}}
                                                {{--@endphp--}}
                                                {{--{{--}}
                                                    {{--Html::linkRoute(--}}
                                                        {{--'page::location::country::state::office-state',--}}
                                                        {{--$state['state_model']->state_slug_name,--}}
                                                        {{--['country' => $state['state_model']->country_slug_lower_case, 'state' => $state['state_model']->state_slug_lower_case],--}}
                                                        {{--['class' => 'caption-title']--}}
                                                    {{--)--}}
                                                {{--}}--}}
                                            {{--</li>--}}
                                        {{--@endif--}}
                                    {{--@endforeach--}}
                                {{--</ul>--}}
                            {{--</div>--}}
                        {{--@endforeach--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-3">--}}
            {{--</div>--}}
        {{--</div>--}}
        {{--<hr/>--}}


        <div class="row">
            <div class="col-md-12">
                <div class="header-section">
                    <div class="page-header b-b-none">
                        <h3 class="text-green">
                            <b>
                                {{ Translator::transSmart("app.Our Newest Spaces", "Our Newest Spaces") }}
                            </b>
                        </h3>
                    </div>
                </div>
                <div class="row">
                    @if ($newestSpaces->isNotEmpty())
                        @foreach ($newestSpaces as $location)
                            @if ($loop->iteration <= 3)

                                @php
                                    $image = $location->profilesSandboxWithQuery->first();
                                @endphp

                                <div class="col-sm-4 image-wrapper">
                                    <div class="thumbnail">
                                        <div class="responsive-img-container" style="height: 236px">
                                            <div class="responsive-img-inner">
                                                <div class="responsive-img-frame">
                                                    {{--<div class="clickable-img h-100-stretch" data-clickable-img="{{ $sandbox::s3()->link($image, $location, $config, $dimension, ['class' => 'responsive-img'], null, true) }}"></div>--}}
                                                    <a href="{{ (!$location->metaWithQuery) ? 'javascript:void(0);' : $location->metaWithQuery->full_url_with_current_root }}">
                                                        {{ $sandbox::s3()->link($image, $location, $config, $dimension, ['class' => 'responsive-img'], null) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="caption">
                                            <div>
                                                {{
                                                    Html::linkRoute(
                                                    'page::location::country::state::office-state',
                                                    $location->state_slug_name . ', ' . $location->country_slug_name,
                                                    ['country' => $location->country_slug_lower_case, 'state' => $location->state_slug_lower_case],
                                                    ['class' => 'caption-title']
                                                    )
                                                }}

                                            </div>
                                            <h4>
                                                
                                                @if($location->metaWithQuery)
                                                    {{ Html::link($location->metaWithQuery->full_url_with_current_root, $location->place, ['class' => 'text-green', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                @else
                                                    <a href="javascript:void(0);" class="text-green disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{$location->place}}</a>
                                                @endif

                                                @if ($location->building)
                                                    <br/>
        
                                                    @if($location->metaWithQuery)
                                                        {{ Html::link($location->metaWithQuery->full_url_with_current_root, $location->building, ['class' => 'text-green m-t-5 f-14', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                    @else
                                                        <a href="javascript:void(0);" class="text-green m-t-5 f-14 disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{$location->building}}</a>
                                                    @endif
                                                    
                                                @else
                                                    <br/>
                                                    <br/>
                                                @endif

                                            </h4>
                                            <div class="button-section m-t-10-full">
                                                @if($location->metaWithQuery)
                                                    {{ Html::link($location->metaWithQuery->full_url_with_current_root, Translator::transSmart("app.Book a Tour", "Book a Tour"), ['class' => 'btn btn-green btn-block', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                @else
                                                    <a href="javascript:void(0);" class="btn btn-green btn-block disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}</a>
                                                @endif
                                                
                                              
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="divider-row"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="header-section">
                    <div class="page-header b-b-none">
                        <h3 class="text-green">
                            <b>
                                {{ Translator::transSmart("app.Our Locations", "Our Locations") }}
                            </b>
                        </h3>
                    </div>
                </div>

                <div class="row">
                    @if ($popularLocations->isNotEmpty())
                        @foreach ($popularLocations as $location)
                                @php
                                    $image = $location->profilesSandboxWithQuery->first();
                                @endphp

                                <div class="col-sm-4 image-wrapper">
                                    <div class="thumbnail">
                                        <div class="responsive-img-container" style="height: 236px">
                                            <div class="responsive-img-inner">
                                                <div class="responsive-img-frame">
                                                    {{--<div class="clickable-img h-100-stretch" data-clickable-img="{{ $sandbox::s3()->link($image, $location, $config, $dimension, ['class' => 'responsive-img'], null, true) }}"></div>--}}
                                                    <a href="{{ (!$location->metaWithQuery) ? 'javascript:void(0);' : $location->metaWithQuery->full_url_with_current_root }}">
                                                        {{ $sandbox::s3()->link($image, $location, $config, $dimension, ['class' => 'responsive-img'], null) }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="caption">
                                            <div>
                                                {{
                                                    Html::linkRoute(
                                                    'page::location::country::state::office-state',
                                                    $location->state_slug_name . ', ' . $location->country_slug_name,
                                                    ['country' => $location->country_slug_lower_case, 'state' => $location->state_slug_lower_case],
                                                    ['class' => 'caption-title']
                                                    )
                                                }}
                                            </div>
                                            <h4>
    
                                                @if($location->metaWithQuery)
                                                    {{ Html::link($location->metaWithQuery->full_url_with_current_root, $location->place, ['class' => 'text-green', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                @else
                                                    <a href="javascript:void(0);" class="text-green disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{$location->place}}</a>
                                                @endif
                                                
                                                @if ($location->building)
                                                    <br/>
        
                                                    @if($location->metaWithQuery)
                                                        
                                                        {{ Html::link($location->metaWithQuery->full_url_with_current_root, $location->building, ['class' => 'text-green m-t-5 f-14', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
                                                        
                                                    @else
                                                        <a href="javascript:void(0);" class="text-green m-t-5 f-14 disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{$location->building}}</a>
                                                    @endif
                                                    
                                                  
                                                @else
                                                    <br/>
                                                    <br/>
                                                @endif

                                            </h4>
                                            <div class="button-section m-t-10-full">
    
                                                @if($location->metaWithQuery)
        
                                                    {{ Html::link($location->metaWithQuery->full_url_with_current_root, Translator::transSmart("app.Book a Tour", "Book a Tour"), ['class' => 'btn btn-green btn-block', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}
    
                                                @else
                                                    <a href="javascript:void(0);" class="btn btn-green btn-block disabled" title="{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}">{{Translator::transSmart("app.Book a Tour", "Book a Tour")}}</a>
                                                @endif
                                                
                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {{--@endif--}}
                        @endforeach
                    @endif
                </div>
            </div>
        </div>





        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="divider-row"></div>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="header-section">--}}
                    {{--<div class="page-header b-b-none">--}}
                        {{--<h3 class="text-green">--}}
                            {{--<b>--}}
                                {{--{{ Translator::transSmart("app.Coming Soon", "Coming Soon") }}--}}
                            {{--</b>--}}
                        {{--</h3>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="row">--}}
                    {{--@if ($comingSoon->isNotEmpty())--}}
                        {{--@foreach ($comingSoon as $location)--}}

                            {{--@php--}}
                                {{--$image = $location->profilesSandboxWithQuery->first();--}}
                            {{--@endphp--}}

                            {{--@if ($loop->iteration <= 3)--}}
                                {{--<div class="col-sm-4 image-wrapper">--}}
                                    {{--<div class="thumbnail">--}}
                                        {{--<div class="image-frame">--}}
                                            {{--{{ $sandbox::s3()->link($image, $location, $config, $dimension) }}--}}
                                        {{--</div>--}}
                                        {{--<div class="caption">--}}
                                            {{--<h4 class="text-green">--}}
                                                {{--{{ Html::link($location->metaWithQuery->full_url_with_current_root, $location->smart_name, ['class' => 'text-green', 'title' => Translator::transSmart("app.Book a Tour", "Book a Tour")]) }}--}}
                                            {{--</h4>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--@endif--}}
                        {{--@endforeach--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </div>
@endsection
