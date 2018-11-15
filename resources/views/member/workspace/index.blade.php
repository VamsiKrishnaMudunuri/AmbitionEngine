@extends('layouts.member')
@section('title', Translator::transSmart('app.Book Workspace', 'Book Workspace'))

@section('styles')
    @parent
    {{ Html::skin('widgets/bootstrap-datetimepicker/warm-theme.css') }}
    {{ Html::skin('app/modules/member/workspace/index.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/workspace/index.js') }}
@endsection

@section('content')
    <div class="member-workspace-index">

        @php


            $isWrite = Gate::allows(Utility::rights('write.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module]);

        @endphp



        <div class="row">

                <div class="col-sm-12">
                    <div class="section">
                        <div class="page-header">
                            <h3>{{Translator::transSmart('app.Book Workspace', 'Book Workspace')}}</h3>
                        </div>
                    </div>
                    <div class="section-space">

                    </div>

                </div>

        </div>

        <div class="row two-side">
            <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3 left-section">

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
                                    {{Translator::transSmart('app.No reservations. Book a workspace today!', 'No reservations. Book a workspace today!')}}
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
                                            <div class="tool">

                                                @can(Utility::rights('my.slug'), [$member_module_policy, $member_module_model, $member_module_slug, $member_module_module, $rs])

                                                    @if($rs->isAllowedToCancel())

                                                        {{ Form::open(array('route' => array('member::workspace::post-cancel', $rs->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to cancel?', 'Are you sure to cancel?') . '");'))}}
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
                                                                'data-message' =>  Translator::transSmart('app.You are not allowed to cancel this booking as it has already past.', 'You are not allowed to cancel this booking as it has already past.')
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
            <div class="col-xs-12 col-sm-7 col-md-8 col-lg-9 right-section">
                <div class="row">
                    <div class="col-xs-12">
                        {{ Html::success() }}
                        {{ Html::error() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="reservation" data-url="{{URL::route('member::workspace::index')}}" data-start-date="{{$property->exists ? $property->localDate($start_date) : ''}}">
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
                        @if($facilities->isEmpty())
                            <div class="alert alert-warning">
                                {{Translator::transSmart("app.We don't have any workspace for this date. Please adjust your search or check back soon!", "We don't have any workspace for this date. Please adjust your search or check back soon!")}}
                            </div>
                        @else
                            <ul class="card flex">
                                @foreach($facilities as $category => $categories)
                                    @foreach($categories as $facility)
                                        @php

                                          $price = $facility->prices->first();
                                          $reservation_date = $property->localDate($start_date);
                                          $reservation->syncFromProperty($facility->property);
                                          $reservation->syncFromCurrency($currency);
                                          $reservation->syncFromPrice($price);
                                          $reservation->setDiscountBasedOnSubscribingAnyFacilityOnlyForProperty($hasSubscribingAnyFacilityOnlyForProperty, $price);
                                          $reservation->setup($property, $start_date, $end_date);

                                          $unitsWithNoReserved = (!$facility->activeUnitsCountWithQuery->isEmpty()) ? $facility->activeUnitsCountWithQuery->first()->count : 0;

                                           $isInactive = ($reservation->grossPriceInCredits() <= 0 || !$facility->property->isActive() || $facility->property->coming_soon || !$facility->isActive() || !$price->isActive() || $unitsWithNoReserved <= 0) ? true : false;

                                          $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                          $sandbox->magicSubPath($config, [$facility->property->getKey()]);
                                          $mimes = join(',', $config['mimes']);
                                          $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                          $sm_dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                          $lg_dimension =  \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');

                                        @endphp
                                        <li class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                            <div class="item {{$isInactive || !$facility->isOpenBasedOnDayOfWeek($reservation_date->dayOfWeek) ? 'disabled' : ''}}">
                                                @if($isInactive || !$facility->isOpenBasedOnDayOfWeek($reservation_date->dayOfWeek))
                                                    <div class="layer"></div>
                                                @endif
                                                <div class="avatar">
                                                    <a href="javascript:void(0);"  data-url="{{$sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $lg_dimension, array(), null, true)}}" class="lightbox">
                                                        {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $sm_dimension, array('title' => $facility->name)) }}
                                                    </a>
                                                </div>
                                                <div class="content">

                                                    <div class="name">
                                                        {{$facility->name}}
                                                    </div>
                                                    <div class="address">
                                                        {{$facility->property->address}}
                                                    </div>
                                                    <div class="credit">
                                                        {{CLDR::showCredit($reservation->grossPriceInCredits())}}
                                                    </div>
                                                    <div class="capacity">
                                                        <span class="count">{{$unitsWithNoReserved}}</span>
                                                        <span class="available">{{Translator::transSmart('app.available', 'available')}}</span>
                                                    </div>

                                                </div>
                                                <div class="tool fluid">
                                                    @if($facility->isOpenBasedOnDayOfWeek($reservation_date->dayOfWeek))
                                                        {{Html::linkRoute(null, Translator::transSmart('app.Book', 'Book'), [], ['title' => Translator::transSmart('app.Book', 'Book'), 'class' => sprintf('book %s', (!$isWrite || $isInactive) ? ' disabled' : ''), 'data-url' => URL::route('member::workspace::book', ['property_id' => $facility->property->getKey(), 'facility_id' => $facility->id, 'start_date' => Crypt::encrypt($start_date), 'end_date' => Crypt::encrypt($end_date)])])}}
                                                    @else
                                                        {{Html::linkRoute(null, Translator::transSmart('app.Closed', 'Closed'), [], ['title' => Translator::transSmart('app.Closed', 'Closed'), 'class' => 'disabled'])}}
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection