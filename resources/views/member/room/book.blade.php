@extends('layouts.modal')
@section('title', Translator::transSmart('app.Book a Meeting Room', 'Book a Meeting Room'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/member/room/book.js') }}
@endsection

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/room/book.css') }}
@endsection

@section('fluid')

    <div class="member-workspace-book">

        <div class="row">

            <div class="col-sm-12">


                @php

                    $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                    $sandbox->magicSubPath($config, [$facility->property->getKey()]);
                    $mimes = join(',', $config['mimes']);
                    $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                    $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');

                @endphp

                @section('open-tag')
                    {{ Form::open(array('route' => array('member::room::post-book', $property->getKey(), $facility->getKey(), $facility_unit->getKey()), 'class' => 'form-grace member-room-booking-form'))}}
                @endsection

                @section('body')

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($reservation, 'csrf_error')}}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'duration';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Duration', 'Duration');
                                ?>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        {{Form::select($name, $duration, $facility->minutesInterval, array('id' => $field, 'class' => sprintf('form-control input-sm %s', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="show-off">
                                <div class="left-section">
                                   <div class="image-frame">
                                       <a href="javascript:void(0);">
                                           {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension, array('title' => $facility->name)) }}
                                       </a>
                                   </div>
                                </div>
                                <div class="right-section">
                                    <div class="facility">
                                        <div class="name">
                                            {{ $facility->name }}
                                        </div>
                                        <div class="blk">
                                            <span class="lbl">{{Translator::transSmart('app.Building', 'Building')}}</span>
                                            <span class="text">{{$facility->unit_number}}</span>
                                        </div>
                                        <div class="seat">
                                            <span class="lbl">{{Translator::transSmart('app.Label', 'Label')}}</span>
                                            <span class="text">{{$facility_unit->name}}</span>
                                        </div>
                                    </div>
                                    <div class="price">
                                        {{CLDR::showCredit($reservation->priceInclusiveOfTaxInCredits())}}/{{Translator::transSmart('app.hour', 'hour')}}
                                    </div>
                                    <div class="amenities">
                                        {{sprintf('%s %s | %s', $facility->seat, trans_choice('plural.seat', intval($facility->seat)), $facility->facilities)}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="order"
                                 data-reservation = "{{Utility::jsonEncode($reservation->toArray())}}"
                                 data-unit = "{{Config::get('wallet.unit')}}"
                                 data-credit="{{Translator::transSmart('app.credit', 'credit')}}|{{Translator::transSmart('app.credits', 'credits')}}"
                                 data-minute-interval="{{$facility->minutesInterval}}"
                                >
                                <div class="schedule" data-am="{{Translator::transSmart('app.AM', 'AM')}}" data-pm="{{Translator::transSmart('app.PM', 'PM')}}">
                                    <?php
                                        $field = 'start_date';
                                        $field1 = 'end_date';
                                        $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                        $name1 = sprintf('%s[%s]', $reservation->getTable(), $field1);
                                    ?>
                                    {{Html::validation($reservation, [$field, $field1])}}
                                    {{Form::hidden($name, $property->localDate($reservation->getAttribute($field)), array('class' => $field))}}
                                    {{Form::hidden($name1, $property->localDate($reservation->getAttribute($field1)), array('class' => $field1))}}

                                    <span class="start-date" data-date="{{$property->localDate($reservation->getAttribute($field))->format(config('database.datetime.date.format'))}}">
                                        {{CLDR::showDate($property->localDate($reservation->getAttribute($field)), config('app.datetime.date.format'))}}
                                    </span>
                                    <span>
                                        -
                                    </span>
                                    <span class="start-time" data-time="{{$property->localDate($reservation->getAttribute($field))->format(config('database.datetime.time.format'))}}">
                                        {{ CLDR::showTime($property->localDate($reservation->getAttribute($field)), config('app.datetime.time.format'), $property->timezone, $property->timezone)}}
                                    </span>
                                    <span>
                                        {{Translator::transSmart('app.to', 'to')}}
                                    </span>
                                    <span class="end-time" data-time="{{$property->localDate($reservation->getAttribute($field1))->format(config('database.datetime.time.format'))}}">
                                        {{ CLDR::showTime($property->localDate($reservation->getAttribute($field1)), config('app.datetime.time.format'), $property->timezone, $property->timezone)}}
                                    </span>

                                </div>
                                <div class="cost static-text" data-cost="{{$reservation->grossPriceInCredits()}}">
                                    <?php
                                    $field = 'gross_price_credit';
                                    $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                    $translate = Translator::transSmart('app.Reservation Cost', 'Reservation Cost');
                                    ?>

                                    <span class="lbl">{{$translate}}</span>
                                    <span class="text">{{CLDR::showCredit($reservation->grossPriceInCredits())}}</span>

                                </div>
                                <div class="complimentary static-text" data-complimentary="{{$subscription_complimentary->remaining()}}">
                                    <?php
                                    $field = 'complimentary_credit';
                                    $name = sprintf('%s[%s]', $subscription_complimentary->getTable(), $field);
                                    $translate = Translator::transSmart('app.Complimentary', 'Complimentary');
                                    ?>

                                    <span class="lbl">{{$translate}}</span>
                                    <span class="text">
                                        @if($subscription_complimentary->remaining() > 0)
                                            {{CLDR::showCredit($subscription_complimentary->remaining())}}
                                        @else
                                            {{CLDR::showNil()}}
                                        @endif
                                    </span>

                                </div>
                                <div class="charge static-text" data-charge="{{$reservation->grossPriceInCreditsIfNeedToApplySubscriptionComplimentary($subscription_complimentary)}}">
                                    <?php
                                    $field = 'charges';
                                    $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                    $translate = Translator::transSmart('app.Total Charges', 'Total Charges');
                                    ?>
                                    <span class="lbl">{{$translate}}</span>
                                    <span class="text">
                                        {{CLDR::showCredit($reservation->grossPriceInCreditsIfNeedToApplySubscriptionComplimentary($subscription_complimentary))}}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'remark';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Remark', 'Remark');

                                ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        {{Form::textarea($name, $reservation->remark , array('id' => $field, 'class' => 'form-control', 'maxlength' => $reservation->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = $reservation->wallet_key;
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Payment', 'Payment');
                                $translate1 = Translator::transSmart('app.Please select wallet to pay', 'Please select wallet to pay');
                                ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        {{Html::validation($reservation, $field)}}
                                        {{Form::select($name, $wallets , null, array('id' => $name, 'class' => sprintf('%s form-control input-sm', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                @endsection
                @section('footer')

                    <!--
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="help-block text-left">

                            </span>
                        </div>
                    </div>
                    -->

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-board"></div>
                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">
                                    @php
                                        $submit_text = Translator::transSmart('app.Confirm', 'Confirm');
                                    @endphp
                                    {{Html::linkRouteWithIcon(null, $submit_text, null, array(), array(
                                        'title' => $submit_text,
                                        'class' => 'btn btn-theme btn-block submit'
                                    ))}}
                                </div>
                                <div class="btn-group">
                                    {{Html::linkRouteWithIcon(null, Translator::transSmart('app.Cancel', 'Cancel'), null, array(), array(
                                           'title' =>  Translator::transSmart('app.Cancel', 'Cancel'),
                                           'class' => 'btn btn-theme btn-block cancel'
                                     ))}}
                                </div>
                            </div>
                        </div>
                    </div>

                @endsection

                @section('close-tag')
                    {{ Form::close() }}
                @endsection

            </div>

        </div>

    </div>

@endsection