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
</div>