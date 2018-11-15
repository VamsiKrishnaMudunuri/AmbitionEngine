@extends('layouts.member')
@section('title', Translator::transSmart('app.Change Your Password', 'Change Your Password'))
@section('center-justify', true)
@section('content')
    <div class="account-password">
        <div class="section section-zoom-in">
            <div class="row">

                <div class="col-sm-12">

                    <div class="page-header">
                        <h3>{{Translator::transSmart('app.Change Your Password', 'Change Your Password')}}</h3>
                    </div>

                    <div class="content">

                        {{Html::success()}}
                        {{Html::error()}}
                        {{Html::validation(null, 'csrf_error')}}

                        {{ Form::open(array('route' => Domain::route('account::post-password'), 'class' => 'form-grace')) }}
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'password_existing')}}
                                        {{Form::password('password_existing', array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.Your Current Password', 'Your Current Password'), 'placeholder' => Translator::transSmart('app.Your Current Password', 'Your Current Password')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'password')}}
                                        {{Form::password('password', array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.New Password', 'New Password'), 'placeholder' => Translator::transSmart('app.New Password', 'New Password')))}}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        {{Html::validation($user, 'password_confirmation')}}
                                        {{Form::password('password_confirmation', array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.Confirm Your New Password', 'Confirm Your New Password'), 'placeholder' => Translator::transSmart('app.Confirm Your New Password', 'Confirm Your New Password')))}}
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

    </div>
@endsection