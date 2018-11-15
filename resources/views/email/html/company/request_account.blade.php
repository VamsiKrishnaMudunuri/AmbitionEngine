<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Request for Account', 'Request for Account'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.Dear Sir/Madam,', 'Dear Sir/Madam,') }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">
                     {{Translator::transSmart('app.<b>%s</b> requests an account for administrative works.',  sprintf('<b>%s</b> request an account for administrative works.', $user->full_name), true, ['name' => $user->full_name])}}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart('app.You can log on to the website and activate his/her request.', 'You can log on to the website and activate his/her request.') }}
                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
