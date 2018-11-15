@extends('layouts.home')

@section('content')
    <div class="page-index">
        <div class="row">
            <div class="col-md-5 p-l-15 p-l-md-0">
                <div class="headline">
                    <h3 class="f-2-em f-w-700">{{Translator::transSmart("app.Ambition Lives Here", "Ambition Lives Here")}}</h3>
                </div>

                <div class="description f-w-500 f-13 m-t-3-minus-full">
                    <div class="f-17 f-w-700">{{Translator::transSmart("app.Hot Desk | Private Office | Company Headquarters", "Hot Desk | Private Office | Company Headquarters")}}</div>
                    <div>{{Translator::transSmart("app.Whatever your office space needs, get access to a coworking community", "Whatever your office space needs, get access to a coworking community")}}</div>
                    <div>{{Translator::transSmart("app.and lifestyle to take your business to the next level", "and lifestyle to take your business to the next level")}}</div>
                </div>

                <div class="link">
                    {{ Form::open(array('route' => 'page::location::search-office', 'class' => 'form-inline header-form')) }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    @php
                                        $field = 'location';
                                        $name = sprintf('%s', $field);
                                        $translate = Translator::transSmart('app.Location', 'Location');
                                    @endphp
                                    {{
                                        Form::select($field, $temp->getPropertyMenuCountrySortByOccupancy(Cms::landingCCTLDDomain()), null, array('id' => $name, 'class' => 'form-control input-select change-btn-state', 'placeholder' => Translator::transSmart('app.Location', 'Location'), 'data-button-state' => 'button.btn-standup'))
                                    }}
                                </div>
                                {{--<div class="col-md-6">--}}
                                {{--@php--}}
                                {{--$field = 'pax';--}}
                                {{--$name = sprintf('%s', $field);--}}
                                {{--$translate = Translator::transSmart('app.Number of employees', 'Number of employees');--}}
                                {{--@endphp--}}
                                {{--{{--}}
                                {{--Form::select($field, array(--}}
                                {{--1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5,--}}
                                {{--6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10,--}}
                                {{--11 => '10+'--}}
                                {{--), null, array('id' => $field, 'class' => 'form-control input-select', 'title' => $translate, 'placeholder' => $translate))--}}
                                {{--}}--}}
                                {{--</div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="row m-t-20">
                        <div class="col-md-12">
                            <div class="form-group pull-right">
                                <button class="btn btn-theme btn-standup" disabled>
                                    {{ Translator::transSmart('app.Choose a Location', 'Choose a Location') }}
                                </button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
            <div class="col-md-5 pull-right">
                <div class="form-container page-subscribe-form page-book-tour" style=" ">

                    {{--<div class="text-black f-16">--}}
                        {{--{{ Translator::transSmart("app.Experience Common Ground in person. Schedule a free tour today.", "Experience Common Ground in person. Schedule a free tour today.") }}--}}
                    {{--</div>--}}

                    {{--<br/>--}}

                    {{ Form::open(array('route' => array('page::post-booking-tour'))) }}

                    <div class="message-box"></div>

                    {{Form::hidden('type', $booking->type)}}

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                @php
                                    $field = 'name';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.Full Name', 'Full Name');
                                @endphp
                                {{Html::validation($booking, $field)}}
                                {{Form::text($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                @php
                                    $field = 'email';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.E-mail Address', 'E-mail Address');
                                @endphp
                                {{Html::validation($booking, $field)}}
                                {{Form::email($field, $booking->getAttribute($field), array('class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @php
                            $field1 = 'contact_country_code';
                            $name1 = sprintf('%s',  $field1);
                            $translate1 = Translator::transSmart('app.Country Code', 'Country Code');


                            $field2 = 'contact_number';
                            $name2 = sprintf('%s',  $field2);
                            $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                        @endphp
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                               {{ Html::validation($booking, $field1) }}
                                {{Form::select($name1, CLDR::getPhoneCountryCodes() , $booking->getAttribute($field1), array('id' => $name1, 'class' => 'form-control input-select', 'title' => $translate1, 'placeholder' => $translate1))}}
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                {{ Html::validation($booking, $field1) }}
                                {{Form::text($name2, $booking->getAttribute($field2) , array('id' => $name2, 'class' => 'form-control integer-value', 'maxlength' => $booking->getMaxRuleValue($field2), 'title' => $translate2, 'placeholder' => $translate2 ))}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                @php
                                    $field = 'company';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.Company Name', 'Company Name');
                                @endphp
                                {{Html::validation($booking, $field)}}
                                {{Form::text($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="row hide">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="form-group">

                                @php
                                    $field = 'location';
                                    $field1 = 'location_slug';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.CHOOSE A LOCATION', 'CHOOSE A LOCATION');
                                @endphp

                                {{Html::validation($booking, $field)}}

                                {{Form::select($field, [$property->getKey() => $property->smart_name], $property->getKey() , array('id' => $name, 'class' => 'form-control page-booking-location'))}}

                            </div>
                        </div>
                    </div>

                    <div class="row hide">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">

                                @php
                                    $field = 'pax';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.I Need a Space For', 'I Need a Space For');
                                @endphp

                                {{Html::validation($booking, $field)}}

                                {{Form::select($field, array(
                                1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5,
                                6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10,
                                11 => '10+'
                                ), $booking->getAttribute($field), array('id' => $field, 'class' => 'form-control page-booking-package-pax', 'title' => $translate, 'placeholder' => $translate))}}

                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="form-group">
                                @php
                                    $field = 'office';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.Membership Type', 'Membership Type');
                                @endphp
                                {{Html::validation($booking, $field)}}


                                {{Form::select($field, Utility::constant('package', true, array(Utility::constant('package.prime-member.slug'))), $booking->getAttribute($field), array('id' => $field, 'class' => 'form-control page-booking-package', 'title' => $translate, 'placeholder' =>  $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="row hide">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                @php
                                    $field = 'schedule';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.Appointment', 'Appointment');
                                @endphp

                                {{Html::validation($booking, $field)}}
                                <div class="input-group schedule">

                                    @php
                                        $schedule = $booking->localToDate($property, $booking->schedule);
                                    @endphp

                                    {{Form::text($name, $schedule , array('id' => $name, 'class' => 'form-control date-time-picker-property', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => $translate))}}
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>

                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                @php
                                    $field = 'request';
                                    $name = sprintf('%s', $field);
                                    $translate = Translator::transSmart('app.Business Needs', 'Business Needs');
                                @endphp

                                {{Html::validation($booking, $field)}}
                                {{Form::textarea($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate, 'placeholder' => $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <a href="javascript:void(0);" class="btn btn-block btn-theme input-submit" title="submit" data-should-redirect="{{ route('page::booking-tour-thank-you') }}">
                                Submit
                            </a>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection