@php
    $isNeedSchedule = ($booking->type > 0);
@endphp

@extends('layouts.page')
@section('title', $property->smart_name)
@section('description', $property->metaWithQuery->description)
@section('keywords', $property->metaWithQuery->keywords)

@section('link')
    @parent
@endsection

@php

    $hasContent = $property->hasContent();
    $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.profile'));
    $mimes = join(',', $config['mimes']);
    $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.md.slug');

@endphp

@section('og:title',  $property->smart_name)
@section('og:type',  'article')
@section('og:url', URL::route('page::location::country::state::office-home', ['country' => $country, 'state' => $state, 'slug' => $slug]))
@section('og:image', $sandbox::s3()->link($property->profilesSandboxWithQuery->first(), $property, $config, $dimension, array(), null, true))
@section('og:description', $property->metaWithQuery->description)

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/locations/property.css') }}
@endsection

@section('scripts')
    @parent

    {{ Html::skin('app/modules/page/locations/property.js') }}

    <script>

        $(function() {

            var offHours = {!! Utility::jsonEncode($booking->defaultOffHours) !!};
            var onHours = {!! Utility::jsonEncode($booking->defaultOnHours) !!};
            var offDays = {!! Utility::jsonEncode($booking->defaultOffDays) !!};
            var onDays = {!! Utility::jsonEncode($booking->defaultOnDays) !!};

            var _default = {
                minuteStep: 15,
                pickerPosition: "top-left",
                daysOfWeekDisabled: offDays,
                todayBtn: true,
                datesDisabled: []
            };

            var CountryHolidayDate = {
                'my' : {!!  Utility::jsonEncode($booking->defaultCountryHolidayDate) !!},
                'ph' : {!!  Utility::jsonEncode($booking->phCountryHolidayDate) !!}
            }
    
    
            @if(strcasecmp($property->country, $booking->phCountry) == 0)

                _default['datesDisabled'] = CountryHolidayDate['ph'];
    
            @else

                _default['datesDisabled'] = CountryHolidayDate['my'];
    
            @endif
            

            @if(isset($booking->start_date))
                _default['startDate'] = "{{$booking->localToDate($property, $booking->start_date)}}";
            @endif

            @if(isset($booking->initial_date))
                _default['initialDate'] = "{{$booking->localToDate($property, $booking->initial_date)}}";
            @endif

            @if($isNeedSchedule)

                var $datePicker = $(".date-time-picker-property");
                var $page_booking_location = $('.page-booking-location');

                var $datepicker = widget.dateTimePicker(_default, $datePicker).datetimepicker('setHoursDisabled', offHours);
            
            @endif

            widget.integerValueOnly();

        });

    </script>
@endsection

