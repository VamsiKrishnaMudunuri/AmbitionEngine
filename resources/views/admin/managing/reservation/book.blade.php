@extends('layouts.admin')
@section('title', Translator::transSmart('app.Booking', 'Booking'))

@section('styles')
    @parent
    {{ Html::skin('app/modules/admin/managing/reservation/booking.css') }}
@endsection
@section('scripts')
    @parent
    {{ Html::skin('app/modules/admin/managing/reservation/booking.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::reservation::index', [$property->getKey()],  URL::route('admin::managing::reservation::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Bookings', 'Bookings'), [], ['title' =>  Translator::transSmart('app.Bookings', 'Bookings')]],

            [URL::getAdvancedLandingIntended('admin::managing::reservation::check-availability', [$property->getKey()],  URL::route('admin::managing::reservation::check-availability', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Check Availability', 'Check Availability'), [], ['title' =>  Translator::transSmart('app.Check Availability', 'Check Availability')]],

            ['admin::managing::reservation::book', Translator::transSmart('app.Booking', 'Booking'), ['property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'facility_unit_id' => $facility_unit->getKey(), 'pricing_rule' => $pricing_rule, 'start_date' => Hashids::encode($start_date), 'end_date' => Hashids::encode($end_date)], ['title' =>  Translator::transSmart('app.Booking', 'Booking')]]

        ))

    }}
@endsection

@section('content')


    <div class="admin-managing-reservation-book">


        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Booking', 'Booking')}}
                    </h3>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">


                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($reservation, 'csrf_error')}}

                {{ Form::open(array('route' => array('admin::managing::reservation::post-book', $property->getKey(), $facility->getKey(), $facility_unit->getKey(), $pricing_rule, Crypt::encrypt($start_date), Crypt::encrypt($end_date)), 'class' => 'form-horizontal booking-form')) }}

                    <div class="row">

                        <div class="col-sm-2">
                            <div class="photo">
                                <div class="photo-frame lg">
                                    <a href="javacript:void(0);">

                                        <?php
                                        $config = $sandbox->configs(\Illuminate\Support\Arr::get($facility::$sandbox, 'image.profile'));
                                        $sandbox->magicSubPath($config, [$property->getKey()]);
                                        $mimes = join(',', $config['mimes']);
                                        $minDimension =  \Illuminate\Support\Arr::get($config, 'min-dimension');
                                        $dimension =  \Illuminate\Support\Arr::get($config, 'dimension.sm.slug');
                                        ?>

                                        {{ $sandbox::s3()->link($facility->profileSandboxWithQuery, $facility, $config, $dimension)}}

                                    </a>
                                </div>
                                <div class="name">
                                    <a href="javascript:void(0);">
                                        <h4>{{$facility->name}}</h4>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                            $field = 'rule';
                            $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                        ?>

                        {{Form::hidden($name, $reservation->getAttribute($field))}}

                        <div class="col-sm-10 booking-form-right">
                            <div class="form-group">
                                <?php
                                $field = 'start_date';
                                $field1 = 'end_date';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $name1 = sprintf('%s[%s]', $reservation->getTable(), $field1);
                                $translate = Translator::transSmart('app.Reservation Date', 'Reservation Date');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, [$field, $field1])}}
                                    <p class="form-control-static">
                                        {{
                                            sprintf('%s - %s',
                                            CLDR::showDateTime($reservation->getAttribute($field), config('app.datetime.datetime.format_timezone'), $property->timezone),
                                             CLDR::showDateTime($reservation->getAttribute($field1), config('app.datetime.datetime.format_timezone'), $property->timezone))
                                         }}
                                    </p>
                                    {{Form::hidden($name, $property->localDate($reservation->getAttribute($field)))}}
                                    {{Form::hidden($name1, $property->localDate($reservation->getAttribute($field1)))}}
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'name';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Facility Name', 'Facility Name');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'category_name';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Facility Category', 'Facility Category');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'unit_number';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Building', 'Building');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'name';
                                $name = sprintf('%s[%s]', $facility_unit->getTable(), $field);
                                $translate = Translator::transSmart('app.Label', 'Label');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($facility_unit, $field)}}
                                    <p class="form-control-static">{{$facility_unit->getAttribute($field)}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'seat';
                                $name = sprintf('%s[%s]', $facility->getTable(), $field);
                                $translate = Translator::transSmart('app.Seat', 'Seat');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($facility, $field)}}
                                    <p class="form-control-static">{{$facility->getAttribute($field)}}</p>
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'price';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Price %s', sprintf('Price %s', Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule))), false, ['pricing_rule_name' => Utility::constant(sprintf('pricing_rule.%s.name', $facility_price->rule))])
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$reservation->base_currency}}</span>
                                        {{Form::text($name, CLDR::number($reservation->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'discount';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Discount', 'Discount');
                                $translate1 = Translator::transSmart('app.Only allow integer value.', 'Only allow integer value.');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        {{Form::text($name, $reservation->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control integer-value', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                                        <span class="input-group-addon">&#37;</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'net_price';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Net Price', 'Net Price');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$reservation->base_currency}}</span>
                                        {{Form::text($name, CLDR::number($reservation->netPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'data-original-net-price' => $reservation->netPrice(), 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'taxable_amount';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Taxable Amount', 'Taxable Amount');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation( $reservation, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$reservation->base_currency}}</span>
                                        {{Form::text($name, CLDR::number( $reservation->taxableAmount(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'tax';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($reservation->tax_value)), true, ['tax' => CLDR::showTax($reservation->tax_value)])
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$reservation->base_currency}}</span>
                                        {{Form::text($name, CLDR::number($reservation->tax(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'data-is-taxable' => $reservation->is_taxable, 'data-tax-value' => $reservation->tax_value,'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'gross_price';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$reservation->base_currency}}</span>
                                        {{Form::text($name, CLDR::number($reservation->grossPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <hr />
                                    <h3>
                                        {{Translator::transSmart('app.Payment', 'Payment')}}
                                    </h3>
                                    <hr />
                                </div>
                            </div>

                            <div class="form-group">
                                <?php
                                $field = 'gross_price_credit';
                                $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    <div class="input-group">
                                        @php
                                            $credits = $reservation->grossPriceInCredits();
                                        @endphp
                                        <span class="input-group-addon">{{trans_choice('plural.credit', $credits)}}</span>
                                        {{Form::text($name, CLDR::number($credits, 0), array('id' => $name, 'readonly' => 'readonly', 'data-quote-rate' => $currency->quote_amount, 'class' => sprintf('%s form-control price-value', $field), 'data-wallet-unit' => Config::get('wallet.unit'),  'title' => $translate))}}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <p class="form-control-static">
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

                                           <br />  <br />
                                           {{Translator::transSmart('app.Complimentary credit(s) will be applied automatically by the system if any.', 'Complimentary credit(s) will be applied automatically by the system if any.')}}

                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="form-group required">
                                <?php
                                    $field = 'user_id';
                                    $name = sprintf('%s[%s]', $reservation->getTable(), $field);
                                    $name1 = sprintf('%s[%s]', 'typeahead', $field);
                                    $translate = Translator::transSmart('app.Member', 'Member');
                                    $route =  URL::route('admin::managing::member::reservation', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey()));
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($reservation, $field)}}
                                    {{Form::hidden($name, $reservation->getAttribute($field), array('class' => sprintf('%s_hidden form-control', $field)))}}

                                    <div class="twitter-typeahead-container">
                                        {{Form::text($name1, $reservation->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'data-url' => $route, 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => Translator::transSmart('app.Search by name, username or email.', 'Search by name, username or email.')))}}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <div class="btn-group">
                                        @php
                                            $submit_text = Translator::transSmart('app.Pay', 'Pay');
                                        @endphp
                                        {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
                                    </div>
                                    <div class="btn-group">

                                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::reservation::check-availability', [$property->getKey()],  URL::route('admin::managing::reservation::check-availability', array('property_id' => $property->getKey()))) . '"; return false;')) }}

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>


                {{ Form::close() }}



            </div>
        </div>

    </div>

@endsection