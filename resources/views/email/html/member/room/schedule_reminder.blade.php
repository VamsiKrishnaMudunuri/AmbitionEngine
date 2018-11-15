<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email_blank')

@section('title', Translator::transSmart('app.Meeting Room Schedule Reminder', 'Meeting Room Schedule Reminder'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner-fluid'] }} width: 100%;" align="left" width="100%" cellpadding="0" cellspacing="0">

        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">

                <div style="padding: 5px 30px 50px 30px; text-align: justify;">
                    <!-- Greeting -->
                    <h1 style="{{ $style['header-1'] }} font-size: 18px;">
                        {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($reservation->user->full_name)]) }}
                    </h1>

                    <!-- Intro -->
                    <p style="{{ $style['paragraph'] }} font-size: 18px;">
                        {{ Translator::transSmart('app.Kindly be informed that your upcoming meeting room book is listed as below:', 'Kindly be informed that your upcoming meeting room book is listed as below:') }}
                    </p>


                    <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                        <col width="50">
                        <col width="10">
                        <col width="455">
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Venue', 'Venue') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                                {{$reservation->property->name}}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Building', 'Building') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                               {{ sprintf( '%s-%s-%s', $reservation->facility->block, $reservation->facility->level, $reservation->facility->unit )  }}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Room', 'Room') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                                {{ sprintf('%s-%s', $reservation->facility->name, $reservation->facilityUnit->name) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Date and Time', 'Date and Time') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">


                                {{ sprintf('%s %s %s', CLDR::showDateTime($reservation->start_date, config('app.datetime.datetime.format_timezone'), $reservation->property->timezone), Translator::transSmart('app.to', 'to'), CLDR::showDateTime($reservation->end_date, config('app.datetime.datetime.format_timezone'), $reservation->property->timezone)) }}
                            </td>
                        </tr>
                    </table>

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Thank You.", "Thank You.") }}
                    </p>

                    <!-- Salutation -->
                    <p style="{{ $style['paragraph'] }}">
                        Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                    </p>

                </div>


            </td>
        </tr>
    </table>


@endsection
