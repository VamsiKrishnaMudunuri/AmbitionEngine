@extends('layouts.member')
@section('title', Translator::transSmart('app.Events - %s', sprintf('Events - %s', $post->name), false, ['name' => $post->name]))
@section('description', $post->message)
@section('keywords', $post->keyword)

@php
    $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');
@endphp



@section('og:title',  $post->name)
@section('og:type',  'article')
@section('og:url', URL::route('member::event::event', array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug)))
@section('og:image', \App\Models\Sandbox::s3()->link((!$post->galleriesSandboxWithQuery->isEmpty()) ? $post->galleriesSandboxWithQuery->first() : $sandbox, $post, $config, $dimension, array(), null, true))
@section('og:description', $post->pure_message)


@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/feed.css') }}
    {{ Html::skin('app/modules/member/event/event.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
    {{ Html::skin('widgets/social-media/share.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('app/modules/member/activity/going.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
    {{ Html::skin('widgets/social-media/feed.js') }}
    {{ Html::skin('app/modules/member/event/event.js') }}

@endsection

@section('content')

    <div class="member-event-event">

        <div class="row">

            <div class="col-sm-4 col-sm-push-8 hidden-xs right-side-container">

                    @if(Utility::hasArray($post->tags))
                        <div class="tag-box">
                            <div class="tags">
                                @foreach($post->tags as $tag)
                                    <div class="tag">
                                        <span>{{$tag}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(Auth::check())

                        @php

                            $isWrite = false;
                            if(Auth::check()){
                                $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_model->metaWithQuery->slug, config('acl.member.event.event'), $post]);
                            }
                        @endphp

                        <div class="comment-box">
                            <div class="social-feed event" data-feed-id="{{$post->getKey()}}">
                                <div class="bottom">
                                    @php
                                        $limit = mt_rand($comment->minDisplayForFirstTime, $comment->maxDisplayForFirstTime);

                                        $comments = $post->comments()->orderBy($comment->getKeyName(), 'Desc')->take($limit)->get();
                                        $count =  $comments->count();
                                        $total = $post->stats['comments'];
                                        $remaining = max(0, $post->stats['comments'] - $count );
                                        $pluralText = trans_choice('plural.comment', intval($remaining));
                                        $lastCommentID = '';

                                        $moreText = Translator::transSmart('app.:View %s more %s', sprintf('View %s more %s', $remaining, $pluralText), false, array('figure' => $remaining, 'comment_text' => $pluralText))
                                    @endphp

                                    <div class="comment-container">

                                        @if( $isWrite )
                                            @include('templates.widget.social_media.comment_editor', array('route' => array('member::post::post-comment', $post->getKey()), 'member' => $member, 'post' => $post))
                                        @endif

                                        <div class="comments">
                                            @foreach($comments as $comment)
                                                @php
                                                    $lastCommentID = $comment->getKey();
                                                @endphp
                                                @include('templates.widget.social_media.comment', array('comment' => $comment))
                                            @endforeach
                                        </div>
                                        <div class="more {{$remaining > 0 ? '' : 'hide'}}">
                                            {{Html::linkRoute(null, $moreText, [], ['title' => $moreText, 'class' => 'more-comment', 'data-paging' => $comment->getPaging(), 'data-last-id' => $lastCommentID, 'data-total' => $total, 'data-offset' => $count, 'data-url' => URL::route('member::post::comment', array('id' => $post->getKey()))])}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

            </div>

            <div class="col-sm-8 col-sm-pull-4 left-side-container">

                <div class="row">
                    <div class="col-sm-5">
                        <div class="left-side">
                            <div class="top">
                                <div class="profile-photo">
                                    <div class="frame">
                                        <a href="{{URL::route('member::event::event', array($post->getKeyname() => $post->getKey(), 'slug' => $post->slug, ))}}">

                                            @php
                                                $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
                                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                            @endphp

                                            {{ \App\Models\Sandbox::s3()->link((!$post->galleriesSandboxWithQuery->isEmpty()) ? $post->galleriesSandboxWithQuery->first() : $sandbox, $post, $config, $dimension)}}

                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="bottom">
                                <div class="activity">

                                    @if(Auth::check())

                                        @php

                                            $isExpired = !$post->isOpen();

                                            $inviteAttributes = array('class' => 'btn btn-white invite', 'title' => Translator::transSmart('app.Invite', 'Invite'), 'data-url' => URL::route('member::activity::invite-event', array($post->getKeyName() => $post->getKey())));

                                            if($isExpired){

                                              $inviteAttributes['disabled'] = 'disabled';

                                            }

                                        @endphp

                                        {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Invite', 'Invite'), '', [], $inviteAttributes)}}

                                        @include('templates.member.activity.going_action', array('going_url' => Url::route('member::activity::post-going-event', ['id' => $post->getKey()]), 'leave_url' => Url::route('member::activity::post-delete-going-event', ['id' => $post->getKey()]), 'instance' => $post, 'is_already_going' => (isset($post->getRelations()['goings']) && !$post->goings->isEmpty()), 'is_expired' => $isExpired, 'policy' => $member_module_policy, 'model' => $member_module_model, 'slug' => $member_module_slug, 'module' => $member_module_module))

                                    @endif


                                </div>
                                <div class="divider">

                                </div>

                                @php

                                    $limit = mt_rand(2, 10);

                                    $goings = $post->goings()->with(['user', 'user.profileSandboxWithQuery'])->orderBy($going->getKeyName(), 'Desc')->take($limit)->get();
                                    $count =  $goings->count();
                                    $total = $going->number($post);
                                    $remaining = max(0, $total - $count );

                                @endphp


                                <div class="figure {{$total  <= 0 ? ' hide' : ''}}" data-vertex-id="figure-{{$post->getKey()}}" data-vertex-layout="long">

                                    @php
                                        $stats_going_text = $going->text($post)['long'];
                                    @endphp
                                    {{Html::linkRoute(null, $stats_going_text, [], ['title' => $stats_going_text, 'class' => 'stats-info stats-going see-all-members', 'data-url' => URL::route('member::activity::going-event-members', array($post->getKeyName() => $post->getKey()))])}}

                                </div>

                                @include('templates.widget.social_media.member.circle_layout', array('vertex' => $post, 'edges' => $goings, 'remaining' => $remaining, 'remaining_url' => URL::route('member::activity::going-event-members', array($post->getKeyName() => $post->getKey()))))

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="right-side">
                            <div class="name">
                                {{Html::linkRoute('member::event::event', $post->name, array( $post->getKeyName() => $post->getKey(), 'slug' => $post->slug), array('title' => $post->name))}}
                                <div class="category">
                                    {{$post->category}}
                                </div>
                            </div>

                            <div class="time">
                                @php
                                    $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
                                    $start_date = CLDR::showDate($post->start->copy()->setTimezone($post->timezone), config('app.datetime.date.format'));
                                    $end_date = CLDR::showDate($post->end->copy()->setTimezone($post->timezone), config('app.datetime.date.format'));
                                    $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
                                    $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
                                    $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);

                                    if(config('features.member.event.timezone')){
                                     $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);
                                    }else{
                                     $time = Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_time, $end_time), false, ['start_date' => $start_time, 'end_date' => $end_time]);
                                    }

                                @endphp
                                <div class="icon">
                                    <i class="fa fa-clock-o"></i>
                                </div>
                                <div class="text">
                                    <a href="javascript:void(0);" title="{{$date}}">
                                        {{$date}}
                                    </a>
                                    <br />
                                    <a href="javascript:void(0);" title="{{$time}}">
                                        {{$time}}
                                    </a>
                                </div>
                            </div>
                            <div class="location">
                                <div class="icon">

                                    <i class="fa fa-map-marker"></i>

                                </div>
                                <div class="place">
                                    @if($post->hostWithQuery)
                                        @if($post->hostWithQuery->name)
                                            <div class="first-layout">
                                                <div class="name">
                                                    {{$post->hostWithQuery->name}}
                                                </div>
                                                <div class="address">
                                                    {{$post->hostWithQuery->address}}
                                                </div>
                                            </div>
                                        @else
                                            <div class="second-layout">
                                                <div class="address">
                                                    {{$post->hostWithQuery->address}}
                                                </div>
                                            </div>
                                        @endif

                                    @else


                                    @endif

                                </div>


                            </div>
                            <div class="description">
                                <div class="title">
                                    {{Translator::transSmart('app.About', 'About')}}
                                </div>
                                <div class="content">
                                     {!! $post->message !!}
                                </div>
                            </div>

                            <div class="share">
                                <div class="title">
                                    {{Translator::transSmart('app.Share This Event', 'Share This Event')}}

                                </div>
                                <div class="social-links-share md">
                                    {!!
                                            Share::currentPage(sprintf('%s %s', $start_date, $post->name))
                                            ->facebook()
                                            ->twitter()
                                            ->googlePlus()
                                            ->linkedin($post->pure_message)
                                    !!}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>


    </div>

@endsection