@extends('layouts.auth')
@section('title', Translator::transSmart('app.Sign Up for Prime Member', 'Sign Up for Prime Member'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/auth/signup-prime-member.js') }}
@endsection

@section('full-width-section')
    <section class="auth auth-signup-prime-member" style="background-color: rgb(254, 198, 92)">
        <div class="container">
            <div class="row m-b-5-full m-t-5-full">
                <div class="col-md-2">
                </div>
                <div class="col-md-8 d-flex justify-content-center align-content-center form-auth-container flex-column">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="page-header border-b-no text-center">
                                <h3>
                                    <b>
                                        {{ Translator::transSmart("app.Sign Up For Prime Member", "Sign Up For Prime Member") }}
                                    </b>
                                </h3>
                            </div>

                            <div class="">

                                {{Html::success()}}
                                {{Html::error()}}
                                {{Html::validation($user, 'csrf_error')}}

                                {{ Form::open(array('route' => Domain::route('auth::post-signup-prime-member'), 'class' => 'sign-up-form form-horizontal m-y-10 form-feedback p-x-15')) }}

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <?php
                                        $field = 'first_name';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.First Name', 'First Name');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                                        $field = 'last_name';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Last Name', 'Last Name');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12 m-t-10-full m-t-md-0">
                                        <?php
                                        $field = 'company';
                                        $field1 = sprintf('_%s_hidden', $field);
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Company Name', 'Company Name');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::hidden($field1, null, array('class' => sprintf('%s-hidden', $field)))}}
                                        <div>
                                            <div class="twitter-typeahead-container">
                                                {{Form::text($name, $user->getAttribute($field),  array('class' => sprintf('form-control %s', $field) . ' input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'data-url' => URL::route('api::company::search'), 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <div class="col-xs-4"
                                                 style="border-right: 1px solid rgba(157, 118, 48, .5)">
                                                @php
                                                    $field = 'handphone_country_code';
                                                    $name = sprintf('%s[%s]', $user->getTable(), $field);
                                                    $translate1 = Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                                                    $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                                                @endphp

                                                {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none input-select', 'title' => $translate2, 'placeholder' => null))}}

                                            </div>
                                            <div class="col-xs-8">
                                                @php
                                                    $field = 'handphone_number';
                                                    $name = sprintf('%s',  $field);
                                                    $translate1 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                    $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                @endphp
                                                {{Html::validation($user, ['handphone_country_code', 'handphone_number'])}}

                                                {{Form::text($name, null , array('id' => $name, 'class' => 'form-control b-x-none b-y-none input-transparent number integer-value', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}
                                                <span></span>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="btm-divider"
                                                     style="border-bottom: 1px solid rgba(157, 118, 48, .5)"></div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-6 m-t-10-full m-t-md-0">
                                        @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)

                                            <?php
                                            $field = config('auth.login.email.slug');
                                            $name = sprintf('%s[%s]', $user->getTable(), $field);
                                            $translate = Translator::transSmart('app.Email', 'Email');
                                            ?>

                                            {{Html::validation($user, $field)}}
                                            {{Form::email($name, null, array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                        @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)

                                            <?php
                                            $field = config('auth.login.username.slug');
                                            $name = sprintf('%s[%s]', $user->getTable(), $field);
                                            $translate = Translator::transSmart('app.Email', 'Email');
                                            ?>

                                            {{Html::validation($user, $field)}}
                                            {{Form::text($name, null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                        @endif
                                    </div>

                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <?php
                                        $field = 'password';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Password', 'Password');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::password($name, array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                    <div class="col-md-6 m-t-10-full m-t-md-0">
                                        <?php
                                        $field = 'password_confirmation';
                                        $field1 = 'password';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Confirm Password', 'Confirm Password');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::password($name, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue($field1), 'autocomplete' => 'off', 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <?php
                                        $field = 'nric';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.NRIC', 'NRIC');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                    <div class="col-md-6 m-t-10-full m-t-md-0">
                                        <?php
                                        $field = 'passport_number';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Passport No.', 'Passport No.');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::text($name, $user->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <?php
                                        $field = 'birthday';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Birthday', 'Birthday');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::text($name, null , array('id' => $field, 'class' => 'form-control datepicker input-transparent border-color-brown', 'data-datepicker' => '{"yearRange": "-100:+0"}', 'title' => $translate, 'placeholder' => $translate))}}

                                    </div>
                                    <div class="col-md-6 m-t-10-full m-t-md-0">
                                        <?php
                                        $field = 'gender';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Gender', 'Gender');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::select($name, Utility::constant('gender', true), null, array('class' => 'form-control input-transparent border-color-brown input-select', 'title' => $translate, 'placeholder' => $translate))}}

                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        @php
                                            $field = $subscription->property()->getForeignKey();
                                            $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                                            $translate = Translator::transSmart('app.Select A Location', 'Select A Location');
                                            $locations  = $temp->getPropertyMenuIfHasPackage();
                                        @endphp

                                        {{Html::validation($subscription, $field)}}
                                        {{ Form::select($name, $locations, null, array('id' => $name, 'title' => $translate, 'class' => 'form-control input-transparent border-color-brown input-select', 'placeholder' => $translate )) }}

                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?php
                                        $field = 'country';
                                        $name = sprintf('%s[%s]', $user->getTable(), $field);
                                        $translate = Translator::transSmart('app.Select Your Country', 'Select Your Country');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        {{Form::select($name, CLDR::getCountries(), null, array('class' => 'form-control input-transparent border-color-brown input-select', 'title' => $translate, 'placeholder' => $translate))}}

                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        @php
                                            $field =  config('subscription.package.prime.promotion_code_field_name');
                                            $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                                            $translate = Translator::transSmart('app.Promotion Code', 'Promotion Code');
                                        @endphp
                                        {{Html::validation($subscription, $field)}}
                                        {{Form::text($name, $subscription->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => config('subscription.package.prime.promotion_code_field_length'), 'title' => $translate, 'placeholder' => $translate))}}

                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12">
        <span class="help-block">
        @php
            $term = sprintf('<a href="%s" target="_blank" class="text-green f-w-500">%s</a>', URL::route('page::term'), Translator::transSmart('app.Terms of Service', 'Terms of Service'));
            $privacy = sprintf('<a href="%s" target="_blank" class="text-green f-w-500">%s</a>', URL::route('page::privacy'), Translator::transSmart('app.Privacy Policy', 'Privacy Policy'));
        @endphp

            <span class="text-brown">{{Translator::transSmart('app.By clicking submit you agree to our', 'By clicking submit you agree to our')}}</span> {!! $term !!}
            <span class="text-brown">{{ Translator::transSmart('app.and have read and understood our', 'and have read and understood our') }}</span> {!! $privacy !!}
        </span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        {{Form::submit(Translator::transSmart('app.Sign Up', 'Sign Up'), array('title' => Translator::transSmart('app.Sign Up', 'Sign Up'), 'class' => 'btn btn-green btn-block'))}}</div>
                                </div>

                                {{ Form::close() }}
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-md-2">
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer_position', 'position-relative')
