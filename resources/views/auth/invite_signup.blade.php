@extends('layouts.auth')
@section('title', Translator::transSmart('app.Sign Up', 'Sign Up'))

@if(!$isValidToken)
    @section('center-focus', true)
@endif

@section('styles')
    @parent
    {{ Html::skin('app/modules/auth/invite-signup.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/auth/invite-signup.js') }}
@endsection

@section('footer_position', 'position-relative')

@section('full-width-section')
    <section class="sign-up-invite auth feedback" style="background-color: rgb(254, 190, 82)">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="auth-invite-signup m-t-5-full">

                        @if(!$isValidToken)

                            <div class="row">
                                <div class="col-sm-12">

                                    {{$signup_invitation->getInvalidMessage()}}

                                </div>
                            </div>

                        @else

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-4">
                                            <ul class="tabs">
                                                <li class="active" data-tab="sign-up-form">
                                                    <a href="javascript:void(0);">
                                                        {{Translator::transSmart('app.1. Personal Information', '1. Personal Information')}}
                                                    </a>
                                                </li>
                                                <li data-tab="package-form">
                                                    <a href="javascript:void(0);">
                                                        {{Translator::transSmart('app.2. Select a Location', '2. Select a Location')}}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-sm-12 form-feedback-container">

                                    <div class="row">
                                        <div class="col-md-2"></div>
                                        <div class="col-md-8">
                                            <div class="page-header border-b-no text-center m-0 m-t-10">
                                                <h3 class="m-0">
                                                    <b>
                                                        {{ Translator::transSmart("app.Sign Up", "Sign Up") }}
                                                    </b>
                                                </h3>
                                            </div>

                                            {{Html::success()}}
                                            {{Html::error()}}

                                            {{Html::validation($user, 'csrf_error')}}

                                            {{ Form::open(array('route' => array(Domain::route('auth::post-invite-signup-step1'), $token), 'class' => 'form-grace first sign-up-form form-horizontal m-y-10 form-feedback p-x-15 p-b-10-full step-1', 'data-table' => $user->getTable())) }}

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="message-box"></div>
                                                    <div class="sub-title m-l-10-minus">
                                                        <h3 class="text-black m-t-0">
                                                            {{Translator::transSmart('app.Personal Information', 'Personal Information')}}
                                                        </h3>
                                                        <br/>
                                                        <div class="help-block m-t-10-minus-full m-b-5-full text-brown">{{Translator::transSmart('app.* All fields marked with asterisk are required.', '* All fields marked with asterisk are required.')}}</div>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group">
                                                        <?php
                                                        $field = 'first_name';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.First Name', 'First Name');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>

                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group">
                                                        <?php
                                                        $field = 'last_name';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Last Name', 'Last Name');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group ">
                                                        <?php
                                                        $field = 'nric';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = Translator::transSmart('app.NRIC', 'NRIC');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>
                                                </div>

                                                <div class="col-xs-12 col-sm-6">
                                                    <div class="form-group m-l-0">
                                                        <?php
                                                        $field = 'passport_number';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = Translator::transSmart('app.Passport No.', 'Passport No.');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>

                                                </div>

                                            </div>

                                            <div class="row">

                                                <div class="col-sm-12">
                                                    <div class="form-group ">
                                                        <?php
                                                        $field = 'company';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Company Name', 'Company Name');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::hidden(sprintf('_%s_hidden', $field), null, array('class' => sprintf('%s-hidden', $field)))}}
                                                        <div class="twitter-typeahead-container">
                                                            {{Form::text($name, $user->getAttribute($field),  array('class' => sprintf('form-control %s', $field) . ' input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'data-url' => URL::route('api::company::search'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">

                                                <div class="col-sm-12">

                                                    <div class="form-group">


                                                        <?php
                                                        $field = 'birthday';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = Translator::transSmart('app.Birthday', 'Birthday');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::text($name, null , array('id' => $field, 'class' => 'form-control datepicker input-transparent border-color-brown', 'data-datepicker' => '{"yearRange": "-100:+0"}', 'title' => $translate, 'placeholder' => $translate))}}


                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">

                                                <div class="col-sm-12">

                                                    <div class="form-group">

                                                        <?php
                                                        $field = 'gender';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Gender', 'Gender');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::select($name, Utility::constant('gender', true), null, array('class' => 'form-control input-transparent border-color-brown input-select', 'title' => $translate, 'placeholder' => $translate))}}

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6 p-l-0">
                                                    <div class="row m-t-0">
                                                        <div class="col-xs-5"
                                                             style="border-right: 1px solid rgba(157, 118, 48, .5)">
                                                            @php
                                                                $field = 'handphone_country_code';
                                                                $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                                $translate1 = '* ' .  Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                                                                $translate2 = '* ' .  Translator::transSmart('app.Country Code', 'Country Code');
                                                            @endphp

                                                            {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none input-select', 'title' => $translate2, 'placeholder' => $translate2))}}

                                                        </div>
                                                        <div class="col-xs-7">
                                                            @php
                                                                $field = 'handphone_number';
                                                                $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                                $translate1 = '* ' .  Translator::transSmart('app.Phone Number', 'Phone Number');
                                                                $translate2 = '* ' .  Translator::transSmart('app.Phone Number', 'Phone Number');
                                                            @endphp
                                                            {{Html::validation($user, ['handphone_country_code', 'handphone_number'])}}

                                                            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control b-x-none b-y-none input-transparent number integer-value', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}
                                                            <span></span>
                                                        </div>

                                                    </div>
                                                    <div class="row m-t-0">
                                                        <div class="col-md-12">
                                                            <div class="btm-divider"
                                                                 style="border-bottom: 1px solid rgba(157, 118, 48, .5)"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6">

                                                    <div class="form-group">

                                                        <?php
                                                        $field = 'country';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Select Your Country', 'Select Your Country');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::select($name, CLDR::getCountries(), null, array('class' => 'form-control input-transparent border-color-brown input-select', 'title' => $translate, 'placeholder' => $translate))}}


                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">

                                                    <div class="form-group">

                                                        @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)

                                                            <?php
                                                            $field = config('auth.login.email.slug');
                                                            $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                            $translate = '* ' . Translator::transSmart('app.Email', 'Email');
                                                            ?>

                                                            {{Html::validation($user, $field)}}
                                                            {{Form::email($name, null, array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                                        @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)

                                                            <?php
                                                            $field = config('auth.login.username.slug');
                                                            $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                            $translate = '* ' . Translator::transSmart('app.Email', 'Email');
                                                            ?>

                                                            {{Html::validation($user, $field)}}
                                                            {{Form::text($name, null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                                        @endif

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row">

                                                <div class="col-sm-6">

                                                    <div class="form-group">
                                                        <?php
                                                        $field = 'password';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Password', 'Password');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::password($name, array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>

                                                </div>

                                                <div class="col-sm-6">

                                                    <div class="form-group m-l-0">
                                                        <?php
                                                        $field = 'password_confirmation';
                                                        $field1 = 'password';
                                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                        $translate = '* ' . Translator::transSmart('app.Confirm Password', 'Confirm Password');
                                                        ?>
                                                        {{Html::validation($user, $field)}}
                                                        {{Form::password($name, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field1), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                                    </div>

                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                    <span class="help-block text-brown">
                                        {{Translator::transSmart("app.This'll be your Member Network login to connect with our global community.", "This'll be your Member Network login to connect with our global community")}}
                                    </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="message-box"></div>
                                                    <div class="form-group submit-container">
                                                        {{Form::button(Translator::transSmart('app.Next', 'Next'), array('type' => 'submit', 'title' => Translator::transSmart('app.Next', 'Next'), 'class' => 'btn btn-green submit' , 'data-next' => 'package-form' ))}}
                                                    </div>
                                                </div>
                                            </div>

                                            {{ Form::close() }}

                                            {{ Form::open(array('route' => array(Domain::route('auth::post-invite-signup-step2'), $token), 'class' => 'form-grace package-form form-feedback step-2', 'data-table' => $property->getTable())) }}


                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="message-box"></div>
                                                    <div class="sub-title">
                                                        <h3 class="text-black">
                                                            {{Translator::transSmart('app.Select a Location', 'Select a Location')}}
                                                        </h3>
                                                        <br/>
                                                        <span class="help-block text-brown">{{Translator::transSmart('app.All fields required.', 'All fields required.')}}</span>
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <?php
                                                        $field = 'property_select';
                                                        $name = sprintf('%s[%s]', $property->getTable(), $field);
                                                        $translate = Translator::transSmart('app.Select A Location', 'Select A Location');
                                                        ?>
                                                        {{ Form::select($name, $temp->getPropertyMenu(), null, array('id' => $name, 'title' => $translate, 'class' => sprintf('form-control %s', 'property-select input-transparent border-color-brown input-select'), 'data-location-loading' => 'property-package', 'placeholder' => $translate )) }}

                                                        <?php
                                                        $field = 'property_chosen';
                                                        $name = sprintf('%s[%s]', $property->getTable(), $field);
                                                        ?>

                                                        {{ Form::hidden($name, '', array('class' => 'property-chosen')) }}

                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row m-t-0">
                                                <div class="col-sm-12">
                                                    <div class="package-message-box"></div>
                                                    <div class="property-package" data-location-loading-place="property-package"
                                                         data-url="{{URL::route('api::subscription::invite-check-availability')}}">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="message-box"></div>
                                                    <div class="form-group submit-container flex">

                                                        {{Form::button(Translator::transSmart('app.Prev', 'Prev'), array('type' => 'submit', 'title' => Translator::transSmart('app.Prev', 'Prev'), 'class' => 'btn btn-green prev' , 'data-prev' => 'sign-up-form' ))}}


                                                        {{Form::button(Translator::transSmart('app.Sign Up', 'Sign Up'), array('type' => 'submit', 'title' => Translator::transSmart('app.Sign Up', 'Sign Up'), 'class' => 'btn btn-green submit' , 'data-next' => 'payment-form' ))}}

                                                    </div>
                                                </div>
                                            </div>

                                            {{ Form::close() }}

                                            <div class="welcome-box"
                                                 data-url="{{URL::route(Domain::route('auth::invite-signup-step3'), ['token' => $token])}}"
                                                 data-location-loading="welcome-box" data-location-loading-place="welcome-box">

                                                {{Translator::transSmart('app.Thank You for signing up! We are redirecting you to our member community.', 'Thank You for signing up! We are redirecting you to our member community.')}}

                                            </div>
                                        </div>
                                        <div class="col-md-2"></div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                </div>
                                <div class="col-sm-5">

                                    <div class="order hide">

                                        <h2 class="title">
                                            {{Translator::transSmart('app.Order Summary', 'Order Summary')}}
                                        </h2>

                                        <div class="summary" data-location-loading-place="summary">
                                            <div class="order-message-box"></div>
                                            <div class="table-empty">
                                                <table class="table table-condensed table-package">
                                                    <colgroup>
                                                        <col width="70%">
                                                        <col width="30%">
                                                    </colgroup>
                                                    <tr>
                                                        <td>
                                                            <b>
                                                                {{Translator::transSmart('app.No package(s) added yet.', 'No package(s) added yet.')}}
                                                            </b>
                                                        </td>
                                                        <td>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <b>
                                                                {{Translator::transSmart('app.Total', 'Total')}}
                                                            </b>
                                                        </td>
                                                        <td>
                                                            <b>
                                                                0.00
                                                            </b>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="table-no-empty">

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection