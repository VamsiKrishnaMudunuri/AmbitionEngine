@php

   $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $post]);

@endphp

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

<div class="social-feed event-mix" data-feed-id="{{$post->getKey()}}">
    <div class="top">
        <div class="profile">
            <div class="profile-photo">
                <div class="frame">
                    @php
                        $publisher =  $post->user;
                    @endphp
                    <a href="{{URL::route('member::member::profile::index', array('username' => $publisher->username))}}">

                        @php
                            $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                            $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                        @endphp

                        {{ \App\Models\Sandbox::s3()->link($publisher->profileSandboxWithQuery, $publisher, $config, $dimension)}}

                    </a>
                </div>
            </div>
            <div class="details">
                <div class="name">

                    {{Html::linkRoute('member::member::profile::index', $publisher->full_name, ['username' => $publisher->username], ['title' => $publisher->full_name])}}

                    <i class="fa fa-caret-right fa-fw"></i>

                    {{Html::linkRoute('member::event::event', $post->name, array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug), array('title' => $post->name))}}

                </div>
                @if(config('features.username'))
                    <div class="username">
                        {{Html::linkRoute('member::member::profile::index', $publisher->username_alias, ['username' => $publisher->username], ['title' => $publisher->username_alias])}}
                    </div>
                @endif
                <div class="company">
                    <span>{!! $publisher->smart_company_link !!}</span>
                </div>
                <div class="time">
                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($post->getAttribute($post->getCreatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                        {{CLDR::showRelativeDateTime($post->getAttribute($post->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                    </a>

                    <i class="fa fa-fw fa-send" title="{{Translator::transSmart('app.Members of :name', sprintf('Members of %s', $post->name), false, ['name' => $post->name])}}"></i>

                </div>
            </div>
            <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $post->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isWrite)

                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit-event', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()), 'data-url' => URL::route('member::post::post-edit-event-mix', array($post->getKeyName() => $post->getKey()))))}}
                        </li>
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete event. Are you sure?', 'You are about to delete event. Are you sure?'), 'data-url' => URL::route('member::post::post-delete', array($post->getKeyName() => $post->getKey()))))}}
                        </li>

                        <li role="separator" class="divider"></li>

                    @endif

                    <li class="share">


                        <div class="title">
                            {{Translator::transSmart('app.Share This Event', 'Share This Event')}}
                        </div>

                        <div class="social-media social-links-share sm">
                            {!!
                                    Share::page(URL::route('member::event::event', array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug) ), sprintf('%s %s', $start_date, $post->name))
                                    ->facebook()
                                    ->twitter()
                                    ->googlePlus()
                                    ->linkedin($post->pure_message)
                            !!}
                        </div>

                    </li>

                </ul>
            </div>
        </div>
        <div class="message-container">
            <div class="event" onclick="location.href='{{URL::route('member::event::event', array($post->getKeyName() => $post->getKey(), 'slug' => $post->slug))}}'">
                <div class="profile-photo">
                    <div class="frame">
                        <a href="javascript:void(0);">

                            @php
                                $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                            @endphp

                            {{ \App\Models\Sandbox::s3()->link((!$post->galleriesSandboxWithQuery->isEmpty()) ? $post->galleriesSandboxWithQuery->first() : $sandbox, $post, $config, $dimension)}}

                        </a>
                    </div>
                </div>
                <div class="details">
                    <div class="category">
                        {{$post->category}}
                    </div>
                    <div class="time">
                        <a href="javascript:void(0);" title="{{$date}}">
                          {{$date}}
                        </a>
                        <br />
                        <a href="javascript:void(0);" title="{{$time}}">
                            {{$time}}
                        </a>
                    </div>
                    <div class="place">
                        @php
                            $location = '';

                            if($post->hostWithQuery){
                                $location = $post->hostWithQuery->name_or_address;
                            }
                        @endphp
                        <a href="javascript:void(0);" title="{{$location}}">
                            <span>
                                <i class="fa fa-map-marker fa-lg"></i>
                            </span>
                            <span>
                                  {{$location}}
                            </span>
                        </a>

                    </div>
                    <div class="name">

                        {{ $post->name }}

                    </div>

                    <div class="message">
                        {!! $post->message !!}
                    </div>
                    @if(Utility::hasArray($post->tags))
                        <div class="tag-container">
                            <div class="tags">
                                @foreach($post->tags as $tag)
                                    <div class="tag">
                                        <span>{{$tag}}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
        <div class="activity">
            <div class="action">
                @php
                    $going_text = Translator::transSmart('app.Going', 'Going');
                    $going_delete_text = Translator::transSmart('app.Leave', 'Leave');
                    $class = 'edge-action';
                    $current_text = $going_text ;

                    if(!$post->goings->isEmpty()){
                        $current_text = $going_delete_text;
                        $class .= ' active';
                    }

                    $goingAttributes = ['title' => $current_text, 'class' => $class, 'data-edge-info' => 'stats-going', 'data-edge-text' =>  $going_text, 'data-edge-delete-text' => $going_delete_text, 'data-edge-url' => URL::route('member::activity::post-going-event', array('id' => $post->getKey())), 'data-edge-delete-url' =>  URL::route('member::activity::post-delete-going-event', array('id' => $post->getKey()))];

                    $inviteAttributes = array('title' => Translator::transSmart('app.Invite', 'Invite'),  'class' => 'invite', 'data-url' => URL::route('member::activity::invite-event', array($post->getKeyName() => $post->getKey())));

                    if(!$post->isOpen()){

                        $inviteAttributes['disabled'] = 'disabled';
                        if($post->goings->isEmpty()){
                         $goingAttributes['disabled'] = 'disabled';
                        }

                    }

                @endphp

                {{Html::linkRoute(null, $current_text, [], $goingAttributes)}}

                {{Html::linkRoute(null, Translator::transSmart('app.Comment', 'Comment'), [], ['title' => Translator::transSmart('app.Comment', 'Comment'), 'class' => 'comment'])}}

                {{Html::linkRoute(null, Translator::transSmart('app.Invite', 'Invite'), array(), $inviteAttributes)}}


            </div>
            <div class="location v-hidden">
                @php
                    $location = ($post->place) ? $post->place->location : '';
                @endphp
                <span title="{{$location}}" class="{{Utility::hasString($location) ? '' : 'v-hidden'}}">
                        <i class="fa fa-map-marker"></i>
                    {{$location}}
                    </span>
            </div>
        </div>
    </div>
    <div class="center {{$going->number($post) <= 0 ? ' hide' : ''}}">
        <div class="stats">

            @php
                $stats_going_text = $going->text($post)['long'];
            @endphp
            {{Html::linkRoute(null, $stats_going_text, [], ['title' => $stats_going_text, 'class' => 'stats-info stats-going', 'data-url' => URL::route('member::activity::going-event-members', array($post->getKeyName() => $post->getKey()))])}}
        </div>
    </div>
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

            @include('templates.widget.social_media.comment_editor', array('route' => array('member::post::post-comment', $post->getKey()), 'member' => $member, 'post' => $post))

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

