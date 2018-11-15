@extends('layouts.admin')
@section('title', Translator::transSmart('app.Reservations', 'Reservations'))

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/reservation/index.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::reservation::index', [$property->getKey()],  URL::route('admin::managing::reservation::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Bookings', 'Bookings'), [], ['title' =>  Translator::transSmart('app.Bookings', 'Bookings')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-reservation-index">

        @include('templates.admin.managing.header', array('property' => $property, 'title' => Translator::transSmart('app.Reservations', 'Reservations')))

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('read.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::reservation::index', $property->getKey()), 'class' => 'form-search')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group"> 
                                @php
                                    $name = 'user';
                                    $translate = Translator::transSmart('app.Member', 'Member');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'status';
                                    $translate = Translator::transSmart('app.Status', 'Status');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, Utility::constant('reservation_status', true), Request::get($name), array('id' => $name, 'class' => 'form-control', 'title' => $translate, 'placeholder' => ''))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'ref';
                                    $translate = Translator::transSmart('app.Reference', 'Reference');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::text($name, Request::get($name) , array('id' => $name, 'class' => 'form-control', 'title' => $name))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">

                            </div>
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
                                              'class' => 'btn btn-theme export-btn hide',
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

                <div class="toolbox">
                    <div class="tools">

                        {{
                          Html::linkRouteWithIcon(
                            'admin::managing::reservation::check-availability',
                           Translator::transSmart('app.Check Availability', 'Check Availability'),
                           'fa-search',
                           ['property_id' => $property->getKey()],
                           [
                           'title' => Translator::transSmart('app.Check Availability', 'Check Availability'),
                           'class' => 'btn btn-theme'
                           ]
                          )
                        }}



                    </div>

                </div>

                <div class="table-responsive">
                    <table class="table table-condensed table-crowded">

                        <thead>
                            <tr>
                                <th>{{Translator::transSmart('app.#', '#')}}</th>
                                <th>{{Translator::transSmart('app.Member', 'Member')}}</th>
                                <th>{{Translator::transSmart('app.Reference No.', 'Reference No.')}}</th>
                                <th>{{Translator::transSmart('app.Status', 'Status')}}</th>
                                <th>{{Translator::transSmart('app.Building', 'Building')}}</th>
                                <th>{{Translator::transSmart('app.Facility', 'Facility')}}</th>
                                <th>{{Translator::transSmart('app.Pricing Rule', 'Pricing Rule')}}</th>
                                <th>{{Translator::transSmart('app.Reservation Date', 'Reservation Date')}}</th>
                                <th>{{trans_choice('plural.credit', 2)}}</th>
                                <th>{{Translator::transSmart('app.Remark', 'Remark')}}</th>
                                <th>{{Translator::transSmart('app.Person', 'Person')}}</th>
                                <th>{{Translator::transSmart('app.Time', 'Time')}}</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            @if($reservations->isEmpty())
                                <tr>
                                    <td class="text-center empty" colspan="13">
                                        --- {{ Translator::transSmart('app.No Record.', 'No Record.') }} ---
                                    </td>
                                </tr>
                            @endif
                            <?php $count = 0; ?>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>{{++$count}}</td>
                                    <td>
                                        @if($isReadMemberProfile)
                                            {{
                                              Html::linkRoute(
                                               'admin::managing::member::profile',
                                               $reservation->user->full_name,
                                               [
                                                'property_id' => $property->getKey(),
                                                'id' => $reservation->user->getKey()
                                               ],
                                               [
                                                'target' => '_blank'
                                               ]
                                              )
                                            }}
                                        @else
                                            <b>{{Translator::transSmart('app.Name', 'Name')}}</b>
                                            <hr />
                                            {{$reservation->user->full_name}}
                                            <hr />
                                            <b>{{Translator::transSmart('app.Username', 'Username')}}</b>
                                            <hr />
                                            {{$reservation->user->username}}
                                            <hr />
                                            <b>{{Translator::transSmart('app.Email', 'Email')}}</b>
                                            <hr />
                                            {{$reservation->user->email}}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Reference No.', 'Reference No.')}}</h6>
                                            <span>{{$reservation->ref}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Receipt No.', 'Receipt No.')}}</h6>
                                            <span>{{$reservation->rec}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{Utility::constant(sprintf('reservation_status.%s.name', $reservation->status))}}
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6 class="inline">{{Translator::transSmart('app.Block', 'Block')}}</h6>
                                            <span>{{$reservation->facility->block}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6 class="inline">{{Translator::transSmart('app.Level', 'Level')}}</h6>
                                            <span>{{$reservation->facility->level}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6 class="inline">{{Translator::transSmart('app.Unit', 'Unit')}}</h6>
                                            <span>{{$reservation->facility->unit}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Name', 'Name')}}</h6>
                                            <span>{{$reservation->facility->name}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Label', 'Label')}}</h6>
                                            <span>{{$reservation->facilityUnit->name}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Seat', 'Seat')}}</h6>
                                            <span>{{$reservation->seat}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{Utility::constant(sprintf('pricing_rule.%s.name', $reservation->rule))}}
                                    </td>
                                    <td>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Start', 'Start')}}</h6>
                                            <span>{{CLDR::showDateTime($reservation->start_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}</span>
                                        </div>

                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.End', 'End')}}</h6>
                                            <span>{{CLDR::showDateTime($reservation->end_date, config('app.datetime.datetime.format_timezone'), $property->timezone)}}</span>
                                        </div>
                                    </td>

                                    <td>
                                        @php
                                            $reservation->setup($property, $reservation->start_date, $reservation->end_date);
                                            $pricing_rule_name = Utility::constant(sprintf('pricing_rule.%s.name', $reservation->rule));
                                            $complimentary_credit = ($reservation->complimentary_credit > 0) ? CLDR::showCredit($reservation->complimentary_credit) : CLDR::showNil();

                                            $original_currency = clone $currency;
                                            $currency->base = $reservation->base_currency;
                                            $currency->quote  = $reservation->quote_currency;
                                            $currency->base_amount = $reservation->base_rate;
                                            $currency->quote_amount = $reservation->quote_rate;

                                            $original_currency->base = $reservation->quote_currency;
                                            $original_currency->quote = $reservation->base_currency;
                                            $original_currency->base_amount = $reservation->base_rate;
                                            $original_currency->quote_amount = $currency->convertFromQuoteToBase($reservation->base_rate);

                                            $rate = sprintf(
                                                       '[%s = %s] on %s',
                                                        $original_currency->formatBaseAmountWithCredit(),
                                                        CLDR::showPrice($original_currency->convertFromBaseToQuote($wallet->creditToBaseAmount($original_currency->base_amount)), $original_currency->quote, $original_currency->getPrecision()),
                                                       CLDR::showDateTime($reservation->getAttribute($reservation->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)
                                                   );


                                        @endphp

                                        <a href="javascript:void(0);" class="show-price">
                                            {{CLDR::showCredit($reservation->grossPriceInGrossCredits(), 0, true)}}
                                        </a>

                                        @include('templates.widget.bootstrap.modal', array('modal_title' => Translator::transSmart('app.Price Breakdown', 'Price Breakdown'), 'modal_body_html' => sprintf('<table class="table table-bordered table-condensed table-crowded"><tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr> <tr><td>%s</td><td>%s</td></tr>
                                        <tr><td>%s</td><td>%s</td></tr></table><div><span class="help-block">%s</span></div>',Translator::transSmart('app.Price %s', sprintf('Price %s', $pricing_rule_name), false, ['pricing_rule', $pricing_rule_name]), CLDR::showPrice($reservation->price, $reservation->base_currency, Config::get('money.precision')), Translator::transSmart('app.Discount (%s)', sprintf('Discount (%s)', '&#37;'), true, ['symbol' => '&#37;']), CLDR::showDiscount($reservation->discount, true), Translator::transSmart('app.Net Price', 'Net Price'), CLDR::showPrice($reservation->netPrice(), $reservation->base_currency, Config::get('money.precision')),Translator::transSmart('app.Taxable Amount', 'Taxable Amount'), CLDR::showPrice($reservation->taxableAmount(), $reservation->base_currency, Config::get('money.precision')), Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($reservation->tax_value)), true, ['tax' => CLDR::showTax($reservation->tax_value)]), CLDR::showPrice($reservation->tax(), $reservation->base_currency, Config::get('money.precision')), Translator::transSmart('app.Total Price', 'Total Price'), CLDR::showPrice($reservation->grossPrice(), $reservation->base_currency, Config::get('money.precision')),
                                        Translator::transSmart('app.Total Credit', 'Total Credit'), CLDR::showCredit($reservation->grossPriceInCredits()),
                                        Translator::transSmart('app.Complimentary Credit', 'Complimentary Credit'), $complimentary_credit,
                                          Translator::transSmart('app.Total Charges', 'Total Charges'), CLDR::showCredit($reservation->grossPriceInGrossCredits()), $rate
                                        )))
                                    </td>

                                    <td>
                                        {{$reservation->remark}}
                                    </td>

                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Creator', 'Creator')}}</h6>
                                            <span>{{$reservation->getCreatorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Editor', 'Editor')}}</h6>
                                            <span>{{$reservation->getEditorFullName(Translator::transSmart('app.System', 'System'))}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Created', 'Created')}}</h6>
                                            <span>
                                                   {{CLDR::showDateTime($reservation->getAttribute($reservation->getCreatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                        <div class="child-col">
                                            <h6>{{Translator::transSmart('app.Modified', 'Modified')}}</h6>
                                            <span>
                                                 {{CLDR::showDateTime($reservation->getAttribute($reservation->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)}}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="item-toolbox">

                                        @if($isWrite)

                                            @if($reservation->isConfirm())
                                                {{ Form::open(array('route' => array('admin::managing::reservation::post-cancel', $property->getKey(), $reservation->getKey()), 'class' => 'text-inline', 'onsubmit' => 'return confirm("' . Translator::transSmart('app.Are you sure to cancel?', 'Are you sure to cancel?') . '");'))}}
                                                    {{ method_field('POST') }}

                                                    {{
                                                      Html::linkRouteWithIcon(
                                                        null,
                                                       Translator::transSmart('app.Cancel', 'Cancel'),
                                                       'fa-trash',
                                                       [],
                                                       [
                                                       'title' => Translator::transSmart('app.Cancel', 'Cancel'),
                                                       'class' => 'btn btn-theme',
                                                       'onclick' => '$(this).closest("form").submit(); return false;'
                                                       ]
                                                      )
                                                    }}

                                                {{ Form::close() }}
                                            @endif

                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container">
                    @php
                        $query_search_param = Utility::parseQueryParams();
                    @endphp
                    {!! $reservations->appends($query_search_param)->render() !!}
                </div>

            </div>
        </div>

    </div>

@endsection