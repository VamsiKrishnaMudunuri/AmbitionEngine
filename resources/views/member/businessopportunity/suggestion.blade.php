@extends('layouts.member')
@section('title', Translator::transSmart('app.Business Opportunities', 'Business Opportunities'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}

    {{ Html::skin('widgets/social-media/business-opportunity/dashboard.css') }}
    {{ Html::skin('app/modules/member/business-opportunity/listing.css') }}

@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}

    {{ Html::skin('widgets/social-media/business-opportunity/dashboard.js') }}
    {{ Html::skin('app/modules/member/business-opportunity/suggestion.js') }}
@endsection

@section('tab')
    @include('templates.member.businessopportunity.menu')
@endsection

@section('content')

    <div class="member-business-opportunity-suggestion">

        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('member::businessopportunity::suggestion'), 'class' => 'form-search')) }}


                    <div class="form-group">
                        @php
                            $name = 'query';
                            $queryName = $name;
                            $translate = Translator::transSmart('app.Search Business Opportunities (etc: Company, Business Title/Opportunities, or Location)', 'Search Business Opportunities (etc: Company, Business Title/Opportunities, or Location)');
                        @endphp

                        {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => $translate))}}
                        {{
                           Html::linkRouteWithIcon(
                               null,
                               Translator::transSmart('app.Search', 'Search'),
                               'fa-search',
                              array(),
                              [
                                  'title' => Translator::transSmart('app.Search', 'Search'),
                                  'class' => 'btn btn-theme search-btn',
                                  'onclick' => "$(this).closest('form').submit();"
                              ]
                           )
                       }}
                    </div>

                {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                @php

                    $route_parameters = [];

                    if(Utility::hasString(Request::get($queryName))){
                      $route_parameters = [$queryName => Request::get($queryName)];
                    }

                @endphp

                @php
                    $member_fan_page_link = URL::route(Domain::route('member::profile::index'), ['username' => $member_module_auth_user->username]);
                    $data_empty_text = Translator::transSmart('app.<span>Please fill-up business opportunities and tags at your <a href="%s">fan</a> page. So we can curate most relevant content for you.</span>',
                    sprintf('<span>Please fill-up business opportunities and tags at your <a href="%s">fan</a> page.<br/> So we can curate most relevant content for you.</span>', $member_fan_page_link), false, ['link' => $member_fan_page_link ]
                    );

                @endphp
                <div class="business-opportunity-container infinite" data-paging="{{$business_opportunity->getPaging()}}" data-url="{{URL::route('member::businessopportunity::feed-suggestion', $route_parameters)}}"  data-empty-text="{{$data_empty_text}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">
                    @foreach($business_opportunities as $business_opportunity)

                        @include('templates.widget.social_media.businessopportunity.dashboard', array('business_opportunity' => $business_opportunity))

                    @endforeach
                </div>
            </div>
        </div>

    </div>

@endsection