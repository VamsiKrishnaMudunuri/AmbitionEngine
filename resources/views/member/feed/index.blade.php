@extends('layouts.member')
@section('title', Translator::transSmart('app.Home', 'Home'))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/post.css') }}
    {{ Html::skin('widgets/social-media/feed.css') }}
    {{ Html::skin('widgets/social-media/event/upcoming.css') }}
    {{ Html::skin('widgets/social-media/event/hottest.css') }}
    {{ Html::skin('app/modules/member/feed/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/share.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
    {{ Html::skin('widgets/social-media/post.js') }}
    {{ Html::skin('widgets/social-media/feed.js') }}
    {{ Html::skin('app/modules/member/feed/index.js') }}
@endsection

@section('content')

    <div class="member-feed-index">

        <div class="row">
            <div class="hidden-xs col-sm-5 col-md-4 col-sm-push-7 col-md-push-8">

                @include('templates.widget.social_media.event.hottest')
                @include('templates.widget.social_media.event.upcoming')

            </div>
            <div class="col-sm-7 col-md-8 col-sm-pull-5 col-md-pull-4">

                <div class="post-container">
                    @include('templates.widget.social_media.post_editor', array('route' => array('member::post::post-feed'), 'member' => $member, 'post' => $post, 'sandbox' => $sandbox))
                </div>


                <div class="new-feed-notification hide" data-type="{{Utility::constant('post_type.0.slug')}}" data-group-id="">
                    <a href="javascript:void(0);" class="btn btn-theme" data-url="{{URL::route('member::post::new-feed')}}" data-figure='0' data-new-text={{Translator::transSmart('app.new', 'new')}} data-singular-text="{{trans_choice('plural.post', intval(1))}}" data-plural-text="{{trans_choice('plural.post',2)}}" data-paging="{{$post->getPaging()}}" data-feed-id="">
                        <span class="text"></span>
                        <span class="icon">
                            <i class="fa fa-refresh"></i>
                        </span>
                    </a>
                </div>

                {{ Form::open(array('route' => array('member::feed::index'), 'class' => 'form-search')) }}

                    <div class="form-group">

                        <div class="form-search-container">
                            @php
                                $name = 'query';
                                $queryName = $name;
                                $translate = Translator::transSmart('app.Search Feed (etc: country or office)', 'Search Feed (etc: country or office)');
                            @endphp

                            <div class="form-search-input-container twitter-typeahead-container">

                                {{Form::textarea($name, '', array('id' => $name, 'class' => 'form-control form-search-input', 'rows' => 2,  'data-location' => $feed_master_filter_menu->toJson(), 'data-query' => Request::get($name), 'title' => $name, 'placeholder' => $translate))}}

                            </div>

                            <div class="form-search-button-container">
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
                        </div>

                </div>

                {{ Form::close() }}


                @php

                    $route_parameters = [];

                    if(Utility::hasString(Request::get($queryName))){
                      $route_parameters = [$queryName => Request::get($queryName)];
                    }

                @endphp

                <div class="feed-container infinite" data-paging="{{$post->getPaging()}}" data-url="{{URL::route('member::post::feed', $route_parameters)}}" data-empty-text="{{Translator::transSmart('app.The more activities you do, the more posts you will see in Member Feed.', 'The more activities you do, the more posts you will see in Member Feed.')}}" data-ending-text="{{-- Translator::transSmart('app.No More', 'No More') --}}">
                    @if($filteredPost->exists)

                        <div class="social-feed-section-title" id="filtered-post">
                         {{Translator::transSmart('app.Filtered Post', 'Filtered Post')}}
                        </div>

                        @include('templates.widget.social_media.feed', array('post' => $filteredPost))

                        <div class="social-feed-section-title" id="other-posts">
                            {{Translator::transSmart('app.Posts', 'Posts')}}
                        </div>

                    @endif

                    @foreach($posts as $key => $p)


                        @if($p instanceOf $post)

                            @if(strcasecmp($p->type, Utility::constant('post_type.2.slug')) == 0)

                                @include('templates.widget.social_media.event_mix', array('post' => $p, 'comment' => $comment, 'going' => $going, 'sandbox' => $sandbox))

                            @else

                                @include('templates.widget.social_media.feed_mix', array('post' => $p, 'comment' => $comment, 'like' => $like))

                            @endif

                       @else

                            @if($p instanceOf $business_opportunity)

                                @include('templates.widget.social_media.feed_business_opportunity', array('post' => $p, 'business_opportunity' => $business_opportunity, 'comment' => $comment, 'like' => $like))


                            @endif

                       @endif




                     @endforeach
                </div>
            </div>

        </div>


    </div>

@endsection