@section('carousel')

    <div id="myCarousel" class="carousel slide {{$property->coming_soon ? 'coming-soon' : ''}}" data-ride="carousel">
        <div class="layer"></div>


        @if($property->coming_soon)

            <div class="message-box">
                <div>

                    <h2>
                        {{Translator::transSmart('app.COMING SOON', 'COMING SOON')}}
                    </h2>

                </div>
            </div>

        @endif

        <!-- Indicators -->
        <ol class="carousel-indicators">


            @foreach($property->coversSandboxWithQuery as $key => $cover)
                <li data-target="#myCarousel" data-slide-to="{{$key}}" class="{{$key == 0 ? 'active' : ''}}"></li>
            @endforeach


        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">

            @php

                $config = $sandbox->configs(\Illuminate\Support\Arr::get($property::$sandbox, 'image.cover'));
                $mimes = join(',', $config['mimes']);
                $dimension =   \Illuminate\Support\Arr::get($config, 'dimension.lg.slug');

            @endphp

            @if($property->coversSandboxWithQuery->isEmpty())
                <div class="item active">
                    <div class='img-fluid'>
                        <div class='img-fluid-frame'>
                                {{ $sandbox::s3()->link($sandbox, $property, $config, $dimension)}}
                        </div>
                    </div>
                </div>
            @endif

            @foreach($property->coversSandboxWithQuery as $key => $cover)

                <div class="item {{$key == 0 ? 'active' : ''}}">
                    <div class='img-fluid'>
                        <div class='img-fluid-frame'>
                            {{ $sandbox::s3()->link($cover, $property, $config, $dimension)}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('container', 'container')

@section('content')

    <div class="page-location-property">

        <div class="office-container">

            <div class="office-box col-xs-12 col-sm-6 col-md-5 col-lg-4">
                <div class="box-content">
                    <div class="location">
                        <div class="l-country">

                            {{$property->state_slug_name}}, {{$property->country_slug_name}}

                        </div>
                        <div class="l-place">

                            {{$property->place}}

                        </div>

                        @if(Utility::hasString($property->building))
                            <div class="l-building hide">

                                    {{$property->building}}

                            </div>
                        @endif
                    </div>

                    <div class="form-container">

                        {{ Form::open(array('route' => array('page::post-booking'))) }}

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
                                <div class="col-xs-12 col-sm-12">
                                    <div class="form-group">

                                        @php
                                            $field1 = 'contact_country_code';
                                            $name1 = sprintf('%s',  $field1);
                                            $translate1 = Translator::transSmart('app.Country Code', 'Country Code');


                                            $field2 = 'contact_number';
                                            $name2 = sprintf('%s',  $field2);
                                            $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');

                                        @endphp

                                        {{Html::validation($booking, [$field1, $field2])}}


                                        <div class="input-group my-select-group">
                                            {{Form::select($name1, CLDR::getPhoneCountryCodes() , $booking->getAttribute($field1), array('id' => $name1, 'class' => 'form-control input-select  w-40-stretch', 'title' => $translate1, 'placeholder' => $translate1))}}
                                            {{Form::text($name2, $booking->getAttribute($field2) , array('id' => $name2, 'class' => 'form-control w-60-stretch integer-value', 'maxlength' => $booking->getMaxRuleValue($field2), 'title' => $translate2, 'placeholder' => $translate2 ))}}
                                        </div>


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

                            <div class="row">
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

                            @if($isNeedSchedule)
                                <div class="row">
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
                            @endif

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
                                    <div class="description">
                                        @if($booking->type)

                                            {{Translator::transSmart("app.Book a site visit today so we can tailor a tour specially for you.", "Book a site visit today so we can tailor a tour specially for you.")}}

                                        @else

                                            {{Translator::transSmart('app.Leave us your information and we’ll keep you posted on updates about the launch of this space.', 'Leave us your information and we’ll keep you posted on updates about the launch of this space.' )}}

                                        @endif
                                    </div>
                                </div>
                            </div>

                            @php

                                if($booking->type){
                                     $submit_text = Translator::transSmart('app.Book a Tour', 'Book a Tour');
                                }else{
                                      $submit_text = Translator::transSmart('app.Find Out More', 'Find Out More');
                                }

                            @endphp

                            <div class="row">
                                <div class="col-xs-12 col-sm-12">
                                    <a href="javascript:void(0);" class="btn btn-block btn-theme input-submit" title="{{$submit_text}}">
                                        {{$submit_text}}
                                    </a>
                                </div>
                            </div>

                        {{ Form::close() }}



                    </div>



                </div>
            </div>

            <div class="row">

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 first {{$property->coming_soon  && !$hasContent ? 'coming-soon' : ''}}">

                    @if($property->coming_soon &&  !$hasContent)
                        {{Translator::transSmart("app.This location will be opening soon. Give us your info and get the latest updates.", "This location will be opening soon. Give us your info and get the latest updates.")}}
                    @else
                        {!! $property->body  !!}
                    @endif


                </div>

                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                </div>
            </div>

        </div>

    </div>


@endsection
