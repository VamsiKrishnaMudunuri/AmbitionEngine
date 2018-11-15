@php


    $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $comment]);



@endphp

<div class="social-comment comment" data-comment-id="{{$comment->getKey()}}">

        <div class="profile">
            <div class="profile-photo">
                <div class="frame">
                    @php
                        $publisher =  $comment->user;
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
                    <a href="javascript:void(0);" title="{{CLDR::showRelativeDateTime($comment->getAttribute($comment->getUpdatedAtColumn()), config('social_media.datetime.datetime.full.format'))}}">
                        {{CLDR::showRelativeDateTime($comment->getAttribute($comment->getUpdatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}
                    </a>

                </div>
                <div class="message-container">
                    <div class="message">
                        {!! $comment->message !!}
                    </div>
                </div>
            </div>
            <div class="menu">


                {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $comment->getKey()),
                'title' => Translator::transSmart('app.Menu', 'Menu')])}}

                <ul class="dropdown-menu dropdown-menu-right">

                    @if($isWrite)
                        <li>

                            {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit-comment',  'data-inline-loading' => sprintf('menu-%s', $comment->getKey()), 'data-url' => URL::route('member::post::edit-comment', array($comment->getKeyName() => $comment->getKey()))))}}


                        </li>
                        <li>

                            {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete-comment', 'data-inline-loading' => sprintf('menu-%s', $comment->getKey()), 'data-url' => URL::route('member::post::post-delete-comment', array($comment->getKeyName() => $comment->getKey()))))}}

                        </li>
                    @endif

                </ul>





            </div>
        </div>

</div>

