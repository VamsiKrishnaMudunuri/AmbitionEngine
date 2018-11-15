@php

    $isWrite = Gate::allows(Utility::rights('creator.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $post]);

@endphp

<div class="item" data-feed-id="{{$post->getKey()}}">
    <div class="top">
        <div class="profile-photo">
            <div class="frame">
                @php
                    $publisher =  $post->user;
                @endphp
                <a href="{{URL::route('member::event::event', array($post->getKeyname() => $post->getKey()))}}">

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
        <div class="time">
            @php

                $timezoneName =  CLDR::getTimezoneByCode($post->timezone, true);
                $start_date = CLDR::showDate($post->start->setTimezone($post->timezone), config('app.datetime.date.format'));
                $end_date = CLDR::showDate($post->end->setTimezone($post->timezone), config('app.datetime.date.format'));
                $start_time = CLDR::showTime($post->start, config('app.datetime.time.format'), $post->timezone);
                $end_time = CLDR::showTime($post->end, config('app.datetime.time.format'), $post->timezone);
                $date =  Translator::transSmart('app.%s to %s', sprintf('%s to %s', $start_date, $end_date), false, ['start_date' => $start_date, 'end_date' => $end_date]);
                $time = Translator::transSmart('app.%s to %s %s', sprintf('%s to %s %s', $start_time, $end_time,  $timezoneName), false, ['start_date' => $start_time, 'end_date' => $end_time, 'timezone' =>  $timezoneName]);
            @endphp
            {{$start_date}}
        </div>
        <div class="name">
            {{Html::linkRoute('member::event::event', $post->name, array($post->getKeyName() => $post->getKey()), array('title' => $post->name))}}
        </div>

    </div>
    <div class="menu">

        {{Html::linkRouteWithLRIcon(null, null, null, 'fa-chevron-down', [], ['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown', 'data-inline-loading-place' => sprintf('menu-%s', $post->getKey()),
         'title' => Translator::transSmart('app.Menu', 'Menu')])}}

        <ul class="dropdown-menu dropdown-menu-right">

            @if($isWrite)

                <li>
                    {{Html::linkRoute(null, Translator::transSmart('app.Edit', 'Edit'), array(), array('class' => 'edit-group-event', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()), 'data-url' => URL::route('member::post::post-edit-group-event', array($post->getKeyName() => $post->getKey()))))}}
                </li>
                <li>
                    {{Html::linkRoute(null, Translator::transSmart('app.Delete', 'Delete'), array(), array('class' => 'delete-group-event', 'data-inline-loading' => sprintf('menu-%s', $post->getKey()),  'data-confirm-message' => Translator::transSmart('app.You are about to delete event. Are you sure?', 'You are about to delete event. Are you sure?'), 'data-url' => URL::route('member::post::post-delete', array($post->getKeyName() => $post->getKey()))))}}
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