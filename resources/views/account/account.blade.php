@extends('layouts.member')
@section('title', Translator::transSmart('app.Account', 'Account'))
@section('center-justify', true)
@section('content')
    <div class="account-profile">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Account', 'Account')}}</h3>
                    </div>

                </div>

            </div>

            <div class="row">

                <div class="col-sm-12">


                        {{Html::success()}}
                        {{Html::error()}}

                        {{Html::validation(null, 'csrf_error')}}

                        {{ Form::open(array('route' => Domain::route('account::post-account'), 'files' => true, 'class' => 'form-grace')) }}

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group required">

                                        @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)
                                            {{Html::validation($user, config('auth.login.email.slug'))}}
                                            <label for="{{config('auth.login.email.slug')}}" class="control-label">{{Translator::transSmart('app.Email', 'Email')}}</label>
                                            {{Form::email('email', $user->getAttribute(config('auth.login.email.slug')), array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue(config('auth.login.email.slug')), 'title' => Translator::transSmart('app.Email', 'Email')))}}

                                        @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)
                                            {{Html::validation($user, config('auth.login.username.slug'))}}
                                            <label for="{{config('auth.login.usename.slug')}}" class="control-label">{{Translator::transSmart('app.Username', 'Username')}}</label>
                                            {{Form::text('username', $user->getAttribute(config('auth.login.username.slug')), array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username')))}}

                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="form-group required">
                                        {{Html::validation($user, config('auth.login.username.slug'))}}
                                        <label for="{{config('auth.login.usename.slug')}}" class="control-label">{{Translator::transSmart('app.Username', 'Username')}}</label>

                                        <div class="input-group input-group-responsive">
                                            <span class="input-group-addon">{{$member->prefix_url}}</span>
                                            {{Form::text('username', $user->getAttribute(config('auth.login.username.slug')), array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username')))}}

                                        </div>
                                        <span class="help-block">
                                            {{ Translator::transSmart('app.Only use letters, numbers and - character.', 'Only use letters, numbers and - character.') }}
                                        </span>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="page-header">
                                        <h3>{{Translator::transSmart('app.Personal Info', 'Personal Info')}}</h3>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'salutation')}}
                                        <label for="salutation" class="control-label">{{Translator::transSmart('app.Salutation', 'Salutation')}}</label>
                                        {{Form::select('salutation', Utility::constant('salutation', true) , $user->salutation, array('id' => 'salutation', 'class' => 'form-control', 'title' => Translator::transSmart('app.Salutation', 'Salutation'), 'placeholder' => ''))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group required">
                                        {{Html::validation($user, 'first_name')}}
                                        <label for="first_name" class="control-label">{{Translator::transSmart('app.First Name', 'First Name')}}</label>
                                        {{Form::text('first_name', $user->first_name,  array('id' => 'first_name', 'class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('first_name'), 'title' => Translator::transSmart('app.First Name', 'First Name')))}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group required">
                                        {{Html::validation($user, 'last_name')}}
                                        <label for="last_name" class="control-label">{{Translator::transSmart('app.Last Name', 'Last Name')}}</label>
                                        {{Form::text('last_name', $user->last_name,  array('id' => 'last_name', 'class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('last_name'), 'title' => Translator::transSmart('app.Last Name', 'Last Name')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'nric')}}
                                        <label for="nric" class="control-label">{{Translator::transSmart('app.NRIC', 'NRIC')}}</label>
                                        {{Form::text('nric', $user->nric , array('id' => 'nric',  'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('nric'), 'title' => Translator::transSmart('app.NRIC', 'NRIC')))}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'passport_number')}}
                                        <label for="passport_number" class="control-label">{{Translator::transSmart('app.Passport No.', 'Passport No.')}}</label>
                                        {{Form::text('passport_number', $user->passport_number , array('id' => 'passport_number',  'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('passport_number'), 'title' => Translator::transSmart('app.Passport No.', 'Passport No.')))}}
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-group required">
                                            {{Html::validation($user, 'gender')}}
                                            <label for="gender" class="control-label">{{Translator::transSmart('app.Gender', 'Gender')}}</label>
                                            {{Form::select('gender', Utility::constant('gender', true), $user->gender, array('id' => 'gender', 'class' => 'form-control', 'title' => Translator::transSmart('app.Gender', 'Gender')))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'nationality')}}
                                        <label for="nationality" class="control-label">{{Translator::transSmart('app.Nationality', 'Nationality')}}</label>
                                        {{Form::select('nationality', CLDR::getNationalities() , $user->nationality, array('id' => 'nationality', 'class' => 'form-control', 'title' => Translator::transSmart('app.Nationality', 'Nationality'), 'placeholder' => ''))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <div class="form-group required">
                                            {{Html::validation($user, 'birthday')}}
                                            <label for="birthday" class="control-label">{{Translator::transSmart('app.Birthday', 'Birthday')}}</label>
                                            {{Form::text('birthday', $user->birthday , array('id' => 'birthday', 'class' => 'form-control datepicker', 'data-datepicker' => '{"yearRange": "-100:+0"}', 'title' => Translator::transSmart('app.Birthday', 'Birthday')))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="page-header">
                                        <h3>{{Translator::transSmart('app:Contacts', 'Contacts')}}</h3>
                                    </div>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">

                                        <label for="phone_country_code" class="control-label">{{Translator::transSmart('app.Phone', 'Phone')}}</label>
                                        <div class="phone">
                                            {{Html::validation($user, ['phone_country_code', 'phone_area_code', 'phone_number'])}}
                                            {{Form::select('phone_country_code', CLDR::getPhoneCountryCodes() , $user->phone_country_code, array('id' => 'phone_country_code', 'class' => 'form-control country-code', 'title' => Translator::transSmart('app.Phone Country Code', 'Phone Country Code'), 'placeholder' => Translator::transSmart('app.Country Code', 'Country Code')))}}
                                            <span>-</span>
                                            {{Form::text('phone_number', $user->phone_number , array('id' => 'phone_number', 'class' => 'form-control number integer-value', 'maxlength' => $user->getMaxRuleValue('phone_number'), 'title' => Translator::transSmart('app.Number', 'Number'), 'placeholder' => Translator::transSmart('app.Number', 'Number')))}}
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">

                                        <label for="handphone_country_code" class="control-label">{{Translator::transSmart('app.Mobile Phone', 'Mobile Phone')}}</label>
                                        <div class="phone">
                                            {{Html::validation($user, ['handphone_country_code', 'handphone_area_code', 'handphone_number'])}}
                                            {{Form::select('handphone_country_code', CLDR::getPhoneCountryCodes() , $user->handphone_country_code, array('id' => 'handphone_country_code', 'class' => 'form-control country-code', 'title' => Translator::transSmart('app.Phone Country Code', 'Phone Country Code'), 'placeholder' => Translator::transSmart('app.Country Code', 'Country Code')))}}
                                            <span>-</span>
                                            {{Form::text('handphone_number', $user->handphone_number , array('id' => 'handphone_number', 'class' => 'form-control number integer-value', 'maxlength' => $user->getMaxRuleValue('handphone_number'), 'title' => Translator::transSmart('app.Number', 'Number'), 'placeholder' => Translator::transSmart('app.Number', 'Number')))}}
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                {{Html::validation($user, 'address1')}}
                                                <label for="address1" class="control-label">{{Translator::transSmart('app.Address 1', 'Address 1')}}</label>
                                                {{Form::text('address1', $user->address1 , array('id' => 'address1', 'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('address1'), 'title' => Translator::transSmart('app.Address 1', 'Address 1')))}}
                                            </div>
                                        </div>
                                    </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'address2')}}
                                        <label for="address2" class="control-label">{{Translator::transSmart('app.Address 2', 'Address 2')}}</label>
                                        {{Form::text('address2', $user->address2 , array('id' => 'address2', 'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('address2'), 'title' => Translator::transSmart('app.Address 2', 'Address 2')))}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'city')}}
                                        <label for="city" class="control-label">{{Translator::transSmart('app.City', 'City')}}</label>
                                        {{Form::text('city', $user->city , array('id' => 'city', 'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('city'), 'title' => Translator::transSmart('app.City', 'City')))}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'state')}}
                                        <label for="state" class="control-label">{{Translator::transSmart('app.State', 'State')}}</label>
                                        {{Form::text('state', $user->state , array('id' => 'state', 'class' => 'form-control', 'maxlength' => $user->getMaxRuleValue('state'), 'title' => Translator::transSmart('app.State', 'State')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        {{Html::validation($user, 'postcode')}}
                                        <label for="postcode" class="control-label">{{Translator::transSmart('app.Postcode', 'Postcode')}}</label>
                                        {{Form::text('postcode', $user->postcode , array('id' => 'postcode', 'class' => 'form-control integer-value', 'maxlength' => $user->getMaxRuleValue('postcode'), 'title' => Translator::transSmart('app.Postcode', 'Postcode')))}}
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group required">
                                        {{Html::validation($user, 'country')}}
                                        <label for="country" class="control-label">{{Translator::transSmart('app.Country', 'Country')}}</label>
                                        {{Form::select('country', CLDR::getCountries() , $user->country, array('id' => 'country', 'class' => 'form-control', 'title' => Translator::transSmart('app.Country', 'Country'), 'placeholder' => ''))}}
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group text-center">
                                        <div class="btn-group">
                                             {{Form::submit(Translator::transSmart('app.Update', 'Update'), array('title' => Translator::transSmart('app.Update', 'Update'), 'class' => 'btn btn-theme btn-block'))}}
                                        </div>
                                    </div>

                                </div>
                            </div>

                        {{ Form::close() }}


                </div>

            </div>
        </div>
    </div>
@endsection