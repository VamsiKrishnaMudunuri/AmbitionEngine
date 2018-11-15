@extends('layouts.auth')
@section('title', Translator::transSmart('app.Sign Up', 'Sign Up'))

@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
    {{ Html::skin('app/modules/auth/signup.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/auth/signup.js') }}
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
@endsection

@section('footer_position', 'position-relative')


@section('content')
    <div class="auth-signup">

        <div class="row">
            <div class="col-sm-12">
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
                    <li data-tab="payment-form">
                        <a href="javascript:void(0);">
                            {{Translator::transSmart('app.3. Payment Information', '3. Payment Information')}}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">

            <div class="col-sm-6">

                <h2 class="title">
                    {{Translator::transSmart('app.Sign Up', 'Sign Up ')}}
                </h2>

                {{Html::success()}}
                {{Html::error()}}

                {{Html::validation($user, 'csrf_error')}}

                {{ Form::open(array('route' => Domain::route('auth::post-signup-step1'), 'class' => 'form-grace first sign-up-form', 'data-table' => $user->getTable())) }}

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-box"></div>
                            <div class="sub-title">
                                <h3>
                                    {{Translator::transSmart('app.Personal Information', 'Personal Information')}}
                                </h3>
                                <span class="help-block">{{Translator::transSmart('app.All fields marked with asterisk are required.', 'All fields marked with asterisk are required.')}}</span>
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
                                {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
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
                                {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                            </div>

                        </div>

                    </div>
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="form-group ">
                                <?php
                                $field = 'nric';
                                $name = sprintf('%s[%s]', $user->getTable(), $field);
                                $translate = Translator::transSmart('app.NRIC', 'NRIC');
                                ?>
                                {{Html::validation($user, $field)}}
                                {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                            </div>

                        </div>

                    </div>
                    <div class="row">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <?php
                                $field = 'passport_number';
                                $name = sprintf('%s[%s]', $user->getTable(), $field);
                                $translate = Translator::transSmart('app.Passport No.', 'Passport No.');
                                ?>
                                {{Html::validation($user, $field)}}
                                {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
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
                                    {{Form::text($name, $user->getAttribute($field),  array('class' => sprintf('form-control %s', $field),  'maxlength' => $user->getMaxRuleValue($field), 'data-url' => URL::route('api::company::search'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
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
                                {{Form::text($name, null , array('id' => $field, 'class' => 'form-control datepicker', 'data-datepicker' => '{"yearRange": "-100:+0"}', 'title' => $translate, 'placeholder' => $translate))}}



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
                                {{Form::select($name, Utility::constant('gender', true), null, array('class' => 'form-control', 'title' => $translate, 'placeholder' => $translate))}}

                            </div>
                        </div>

                    </div>
                    <div class="row">

                        <div class="col-sm-12">

                            <div class="form-group">

                                <?php
                                $field = 'country';
                                $name = sprintf('%s[%s]', $user->getTable(), $field);
                                $translate = '* ' . Translator::transSmart('app.Select Your Country', 'Select Your Country');
                                ?>
                                {{Html::validation($user, $field)}}
                                {{Form::select($name, CLDR::getCountries(), null, array('class' => 'form-control', 'title' => $translate, 'placeholder' => $translate))}}


                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">

                                @php
                                    $field = 'handphone_country_code';
                                    $name = sprintf('%s[%s]', $user->getTable(), $field);
                                    $translate1 = '* ' . Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                                    $translate2 = '* ' . Translator::transSmart('app.Country Code', 'Country Code');
                                @endphp

                                <div class="phone">

                                    {{Html::validation($user, ['handphone_country_code', 'handphone_number'])}}

                                    {{Form::select($name, CLDR::getPhoneCountryCodes() , null, array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                                    <span>-</span>

                                    @php
                                        $field = 'handphone_number';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate1 = '* ' . Translator::transSmart('app.Phone Number', 'Phone Number');
                                        $translate2 = '* ' . Translator::transSmart('app.Phone Number', 'Phone Number');
                                    @endphp

                                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-sm-12">

                            <br />

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">

                            <div class="form-group">

                                @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)

                                    <?php
                                        $field =  config('auth.login.email.slug');
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = '* ' . Translator::transSmart('app.Email', 'Email');
                                    ?>

                                    {{Html::validation($user, $field)}}
                                    {{Form::email($name, null, array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)

                                    <?php
                                        $field =  config('auth.login.username.slug');
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = '* ' . Translator::transSmart('app.Email', 'Email');
                                    ?>

                                    {{Html::validation($user, $field)}}
                                    {{Form::text($name, null, array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                @endif

                            </div>
                        </div>

                   </div>
                    <div class="row">

                        <div class="col-sm-6">

                                <div class="form-group">
                                    <?php
                                     $field =  'password';
                                     $name = sprintf('%s[%s]', $user->getTable(), $field);
                                     $translate = '* ' . Translator::transSmart('app.Password', 'Password');
                                    ?>
                                    {{Html::validation($user, $field)}}
                                    {{Form::password($name, array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue($field), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                </div>

                        </div>

                        <div class="col-sm-6">

                            <div class="form-group">
                                <?php
                                    $field =  'password_confirmation';
                                    $field1 =  'password';
                                    $name = sprintf('%s[%s]', $user->getTable(), $field);
                                    $translate = '* ' . Translator::transSmart('app.Confirm Password', 'Confirm Password');
                                ?>
                                {{Html::validation($user, $field)}}
                                {{Form::password($name, array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue($field1), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                            </div>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <span class="help-block">
                                    {{Translator::transSmart("app.This'll be your Member Network login to connect with our global community.", "This'll be your Member Network login to connect with our global community")}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-box"></div>
                            <div class="form-group submit-container">
                                {{Form::button(Translator::transSmart('app.Next', 'Next'), array('type' => 'submit', 'title' => Translator::transSmart('app.Next', 'Next'), 'class' => 'btn btn-theme submit' , 'data-next' => 'package-form' ))}}
                            </div>
                        </div>
                    </div>

                {{ Form::close() }}

                {{ Form::open(array('route' => Domain::route('auth::post-signup-step2'), 'class' => 'form-grace package-form', 'data-table' => $property->getTable())) }}


                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-box"></div>
                            <div class="sub-title">
                                <h3>
                                    {{Translator::transSmart('app.Select a Location', 'Select a Location')}}
                                </h3>
                                <span class="help-block">{{Translator::transSmart('app.All fields required.', 'All fields required.')}}</span>
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
                                {{ Form::select($name, $temp->getPropertyMenu(), null, array('id' => $name, 'title' => $translate, 'class' => sprintf('form-control %s', 'property-select'), 'data-location-loading' => 'property-package', 'placeholder' => $translate )) }}

                                <?php
                                $field = 'property_chosen';
                                $name = sprintf('%s[%s]', $property->getTable(), $field);
                                ?>

                                {{ Form::hidden($name, '', array('class' => 'property-chosen')) }}

                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="package-message-box"></div>
                            <div class="property-package" data-location-loading-place="property-package" data-url="{{URL::route('api::subscription::check-availability-all-package')}}">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-box"></div>
                            <div class="form-group submit-container flex">

                                {{Form::button(Translator::transSmart('app.Prev', 'Prev'), array('type' => 'submit', 'title' => Translator::transSmart('app.Prev', 'Prev'), 'class' => 'btn btn-theme prev' , 'data-prev' => 'sign-up-form' ))}}


                                {{Form::button(Translator::transSmart('app.Next', 'Next'), array('type' => 'submit', 'title' => Translator::transSmart('app.Next', 'Next'), 'class' => 'btn btn-theme submit' , 'data-next' => 'payment-form' ))}}

                            </div>
                        </div>
                    </div>

                {{ Form::close() }}

                {{ Form::open(array('route' => Domain::route('auth::post-signup-step3'), 'class' => 'form-grace payment-form', 'data-table' => $transaction->getTable())) }}


                    <div class="row">
                        <div class="col-sm-12">
                            <div class="message-box"></div>
                            <div class="sub-title">
                                <h3>
                                    {{Translator::transSmart('app.Payment Information', 'Payment Information')}}
                                </h3>
                                <span class="help-block">{{Translator::transSmart('app.All fields required.', 'All fields required.')}}</span>
                            </div>


                        </div>
                    </div>

                    <div class="{{sprintf('payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                        @include('templates.widget.braintree.credit_card_vertical', array('transaction' => $transaction))
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="info-box">
                                <span class="help-block">
                                    {{Translator::transSmart('app.Please do not refresh the page and wait while we are processing your payment.', 'Please do not refresh the page and wait while we are processing your payment.')}}
                                </span>
                            </div>
                            <div class="message-box"></div>
                            <div class="form-group submit-container flex">

                                {{Form::button(Translator::transSmart('app.Prev', 'Prev'), array('type' => 'submit', 'title' => Translator::transSmart('app.Prev', 'Prev'), 'class' => 'btn btn-theme prev' , 'data-prev' => 'package-form' ))}}


                                {{Form::button(Translator::transSmart('app.Sign Up', 'Sign Up'), array('type' => 'submit', 'title' => Translator::transSmart('app.Sign Up', 'Sign Up'), 'class' => 'btn btn-theme btn-block submit' , 'data-next' => '' ))}}
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                 <span class="help-block">

                                       @php
                                           $term = sprintf('<a href="%s" target="_blank">%s</a>', URL::route('page::term'), Translator::transSmart('app.Terms of Service', 'Terms of Service'));
                                           $privacy = sprintf('<a href="%s" target="_blank">%s</a>', URL::route('page::privacy'), Translator::transSmart('app.Privacy Policy', 'Privacy Policy'));
                                       @endphp

                                        {{Translator::transSmart('app.By sign up, you agree to our %s and %s.',
                                         sprintf('By sign up you agree to our %s and %s.', $term, $privacy),
                                         true, ['term' => $term, 'privacy' => $privacy])}}

                                        {{Translator::transSmart('app.You will be charged on the first of the month until you cancel your subscription.', 'You will be charged on the first of the month until you cancel your subscription.')}}

                                </span>
                            </div>
                        </div>
                    </div>

                {{ Form::close() }}

                <div class="welcome-box" data-url="{{URL::route(Domain::route('auth::signup-step4'))}}" data-location-loading="welcome-box" data-location-loading-place="welcome-box">

                    {{Translator::transSmart('app.Thank You for signing up! We are redirecting you to our member community.', 'Thank You for signing up! We are redirecting you to our member community.')}}

                </div>


            </div>
            <div class="col-sm-1">
            </div>
            <div class="col-sm-5">

                <div class="order">

                    <h2 class="title">
                        {{Translator::transSmart('app.Order Summary', 'Order Summary')}}
                    </h2>

                    <div class="summary" data-location-loading-place="summary">
                        <div class="order-message-box"></div>
                        <div class="table-empty">
                            <table class="table table-condensed">
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
    </div>
@endsection