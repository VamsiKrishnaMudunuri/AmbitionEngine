@extends('layouts.member')
@section('title', Translator::transSmart('app.Events', 'Events'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/feed.css') }}
    {{ Html::skin('widgets/social-media/event/upcoming.css') }}
    {{ Html::skin('widgets/social-media/event/hottest.css') }}
    {{ Html::skin('app/modules/member/event/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/share.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
    {{ Html::skin('widgets/social-media/feed.js') }}
    {{ Html::skin('app/modules/member/event/index.js') }}
@endsection

@section('tab')
    <div class="tabs">

    </div>
    <div class="menus">

        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Suggest an Event', 'Suggest an Event'), null, array(), array('title' => Translator::transSmart('app.Suggest an Event', 'Suggest an Event'), 'class' => 'btn btn-theme add-new-post', 'data-url' => URL::route('member::post::add-event')))}}

    </div>
@endsection

@section('content')

    <div class="member-event-index">

        <div class="row">
            <div class="hidden-xs col-sm-5 col-md-4 col-sm-push-7 col-md-push-8">

                @include('templates.widget.social_media.event.hottest')
                @include('templates.widget.social_media.event.upcoming')

            </div>
            <div class="col-sm-7 col-md-8 col-sm-pull-5 col-md-pull-4">

                <div class="new-feed-notification hide" data-type="{{Utility::constant('post_type.2.slug')}}" data-group-id="">
                    <a href="javascript:void(0);" class="btn btn-theme" data-url="{{URL::route('member::post::new-event')}}" data-figure='0' data-new-text={{Translator::transSmart('app.new', 'new')}} data-singular-text="{{trans_choice('plural.event', intval(1))}}" data-plural-text="{{trans_choice('plural.event',2)}}" data-paging="{{$post->getPaging()}}" data-feed-id="">
                        <span class="text"></span>
                        <span class="icon">
                            <i class="fa fa-refresh"></i>
                        </span>
                    </a>
                </div>

                {{ Form::open(array('route' => array('member::event::index'), 'class' => 'form-search form-inline')) }}

                    <div class="form-group">
                        @php
                            $name = 'query';
                            $queryName = $name;
                            $translate = Translator::transSmart('app.Search Events (etc: name, category or tags)', 'Search Events (etc: name, category or tags)');
                        @endphp

                        {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name, 'placeholder' => $translate))}}
                      
                    </div>
    
                    <div class="form-group">
            
                        <?php
                        $field = 'property';
                        $name = $field;
                        $propertyName = $name;
                        $translate = Translator::transSmart('app.Location', 'Location');
                        ?>
            
                        {!! Form::select($name, $menu, Request::get($name), array('id' => $name, 'title' => $translate, 'class' => 'form-control', 'placeholder' => Translator::transSmart('app.All Events', 'All Events'))) !!}
        
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


                @php

                    $route_parameters = [];

                    if(Utility::hasString(Request::get($queryName))){
                      $route_parameters = [$queryName => Request::get($queryName)];
                    }
                
                    if(Utility::hasString(Request::get($propertyName))){
                    	$route_parameters[$propertyName] = Request::get($propertyName);
                    }

                @endphp

                <div class="feed-container infinite" data-paging="{{$post->getPaging()}}" data-url="{{URL::route('member::post::event', $route_parameters)}}" data-empty-text="" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">
                    @if($filteredPost->exists)

                        <div class="social-feed-section-title" id="filtered-post">
                         {{Translator::transSmart('app.Filtered Event', 'Filtered Event')}}
                        </div>

                        @include('templates.widget.social_media.feed', array('post' => $filteredPost))

                        <div class="social-feed-section-title" id="other-posts">
                            {{Translator::transSmart('app.Events', 'Events')}}
                        </div>

                    @endif
                    @foreach($posts as $post)

                        @include('templates.widget.social_media.event', array('post' => $post, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox))

                     @endforeach
                </div>
            </div>

        </div>


    </div>

@endsection