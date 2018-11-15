<!--
<div class="page-header">
    <h3>
        {{Translator::transSmart('app.PACKAGES', 'PACKAGES')}}
    </h3>
</div>

<div class="package">
    <table class="discount">
        <tbody>
            @if(is_null($property->facilities) || $property->facilities->isEmpty())
                {{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}
            @else
                @foreach($property->facilities as $facility)
                    <tr>
                        <td>
                            {{Utility::constant(sprintf('facility_category.%s.name', $facility->category))}}
                        </td>
                        <td>
                            @if($facility->min_strike_price > 0)
                                <span class="strike">
                                     {{CLDR::showPrice($facility->min_strike_price, $property->currency, Config::get('money.precision'))}}
                                </span>
                            @endif

                        </td>
                        <td>
                            @php
                                $selling_price = CLDR::showPrice($facility->min_spot_price, $property->currency, Config::get('money.precision'))
                            @endphp

                            @if($facility->category == Utility::constant('facility_category.2.slug'))
                                <div>
                                    <small><i>{{Translator::transSmart('app.Starts from', 'Starts from')}}</i></small>
                                </div>
                            @endif

                            {{Translator::transSmart('app.%s per seat/month', sprintf('%s per seat/month', $selling_price), false, ['price' => $selling_price])}}

                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
-->


<h3>
    <strong>
        {{Translator::transSmart('app.Pricing', 'Pricing')}}
    </strong>
</h3>

<br />

<div class="package-container">

    @if(is_null($property->facilities) || $property->facilities->isEmpty())

        {{Translator::transSmart('app.Coming Soon', 'Coming Soon')}}

    @else

        <table class="package">
            <tr>
                <td>{{Translator::transSmart('app.Desks', 'Desks')}}</td>
                <td>{{Translator::transSmart('app.Starting from', 'Starting from')}}</td>
            </tr>
            @foreach($desks as $desk)
                <tr>
                    <td>
                        <div class="package-info">
                            <span class="name">{{Utility::constant(sprintf('facility_category.%s.name', $desk->category))}}</span>
                            <span class="seat">{{Translator::transSmart('app.(one person)', '(one person)')}}</span>
                        </div>

                    </td>
                    <td>
                        @php
                            $selling_price = CLDR::showPrice($desk->min_spot_price, null, Config::get('money.precision'))
                        @endphp

                        <div class="package-price">
                            <small class="currency">
                                {{$property->currency}}
                            </small>
                            <span class="price-figure">
                                {{Translator::transSmart('app.%s Per Seat/Month', sprintf('%s Per Seat/Month', $selling_price), false, ['price' => $selling_price])}}
                            </span>
                        </div>

                    </td>
                </tr>
            @endforeach
        </table>

        <br />


        <table class="package">
            <tr>
                <td>{{Translator::transSmart('app.Private Office', 'Private Office')}}</td>
                <td>{{Translator::transSmart('app.Starting from', 'Starting from')}}</td>
            </tr>
            @foreach($privateOffices as $desk)
                <tr>
                    <td>
                        <div class="package-info">
                            <span class="name">{{Utility::constant(sprintf('facility_category.%s.name', $desk->category))}}</span>
                            <span class="seat"></span>
                        </div>

                    </td>
                    <td>
                        @php
                            $selling_price = CLDR::showPrice($desk->min_spot_price, null, Config::get('money.precision'))
                        @endphp

                        <div class="package-price">
                            <small class="currency">
                                {{$property->currency}}
                            </small>
                            <span class="price-figure">
                                {{Translator::transSmart('app.%s Per Seat/Month', sprintf('%s Per Seat/Month', $selling_price), false, ['price' => $selling_price])}}
                            </span>
                        </div>

                    </td>
                </tr>
            @endforeach
        </table>

    @endif

</div>

<br />