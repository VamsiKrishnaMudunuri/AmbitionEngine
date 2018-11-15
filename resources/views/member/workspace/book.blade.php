@extends('layouts.modal')
@section('title', Translator::transSmart('app.Book a Workspace', 'Book a Workspace'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/member/workspace/book.css') }}
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
                    {{ Form::open(array('route' => array('member::workspace::book', $property->getKey(), $facility->getKey(), Crypt::encrypt($start_date), Crypt::encrypt($end_date)), 'class' => 'form-horizontal form-grace member-workspace-booking-form'))}}
                @endsection

                @section('body')

                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($reservation, 'csrf_error')}}

                    <div class="row">
                        <div class="col-sm-2 left-section">

                            <div class="photo">
                                <div class="photo-frame lg">

                                    <a href="javascript:void(0);">
                                        {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension, array('title' => $facility->name)) }}
                                    </a>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-10 right-section">
                            <div class="form-group">
                                <?php
                                $field = 'start_date';
                                $field1 = 'end_date';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $name1 = sprintf('%s[%s]', $reservation->getTable(), $field1);
                                $translate = Translator::transSmart('app.Reservation Date', 'Reservation Date');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    {{Html::validation($reservation, [$field, $field1])}}
                                    <p class="form-control-static">
                                        {{
                                            sprintf('%s',
                                              CLDR::showDate($property->localDate($reservation->getAttribute($field)), config('app.datetime.date.format'))
                                             )
                                         }}
                                    </p>
                                    {{Form::hidden($name, $property->localDate($reservation->getAttribute($field)))}}
                                    {{Form::hidden($name1, $property->localDate($reservation->getAttribute($field1)))}}
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'name';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Facility Name', 'Facility Name');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'unit_number';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Building', 'Building');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>


                            <div class="form-group">
                                <?php
                                $field = 'seat';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Seat', 'Seat');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'gross_price_credit';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Reservation Cost', 'Reservation Cost');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">
                                        {{CLDR::showCredit($reservation->grossPriceInCredits())}}
                                    </p>
                                </div>
                            </div>


                            <div class="form-group">
                                <?php
                                $field = 'complimentary_credit';
                                $name = sprintf('%s[%s]', $subscription_complimentary->getTable(), $field);
                                $translate = Translator::transSmart('app.Complimentary', 'Complimentary');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">
                                        @if($subscription_complimentary->remaining() > 0)
                                            {{CLDR::showCredit($subscription_complimentary->remaining())}}
                                        @else
                                            {{CLDR::showNil()}}
                                        @endif

                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'charges';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Total Charges', 'Total Charges');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    <p class="form-control-static">
                                        {{CLDR::showCredit($reservation->grossPriceInCreditsIfNeedToApplySubscriptionComplimentary($subscription_complimentary))}}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = $reservation->wallet_key;
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Payment', 'Payment');
                                $translate1 = Translator::transSmart('app.Please select wallet to pay', 'Please select wallet to pay');
                                ?>
                                <label for="{{$name}}" class="col-sm-5 control-label">{{$translate}}</label>
                                <div class="col-sm-7">
                                    {{Html::validation($reservation, $field)}}
                                    {{Form::select($name, $wallets , null, array('id' => $name, 'class' => sprintf('%s form-control input-sm', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                                </div>
                            </div>


                        </div>
                    </div>
                @endsection
                @section('footer')

                    <div class="row">
                        <div class="col-sm-12">
                            <span class="help-block text-left">
                              {{Translator::transSmart('app.You may cancel this reservation within %s minutes from the time/date of creation. You can contact our staff if you insist to cancel this reservation after %s minutes.', sprintf('You may cancel this reservation within %s minutes from the time/date of creation. You can contact our staff if you insist to cancel this reservation after %s minutes.', config('reservation.cancel_interval'), config('reservation.cancel_interval')), false, ['minute1' => config('reservation.cancel_interval'), 'minute2' => config('reservation.cancel_interval')])}}
                            </span>
                        </div>
                    </div>
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