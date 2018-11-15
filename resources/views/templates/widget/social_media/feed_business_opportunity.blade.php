@php

   $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $business_opportunity]);

@endphp

<div class="social-feed feed-recommendation" data-feed-id="{{$post->getKey()}}">
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

                    {{Html::linkRoute('member::businessopportunity::business-opportunity', $post->business_title, array($post->getKeyName() => $post->getKey()), array('title' => $post->business_title))}}

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

                    <i class="fa fa-fw fa-briefcase" title="{{Translator::transSmart('app.Business Opportunity - :name', sprintf('Business Opportunity - %s', $post->business_title), false, ['name' => $post->business_title])}}"></i>

                </div>
            </div>
            <div class="menu hide">

                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $post->getKey()),
                 'title' => Translator::transSmart('app.Menu', 'Menu')])}}
                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isWrite)
                        <li>

                        </li>
                    @endif

                </ul>
            </div>
        </div>
        <div class="message-container">
            <div class="business-opportunity" onclick="location.href='{{URL::route('member::businessopportunity::business-opportunity', array($post->getKeyname() => $post->getKey()))}}'">
                <div class="profile-photo hide">
                    <div class="frame">
                        <a href="javascript:void(0);">


                        </a>
                    </div>
                </div>
                <div class="details">

                    <div class="category">
                        {{Utility::constant(sprintf('business_opportunity_type.%s.name', $post->business_opportunity_type))}}
                    </div>

                    <div class="name">

                        {{$post->business_title}}

                    </div>

                    <div class="company_name">

                        {{$post->company_name}}

                    </div>
                    <div class="company_location">
                        @php
                            $location = $post->company_location;

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

                    <div class="message">
                        {!! $post->business_description !!}
                    </div>

                    @if(Utility::hasArray($post->business_opportunities))
                        <div class="tag-container">
                            <div class="tags">
                                @foreach($post->business_opportunities as $tag)
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
        <div class="activity hide">
            <div class="action">


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
    <div class="center hide">
        <div class="stats">

        </div>
    </div>
    <div class="bottom hide">

        <div class="comment-container">


            <div class="comments">

            </div>
            <div class="more hide">

            </div>
        </div>
    </div>

</div>

