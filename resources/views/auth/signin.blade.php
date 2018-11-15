@extends('layouts.auth')
@section('title', Translator::transSmart('app.Sign In', 'Sign In'))

@section('center-focus', true)

@section('full-width-section')
    <div class="page-auth">
        <section class="auth">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4 d-flex form-auth-container">
                        <div class="row">
                            <div class="col-md-12" style="min-width: 360px">
                                <div class="page-header border-b-no text-center">
                                    <h3>
                                        <b>
                                            {{ Translator::transSmart("app.Sign In", "Sign In") }}
                                        </b>
                                    </h3>
                                </div>

                                <div class="">

                                    {{Html::success()}}
                                    {{Html::error()}}

                                    @if( Session::has('status') )
                                        {{Html::successBox(Session::get('status'))}}
                                    @endif

                                    {{ Form::open(array('route' => Domain::route('auth::post-signin'), 'class' => 'sign-in-form form-horizontal m-y-10 form-feedback p-x-15 p-y-10-full')) }}
                                    <div class="form-group">
                                        @if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)

                                            {{Html::validation($user, ['csrf_error', 'email'])}}
                                            {{Form::email('email', null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue(config('auth.login.email.slug')), 'title' => Translator::transSmart('app.Email', 'Email'), 'placeholder' => Translator::transSmart('app.E-mail Address', 'E-mail Address')))}}

                                        @elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)

                                            {{Html::validation($user, ['csrf_error', 'username'])}}
                                            {{Form::text('username', null, array('class' => 'form-control input-transparent border-color-brown', 'maxlength' => $user->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username'), 'placeholder' => Translator::transSmart('app.Username', 'Username')))}}

                                        @endif
                                    </div>

                                    <div class="form-group m-t-10-full">
                                        {{Html::validation($user, 'password')}}
                                        {{Form::password('password', array('class' => 'form-control input-transparent border-color-brown',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.Password', 'Password'), 'placeholder' => Translator::transSmart('app.Password', 'Password')))}}
                                    </div>

                                    <div class="form-group m-t-10-full">
                                        <div class="pull-left text-black">
                                            <p class="checkbox-container">
                                                {{Form::checkbox('remember', true, false, array('title' => Translator::transSmart('app.Remember Me', 'Remember Me'), 'class' => 'input-transparent border-color-brown', 'id' => 'remember_me'))}}
                                                <label for="remember_me"
                                                       class="f-w-500">{{Translator::transSmart('app.Remember Me', 'Remember Me')}}</label>
                                            </p>

                                        </div>
                                        <div class="pull-right">
                                            {{ Html::linkRoute(Domain::route('auth::recover'),  Translator::transSmart('app.Forgot Your Password', 'Forgot Your Password?'), [], array('title' => Translator::transSmart('app.Forgot Your Password', 'Forgot Your Password?'), 'class' => 'text-black f-w-500')) }}
                                        </div>
                                    </div>
                                    <br/>
                                    <br/>
                                    <div class="form-group">
                                        {{Form::submit(Translator::transSmart('app.Sign In', 'Sign In'), array('title' => Translator::transSmart('app.Sign In', 'Sign In'), 'class' => 'btn btn-green btn-block'))}}
                                        {{ Html::linkRoute(Domain::route('auth::signup-prime-member'),  Translator::transSmart('app.Sign Up As Prime Member', 'Sign Up As Prime Member?'), [], array('title' => Translator::transSmart('app.Sign Up As Prime Member', 'Sign Up As Prime Member?'), 'class' => 'btn btn-green btn-block')) }}

                                        @if(config('features.member.auth.sign-up-with-payment'))
                                            {{ Html::linkRoute(Domain::route('auth::signup'),  Translator::transSmart('app.Sign up now', 'Sign up now'), [], array('title' => Translator::transSmart('app.Sign up now', 'Sign up now'), 'class' => 'btn btn-green btn-block')) }}
                                        @endif
                                    </div>

                                    {{ Form::close() }}
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

{{--@section('content')--}}
    {{--<div class="auth-signin block block-fbox border">--}}
        {{--<div class="row">--}}

            {{--<div class="col-sm-12">--}}

                {{--<div class="page-header">--}}
                    {{--<h3>{{Translator::transSmart('app.Welcome', 'Welcome')}}</h3>--}}
                {{--</div>--}}
                {{--<div class="content">--}}

                    {{--{{Html::success()}}--}}
                    {{--{{Html::error()}}--}}

                    {{--@if( Session::has('status') )--}}
                        {{--{{Html::successBox(Session::get('status'))}}--}}
                    {{--@endif--}}

                    {{--{{ Form::open(array('route' => Domain::route('auth::post-signin'))) }}--}}

                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}
                                {{--<div class="form-group">--}}

                                    {{--@if(strcasecmp(config('auth.login.main'), config('auth.login.email.slug')) == 0)--}}

                                        {{--{{Html::validation($user, ['csrf_error', 'email'])}}--}}
                                        {{--{{Form::email('email', null, array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue(config('auth.login.email.slug')), 'title' => Translator::transSmart('app.Email', 'Email'), 'placeholder' => Translator::transSmart('app.Email', 'Email')))}}--}}

                                    {{--@elseif(strcasecmp(config('auth.login.main'), config('auth.login.username.slug')) == 0)--}}

                                        {{--{{Html::validation($user, ['csrf_error', 'username'])}}--}}
                                        {{--{{Form::text('username', null, array('class' => 'form-control', 'maxlength' => $user->getMaxRuleValue(config('auth.login.username.slug')),  'title' => Translator::transSmart('app.Username', 'Username'), 'placeholder' => Translator::transSmart('app.Username', 'Username')))}}--}}

                                    {{--@endif--}}

                                {{--</div>--}}

                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}
                                {{--<div class="form-group">--}}
                                    {{--{{Html::validation($user, 'password')}}--}}
                                    {{--{{Form::password('password', array('class' => 'form-control',  'maxlength' => $user->getMaxRuleValue('password'), 'autocomplete' => 'off', 'title' => Translator::transSmart('app.Password', 'Password'), 'placeholder' => Translator::transSmart('app.Password', 'Password')))}}--}}
                                    {{--<div class="checkbox">--}}
                                        {{--<label>--}}
                                            {{--{{Form::checkbox('remember', true, false, array('title' => Translator::transSmart('app.Keep me signed in', 'Keep me signed in')))}} {{Translator::transSmart('app.Keep me signed in', 'Keep me signed in')}}--}}
                                        {{--</label>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}
                                {{--<div class="form-group">--}}
                                    {{--{{Form::submit(Translator::transSmart('app.Sign In', 'Sign In'), array('title' => Translator::transSmart('app.Sign In', 'Sign In'), 'class' => 'btn btn-theme btn-block'))}}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12">--}}
                                {{--<div class="form-group">--}}
                                    {{--@if(config('features.member.auth.sign-up-with-payment'))--}}
                                        {{--{{ Html::linkRoute(Domain::route('auth::signup'),  Translator::transSmart('app.Sign up now', 'Sign up now'), [], array('title' => Translator::transSmart('app.Sign up now', 'Sign up now'))) }}--}}
                                        {{--<span class="separator">·</span>--}}
                                    {{--@endif--}}
                                    {{--{{ Html::linkRoute(Domain::route('auth::recover'),  Translator::transSmart('app.Forgot your password', 'Forgot your password?'), [], array('title' => Translator::transSmart('app.Forgot your password', 'Forgot your password?'))) }}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-sm-12 text-center">--}}
                                {{--@php--}}
                                    {{--$term = sprintf('<a href="%s" target="_blank">%s</a>', URL::route('page::term'), Translator::transSmart('app.Terms of Service', 'Terms of Service'));--}}
                                    {{--$privacy = sprintf('<a href="%s" target="_blank">%s</a>', URL::route('page::privacy'), Translator::transSmart('app.Privacy Policy', 'Privacy Policy'));--}}
                                {{--@endphp--}}
                                {{--{{Translator::transSmart('app.By signing in you agree to our %s and %s',--}}
                                {{--sprintf('By signing in you agree to our %s and %s', $term, $privacy),--}}
                                {{--true,--}}
                                {{--['term' => $term, 'privacy' => $privacy])}}--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    {{--{{ Form::close() }}--}}
                {{--</div>--}}

            {{--</div>--}}

        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}