@if(!Utility::hasString($template) || $template == 0)

    @if($package_price->strike_price > 0)
        <div>
            <span class="strike">
               {{CLDR::showPrice($package_price->strike_price, $package_price->currency, Config::get('money.precision'))}}
            </span>
        </div>
    @endif
    <div>
        @php
            $selling_price = CLDR::showPrice($package_price->starting_price, $package_price->currency, Config::get('money.precision'));
        @endphp
        {{Translator::transSmart("app.STARTING at %s", sprintf('STARTING at %s', $selling_price), false, ['price' => $selling_price])}}
        <!--
        @if($package_price->type == Utility::constant('packages.3.slug'))
            {{Translator::transSmart("app.STARTING at %s", sprintf('STARTING at %s', $selling_price), false, ['price' => $selling_price])}}
        @else
            {{$selling_price}}
        @endif
        -->
    </div>
    <div>
        &nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}
    </div>
    <div class="note">
        * {{Translator::transSmart("app.Prices varies based on location", "Prices varies based on location")}}
    </div>

@elseif($template == 1)

    <h3>

        @if($package_price->strike_price > 0)


            <div>
                <span class="strike">
                      <b>
                          {{CLDR::showPrice($package_price->strike_price, $package_price->currency, Config::get('money.precision'))}}
                      </b>
                </span>
            </div>


        @endif

        @php
            $selling_price = CLDR::showPrice($package_price->starting_price, $package_price->currency, Config::get('money.precision'));
        @endphp

        @if($package_price->type == Utility::constant('packages.3.slug'))
            <small>{{Translator::transSmart("app.STARTING FROM", "STARTING FROM")}}</small>
        @endif


        <div>
            <b>
                {{$selling_price}}

            </b>
        </div>
        <div>
            <b>
                &nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}
            </b>
        </div>


    </h3>

@elseif($template == 2)

    <small class="pricing-small-text b-20 l-10">
        {{ Translator::transSmart("app.From", "From") }} {{$package_price->currency}}</small> {{ CLDR::showPrice($package_price->starting_price, null, 0) }}-{{ CLDR::showPrice($package_price->ending_price, null, 0)}}<small class="pricing-small-text">&nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}</small>

@elseif($template == 3)

    {{$package_price->currency}} {{ $package_price->starting_price }}&nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}

@elseif($template == 4)

    <h3 class="p-t-0 m-t-0">{{$package_price->currency}} {{ CLDR::showPrice($package_price->starting_price, null, 0) }}&nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}</h3>

@elseif($template == 5)

    <h3 class="p-t-0 m-t-0">{{$package_price->currency}} {{ CLDR::showPrice($package_price->spot_price, null, 0) }}&nbsp;{{Translator::transSmart("app.Per Seat/Month", 'Per Seat/Month')}}</h3>

@endif

