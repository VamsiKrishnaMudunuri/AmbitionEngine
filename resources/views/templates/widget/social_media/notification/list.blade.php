
@if($notification->news instanceof \App\Models\MongoDB\Activity)
    @php

        $activity = $notification->news;
        $activity->setRelation('sender', $notification->news->sender);
        $activity->setRelation('receiver', $notification->news->receiver);
        $activity->setRelation('action', $notification->news->action);
        $activity->setRelation('edge', $notification->news->edge);
        $message =  $activity->attractiveText(false, array(Utility::constant('activity_type.13.slug') => 2));
        $url = ($activity->target_url) ? $activity->target_url : "";

    @endphp

    @if($message)

        <li class="notification-feed {{$notification->is_read ? 'read' : ''}}" data-notification-id="{{$notification->getKey()}}">
            <a class="notification-feed-link" href="{{($url) ? URL::route(Domain::route('member::notification::link', 'member'), array($notification->getKeyName() => $notification->getKey(), 'url' => $url)) : 'javascript:void(0);'}}">
                <div class="profile">
                    <div class="profile-photo">
                        <div class="frame">
                            @php
                                $publisher = $notification->news->sender;
                            @endphp

                            @php
                                $config = \Illuminate\Support\Arr::get(\App\Models\User::$sandbox, 'image.profile');
                                $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                            @endphp

                            {{ \App\Models\Sandbox::s3()->link($publisher->profileSandboxWithQuery, $publisher, $config, $dimension)}}

                        </div>
                    </div>
                    <div class="details">
                        <div class="message">
                           {!! $message !!}
                        </div>
                        <div class="time">

                            {{CLDR::showRelativeDateTime($activity->getAttribute($activity->getCreatedAtColumn()), config('social_media.datetime.datetime.short.format')  )}}

                        </div>
                    </div>
                </div>
            </a>
        </li>

    @endif

@endif