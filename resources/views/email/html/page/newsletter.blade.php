<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Newsletter', 'Newsletter'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.Dear Sir/Madam,', 'Dear Sir/Madam,') }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart('app.Thanks for signing up to our newsletter.', 'Thanks for signing up to our newsletter.') }}
                </p>

                <p style="{{ $style['paragraph'] }}">
                    {{ Translator::transSmart("app.You're now part of an exclusive group of people that'll receive special offers, updates on the awesome events we have planned and much more.", "You're now part of an exclusive group of people that'll receive special offers, updates on the awesome events we have planned and much more.") }}
                </p>

                <p style="{{ $style['paragraph'] }}">

                    {{Html::linkRoute('page::index', Translator::transSmart("app.Begin your journey here", "Begin your journey here"), ['slug' => 'choose-us'], ['title' => Translator::transSmart("app.Begin your journey here", "Begin your journey here")])}}

                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
