<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email_blank')

@section('title', Translator::transSmart('app.Cancellation for meeting room booking', 'Cancellation for meeting room booking'))

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
                        {{ Translator::transSmart('app.This is an auto-generated message confirming the cancellation of your meeting room booking.', 'This is an auto-generated message confirming the cancellation of your meeting room booking.') }}
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
                    </table>


                    <p style="{{ $style['paragraph'] }}">

                        @if($penaltyChargeInPercentage <= 0)

                            {{ Translator::transSmart('app.Your allowance will be reimbursed to your wallet within 24 hours of your cancellation.', 'Your allowance will be reimbursed to your wallet within 24 hours of your cancellation.') }}
                        @else

                            {{ Translator::transSmart("app.Your meeting room booking has been cancelled %s hour(s) before the booked meeting room time, therefore %s% of the credit's charged for the meeting room booking will be incurred as a penalty, as disclosed under the meeting room booking policy.', 'Your allowance will be reimbursed to your wallet within 24 hours of your cancellation.", sprintf("Your meeting room booking has been cancelled %s hours before the booked meeting room time, therefore %s% of the credit's charged for the meeting room booking will be incurred as a penalty, as disclosed under the meeting room booking policy.', 'Your allowance will be reimbursed to your wallet within 24 hours of your cancellation.", $penaltyHour, $penaltyChargeInPercentage), false, ['hour' => $penaltyHour, 'charge' => $penaltyChargeInPercentage]) }}

                        @endif

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
