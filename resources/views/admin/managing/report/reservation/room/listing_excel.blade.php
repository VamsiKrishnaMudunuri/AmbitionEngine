@extends('layouts.excel')

@section('content')

    <table>
        <thead>
            <tr>
                <th width="8" valign="top" align="left" style="wrap-text:true;"></th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Company name which booked the meeting room', 'Company name which booked the meeting room')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Date that the meeting room is booked for', 'Date that the meeting room is booked for')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Start time of the meeting room booking', 'Start time of the meeting room booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.End time of the meeting room booking', 'End time of the meeting room booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Duration of the meeting room booking', 'Duration of the meeting room booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Name of the meeting room that is booked', 'Name of the meeting room that is booked')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Credits Charged for meeting room booking', 'Credits Charged for meeting room booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Name of person who made the booking', 'Name of person who made the booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Date that the person made the booking', 'Date that the person made the booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Time that the person made the booking', 'Time that the person made the booking')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Date the cancellation was made', 'Date the cancellation was made')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Time of the cancellation', 'Time of the cancellation')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Penalty charged for late cancellation', 'Penalty charged for late cancellation')}}</th>
                <th width="30" valign="top" align="left" style="wrap-text:true;">{{Translator::transSmart('app.Refund of credits after penalty charged', 'Refund of credits after penalty charged')}}</th>
            </tr>
            <tr>
                <th valign="top" align="left">{{Translator::transSmart('app.No.', 'No.')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Company Name', 'Company Name')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Date', 'Date')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Start Time', 'Start Time')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.End Time', 'End Time')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Duration', 'Duration')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Meeting Room Name', 'Meeting Room Name')}}</th>
                <th valign="top" align="left">{{Translator::transSmart('app.Credits Charged', 'Credits Charged')}}</th>
                <th colspan="3" valign="top" align="center">{{Translator::transSmart('app.Booking', 'Booking')}}</th>
                <th colspan="4" valign="top" align="center">{{Translator::transSmart('app.Cancellation (if any)', 'Cancellation (if any)')}}</th>
            </tr>
            <tr>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="left"></th>
                <th valign="top" align="center">{{Translator::transSmart('app.By', 'By')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Date', 'Date')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Time', 'Time')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Date', 'Date')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Time', 'Time')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Penalty', 'Penalty')}}</th>
                <th valign="top" align="center">{{Translator::transSmart('app.Refund', 'Refund')}}</th>
            </tr>
        </thead>
        <tbody>

            <?php $count = 0; ?>
            @foreach($reservations as $reservation)
                @php
                    $reservation->setup($property, $reservation->start_date, $reservation->end_date);
                    $pricing_rule_name = Utility::constant(sprintf('pricing_rule.%s.name', $reservation->rule));
                    $complimentary_credit = ($reservation->complimentary_credit > 0) ? CLDR::showCredit($reservation->complimentary_credit) : CLDR::showNil();
                @endphp
                <tr>
                    <td valign="top" align="left" style="wrap-text:true;">{{++$count}}</td>
                    <td valign="top" align="left" style="wrap-text:true;">{{$reservation->user->company}} {{$reservation->user->company}} {{$reservation->user->company}}</td>
                    <td valign="top" align="left" style="wrap-text:true;">
                        @if($reservation->start_date->setTimezone($property->timezone)->startOfDay()->equalTo($reservation->end_date->setTimezone($property->timezone)->copy()->startOfDay()))
                            {{ CLDR::showDate($reservation->start_date->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                        @else
                            {{sprintf('%s - %s', CLDR::showDate($reservation->start_date->setTimezone($property->timezone), config('app.datetime.date.format')), CLDR::showDate($reservation->end_date->setTimezone($property->timezone), config('app.datetime.date.format')))}}
                        @endif
                    </td>
                    <td valign="top" align="left" style="wrap-text:true;">
                        {{CLDR::showTime($reservation->start_date, config('app.datetime.time.format'), $property->timezone)}}
                    </td>
                    <td valign="top" align="left" style="wrap-text:true;">
                        {{CLDR::showTime($reservation->end_date, config('app.datetime.time.format'), $property->timezone)}}
                    </td>
                    <td valign="top" align="left" style="wrap-text:true;">

                        {{CLDR::showRelativeDateTimeUnit($reservation->start_date->toDateTimeString(), $reservation->end_date->toDateTimeString(), null, $property->timezone)}}

                    </td>
                    <td valign="top" align="left" style="wrap-text:true;">{{$reservation->facility->name}}</td>
                    <td valign="top" align="right" style="wrap-text:true;">{{CLDR::showCredit($reservation->grossPriceInGrossCredits(), 0, true)}}</td>
                    <td valign="top" align="left" style="wrap-text:true;">{{$reservation->user->full_name}}</td>
                    <td valign="top" align="left" style="wrap-text:true;">
                        {{ CLDR::showDate($reservation->getAttribute($reservation->getCreatedAtColumn())->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                    </td>
                    <td valign="top" align="left" style="wrap-text:true;">
                        {{CLDR::showTime($reservation->getAttribute($reservation->getCreatedAtColumn()), config('app.datetime.time.format'), $property->timezone)}}
                    </td>
                    @if(in_array($reservation->status, $reservation->cancelStatus))
                        <td valign="top" align="left" style="wrap-text:true;">
                            {{ CLDR::showDate($reservation->cancel_date->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                        </td>
                        <td valign="top" align="left" style="wrap-text:true;">
                            {{CLDR::showTime($reservation->cancel_date, config('app.datetime.time.format'), $property->timezone)}}
                        </td>
                        <td valign="top" align="right" style="wrap-text:true;">
                            {{CLDR::showCredit($reservation->grossPriceInGrossCreditsForPenaltyCharge(), 0, true)}}
                        </td>
                        <td valign="top" align="right" style="wrap-text:true;">
                            {{CLDR::showCredit($reservation->refundInCredits(), 0, true)}}
                        </td>
                    @else
                        <td valign="top" align="left" style="wrap-text:true;"></td>
                        <td valign="top" align="left" style="wrap-text:true;"></td>
                        <td valign="top" align="left" style="wrap-text:true;"></td>
                        <td valign="top" align="left" style="wrap-text:true;"></td>
                    @endif
                </tr>
            @endforeach
        </tbody>

    </table>

@endsection