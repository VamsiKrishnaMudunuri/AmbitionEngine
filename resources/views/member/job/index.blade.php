@extends('layouts.member')
@section('title', Translator::transSmart('app.Jobs Board', 'Jobs Board'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/job/dashboard.css') }}
    {{ Html::skin('app/modules/member/job/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/job/dashboard.js') }}
    {{ Html::skin('app/modules/member/job/index.js') }}
@endsection

@section('tab')
    <div class="tabs">
        <ul>
            <li></li>
        </ul>
    </div>
    <div class="menus">
        @if(Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]))
            {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Post Job', 'Post Job'), null, array(), array('title' => Translator::transSmart('app.Post Job', 'Post Job'), 'class' => 'btn btn-theme add-job', 'data-url' => URL::route('member::job::add')))}}
        @endif
    </div>
@endsection

@section('content')

    <div class="member-job-index">

        <div class="row">
            <div class="col-sm-12">
                {{ Form::open(array('route' => array('member::job::index'), 'class' => 'form-search')) }}


                    <div class="form-group">
                        @php
                            $name = 'query';
                            $queryName = $name;
                            $translate = Translator::transSmart('app.Search Jobs (etc: Company, Job Title, Skills or Location)', 'Search Jobs (etc: Company, Job Title, Skills or Location)');
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

                <div class="job-container infinite" data-paging="{{$job->getPaging()}}" data-url="{{URL::route('member::job::feed', $route_parameters)}}"  data-empty-text="{{Translator::transSmart('app.Not have job', 'Not have job')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">
                    @foreach($jobs as $job)

                        @include('templates.widget.social_media.job.dashboard', array('job' => $job))

                    @endforeach
                </div>
            </div>
        </div>

    </div>

@endsection