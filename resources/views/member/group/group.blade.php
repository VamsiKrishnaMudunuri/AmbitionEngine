@extends('layouts.member')
@section('title', Translator::transSmart('app.Groups - %s', sprintf('Groups - %s', $group->name), false, ['name' => $group->name]))

@section('styles')
    @parent
    {{ Html::skinForVendor('jquery-textext/all.css') }}
    {{ Html::skin('widgets/social-media/member/circle.css') }}
    {{ Html::skin('widgets/social-media/group/single.css') }}
    {{ Html::skin('widgets/social-media/post.css') }}
    {{ Html::skin('widgets/social-media/feed.css') }}
    {{ Html::skin('app/modules/member/group/group.css') }}
@endsection

@section('scripts')@parent
    {{ Html::skinForVendor('jquery-textext/all.js') }}
{{ Html::skin('widgets/social-media/share.js') }}
    {{ Html::skin('widgets/social-media/infinite.js') }}
    {{ Html::skin('widgets/social-media/infinite-more.js') }}
    {{ Html::skin('widgets/social-media/group/single.js') }}
    {{ Html::skin('app/modules/member/activity/join.js') }}
    {{ Html::skin('app/modules/member/activity/following.js') }}
    {{ Html::skin('widgets/social-media/post.js') }}
    {{ Html::skin('widgets/social-media/feed.js') }}
    {{ Html::skin('app/modules/member/group/group.js') }}
@endsection

@section('content')

    <div class="member-group-group">

        <div class="row">
            <div class="col-sm-4 col-sm-push-8 hidden-xs">

                @include('templates.widget.social_media.group.single', array('group' => $group, 'join' => $join))

                @php

                    $limit = mt_rand(5, 30);

                    $joins = $group->joins()->with(['user', 'user.profileSandboxWithQuery'])->orderBy($join->getKeyName(), 'Desc')->take($limit)->get();
                    $count =  $joins->count();
                    $total = $join->number($group);
                    $remaining = max(0, $total - $count );

                @endphp

                <div class="member-container">
                    <div class="member-info">
                        <div class="figure  {{$total > 0 ? '' : 'hide'}}" data-vertex-id="figure-{{$group->getKey()}}" data-vertex-layout="simple">
                            {{$join->text($group)['simple']}}
                        </div>
                        <div class="see">
                            <a href="javascript:void(0);" class="see-all-members" data-url="{{URL::route('member::activity::join-group-members', array($group->getKeyName() => $group->getKey()))}}">

                                {{Translator::transSmart('app.See All', 'See All')}}

                            </a>
                        </div>
                    </div>
                    @include('templates.widget.social_media.member.circle_layout', array('vertex' => $group, 'edges' => $joins , 'remaining' => $remaining, 'remaining_url' => URL::route('member::activity::join-group-members', array($group->getKeyName() => $group->getKey()))))
                </div>

                @if(Utility::hasArray($group->tags))
                    <div class="tag-container">
                        <div class="tags">
                            @foreach($group->tags as $tag)
                                <div class="tag">
                                    <span>{{$tag}}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="event-container">

                    <div class="headline">
                        <span class="title">
                            {{Translator::transSmart('app.Events', 'Events')}}
                        </span>
                        <span class="menu">
                            {{Html::linkRouteWithIcon(null, null, 'fa-plus', array(), array('title' => Translator::transSmart('app.Suggest an Event', 'Suggest an Event'), 'class' => 'add-new-group-event', 'data-url' => URL::route('member::post::add-group-event', array($post->group()->getForeignKey() => $group->getKey()))))}}
                        </span>
                    </div>

                    <div class="listing-container infinite-more" data-paging="{{$event->getPaging()}}" data-url="{{URL::route('member::post::group-event', array($event->group()->getForeignKey() => $group->getKey()))}}" data-empty-text="" data-more-text="{{Translator::transSmart('app.More', 'More')}}" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">

                        @foreach($events as $event)

                            @include('templates.member.post.event.single_group_event', array('post' => $event))

                        @endforeach

                    </div>


                </div>

            </div>

            <div class="col-sm-8 col-sm-pull-4">


                <div class="post-container">
                    @include('templates.widget.social_media.post_editor', array('route' => array('member::post::post-group', $group->getKey()), 'member' => $member, 'post' => $post, 'sandbox' => $sandbox, 'placeholder' => Translator::transSmart('app.Start a discussion.', 'Start a discussion.')))
                </div>


                <div class="new-feed-notification hide" data-type="{{Utility::constant('post_type.1.slug')}}" data-group-id="{{$group->getKey()}}">
                    <a href="javascript:void(0);" class="btn btn-theme" data-url="{{URL::route('member::post::new-group-feed', array($group->getKeyName() => $group->getKey()))}}" data-figure='0' data-new-text={{Translator::transSmart('app.new', 'new')}} data-singular-text="{{trans_choice('plural.post', intval(1))}}" data-plural-text="{{trans_choice('plural.post',2)}}" data-paging="{{$post->getPaging()}}" data-feed-id="">
                        <span class="text"></span>
                        <span class="icon">
                            <i class="fa fa-refresh"></i>
                        </span>
                    </a>
                </div>

                <div class="feed-container infinite" data-paging="{{$post->getPaging()}}" data-url="{{URL::route('member::post::group', array($group->getKeyName() => $group->getKey()))}}" data-empty-text="" data-ending-text="{{--Translator::transSmart('app.No More', 'No More')--}}">

                    @if($filteredPost->exists)

                        <div class="social-feed-section-title" id="filtered-post">
                            {{Translator::transSmart('app.Filtered Post', 'Filtered Post')}}
                        </div>

                        @include('templates.widget.social_media.feed', array('post' => $filteredPost))

                        <div class="social-feed-section-title" id="other-posts">
                            {{Translator::transSmart('app.Posts', 'Posts')}}
                        </div>

                    @endif

                    @foreach($posts as $post)

                        @include('templates.widget.social_media.feed', array('post' => $post))

                    @endforeach
                </div>


            </div>
        </div>


    </div>

@endsection