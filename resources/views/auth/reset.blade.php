@extends('layouts.auth')
@section('title', Translator::transSmart('app.Reset Your Password', 'Reset Your Password'))

@section('center-focus', true)

@section('full-width-section')
    <div class="page-auth">
        <section class="auth auth-reset">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                    </div>
                    <div class="col-sm-12 col-md-4 form-auth-container">
                        <div class="row">
                            <div class="col-md-12" style="min-width: 360px">
                                <div class="page-header border-b-no text-center">
                                    <h3>{{Translator::transSmart('app.Reset Your Password', 'Reset Your Password')}}</h3>
                                </div>
                            </div>
                        </div>

                        <div class="content">

                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{Html::success()}}
                            {{Html::error()}}


                            {{ Form::open(array('route' => Domain::route('auth::post-reset'), 'class' => 'form-horizontal m-y-10 form-feedback p-x-15 p-y-10-full')) }}

                                <div class="form-group">

                                    {{Form::hidden('token', $token)}}

                                    @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)

                                        {{Html::validation($user, ['csrf_error', 'token', config('auth.login.email.slug')])}}
                                        {{Form::email(config('auth.login.email.slug'), null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue(config('auth.login.email.slug')), 'title' => Translator::transSmart('app.Email', 'Email'), 'placeholder' => Translator::transSmart('app.Email', 'Email')))}}

                                    @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)

                                        {{Html::validation($user, ['csrf_error', 'token', config('auth.login.username.slug')])}}
                                        {{Form::text(config('auth.login.username.slug'), null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue(config('auth.login.username.slug')), 'title' => Translator::transSmart('app.Username', 'Username'), 'placeholder' => Translator::transSmart('app.Username', 'Username')))}}

                                    @endif


                                </div>
                                <div class="form-group">
                                    {{Html::validation($user, 'password')}}
                                    {{Form::password('password', array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.New Password', 'New Password'), 'placeholder' => Translator::transSmart('app.New Password', 'New Password')))}}
                                </div>
                                <div class="form-group">
                                    {{Html::validation($user, 'password_confirmation')}}
                                    {{Form::password('password_confirmation', array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.Confirm Your New Password', 'Confirm Your New Password'), 'placeholder' => Translator::transSmart('app.Confirm Your New Password', 'Confirm Your New Password')))}}
                                </div>

                                <br/>
                                <br/>

                                <div class="form-group">
                                    {{Form::submit(Translator::transSmart('app.Reset', 'Reset'), array('title' => Translator::transSmart('app.Reset', 'Reset'), 'class' => 'btn btn-green btn-block'))}}
                                </div>
                                <div class="form-group">
                                    {{ Html::linkRoute(Domain::route('auth::signin'),  Translator::transSmart('app.Sign in', 'Sign in'), [], array('title' => Translator::transSmart('app.Sign in', 'Sign in'), 'class' => 'text-black f-w-500')) }}
                                    @if(config('features.member.auth.sign-up-with-payment'))
                                        <span class="separator">·</span>
                                        {{ Html::linkRoute(Domain::route('auth::signup'),  Translator::transSmart('app.Sign up now', 'Sign up now'), [], array('title' => Translator::transSmart('app.Sign up now', 'Sign up now'), 'class' => 'text-black f-w-500')) }}
                                    @endif
                                </div>

                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection