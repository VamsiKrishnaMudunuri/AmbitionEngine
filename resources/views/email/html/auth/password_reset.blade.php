<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Reset Your Password', 'Reset Your Password'))

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
                    {{ Translator::transSmart('app.You are receiving this email because we received a password reset request for your account.', 'You are receiving this email because we received a password reset request for your account.') }}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart('app.Kindly note that the link to reset the password will expire after 1 hour.', 'Kindly note that the link to reset the password will expire after 1 hour.') }}
                </p>

                <!-- Action Button -->
                <table style="{{ $style['body-action'] }}" align="center" width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center">
                            <a href="{{ URL::route('member::auth::reset', ['token' => $user->token]) }}"
                               style="{{ $fontFamily }} {{ $style['button'] }} {{ $style['button-theme'] }}"
                               class="button"
                               target="_blank">
                                {{ Translator::transSmart('app.Reset Your Password', 'Reset Your Password') }}
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Outro -->
                <p style="{{ $style['paragraph'] }}">

                    @php
                        $support_email = sprintf('<a style="%s" href="mailto:%s" target="_blank">%s</a>', $style['anchor'], config('company.email.webmaster'), config('company.email.webmaster'));
                    @endphp
                    {{ Translator::transSmart('app.If you did not request to reset your password, kindly contact the administrator at %s.', sprintf('If you did not request to reset your password, kindly contact the administrator at %s.', $support_email), true, ['email' => $support_email])}}

                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

                <!-- Sub Copy -->
                <!--
                <table style="{{ $style['body-sub'] }}">
                    <tr>
                        <td style="{{ $fontFamily }}">
                            <p style="{{ $style['paragraph-sub'] }}">
                                {{ Translator::transSmart("app.If you’re having trouble clicking the \"Reset Your Password\" button, copy and paste the URL below into your web browser:", "If you’re having trouble clicking the \"Reset Your Password\" button, copy and paste the URL below into your web browser:") }}
                            </p>
                            <p style="{{ $style['paragraph-sub'] }}">
                                <a style="{{ $style['anchor'] }}" href="{{ URL::route('member::auth::reset', ['token' => $user->token]) }}" target="_blank">
                                    {{ URL::route('member::auth::reset', ['token' => $user->token]) }}
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
                -->
            </td>
        </tr>
    </table>

@endsection
