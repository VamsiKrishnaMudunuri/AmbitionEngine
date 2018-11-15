<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Book A Site visit', 'Book A Site Visit'))

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
                    {{ Translator::transSmart('app.Details for site visit as follows:', 'Details for site visit as follows:') }}
                </p>

                <!-- Action Button -->
                <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                    <col width="150">
                    <col width="10">
                    <col width="355">
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.FULL NAME', 'FULL NAME') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$booking->name}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.EMAIL', 'EMAIL') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                              {{$booking->email}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.CONTACT', 'CONTACT') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$booking->contact}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.COMPANY NAME', 'COMPANY NAME') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$booking->company}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.LOCATION', 'LOCATION') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            @php
                                $location =  ($booking->isOldVersion()) ? $booking->nice_location : (($booking->property &&  $booking->property->exists) ? $booking->property->smart_name : '');
                            @endphp
                            {{$location}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.I NEED SPACE FOR', 'I NEED SPACE FOR') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{($booking->pax > 10) ? '10+' : $booking->pax}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.MEMBERSHIP TYPE', 'MEMBERSHIP TYPE') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$booking->office}}
                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.APPOINTMENT', 'APPOINTMENT') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">

                            @php
                                $date = '';
                                $timezone = '';

                                if($booking->isOldVersion()){

                                    $date = CLDR::showDateTime($booking->schedule, config('app.datetime.date.format'), $booking->defaultTimezone);
                                     $timezone = CLDR::getTimezoneByCode($booking->defaultTimezone, true);
                                }else{

                                    if($booking->property && $booking->property->exists){


                                       $date = CLDR::showDateTime($booking->schedule, config('app.datetime.date.format'), $booking->property->timezone);
                                       $timezone = CLDR::getTimezoneByCode($booking->property->timezon, true);

                                    }


                                }

                            @endphp

                            {{$date}} {{$timezone}}

                        </td>
                    </tr>
                    <tr>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.SPECIAL REQUEST', 'SPECIAL REQUEST') }}</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                        <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                            {{$booking->request}}
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
