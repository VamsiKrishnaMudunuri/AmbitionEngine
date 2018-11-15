@php

 $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $post]);

@endphp
<div class="social-feed feed" data-feed-id="{{$post->getKey()}}">

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
                </div>
            </div>
            <div class="menu">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $post->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isWrite)
                        <li>
                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit-post', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()), 'data-url' => URL::route('member::post::edit-feed', array($post->getKeyName() => $post->getKey()))))}}
                        </li>
                        <li>
                           {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete post. Are you sure?', 'You are about to delete post. Are you sure?'), 'data-url' => URL::route('member::post::post-delete', array($post->getKeyName() => $post->getKey()))))}}
                        </li>
                    @endif

                </ul>
            </div>
        </div>
        <div class="message-container">
            <div class="message">
                {!! $post->message !!}
            </div>

            @php
                $photos = $post->getRandomGalleryPhotos();
            @endphp


            <div class="photos {{count($photos['photos']) > 0 ? 'has-photo' : ''}}" data-photos="{{Utility::jsonEncode($photos['photos'])}}" data-cells="{{$photos['layout']}}">

            </div>


        </div>
        <div class="activity">
            <div class="action">
                @php

                    $like_text = Translator::transSmart('app.Like', 'Like');
                    $like_delete_text = Translator::transSmart('app.Liked', 'Liked');
                    $class = 'edge-action';
                    $current_text = $like_text;

                    if(!$post->likes->isEmpty()){
                        $current_text = $like_delete_text;
                        $class .= ' active';
                    }

                @endphp

                {{Html::linkRoute(null, $current_text, [], ['title' => $current_text, 'class' => $class, 'data-edge-info' => 'stats-like', 'data-edge-text' =>  $like_text, 'data-edge-delete-text' => $like_delete_text, 'data-edge-url' => URL::route('member::activity::post-like-post', array('id' => $post->getKey())), 'data-edge-delete-url' =>  URL::route('member::activity::post-delete-like-post', array('id' => $post->getKey()))])}}

                {{Html::linkRoute(null, Translator::transSmart('app.Comment', 'Comment'), [], ['title' => Translator::transSmart('app.Comment', 'Comment'), 'class' => 'comment'])}}


            </div>
            @if(config('features.member.feed.location'))
                <div class="location">
                    @php
                        $location = ($post->place) ? $post->place->location : '';
                    @endphp
                    <span title="{{$location}}" class="{{Utility::hasString($location) ? '' : 'v-hidden'}}">
                        <i class="fa fa-map-marker"></i>
                        {{$location}}
                    </span>
                </div>
            @endif
        </div>
    </div>
    <div class="center {{$like->number($post) <= 0 ? ' hide' : ''}}">
        <div class="stats">

            @php
                $stats_like_text = $like->text($post)['long'];
            @endphp
            {{Html::linkRoute(null, $stats_like_text, [], ['title' => $stats_like_text, 'class' => 'stats-info stats-like', 'data-url' => URL::route('member::activity::like-post-members', array($post->getKeyName() => $post->getKey()))])}}
        </div>
    </div>
    <div class="bottom">
        @php
            $limit = mt_rand($comment->minDisplayForFirstTime, $comment->maxDisplayForFirstTime);

            $comments = $post->comments()->with(['user', 'user.profileSandboxWithQuery', 'user.work.company.metaWithQuery'])->orderBy($comment->getKeyName(), 'Desc')->take($limit)->get();
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

