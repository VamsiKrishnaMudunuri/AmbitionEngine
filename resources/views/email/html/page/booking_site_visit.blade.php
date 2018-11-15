<?php
    require(current(Config::get('view.paths')) . '/templates/email/html/style.php');
?>

@extends('layouts.email')

@section('title', Translator::transSmart('app.Book A Site visit', 'Book A Site visit'))

@section('styles')
    @parent
@endsection

@section('content')

    <table style="{{ $style['email-body-inner'] }}" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td style="{{ $fontFamily }} {{ $style['email-body-cell'] }}">
                <!-- Greeting -->
                <h1 style="{{ $style['header-1'] }}">
                    {{ Translator::transSmart('app.common_address', '', false, ['name' => Str::title($booking->name)]) }}
                </h1>

                <!-- Intro -->
                <p style="{{ $style['paragraph'] }}">

                    @php
                        $date = '';
                        $time = '';
                        $timezone = '';
                        if($booking->isOldVersion()){
                            $booking->schedule = $booking->schedule->copy()->timezone($booking->defaultTimezone);
                            $date = CLDR::showDate($booking->schedule, config('app.datetime.date.format'));
                            $time = CLDR::showTime($booking->schedule, config('app.datetime.time.format'), $booking->defaultTimezone);
                            $timezone = CLDR::getTimezoneByCode($booking->defaultTimezone, true);
                        }else{

                            if($booking->property && $booking->property->exists){

                                $schedule = $booking->property->localDate($booking->schedule);

                                $date = CLDR::showDate($booking->schedule, config('app.datetime.date.format'));
                                $time = CLDR::showTime($booking->schedule, config('app.datetime.time.format'), $booking->property->timezone);
                                $timezone = CLDR::getTimezoneByCode($booking->property->timezon, true);

                            }


                        }

                    @endphp

                    {{

                    Translator::transSmart('app.Thanks for booking a site visit on <b>%s</b> at <b>%s %s</b>.',
                        sprintf('Thanks for booking a site visit on <b>%s</b> at <b>%s %s</b>.',
                          $date,
                          $time,
                          $timezone
                         ),
                        true,
                        ['date' =>  $date, 'time' => $time, 'timezone' => $timezone]
                    )

                    }}
                </p>

                <p style="{{ $style['paragraph'] }}">

                    {{Translator::transSmart("app.We're looking forward to seeing you then.", "We're looking forward to seeing you then.")}}

                </p>

                <p style="{{ $style['paragraph'] }}">
                    @if($booking->property && $booking->property->readyForSiteVisitBooking())
                        {{Translator::transSmart("app.Your site visit will be at Common Ground, %s Venue, Kindly note the address below:", sprintf("Your site visit will be at Common Ground, %s Venue, Kindly note the address below:", $booking->property->place), false, ['venue' => $booking->property->place])}} <br />
                    @else
                        {{Translator::transSmart("app.Your site visit will be at Common Ground, Damansara Heights Venue, Kindly note the address below:", "Your site visit will be at Common Ground, Damansara Heights Venue, Kindly note the address below:")}} <br />
                    @endif
                    <b>
                        @if($booking->property && $booking->property->readyForSiteVisitBooking())

                            {{$booking->property->address}}

                        @else

                            Penthouse 16-1 Level 16, Wisma UOA Damansara Ⅱ, No 6, Changkat Semantan, Damansara Heights, 50490 Kuala Lumpur.

                        @endif
                    </b>
                <p>

                <p style="{{ $style['paragraph'] }}">

                    @php
                       $email = $company->info_email;
                       $phone = $company->office_phone;

                       if($booking->property && $booking->property->exists){

                        if(Utility::hasString($booking->property->info_email) && Utility::hasString(($booking->property->office_phone))){
                            $email = $booking->property->info_email;
                            $phone = $booking->property->office_phone;
                        }

                       }


                    @endphp

                    {{ Translator::transSmart("app.If you can't make it at the scheduled time, just drop us an email at %s or call us at %s",
                    sprintf("If you can't make it at the scheduled time, just drop us an email at %s or call us at %s",
                     $email, $phone),
                     false,
                     ['email' => $email, 'contact' => $phone]
                     ) }}
                </p>

                <!-- Salutation -->
                <p style="{{ $style['paragraph'] }}">
                    Sincerely,<br>{{ Utility::constant('mail.sincere.name') }}
                </p>

            </td>
        </tr>
    </table>

@endsection
