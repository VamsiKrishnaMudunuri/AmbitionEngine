@extends('layouts.admin')
@section('title', Translator::transSmart('app.Meeting Room', 'Meeting Room'))

@section('styles')
    @parent

@endsection

@section('scripts')
    @parent

@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            ['admin::managing::report::reservation::room::listing', Translator::transSmart('app.Reports', 'Reports'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Reports', 'Reports')]],

            ['admin::managing::report::reservation::room::listing', Translator::transSmart('app.Meeting Room', 'Meeting Room'), ['property_id' => $property->getKey()], ['title' => Translator::transSmart('app.Meeting Room', 'Meeting Room')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-report-reservation-room-listing">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => $property->name))

        @php

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);
            $isDelete = Gate::allows(Utility::rights('delete.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::report::reservation::room::listing', $property->getKey()), 'class' => 'form-search')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'from_date';
                                    $translate = Translator::transSmart('app.From', 'From');
                                @endphp

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Html::validation($property, $name)}}
                                <div class="input-group schedule">

                                    {{Form::text($name, $from_date , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => ''))}}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'to_date';
                                    $translate = Translator::transSmart('app.To', 'To');
                                @endphp

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Html::validation($property, $name)}}
                                <div class="input-group schedule">
                                    {{Form::text($name, $to_date , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => ''))}}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">

                        </div>

                        <div class="col-sm-3">

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-sm-12 toolbar">

                            <div class="btn-toolbar pull-right">
                                <div class="btn-group">

                                    {{
                                       Form::button(
                                           sprintf('<i class="fa fa-fw fa-file-excel-o"></i> <span>%s</span>', Translator::transSmart('app.Export', 'Export')),
                                          array(
                                              'name' => '_excel',
                                              'type' => 'submit',
                                              'value' => true,
                                              'title' => Translator::transSmart('app.Export', 'Export'),
                                              'class' => 'btn btn-theme export-btn',
                                              'onclick' => "$(this).closest('form').submit();"
                                          )
                                       )
                                   }}

                                </div>
                                <div class="btn-group">
                                    {{
                                        Html::linkRouteWithIcon(
                                            null,
                                            Translator::transSmart('app.Search', 'Search'),
                                            'fa-search',
                                           array(),
                                           [
                                               'title' => Translator::transSmart('app.Search', 'Search'),
                                               'class' => 'btn btn-theme search-btn',
                                               'onclick' => "$(this).closest('form').submit();"
                                           ]
                                        )
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>

                {{ Form::close() }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr />
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{Translator::transSmart('app.Company name which booked the meeting room', 'Company name which booked the meeting room')}}</th>
                                <th>{{Translator::transSmart('app.Date that the meeting room is booked for', 'Date that the meeting room is booked for')}}</th>
                                <th>{{Translator::transSmart('app.Start time of the meeting room booking', 'Start time of the meeting room booking')}}</th>
                                <th>{{Translator::transSmart('app.End time of the meeting room booking', 'End time of the meeting room booking')}}</th>
                                <th>{{Translator::transSmart('app.Duration of the meeting room booking', 'Duration of the meeting room booking')}}</th>
                                <th>{{Translator::transSmart('app.Name of the meeting room that is booked', 'Name of the meeting room that is booked')}}</th>
                                <th>{{Translator::transSmart('app.Credits Charged for meeting room booking', 'Credits Charged for meeting room booking')}}</th>
                                <th>{{Translator::transSmart('app.Name of person who made the booking', 'Name of person who made the booking')}}</th>
                                <th>{{Translator::transSmart('app.Date that the person made the booking', 'Date that the person made the booking')}}</th>
                                <th>{{Translator::transSmart('app.Time that the person made the booking', 'Time that the person made the booking')}}</th>
                                <th>{{Translator::transSmart('app.Date the cancellation was made', 'Date the cancellation was made')}}</th>
                                <th>{{Translator::transSmart('app.Time of the cancellation', 'Time of the cancellation')}}</th>
                                <th>{{Translator::transSmart('app.Penalty charged for late cancellation', 'Penalty charged for late cancellation')}}</th>
                                <th>{{Translator::transSmart('app.Refund of credits after penalty charged', 'Refund of credits after penalty charged')}}</th>
                            </tr>
                            <tr>
                                <th>{{Translator::transSmart('app.No.', 'No.')}}</th>
                                <th>{{Translator::transSmart('app.Company Name', 'Company Name')}}</th>
                                <th>{{Translator::transSmart('app.Date', 'Date')}}</th>
                                <th>{{Translator::transSmart('app.Start Time', 'Start Time')}}</th>
                                <th>{{Translator::transSmart('app.End Time', 'End Time')}}</th>
                                <th>{{Translator::transSmart('app.Duration', 'Duration')}}</th>
                                <th>{{Translator::transSmart('app.Meeting Room Name', 'Meeting Room Name')}}</th>
                                <th>{{Translator::transSmart('app.Credits Charged', 'Credits Charged')}}</th>
                                <th colspan="3" align="center" class="text-center">{{Translator::transSmart('app.Booking', 'Booking')}}</th>
                                <th colspan="4" align="center" class="text-center">{{Translator::transSmart('app.Cancellation (if any)', 'Cancellation (if any)')}}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.By', 'By')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Date', 'Date')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Time', 'Time')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Date', 'Date')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Time', 'Time')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Penalty', 'Penalty')}}</th>
                                <th align="center" class="text-center">{{Translator::transSmart('app.Refund', 'Refund')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($reservations->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="15">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($reservations as $reservation)
                                @php
                                    $reservation->setup($property, $reservation->start_date, $reservation->end_date);
                                    $pricing_rule_name = Utility::constant(sprintf('pricing_rule.%s.name', $reservation->rule));
                                    $complimentary_credit = ($reservation->complimentary_credit > 0) ? CLDR::showCredit($reservation->complimentary_credit) : CLDR::showNil();
                                @endphp
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>{{$reservation->user->company}}</td>
                                    <td>
                                        @if($reservation->start_date->setTimezone($property->timezone)->startOfDay()->equalTo($reservation->end_date->setTimezone($property->timezone)->copy()->startOfDay()))
                                          {{ CLDR::showDate($reservation->start_date->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                                        @else
                                            {{sprintf('%s - %s', CLDR::showDate($reservation->start_date->setTimezone($property->timezone), config('app.datetime.date.format')), CLDR::showDate($reservation->end_date->setTimezone($property->timezone), config('app.datetime.date.format')))}}
                                        @endif
                                    </td>
                                    <td>
                                        {{CLDR::showTime($reservation->start_date, config('app.datetime.time.format'), $property->timezone)}}
                                    </td>
                                    <td>
                                        {{CLDR::showTime($reservation->end_date, config('app.datetime.time.format'), $property->timezone)}}
                                    </td>
                                    <td>

                                        {{CLDR::showRelativeDateTimeUnit($reservation->start_date->toDateTimeString(), $reservation->end_date->toDateTimeString(), null, $property->timezone)}}

                                    </td>
                                    <td>{{$reservation->facility->name}}</td>
                                    <td>{{CLDR::showCredit($reservation->grossPriceInGrossCredits(), 0, true)}}</td>
                                    <td>{{$reservation->user->full_name}}</td>
                                    <td>
                                        {{ CLDR::showDate($reservation->getAttribute($reservation->getCreatedAtColumn())->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                                    </td>
                                    <td>
                                        {{CLDR::showTime($reservation->getAttribute($reservation->getCreatedAtColumn()), config('app.datetime.time.format'), $property->timezone)}}
                                    </td>
                                    @if(in_array($reservation->status, $reservation->cancelStatus))
                                        <td>
                                            {{ CLDR::showDate($reservation->cancel_date->setTimezone($property->timezone), config('app.datetime.date.format')) }}
                                        </td>
                                        <td>
                                            {{CLDR::showTime($reservation->cancel_date, config('app.datetime.time.format'), $property->timezone)}}
                                        </td>
                                        <td>
                                            {{CLDR::showCredit($reservation->grossPriceInGrossCreditsForPenaltyCharge(), 0, true)}}
                                        </td>
                                        <td>
                                            {{CLDR::showCredit($reservation->refundInCredits(), 0, true)}}
                                        </td>
                                    @else
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>



    </div>

@endsection