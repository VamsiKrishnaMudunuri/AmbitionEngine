@extends('layouts.page')
@section('title', Translator::transSmart('app.Career - Contact', 'Career - Contact'))

@section('full-width-section')
    <div class="page-job-contact">
        <section class="auth section" style="background-color: rgb(254, 198, 92)">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 form-auth-container">
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <div class="page-header border-b-no text-center">
                                    <div class="text-green">
                                        <h3><b>{{ Translator::transSmart("app.Job Form", "Job Form") }}</b></h3>
                                    </div>
                                    <h5><b>{{ Translator::transSmart("app.Position", "Position") }} : {{ $job->title }}</b></h5>
                                </div>
                                <div class="text-center">

                                    {{Html::success()}}
                                    {{Html::error()}}

                                    @if( Session::has('status') )
                                        {{Html::successBox(Session::get('status'))}}
                                    @endif

                                    {{ Form::open(array('route' => ['page::career::job::post-job-contact', $job->getKey()], 'class' => 'post-job-appointment form-horizontal m-y-10 form-feedback p-x-15 p-y-10-full')) }}

                                    <?php
                                    $field = 'career_id';
                                    $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                    ?>
                                    {{ Form::hidden($name, $job->getKey()) }}

                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <?php
                                            $field = 'first_name';
                                            $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                            $translate = Translator::transSmart('app.First Name', 'First Name');
                                            ?>
                                            {{Html::validation($careerAppointment, $field)}}
                                            {{Form::text($name, $careerAppointment->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $careerAppointment->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                            $field = 'last_name';
                                            $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                            $translate = Translator::transSmart('app.Last Name', 'Last Name');
                                            ?>
                                            {{Html::validation($careerAppointment, $field)}}
                                            {{Form::text($name, $careerAppointment->getAttribute($field),  array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $careerAppointment->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-xs-4"
                                                     style="border-right: 1px solid rgba(157, 118, 48, .5)">
                                                    @php
                                                        $field = 'phone_country_code';
                                                        $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                                        $translate1 = Translator::transSmart('app.Phone Country Code', 'Phone Country Code');
                                                        $translate2 = Translator::transSmart('app.Country Code', 'Country Code');
                                                    @endphp

                                                    {{Form::select($name, CLDR::getPhoneCountryCodes(true) , null, array('id' => $name, 'class' => 'form-control country-code input-transparent b-x-none b-y-none input-select', 'title' => $translate2, 'placeholder' => null))}}

                                                </div>
                                                <div class="col-xs-8">
                                                    @php
                                                        $field = 'phone_number';
                                                        $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                                        $translate1 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                        $translate2 = Translator::transSmart('app.Phone Number', 'Phone Number');
                                                    @endphp
                                                    {{Html::validation($careerAppointment, ['phone_country_code', 'phone_number'])}}

                                                    {{Form::text($name, null , array('id' => $name, 'class' => 'form-control b-x-none b-y-none input-transparent number integer-value', 'maxlength' => $careerAppointment->getMaxRuleValue($field), 'title' => $translate1, 'placeholder' => $translate2 ))}}
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

                                                <?php
                                                $field = 'email';
                                                $name = sprintf('%s[%s]', $careerAppointment->getTable(), $field);
                                                $translate = Translator::transSmart('app.Email', 'Email');
                                                ?>

                                                {{Html::validation($careerAppointment, $field)}}
                                                {{Form::text($name, null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $careerAppointment->getMaxRuleValue($field), 'title' => $translate, 'placeholder' => $translate))}}

                                        </div>

                                    </div>

                                    <div class="form-group m-t-15-full">
                                        <div class="col-md-12">
                                            <div class="message-box"></div>
                                            <button class="'btn btn-green btn-block input-submit" title="{{Translator::transSmart('app.Apply', 'Apply')}}" data-should-redirect="{{ route('page::career::job::job-thank-you', $job->getKey()) }}">
                                                {{Translator::transSmart('app.Apply', 'Apply')}}
                                            </button>
                                    </div>

                                    {{ Form::close() }}
                                </div>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/page/jobs.js') }}
@endsection