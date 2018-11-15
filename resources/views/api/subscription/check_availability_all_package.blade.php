@extends('layouts.plain')

@section('styles')
    @parent
    {{ Html::skin('app/modules/api/subscription/check-availability-all-package.css') }}
@endsection

@section('scripts')
    @parent
@endsection


@section('content')

    <div class="api-subscription-check-availability-all-package">

        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}


                <table class="table table-condensed">

                        <colgroup>
                            <col width="5%">
                            <col width="95%">
                        </colgroup>

                        <tbody>

                            @php

                                $subscription->syncFromProperty($property);
                                $subscription->syncFromPrice($package);
                                $subscription->setupInvoice($property, $start_date);

                                $isInactive = ($subscription->price <= 0 || !$property->isActive() || $property->coming_soon || !$package->isActive()) ? true : false;

                            @endphp

                            <tr>
                                <td colspan="2">
                                    <b>
                                        {{$package->name}}
                                    </b>
                                </td>
                            </tr>
                            <tr class="package {{$isInactive ? 'inactive' : ''}}">
                                <td></td>
                                <td>

                                    <?php
                                    $field = 'package';
                                    $name = sprintf('%s[%s]', $property->getTable(), $field);
                                    $type = 0;
                                    $value = sprintf('%s-%s-%s', $property->getKey(), $type, $package->getKey());
                                    $options = array(
                                        'data-url' => URL::route('api::subscription::order-summary', array('property_id' => $property->getKey(), 'type' => $type, 'id' => $package->getKey()))
                                    );
                                    if($isInactive){
                                        $options['disabled'] = 'disabled';
                                    }
                                    ?>

                                    <div class="row">
                                        <div class="col-xs-8 col-sm-8 col-md-9">
                                            <div class="name">
                                                {{Form::radio($name, $value, null, $options)}}
                                                {{$package->name}}
                                            </div>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-3">
                                            <div class="price">
                                                {{CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'))}}
                                            </div>
                                        </div>

                                    </div>




                                </td>

                            </tr>


                            @foreach($facilities as $category => $categories)

                                <tr>
                                    <td colspan="2">
                                        <b>
                                            {{Utility::constant(sprintf('facility_category.%s.name', $category))}}
                                        </b>
                                    </td>
                                </tr>

                                @foreach($categories as $facility)

                                    @php

                                        $price = $facility->prices->first();
                                        $subscription->syncFromProperty($property);
                                        $subscription->syncFromPrice($price);
                                        $subscription->setupInvoice($property, $start_date);
                                        $isInactive = ($subscription->price <= 0 || !$property->isActive() || $property->coming_soon || !$facility->isActive() || !$price->isActive()) ? true : false;

                                        if($facility->activeUnitsCountWithQuery->isEmpty() || $facility->activeUnitsCountWithQuery->first()->count <= 0){
                                            $isInactive = true;
                                        }

                                        $unitsWithNoReserved = (!$facility->activeUnitsCountWithQuery->isEmpty()) ? $facility->activeUnitsCountWithQuery->first()->count : 0;


                                    @endphp

                                    <tr class="package {{$isInactive ? 'inactive' : ''}}">

                                        <td></td>
                                        <td>
                                            <?php
                                            $field = 'package';
                                            $name = sprintf('%s[%s]', $property->getTable(), $field);
                                            $type = 1;
                                            $value = sprintf('%s-%s-%s', $property->getKey(), $type, $facility->getKey());
                                            $options = array(
                                                'data-url' => URL::route('api::subscription::order-summary', array('property_id' => $property->getKey(), 'type' => $type, 'id' => $facility->getKey()))
                                            );
                                            if($isInactive){
                                                $options['disabled'] = 'disabled';
                                            }
                                            ?>

                                            <div class="row">
                                                <div class="col-xs-8 col-sm-8 col-md-9">
                                                    <div class="name">
                                                        {{Form::radio($name, $value, null, $options)}}
                                                        {{$facility->name}}
                                                    </div>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-3">
                                                    <div class="price">
                                                        {{CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'))}}
                                                    </div>
                                                    <div class="note">
                                                        @if($property->coming_soon)
                                                            <div>
                                                                {{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            {{Translator::transSmart('app.%s Seat(s) Left', sprintf('%s Seat(s) Left', $unitsWithNoReserved, false, ['seats' => $unitsWithNoReserved]))}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>

                                    </tr>

                                @endforeach

                            @endforeach


                        </tbody>


                    </table>


            </div>
        </div>

    </div>

@endsection