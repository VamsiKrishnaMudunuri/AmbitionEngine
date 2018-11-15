<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.New Lead Notification', 'New Lead Notification'))

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
                    {{ Translator::transSmart('app.Details for new lead as follows:', 'Details for new lead as follows:') }}
                </p>

                <!-- Action Button -->
                <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                    <col width="50">
                    <col width="10">
                    <col width="455">
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Office', 'Office') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$property->name}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Link', 'Link') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            <a style="{{ $style['anchor'] }}" href="{{ URL::route('admin::managing::lead::edit', array('property_id' => $property->getKey(), 'id' => $lead->getKey())) }}" target="_blank">
                                {{ URL::route('admin::managing::lead::edit', array('property_id' => $property->getKey(), 'id' => $lead->getKey())) }}
                            </a>
                        </td>
                    </tr>
                </table>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
