@extends('layouts.member')
@section('title', Translator::transSmart('app.Book Room', 'Book Room'))

@section('styles')
    @parent
    {{ Html::skin('widgets/bootstrap-datetimepicker/warm-theme.css') }}
    {{ Html::skin('app/modules/member/room/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('widgets/schedule.js') }}
    {{ Html::skin('app/modules/member/room/index.js') }}
@endsection

@section('content')
    <div class="member-room-index">

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]);

        @endphp


        <div class="row">

            <div class="col-sm-12">
                <div class="section">
                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Book A Meeting Room', 'Book A Meeting Room')}}</h3>
                    </div>
                </div>
                <div class="section-space">

                </div>

            </div>

        </div>

        <div class="row two-side">
            <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 left-section">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="calendar"></div>
                    </div>
                </div>

                @php
                    $resevation_history = $upcoming_reservation;
                @endphp
                <div class="row">
                    <div class="col-xs-12">
                        <div class="upcoming info-box">
                            <div class="title">
                                <span class="text">
                                    {{Translator::transSmart('app.Upcoming Reservations', 'Upcoming Reservations')}}
                                </span>
                                <span class="count">
                                    ({{$resevation_history->count()}})
                                </span>
                            </div>
                            <div class="toggle {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                {{Html::linkRoute(null, Translator::transSmart('app.Show', 'Show'), [], array('class' => 'toggle', 'data-show' => Translator::transSmart('app.Show', 'Show'), 'data-hide' => Translator::transSmart('app.Hide', 'Hide')))}}
                            </div>
                            <div class="content {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                @if($resevation_history->isEmpty())
                                    {{Translator::transSmart('app.No reservations. Book a meeting room today!', 'No reservations. Book a meeting room today!')}}
                                @else

                                    @foreach($resevation_history as $rs)

                                        @php
                                            $rs->setup($rs->property, $rs->start_date, $rs->end_date);

                                            $rs_start_date = CLDR::showDate($rs->property->localDate( $rs->start_date), 'full^');
                                            $rs_end_date = CLDR::showDate($rs->property->localDate( $rs->end_date ), 'full^');

                                            $rs_start_time = CLDR::showTime($rs->start_date,  config('app.datetime.time.format'), $rs->property->timezone);
                                            $rs_end_time = CLDR::showTime($rs->end_date,  config('app.datetime.time.format'), $rs->property->timezone);

                                            $rs_credit = $rs->grossPriceInGrossCredits();
                                        @endphp
                                        <div class="board">
                                            <div class="property">
                                                <div class="name">
                                                    {{$rs->property->smart_name}}
                                                </div>
                                                <div class="address">
                                                    {{$rs->property->address}}
                                                </div>
                                            </div>
                                            <div class="facility">
                                                <div class="name">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Workspace', 'Workspace')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->name}}
                                                    </span>

                                                </div>
                                                <div class="unit">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Building', 'Building')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->unit_number}}
                                                    </span>

                                                </div>

                                            </div>
                                            <div class="schedule">
                                                <div class="date">
                                                    {{$rs_start_date}}
                                                </div>
                                                <div class="time">
                                                    {{sprintf('%s - %s', $rs_start_time, $rs_end_time)}}
                                                </div>
                                                <div class="timezone">
                                                    {{CLDR::getTimezoneByCode($rs->property->timezone, true)}}
                                                </div>

                                            </div>
                                            <div class="credit">
                                                @if($rs_credit > 0)
                                                    {{CLDR::showCredit($rs_credit)}}
                                                @else
                                                    {{Translator::transSmart('app.Free', 'Free')}}
                                                @endif

                                            </div>
                                            <div class="tool">

                                                @can(Utility::rights('my.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $rs])

                                                    @if($rs->isAllowedToCancel())

                                                        @php
                                                           $cancelAlertText = $rs->generateRoomCancellationPolicyText(null, true, '\r\n');
                                                           $cancelAlertText .= Translator::transSmart('app.Are you sure to cancel?', 'Are you sure to cancel?');
                                                        @endphp

                                                        {{ Form::open(array('route' => array('member::room::post-cancel', $rs->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . $cancelAlertText . '");'))}}
                                                        {{ method_field('POST') }}


                                                        {{Html::linkRoute(null, Translator::transSmart('app.Cancel', 'Cancel'), [], [
                                                           'title' => Translator::transSmart('app.Cancel', 'Cancel'),
                                                           'class' => 'cancel',
                                                           'onclick' => '$(this).closest("form").submit(); return false;'

                                                         ])}}

                                                        {{ Form::close() }}

                                                    @else

                                                        <!--

                                                            @php

                                                                $attributes = array(
                                                                'class' => 'no-allow-cancel',
                                                                'title' => Translator::transSmart('app.Cancel', 'Cancel'),
                                                                'data-message' => Translator::transSmart('app.You are not allowed to cancel this booking as it has already past.', 'You are not allowed to cancel this booking as it has already past.')
                                                                );

                                                            @endphp

                                                            {{Html::linkRoute(null, Translator::transSmart('app.Cancel', 'Cancel'), [], $attributes)}}

                                                        -->

                                                    @endif

                                                @endcan

                                            </div>
                                        </div>

                                    @endforeach

                                @endif


                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $resevation_history = $past_reservation
                @endphp
                <div class="row">
                    <div class="col-xs-12">
                        <div class="upcoming info-box">
                            <div class="title">
                                <span class="text">
                                    {{Translator::transSmart('app.Past Reservations', 'Past Reservations')}}
                                </span>
                                <span class="count">
                                    ({{$resevation_history->count()}})
                                </span>
                            </div>
                            <div class="toggle {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                {{Html::linkRoute(null, Translator::transSmart('app.Show', 'Show'), [], array('class' => 'toggle', 'data-show' => Translator::transSmart('app.Show', 'Show'), 'data-hide' => Translator::transSmart('app.Hide', 'Hide')))}}
                            </div>
                            <div class="content {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                @if($resevation_history->isEmpty())

                                @else

                                    @foreach($resevation_history as $rs)

                                        @php
                                            $rs->setup($rs->property, $rs->start_date, $rs->end_date);

                                            $rs_start_date = CLDR::showDate($rs->property->localDate( $rs->start_date ), 'full^');
                                            $rs_end_date = CLDR::showDate($rs->property->localDate( $rs->end_date ), 'full^');

                                            $rs_start_time = CLDR::showTime($rs->start_date,  config('app.datetime.time.format'), $rs->property->timezone);
                                            $rs_end_time = CLDR::showTime($rs->end_date,  config('app.datetime.time.format'), $rs->property->timezone);

                                            $rs_credit = $rs->grossPriceInGrossCredits();
                                        @endphp
                                        <div class="board">
                                            <div class="property">
                                                <div class="name">
                                                    {{$rs->property->smart_name}}
                                                </div>
                                                <div class="address">
                                                    {{$rs->property->address}}
                                                </div>
                                            </div>
                                            <div class="facility">
                                                <div class="name">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Workspace', 'Workspace')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->name}}
                                                    </span>

                                                </div>
                                                <div class="unit">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Building', 'Building')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->unit_number}}
                                                    </span>

                                                </div>

                                            </div>
                                            <div class="schedule">
                                                <div class="date">
                                                    {{$rs_start_date}}
                                                </div>
                                                <div class="time">
                                                    {{sprintf('%s - %s', $rs_start_time, $rs_end_time)}}
                                                </div>
                                                <div class="timezone">
                                                    {{CLDR::getTimezoneByCode($rs->property->timezone, true)}}
                                                </div>

                                            </div>
                                            <div class="credit">
                                                @if($rs_credit > 0)
                                                    {{CLDR::showCredit($rs_credit)}}
                                                @else
                                                    {{Translator::transSmart('app.Free', 'Free')}}
                                                @endif

                                            </div>
                                        </div>

                                    @endforeach

                                @endif


                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $resevation_history = $cancelled_reservation
                @endphp
                <div class="row">
                    <div class="col-xs-12">
                        <div class="upcoming info-box">
                            <div class="title">
                                <span class="text">
                                    {{Translator::transSmart('app.Cancelled Reservations', 'Cancelled Reservations')}}
                                </span>
                                <span class="count">
                                    ({{$resevation_history->count()}})
                                </span>
                            </div>
                            <div class="toggle {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                {{Html::linkRoute(null, Translator::transSmart('app.Show', 'Show'), [], array('class' => 'toggle', 'data-show' => Translator::transSmart('app.Show', 'Show'), 'data-hide' => Translator::transSmart('app.Hide', 'Hide')))}}
                            </div>
                            <div class="content {{$resevation_history->count() <= 0 ? 'empty' : ''}}">
                                @if($resevation_history->isEmpty())

                                @else

                                    @foreach($resevation_history as $rs)

                                        @php
                                            $rs->setup($rs->property, $rs->start_date, $rs->end_date);

                                            $rs_start_date = CLDR::showDate($rs->property->localDate( $rs->start_date ), 'full^');
                                            $rs_end_date = CLDR::showDate($rs->property->localDate( $rs->end_date ), 'full^');

                                            $rs_start_time = CLDR::showTime($rs->start_date,  config('app.datetime.time.format'), $rs->property->timezone);
                                            $rs_end_time = CLDR::showTime($rs->end_date,  config('app.datetime.time.format'), $rs->property->timezone);

                                            $rs_credit = $rs->grossPriceInGrossCredits();

                                        @endphp
                                        <div class="board">
                                            <div class="property">
                                                <div class="name">
                                                    {{$rs->property->smart_name}}
                                                </div>
                                                <div class="address">
                                                    {{$rs->property->address}}
                                                </div>
                                            </div>
                                            <div class="facility">
                                                <div class="name">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Workspace', 'Workspace')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->name}}
                                                    </span>

                                                </div>
                                                <div class="unit">
                                                    <span class="title">
                                                        {{Translator::transSmart('app.Building', 'Building')}}
                                                    </span>
                                                    <span class="text">
                                                        {{$rs->facility->unit_number}}
                                                    </span>

                                                </div>

                                            </div>
                                            <div class="schedule">
                                                <div class="date">
                                                    {{$rs_start_date}}
                                                </div>
                                                <div class="time">
                                                    {{sprintf('%s - %s', $rs_start_time, $rs_end_time)}}
                                                </div>
                                                <div class="timezone">
                                                    {{CLDR::getTimezoneByCode($rs->property->timezone, true)}}
                                                </div>

                                            </div>
                                            <div class="credit">
                                                @if($rs_credit > 0)
                                                    {{CLDR::showCredit($rs_credit)}}
                                                @else
                                                    {{Translator::transSmart('app.Free', 'Free')}}
                                                @endif

                                            </div>
                                        </div>

                                    @endforeach

                                @endif


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8 col-lg-9 right-section">
                <div class="row">
                    <div class="col-xs-12">
                        {{ Html::success() }}
                        {{ Html::error() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="reservation" data-url="{{URL::route('member::room::index')}}"
                             data-start-date="{{$property->exists ? $property->localDate($start_date) : ''}}">
                            @if($property->exists)
                                @php
                                    $date = CLDR::showDate($property->localDate($start_date));
                                @endphp
                                {{Translator::transSmart('app.Available %s', sprintf('Available %s', $date), false, ['date' => $date])}}
                            @endif
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <div class="property_select">

                            <?php
                            $field = 'property_select_list';
                            $name = $field;
                            $translate = Translator::transSmart('app.Select A Location', 'Select A Location');
                            ?>
                            {{ Form::select($name, $menu, $property->getKey(), array('id' => $name, 'title' => $translate, 'class' => sprintf('form-control %s', $field), 'data-location-loading' => 'property-package', 'placeholder' => $translate )) }}

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        @php
                            $hasCompanyAccount = $acl->isRootRight() || $acl->isAnyCompanyAccount($user, $member_module_model);
                        @endphp
                        @if($facilities->isEmpty())
                            <div class="alert alert-warning">
                                {{Translator::transSmart("app.We don't have any meeting room for this date. Please adjust your search or check back soon!", "We don't have any meeting room for this date. Please adjust your search or check back soon!")}}
                            </div>
                        @else
                            @foreach($facilities as $category => $categories)
                                @foreach($categories as $unit => $units)
                                    @foreach($units as $facility)
                                        @foreach($facility->units as $gunit)
                                            @php

                                                $timeline_start_time = $reservation->timeline_start_time;
                                                $timeline_end_time = $reservation->timeline_end_time;

                                                $price = $facility->prices->first();
                                                $reservation_date = $property->localDate($start_date);
                                                $reservation->syncFromProperty($facility->property);
                                                $reservation->syncFromCurrency($currency);
                                                $reservation->syncFromPrice($price);
                                                $reservation->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, $price);
                                                $reservation->setup($property, $start_date, $end_date);

                                                 $unitsWithNoReserved = (!$facility->activeUnitsCountWithQuery->isEmpty()) ? $facility->activeUnitsCountWithQuery->first()->count : 0;

                                                $isInactive = ($reservation->grossPriceInCredits() <= 0 || !$facility->property->isActive() || $facility->property->coming_soon || !$facility->isActive() || !$gunit->isActive() || !$price->isActive() || !$isWrite || !$facility->isOpenBasedOnDayOfWeek($reservation_date->dayOfWeek)) ? true : false;

                                                  $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                                  $sandbox->magicSubPath($config, [$facility->property->getKey()]);
                                                  $mimes = join(',', $config['mimes']);
                                                  $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                                  $sm_dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                  $lg_dimension =  \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');

                                                  $image_sm_link = $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $sm_dimension, array(), null, true);

                                                  $image_lg_link = $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $lg_dimension, array(), null, true);


                                                  $schedule = array();

                                                  $option = array(
                                                    'active' => !$isInactive,
                                                    'opening_hours' => \Illuminate\Support\Arr::first($facility->getBusinessHourBasedOnDayOfWeek($reservation_date->dayOfWeek), null, array())
                                                  );



                                                  $timeline_current_time = $facility->property->today();

                                                  if($option['opening_hours']['status']){
                                                      $opening_time = $reservation_date->copy()->setTimeFromTimeString($option['opening_hours']['start']);

                                                      if($timeline_current_time->gte($opening_time)){
                                                           if($timeline_current_time->isSameDay($opening_time)){

                                                             $option['opening_hours']['start'] = $facility->property->localDate($timeline_current_time->copy()->subMinute($facility->minutesInterval))->format(config('database.datetime.time.format'));
                                                           }else{
                                                             $option['opening_hours']['status'] = false;
                                                           }
                                                      }
                                                  }

                                                  if($gunit->subscribing->count() > 0){

                                                       $schedule[] = array(
                                                        'start' => $timeline_start_time,
                                                        'end' => $timeline_end_time,
                                                        'title' => (!$hasCompanyAccount) ?  '' : sprintf('<div class="company">%s</div><div class="creator">%s: %s</div>', $gunit->subscribing->first()->user->company, Translator::transSmart('app.Created by', 'Created by'), $gunit->subscribing->first()->user->full_name),
                                                        'text' => (!$hasCompanyAccount) ?  '' : sprintf('<div class="company">%s</div>', $gunit->subscribing->first()->user->company),
                                                        'url' => ''
                                                       );

                                                        $arr['title'] = (!$hasCompanyAccount) ? '' : sprintf('%s | %s: %s', $confirmedReservation->user->company, Translator::transSmart('app.Created by', 'Created by'),  $confirmedReservation->user->full_name);


                                                  }else if($gunit->reserving->count() > 0){


                                                      $byToday = $reservation_date->copy()->format(config('database.datetime.date.format'));
                                                      $confirmedReservations = $gunit->getConfirmedReservation($gunit->getKey(), $property, sprintf('%s %s', $byToday, $timeline_start_time), sprintf('%s %s', $byToday, $timeline_end_time) );

                                                      foreach($confirmedReservations as $key => $confirmedReservation){


                                                        $arr = array();
                                                        $arr['start'] = $facility->property->localDate($confirmedReservation->start_date)->format(config('database.datetime.time.format'));
                                                        $arr['end'] = $facility->property->localDate($confirmedReservation->end_date)->format(config('database.datetime.time.format'));
                                                        $arr['title'] = (!$hasCompanyAccount) ? '' : sprintf('%s | %s: %s', $confirmedReservation->user->company, Translator::transSmart('app.Created by', 'Created by'),  $confirmedReservation->user->full_name);
                                                        $arr['text'] = (!$hasCompanyAccount) ? '' : sprintf('<div class="company">%s</div>', $confirmedReservation->user->company);

                                                        //sprintf('<div class="company">%s</div><div class="creator">%s: %s</div>', $confirmedReservation->user->company, Translator::transSmart('app.Created by', 'Created by'), $confirmedReservation->user->full_name);

                                                        $arr['url'] = '';

                                                         $schedule[] = $arr;

                                                      }

                                                  }


                                                  $data = array( 0 => array(
                                                        'image_sm' => $image_sm_link,
                                                        'image_lg' => $image_lg_link,
                                                        'image_title' => sprintf('%s.%s.%s', $facility->name, $unit, $gunit->name ),
                                                        'title' => sprintf('<span class="facility">%s</span><span class="separator">.</span><span class="unit">%s</span><span class="separator">.</span><span class="blk">%s</span>', $facility->name, $unit, $gunit->name),
                                                        'amenities' => sprintf('%s %s | %s', $facility->seat, trans_choice('plural.seat', intval($facility->seat)), $facility->facilities),
                                                        'url' => URL::route('member::room::book', array('property_id' => $facility->property->getKey(), 'facility_id' => $facility->getKey(), 'facility_unit_id' => $gunit->getKey())),
                                                        'schedule' =>  $schedule
                                                    )
                                                  );

                                            @endphp

                                            <div class="room" data-start-time="{{$timeline_start_time}}"
                                                 data-end-time="{{$timeline_end_time}}"
                                                 data-second="{{$facility->minutesInterval * 60}}"
                                                 data-option="{{Utility::jsonEncode($option)}}"
                                                 data-row="{{Utility::jsonEncode($data)}}"></div>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection