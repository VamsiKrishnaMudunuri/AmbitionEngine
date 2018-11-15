@php

    $is_for_all_site_visit_flag = (isset($is_all_site_visits) && $is_all_site_visits);
    if($is_modal){
      $isNeedSchedule =  ($is_for_all_site_visit_flag || $property->readyForSiteVisitBooking());
    }else{
      $isNeedSchedule = ($booking->type > 0);
    }


@endphp

@section('styles')
    @parent

@endsection

@section('scripts')
    @parent
    <script>


        var offHours = {!! Utility::jsonEncode($booking->defaultOffHours) !!};
        var onHours = {!! Utility::jsonEncode($booking->defaultOnHours) !!};
        var offDays = {!! Utility::jsonEncode($booking->defaultOffDays) !!};
        var onDays = {!! Utility::jsonEncode($booking->defaultOnDays) !!};

        var _default = {
            minuteStep : 15,
            pickerPosition: "top-left",
            daysOfWeekDisabled: offDays,
            todayBtn: true,
            datesDisabled: []
        };
        
        var CountryHolidayDate = {
            'my' : {!!  Utility::jsonEncode($booking->defaultCountryHolidayDate) !!},
            'ph' : {!!  Utility::jsonEncode($booking->phCountryHolidayDate) !!}
        }

        @if($is_modal)

            @if(strcasecmp($property->country, $booking->phCountry) == 0)
        
                _default['datesDisabled'] = CountryHolidayDate['ph'];

            @else
                
                _default['datesDisabled'] = CountryHolidayDate['my'];
                
            @endif
            
        @endif

        @if(isset($booking->start_date) && $is_need_disable_dates_before_today)

            _default['startDate'] = "{{$booking->localToDate($property, $booking->start_date)}}";

        @endif

        @if(isset($booking->initial_date))

            _default['initialDate'] = "{{$booking->localToDate($property, $booking->initial_date)}}";

        @endif

        @if($isNeedSchedule)

            var $datepicker = widget.dateTimePicker(_default).datetimepicker('setHoursDisabled', offHours);
        
            var $page_booking_location = $('.page-booking-location');
        
            $page_booking_location.change(function(event){
                
                event.preventDefault();
                var $this = $(this);
                var $option = $(this).find('option:selected')
                var group = $option.parent('optgroup');
                var val = $this.val();
                var countryAndCity = group.attr('label');
                @if($is_modal)
                    
                    if(/\bmalaysia\b/gi.test(countryAndCity)) {
                        
                         $datepicker.datetimepicker('setDatesDisabled', CountryHolidayDate['my']);
                     
                    }else if(/\bPhilippines\b/gi.test(countryAndCity)){
                        
                        $datepicker.datetimepicker('setDatesDisabled', CountryHolidayDate['ph']);
                        
                    }
                    
                @endif
                
            })

        @endif

        widget.integerValueOnly();

    </script>

