<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email_blank')

@section('title', Translator::transSmart('app.Confirmation for meeting room booking', 'Confirmation for meeting room booking'))

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
                        {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($user->full_name)]) }}
                    </h1>

                    <!-- Intro -->
                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart('app.This is an auto-generated message confirming your meeting room booking.', 'This is an auto-generated message confirming your meeting room booking.') }}
                    </p>


                    <table style="{{ $style['table'] }}" width="100%" cellpadding="0" cellspacing="0">
                        <col width="50">
                        <col width="10">
                        <col width="455">
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Venue', 'Venue') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                                {{$property->name}}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Building', 'Building') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                               {{ sprintf( '%s-%s-%s', $facility->block, $facility->level, $facility->unit )  }}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Room', 'Room') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                                {{ sprintf('%s-%s', $facility->name, $facility_unit->name) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Date and Time', 'Date and Time') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">

                                {{ sprintf('%s %s %s', CLDR::showDateTime($reservation->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone), Translator::transSmart('app.to', 'to'), CLDR::showDateTime($reservation->end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">{{ Translator::transSmart('app.Remark', 'Remark') }}</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">:</td>
                            <td style="{{ $fontFamily }} {{ $style['table-cell'] }}" align="left">
                                {{ $reservation->remark }}
                            </td>
                        </tr>
                    </table>

                    <p style="{{ $style['paragraph'] }}">

                        {{Translator::transSmart('app.Kindly take note of the following meeting room booking policy.', 'Kindly take note of the following meeting room booking policy.')}} <br /> <br/>

                        {{Translator::transSmart("app.1. Members are only able to utilise their meeting room credits allowance at the Common Ground venue they registered at. ie. Registered member of Damansara Heights can only utilise meeting room credits allowance at Damansara Heights. Meeting room bookings at other Common Ground venues will require the use of wallet's credits as payment.", "1. Members are only able to utilise their meeting room credits allowance at the Common Ground venue they registered at. ie. Registered member of Damansara Heights can only utilise meeting room credits allowance at Damansara Heights. Meeting room bookings at other Common Ground venues will require the use of wallet's credits as payment.")}} <br /><br />

                        {!!  $reservation->generateRoomCancellationPolicyText(Translator::transSmart("app.2.Members are allowed to book meeting rooms across all branches now. At the moment there are no charges, neither through wallet. However cancellation of meeting rooms will incur the following of penalties according to the time of cancellation relative to the booked meeting room time.", "2.Members are allowed to book meeting rooms across all branches now. At the moment there are no charges, neither through wallet. However cancellation of meeting rooms will incur the following of penalties according to the time of cancellation relative to the booked meeting room time."), false, '<br />') !!}

                        <!--
                        {!!  $reservation->generateRoomCancellationPolicyText(Translator::transSmart("app.2. Members are free to book meeting rooms according to their availability however cancellation of meeting rooms will incur the following of penalties according to the time of cancellation relative to the booked meeting room time.", "2. Members are free to book meeting rooms according to their availability however cancellation of meeting rooms will incur the following of penalties according to the time of cancellation relative to the booked meeting room time."), false, '<br />') !!}
                        -->

                    </p>

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Kindly contact the Community Team, should you have any inquiries or concerns.", "Kindly contact the Community Team, should you have any inquiries or concerns.") }}
                    </p>

                    <p style="{{ $style['paragraph'] }}">
                        {{ Translator::transSmart("app.Thanks and have a pleasant day.", "Thanks and have a pleasant day.") }}
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
