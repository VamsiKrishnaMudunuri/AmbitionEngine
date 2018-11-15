@extends('layouts.member')
@section('title', Translator::transSmart('app.Setting', 'Setting'))
@section('center-justify', true)
@section('content')
    <div class="account-setting">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Settings', 'Settings')}}</h3>
                    </div>


                    <div class="content">

                        {{Html::success()}}
                        {{Html::error()}}
                        {{Html::validation(null, 'csrf_error')}}

                        {{ Form::open(array('route' => Domain::route('account::post-setting'), 'class' => 'form-grace')) }}

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?php
                                        $field = 'currency';
                                        $name = $field;
                                        $translate = Translator::transSmart('app.Currency', 'Currency');
                                        ?>
                                        {{Html::validation($user, $field)}}
                                        <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                        {{Form::select($name,  CLDR::getSupportCurrencies(false, true) , $user->getAttribute($field), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'timezone')}}
                                        <label for="timezone" class="control-label">{{Translator::transSmart('app.Timezone', 'Timezone')}}</label>
                                        {{Form::select('timezone', CLDR::getTimezones(false, true), $user->timezone, array('id' => 'timezone', 'class' => 'form-control', 'title' => Translator::transSmart('app.Timezone', 'timezone'), 'placeholder' => ''))}}
                                    </div>
                                </div>
                            </div>
                            <!--
                            <div class="row">
                                <div class="col-sm-12">
                                        <div class="form-group">
                                            {{Html::validation($user, 'language')}}
                                            <label for="language" class="control-label">{{Translator::transSmart('app.Language', 'Language')}}</label>
                                            {{Form::select('language', CLDR::getSupportLanguages(), $user->language, array('id' => 'language', 'class' => 'form-control', 'title' => Translator::transSmart('app.Language', 'Language'), 'placeholder' => ''))}}
                                        </div>
                                </div>
                            </div>
                            -->
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
    </div>
@endsection