<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.New Agent Account', 'New Agent Account'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($user->full_name)]) }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart("app.At Common Ground, we're building a team of passionate partners who believe in redefining the idea of work.", "At Common Ground, we're building a team of passionate partners who believe in redefining the idea of work.") }}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart("app.Thanks for expressing your interest in being an agent of ours. You may login using below credentials:", "Thanks for expressing your interest in being an agent of ours. You may login using below credentials:") }}
                </p>

                <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                    <col width="150">
                    <col width="10">
                    <col width="355">
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.AGENT LOGIN LINK', 'AGENT LOGIN LINK') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {!! $credentials['link'] !!}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.EMAIL', 'EMAIL') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{ $credentials['email'] }}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.PASSWORD', 'PASSWORD') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{ $credentials['password'] }}
                        </td>
                    </tr>
                </table>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart("app.We're excited to work with you and we'll be in contact soon.", "We're excited to work with you and we'll be in contact soon.") }}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart("app.In the mean time, if you have any questions, we'd be happy to help.", "In the mean time, if you have any questions, we'd be happy to help.") }}
                    {{ Translator::transSmart("app.Just drop us an email at %s or call us at %s", sprintf("Just drop us an email at %s or call us at %s", $company->info_email, $company->office_phone), false , ['email' => $company->info_email, 'contact' => $company->office_phone]) }}
                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
