@extends('layouts.admin')
@section('title', Translator::transSmart('app.Check Availability', 'Check Availability'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/reservation/booking-matrix.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/reservation/booking-matrix.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::reservation::index', [$property->getKey()],  URL::route('admin::managing::reservation::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Bookings', 'Bookings'), [], ['title' =>  Translator::transSmart('app.Bookings', 'Bookings')]],

            [URL::getAdvancedLandingIntended('admin::managing::reservation::check-availability', [$property->getKey()],  URL::route('admin::managing::reservation::check-availability', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Check Availability', 'Check Availability'), [], ['title' =>  Translator::transSmart('app.Check Availability', 'Check Availability')]],

        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-reservation-check-availability">

        @php

            $isReadMemberProfile = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, Config::get('acl.admin.managing.member.profile'), $property]);

            $isWrite = Gate::allows(Utility::rights('write.slug'), [$admin_module_policy, $admin_module_model, $admin_module_slug, $managing_module, $property]);

        @endphp

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Check Availability', 'Check Availability')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">

                {{ Form::open(array('route' => array('admin::managing::reservation::check-availability', $property->getKey()), 'class' => 'form-search')) }}

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'category';
                                    $translate = Translator::transSmart('app.Facility', 'Facility');
                                @endphp
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                {{Form::select($name, $reservation->getFacilityList(), $category, array('id' => $name, 'class' => 'form-control', 'title' => $translate))}}
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'pricing_rule';
                                    $translate = Translator::transSmart('app.Rule', 'Rule');
                                @endphp

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group schedule">
                                    {{Form::select($name, Utility::constant('pricing_rule', true, array(), [Utility::constant('pricing_rule.0.slug'), Utility::constant('pricing_rule.1.slug')]), $pricing_rule, array('id' => $name, 'class' => sprintf('form-control %s', $name), 'data-pricing-rule' => Utility::constant('pricing_rule.0.slug'), 'title' => $translate))}}
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

                        </div>


                    </div>

                    <div class="row">

                        <div class="col-sm-3">
                            <div class="form-group">
                                @php
                                    $name = 'start_date';
                                    $translate = Translator::transSmart('app.Reserve Date', 'Reserve Date');
                                @endphp

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group schedule">
                                    {{Form::text($name, $start_date , array('id' => $name, 'class' => 'form-control datepicker', 'readonly' => 'readonly', 'title' => $translate, 'placeholder' => ''))}}
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">

                            <div class="form-group {{$pricing_rule == Utility::constant('pricing_rule.0.slug') ? '' : 'hide'}}">
                                @php
                                    $name = 'start_time';
                                    $field = $name;
                                    $translate = Translator::transSmart('app.Start Time', 'Start Time');
                                @endphp

                                {{Html::validation($reservation, $field)}}

                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group schedule">
                                    {{Form::text($name, $start_time , array('id' => $name, 'class' => sprintf('form-control %s', $name), 'title' => $translate, 'data-minutes' => $facility->minutesInterval, 'placeholder' => ''))}}
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group {{$pricing_rule == Utility::constant('pricing_rule.0.slug') ? '' : 'hide'}}">
                                @php
                                    $name = 'end_time';
                                    $field = $name;
                                    $translate = Translator::transSmart('app.End Time', 'End Time');

                                @endphp

                                {{Html::validation($reservation, $field)}}
                                <label for="{{$name}}" class="control-label">{{$translate}}</label>
                                <div class="input-group schedule">
                                    {{Form::text($name, $end_time , array('id' => $name, 'class' => sprintf('form-control %s', $name), 'title' => $translate, 'data-minutes' => $facility->minutesInterval, 'placeholder' => ''))}}
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-sm-12 toolbar">
                            <div class="btn-toolbar pull-right">
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

                @php
                    $facility_category = Utility::constant(sprintf('facility_category.%s', $category));
                @endphp
                @if(Utility::hasArray($facility_category) && $facility_category['link_to_member_portal']['flag'])

                    <div class="text-right">



                            @if($facility_category['link_to_member_portal']['flow'] == 0)
                                {{
                                  Html::linkRoute(
                                    'member::workspace::index',
                                   Translator::transSmart('app.Workspace Schedule', 'Workspace Schedule'),
                                   ['property_id' => $property->getKey(), 'date' => $property->localDate($start_date)->format(config('database.datetime.date.format'))],
                                   [
                                   'title' => Translator::transSmart('app.Workspace Schedule', 'Workspace Schedule'),
                                   'target' => '_blank',
                                   'class' => 'btn btn-theme'
                                   ]
                                  )
                                }}

                            @elseif($facility_category['link_to_member_portal']['flow'] == 1)
                                {{
                                 Html::linkRoute(
                                   'member::room::index',
                                  Translator::transSmart('app.Meeting Room Schedule', 'Meeting Room Schedule'),
                                  ['property_id' => $property->getKey(), 'date' => $property->localDate($start_date)->format(config('database.datetime.date.format'))],
                                  [
                                  'title' =>  Translator::transSmart('app.Meeting Room Schedule', 'Meeting Room Schedule'),
                                  'target' => '_blank',
                                  'class' => 'btn btn-theme'
                                  ]
                                 )
                               }}
                            @endif
                        <br />

                    </div>
                @endif
                <span class="help-block">

                   @if($original_currency->exists)

                        {{
                          sprintf(
                              '[%s = %s] on %s',
                              $original_currency->formatBaseAmountWithCredit(),
                              CLDR::showPrice($original_currency->convertFromBaseToQuote($wallet->creditToBaseAmount($original_currency->base_amount)), $original_currency->quote, $original_currency->getPrecision()),
                              CLDR::showDateTime($original_currency->getAttribute($original_currency->getUpdatedAtColumn()), config('app.datetime.datetime.format_timezone'), $property->timezone)
                          )
                        }}

                    @else

                        {{
                             Translator::transSmart('app.Please refresh your browser to retrieve latest currency rate.', 'Please refresh your browser to retrieve latest currency rate.')
                        }}

                    @endif
                </span>
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed booking-matrix">

                        <tbody>

                            @if($facilities->isEmpty())
                                <tr>
                                    <td class="text-center" colspan="11">
                                        --- {{ Translator::transSmart('app.No Facility Found.', 'No Facility Found.') }} ---
                                    </td>
                                </tr>
                            @endif

                            @foreach($facilities as $category => $categories)

                                <tr class="package">
                                    <th colspan="11">
                                        {{Utility::constant(sprintf('facility_category.%s.name', $category))}}
                                    </th>
                                </tr>

                                @foreach($categories as $unit => $units)

                                    <tr class="unit">
                                         <th colspan="11">

                                             {{
                                                Html::linkRouteWithIcon(
                                                    null,
                                                    $unit,
                                                    'fa-minus',
                                                   array(),
                                                   [
                                                       'title' => $unit,
                                                       'class' => 'unit-toggle',
                                                       'data-unit' => $unit
                                                   ]
                                                )
                                          }}

                                         </th>
                                     </tr>
                                    <tr class="facilities" data-unit="{{$unit}}">
                                        <td colspan="11">
                                            <table class="table table-condensed">
                                                <colgroup>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col>
                                                    <col width="10%">
                                                    <col>
                                                </colgroup>
                                                <tr>
                                                    <th></th>
                                                    <th class="text-center">{{Translator::transSmart('app.Name', 'Name')}}</th>
                                                    <th class="text-center">{{Translator::transSmart('app.Label', 'Label')}}</th>
                                                    <th class="text-center">{{Translator::transSmart('app.Seat', 'Seat')}}</th>
                                                    <th class="text-center">
                                                        {{Translator::transSmart('app.Price %s', sprintf('Price %s', Utility::constant(sprintf('pricing_rule.%s.name', $pricing_rule))), false, ['pricing_rule_name' => Utility::constant(sprintf('pricing_rule.%s.name', $pricing_rule))])}}
                                                    </th>
                                                    <th class="text-center">{{Translator::transSmart('app.Taxable Amount', 'Taxable Amount')}}</th>
                                                    <th class="text-center">{{Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($property->tax_value)), true, ['tax' => CLDR::showTax($property->tax_value)])}}</th>
                                                    <th class="text-center">{{Translator::transSmart('app.Total', 'Total')}}</th>
                                                    <th class="text-center">{{trans_choice('plural.credit', 0)}}</th>
                                                    <th class="text-center">{{Translator::transSmart('app.Member', 'Member')}}</th>
                                                    <th class="text-center"></th>
                                                </tr>
                                                @foreach($units as $facility)
                                                    @foreach($facility->units as $gunit)
                                                        @php
                                                            $price = $facility->prices->first();
                                                            $reservation->syncFromProperty($property);
                                                            $reservation->syncFromCurrency($currency);
                                                            $reservation->syncFromPrice($price);
                                                            $reservation->setup($property, $start_date, $end_date);
                                                        @endphp
                                                        <tr data-unit="{{$unit}}">
                                                            <td class="third-level-indent">

                                                                <?php
                                                                $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                                                $sandbox->magicSubPath($config, [$property->getKey()]);
                                                                $mimes = join(',', $config['mimes']);
                                                                $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                                                $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                                                ?>

                                                                <div class="photo">
                                                                    <div class="photo-frame md">
                                                                        <a href="javascript:void(0);">
                                                                            {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension)}}
                                                                        </a>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                            <td class="text-center">
                                                                {{$facility->name}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{$gunit->name}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{$facility->seat}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{CLDR::showPrice($reservation->price, $reservation->base_currency, Config::get('money.precision'))}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{CLDR::showPrice($reservation->taxableAmount(), $reservation->base_currency, Config::get('money.precision'))}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{CLDR::showPrice($reservation->tax(), $reservation->base_currency, Config::get('money.precision'))}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{CLDR::showPrice($reservation->grossPrice(), $reservation->base_currency, Config::get('money.precision'))}}
                                                            </td>
                                                            <td class="text-center">
                                                                {{CLDR::showCredit($reservation->grossPriceInCredits(), 0, true)}}
                                                            </td>
                                                            <td class="text-center">

                                                                @php
                                                                    $member = null;
                                                                @endphp

                                                                @if( $gunit->subscribing->count() > 0 )
                                                                    @php

                                                                        $member = $gunit->subscribing->first()->users->where('pivot.is_default', '=', Utility::constant('status.1.slug'))->first();

                                                                    @endphp
                                                                @endif

                                                                @if( $gunit->reserving->count() > 0 )
                                                                    @php
                                                                        $member = $gunit->reserving->first()->user;
                                                                    @endphp
                                                                @endif

                                                                @if(!is_null($member))
                                                                    @if($isReadMemberProfile)
                                                                        {{
                                                                          Html::linkRoute(
                                                                           'admin::managing::member::profile',
                                                                           $member->full_name,
                                                                           [
                                                                            'property_id' => $property->getKey(),
                                                                            'id' => $member->getKey()
                                                                           ],
                                                                           [
                                                                            'target' => '_blank'
                                                                           ]
                                                                          )
                                                                        }}
                                                                    @else
                                                                        {{$member->full_name}}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="text-center">

                                                                @if($gunit->subscribing->count() > 0 || $gunit->reserving->count() > 0)
                                                                    <span class="label label-success">{{Translator::transSmart('app.Reserved', 'Reserved')}}</span>
                                                                @elseif($reservation->grossPrice() <= 0)
                                                                    <span class="label label-success">{{Translator::transSmart('app.Price Not Set Up', 'Price Not Set Up')}}</span>
                                                                @elseif(
                                                                  !$property->isActive() ||
                                                                  !$facility->isActive() ||
                                                                  !$gunit->isActive() ||
                                                                  !$price->isActive()
                                                                )
                                                                    <span class="label label-default">{{Utility::constant('status.0.name')}}</span>
                                                                @elseif($property->coming_soon)
                                                                    <span class="label label-default">{{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}</span>
                                                                @else

                                                                    @if($isWrite)
                                                                        {{
                                                                               Html::linkRouteWithIcon(
                                                                                 'admin::managing::reservation::book',
                                                                                Translator::transSmart('app.Book', 'Book'),
                                                                                '',
                                                                                ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'facility_unit_id' => $gunit->getKey(),
                                                                                'pricing_rule' => $pricing_rule,
                                                                                'start_date' => Crypt::encrypt($start_date),
                                                                                 'end_date' => Crypt::encrypt($end_date)],
                                                                                [
                                                                                'title' => Translator::transSmart('app.Book', 'Book'),
                                                                                'class' => 'btn btn-theme'
                                                                                ]
                                                                               )
                                                                         }}
                                                                    @endif

                                                                @endif

                                                            </td>


                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </table>
                                        </td>
                                    </tr>



                                @endforeach
                            @endforeach


                        </tbody>


                    </table>
                </div>

            </div>
        </div>

    </div>

@endsection