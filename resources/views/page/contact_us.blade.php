@extends('layouts.page')
@section('title', Translator::transSmart('app.Contact Us', 'Contact Us'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/page/contact-us.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/contact-us.js') }}
@endsection

@section('content')

    <div class="page-contact-us">
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <div class="page-header page-no-border">
                    <h2>

                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-1">
            </div>
            <div class="col-xs-12 col-sm-10">
                {{Html::skin('contact-us/contact-us.png', array('class' => 'img-responsive'))}}
            </div>
            <div class="col-xs-12 col-sm-1">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <br />   <br />
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">


                <div class="contact-form-container">

                        {{ Form::open(array('route' => 'page::post-contact-us', 'class' => 'contact-us-form')) }}

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">

                                        <h3>
                                            <b>
                                                {{Translator::transSmart('app.DROP US AN EMAIL', 'DROP US AN EMAIL')}}
                                            </b>
                                        </h3>
                                        <p>
                                            {{Translator::transSmart('app.Use the form below to drop us an email.', 'Use the form below to drop us an email.')}}
                                        </p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            @php
                                                $field = 'name';
                                                $name = sprintf('%s', $field);
                                                $translate = Translator::transSmart('app.FULL NAME', 'FULL NAME');
                                            @endphp
                                            {{Html::validation($contact, $field)}}
                                            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            @php
                                                $field = 'company';
                                                $name = sprintf('%s', $field);
                                                $translate = Translator::transSmart('app.COMPANY NAME', 'COMPANY NAME');
                                            @endphp
                                            {{Html::validation($contact, $field)}}
                                            {{Form::text($name, null , array('id' => $name, 'class' => 'form-control', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            @php
                                                $field = 'email';
                                                $name = sprintf('%s', $field);
                                                $translate = Translator::transSmart('app.EMAIL', 'EMAIL');
                                            @endphp
                                            {{Html::validation($contact, $field)}}
                                            {{Form::email($field, null, array('class' => 'form-control', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">

                                            @php
                                                $field = 'contact_country_code';
                                                $name = sprintf('%s',  $field);
                                                $translate1 = Translator::transSmart('app.Phone Country Code', 'Country Code');
                                                $translate2 = Translator::transSmart('app.Phone Country Code', 'Country Code');
                                            @endphp

                                            <div class="phone">

                                                {{Html::validation($contact, ['contact_country_code', 'contact_number'])}}

                                                {{Form::select($name, CLDR::getPhoneCountryCodes() , null, array('id' => $name, 'class' => 'form-control country-code', 'title' => $translate1, 'placeholder' => $translate2))}}
                                                <span>-</span>

                                                @php
                                                    $field = 'contact_number';
                                                    $name = sprintf('%s',  $field);
                                                    $translate1 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                    $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                @endphp

                                                {{Form::text($name, null , array('id' => $name, 'class' => 'form-control number integer-value', 'maxlength' => $contact->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            @php
                                                $field = 'message';
                                                $name = sprintf('%s', $field);
                                                $translate = Translator::transSmart('app.Message', 'Message');
                                            @endphp
                                            {{Html::validation($contact, $field)}}
                                            {{Form::textarea($name, null , array('id' => $name, 'class' => 'form-control', 'maxlength' => $contact->getMaxRuleValue($field), 'rows' => 10, 'cols' => 50, 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="form-group">
                                            {!! Form::captcha() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12">
                                        <div class="message-box"></div>
                                        <a href="javascript:void(0);" class="btn btn-block btn-lg btn-theme input-submit" title="{{Translator::transSmart('app.SEND', 'SEND')}}">
                                            {{Translator::transSmart('app.SEND', 'SEND')}}
                                        </a>
                                    </div>
                                </div>

                        {{ Form::close() }}

                    </div>


                <div class="contact-info">
                    <div>
                        <div class="text-center">
                            <i class="fa fa-fax fa-5x"></i>
                        </div>
                        <div class="text-center">
                            <h3>
                                <b>
                                    {{$company->office_phone}}
                                </b>
                            </h3>
                        </div>
                    </div>
                </div>


            </div>

        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <br />   <br />
            </div>
        </div>
    </div>

@endsection