@endsection

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($booking, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'site-visit-booking-form')) }}

    <div class="message-box"></div>

    {{Form::hidden('type', $booking->type)}}

    @if ($is_modal)
        {{Form::hidden('is_modal', 1)}}
    @endif

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="form-group {{!$is_modal ? 'required' : ''}}">
                @php
                    $field = 'name';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Full Name', 'Full Name');
                @endphp
                {{Html::validation($booking, $field)}}
                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif
                {{Form::text($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="form-group {{!$is_modal ? 'required' : ''}}">
                @php
                    $field = 'email';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Email', 'Email');
                @endphp
                {{Html::validation($booking, $field)}}
                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif
                {{Form::email($field, $booking->getAttribute($field), array('class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-12">
            <div class="form-group {{!$is_modal ? 'required' : ''}}">

                @php
                    $field = 'contact_country_code';
                    $name = sprintf('%s',  $field);
                    $translate1 = Translator::transSmart('app.Country Code', 'Country Code');
                    $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                @endphp

                {{Html::validation($booking, ['contact_country_code', 'contact_number'])}}

                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Contact', 'Contact')}}</label>
                @endif
                <div class="phone">

                    {{Form::select($name, CLDR::getPhoneCountryCodes() , $booking->getAttribute($field), array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                    <span>-</span>

                    @php
                        $field = 'contact_number';
                        $name = sprintf('%s',  $field);
                        $translate1 = Translator::transSmart('app.Phone Number', 'Phone Number');
                        $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                    @endphp

                    {{Form::text($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => (!$is_modal ? $translate2 : $translate2) ))}}

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
                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif
                {{Form::text($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group {{(isset($is_from_lead) && $is_from_lead) ? '' : (!$is_modal ? 'required' : '')}}">

                @php
                    $field = 'location';
                    $field1 = 'location_slug';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Choose a Location', 'Choose a Location');
                @endphp
                {{Html::validation($booking, $field)}}
                
                @if(isset($is_from_lead) && $is_from_lead)
                    
                    <label for="{{$name}}" class="control-label">{{Translator::transSmart('app.Location', 'Location')}}</label>
                    {{Form::select($field, ($temp->getPropertyMenuAll()), (!is_null($booking->property) && $booking->property->exists) ? $booking->property->getKey() : '', array('id' => $name, 'class' => 'form-control page-booking-location', 'placeholder' => Translator::transSmart('app.Location', 'Location')))}}
                @else
                    
                    @if(!$is_modal)
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    @endif
    
                    @if(!$is_modal)
                        @if($booking->isOldVersion())
    
                            {!! $booking->select($field, $translate, null, '', array(), $booking->getAttribute($field1))  !!}
    
                        @else
    
                            {{ Form::select($field, $temp->getPropertyMenuAll(), (!is_null($booking->property) && $booking->property->exists) ? $booking->property->getKey() : '', array('id' => $field, 'title' => $translate, 'class' => 'form-control page-booking-location')) }}
    
                        @endif
    
                    @else
    
                        @if($is_for_all_site_visit_flag)
    
                            @if(isset($show_property_by_country) && $show_property_by_country)

                                {{Form::select($field, ($temp->getPropertyMenuCountrySiteVisitAll(Cms::landingCCTLDDomain())), (!is_null($booking->property) && $booking->property->exists) ? $booking->property->getKey() : '', array('id' => $name, 'class' => 'form-control page-booking-location', 'placeholder' => Translator::transSmart('app.Location', 'Location')))}}
    
    
                            @else
    
                                {{Form::select($field, ($temp->getPropertyMenuSiteVisitAll()), (!is_null($booking->property) && $booking->property->exists) ? $booking->property->getKey() : '', array('id' => $name, 'class' => 'form-control page-booking-location', 'placeholder' => Translator::transSmart('app.Location', 'Location')))}}
    
                            @endif
    
                        @else
                            {{Form::select($field, [$property->getKey() => $property->smart_name], (!is_null($booking->property) && $booking->property->exists) ? $booking->property->getKey() : '', array('id' => $name, 'class' => 'form-control page-booking-location'))}}
                        @endif
                    @endif
                @endif

            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div class="form-group {{!$is_modal ? 'required' : ''}}">

                @php
                    $field = 'pax';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.I Need a Space For', 'I Need a Space For');
                @endphp

                {{Html::validation($booking, $field)}}
                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif

                {{Form::select($field, array(
                1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5,
                6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10,
                11 => '10+'
                ), $booking->getAttribute($field), array('id' => $field, 'class' => 'form-control page-booking-package-pax', 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}

            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div class="form-group {{!$is_modal ? 'required' : ''}}">
                @php
                    $field = 'office';
                    $name = sprintf('%s', $field);
                    $translate = Translator::transSmart('app.Membership Type', 'Membership Type');
                @endphp
                {{Html::validation($booking, $field)}}

                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif

                {{Form::select($field, Utility::constant('package', true, array(Utility::constant('package.prime-member.slug'))), $booking->getAttribute($field), array('id' => $field, 'class' => 'form-control page-booking-package', 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
            </div>
        </div>
    </div>

    @if($isNeedSchedule)
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group {{!$is_modal ? 'required' : ''}}">
                    @php
                        $field = 'schedule';
                        $name = sprintf('%s', $field);
                        $translate = Translator::transSmart('app.Appointment', 'Appointment');
                    @endphp

                    {{Html::validation($booking, $field)}}
                    @if(!$is_modal)
                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                    @endif
                    <div class="input-group schedule">

                             @php
                                $schedule = ($booking->schedule) ? $booking->localToDate($property, $booking->schedule) : '';
                             @endphp

                            {{Form::text($name, $schedule , array('id' => $name, 'class' => 'form-control date-time-picker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
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
                @if(!$is_modal)
                    <label for="{{$name}}" class="control-label">{{$translate}}</label>
                @endif
                {{Form::textarea($name, $booking->getAttribute($field) , array('id' => $name, 'class' => 'form-control', 'maxlength' => $booking->getMaxRuleValue($field), 'rows' => 5, 'cols' => 50, 'title' => $translate, 'placeholder' => (!$is_modal ? '' : $translate)))}}
            </div>
        </div>
    </div>

    @if(isset($is_email_notification_checkbox) && $is_email_notification_checkbox)
    
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group">
                    @php
                        $field = $booking->isNeedEmailNotificationField;
						$name = sprintf('%s', $field);
						$translate = Translator::transSmart('app.Notify Customer By Email?', 'Notify Customer By Email?');
                    @endphp
                
                    {{Html::validation($booking, $field)}}
                   
                    <div class="checkbox">
                        <label>
                            {{Form::checkbox($name, 1, null, array('class' => sprintf('%s', $field)))}} {{$translate}}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
    @endif

    @if($is_modal)

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <a href="javascript:void(0);" class="btn btn-block btn-theme input-submit" title="{{$submit_text}}">
                    {{$submit_text}}
                </a>
            </div>
        </div>

    @else

        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="form-group text-center">
                    <div class="btn-group">
                        @if(isset($is_from_lead) && $is_from_lead)
                            
                            {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block input-submit'))}}
                            
                        @else
                            
                            {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                            
                        @endif
                        
                    </div>
                    <div class="btn-group">
    
                        @if(isset($is_from_lead) && $is_from_lead)
        
                            <a href="javascript:void(0);"
                               title = "{{Translator::transSmart('app.Cancel', 'Cancel')}}"
                               class="btn btn-theme btn-block" onclick = "javascript:widget.popup.close(false, null, 0)" >
                                {{Translator::transSmart('app.Cancel', 'Cancel')}}
                            </a>
                            
                        @else
        
                            <a href="javascript:void(0);"
                               title = "{{Translator::transSmart('app.Cancel', 'Cancel')}}"
                               class="btn btn-theme btn-block" onclick = "{{'location.href="' . $cancel . '"; return false;'}}" >
                                {{Translator::transSmart('app.Cancel', 'Cancel')}}
                            </a>
                            
                        @endif
                      

                    </div>
                </div>
            </div>
        </div>

    @endif

{{ Form::close() }}

