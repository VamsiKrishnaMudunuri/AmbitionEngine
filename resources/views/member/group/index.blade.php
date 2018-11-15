@extends('layouts.member')
@section('title', Translator::transSmart('app.Discover Groups', 'Discover Groups'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/group/dashboard.css') }}
    {{ Html::skin('widgets/social-media/event/upcoming.css') }}
    {{ Html::skin('widgets/social-media/event/hottest.css') }}
    {{ Html::skin('app/modules/member/group/group-listing.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/group/dashboard.js') }}
    {{ Html::skin('app/modules/member/activity/join.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
    {{ Html::skin('app/modules/member/group/index.js') }}
@endsection

@section('tab')
    @include('templates.member.group.menu')
@endsection

@section('content')

    <div class="member-group-index">

        <div class="row">
            <div class="hidden-xs col-sm-5 col-md-4 col-sm-push-7 col-md-push-8">
                @include('templates.widget.social_media.event.hottest')
                @include('templates.widget.social_media.event.upcoming')
            </div>
            <div class="col-sm-7 col-md-8 col-sm-pull-5 col-md-pull-4">
                <div class="row">
                    <div class="col-sm-12">
                        {{ Form::open(array('route' => array('member::group::index'), 'class' => 'form-search form-inline')) }}

                             <div class="form-group">
                                @php
                                    $name = 'query';
                                    $queryName = $name;
                                    $translate = Translator::transSmart('app.Search Groups (etc: name, category or tags)', 'Search Groups (etc: name, category or tags)');
                                @endphp

                                     {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control query', 'title' => $name, 'placeholder' => $translate))}}

                             </div>
                             <div class="form-group">

                                <?php
                                $field = 'property';
                                $name = $field;
                                $propertyName = $name;
                                $translate = Translator::transSmart('app.Location', 'Location');
                                ?>

                                    {!! Form::select($name, $menu, Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control', 'placeholder' => Translator::transSmart('app.All Groups', 'All Groups'))) !!}

                             </div>
                             <div class="form-group">
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

                @php

                    $route_parameters = [];

                    if(Utility::hasString(Request::get($queryName))){
                      $route_parameters[$queryName] = Request::get($queryName);
                    }

                    if(Utility::hasString(Request::get($propertyName))){
                     $route_parameters[$propertyName] = Request::get($propertyName);
                    }

                @endphp
                <div class="group-container infinite" data-paging="{{$group->getPaging()}}" data-url="{{URL::route('member::group::discover-group', $route_parameters)}}"  data-empty-text="{{Translator::transSmart('app.Not have group', 'Not have group')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">
                    @foreach($groups as $group)

                        @include('templates.widget.social_media.group.dashboard', array('group' => $group, 'join' => $join))

                    @endforeach
                </div>
            </div>
        </div>


    </div>

@endsection