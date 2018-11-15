@extends('layouts.modal')
@section('title', '')

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/property/event/view-event.css') }}
@endsection

@section('body')

    <div class="admin-managing-property-view-event" data-feed-id="{{$post->getKey()}}">

        <div class="top">
            <div class="profile">
                <div class="profile-photo">
                    <div class="frame">
                        <a href="javascript:void(0);">

                            @php
                                $config = \Illuminate\Support\Arr::get($post::$sandbox, 'image.gallery');
                                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');
                            @endphp

                            {{ \App\Models\Sandbox::s3()->link($post->galleriesSandboxWithQuery->first(), $post, $config, $dimension)}}

                        </a>
                    </div>
                </div>
                <div class="details">
                    <div class="name">
                        {{$post->name}}

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
                </div>
            </div>
            <div class="description">
                {{$post->message}}
            </div>
        </div>

    </div>


@endsection