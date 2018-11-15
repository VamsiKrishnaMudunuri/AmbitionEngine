@extends('layouts.plain')

@section('styles')
    @parent
@endsection

@section('scripts')
    @parent
@endsection


@section('content')



    <div class="api-subscription-invite-order-summary">

        <div>
            <span class="help-block">
                <b>
                    {{Translator::transSmart("app.The following order summary is just for your reference and we will only collect package fee and deposit by offline for now.", "The following order summary is just for your reference and we will only collect package fee and deposit by offline for now.")}}
                </b>
            </span>

        </div>



        <table class="table table-condensed">
            <colgroup>
                <col width="60%">
                <col width="40%">
            </colgroup>
            <tbody>

                <tr class="package">
                    <td>
                        <b>
                            <span class="name">
                                {{$item->name}}
                            </span>
                        </b>
                    </td>
                    <td>
                        <span class="price">

                            @php
                                $selling_price = CLDR::showPrice($subscription->price, $subscription->currency, Config::get('money.precision'));
                            @endphp

                            {{Translator::transSmart("app.%s/MONTH", sprintf('%s/MONTH', $selling_price), false, ['price' => $selling_price])}}

                        </span>
                    </td>
                </tr>
                <tr class="chargeable-amount">
                    <td>
                        <b>
                            <span class="name">
                              {{Translator::transSmart('app.Chargeable Amount', 'Chargeable Amount')}} <br />
                              {{
                                   sprintf(
                                   '(%s - %s)',
                                   CLDR::showDate($subscription_invoice->start_date, config('app.datetime.date.format')),
                                   CLDR::showDate($subscription_invoice->end_date, config('app.datetime.date.format'))
                                   )
                              }}
                            </span>
                        </b>
                    </td>
                    <td>
                        <span class="price">
                            {{CLDR::showPrice($subscription->proratedPrice(), $subscription->currency, Config::get('money.precision'))}}
                        </span>
                    </td>
                </tr>
                <tr class="taxable-amount">
                    <td>
                        <b>
                            <span class="name">
                              {{Translator::transSmart('app.Taxable Amount', 'Taxable Amount')}}
                            </span>
                        </b>
                    </td>
                    <td>
                        <span class="price">
                          {{CLDR::showPrice($subscription->taxableAmount(), $subscription->currency, Config::get('money.precision'))}}
                        </span>
                    </td>
                </tr>
                <tr class="tax">
                    <td>
                        <b>
                            <span class="name">
                                {{Translator::transSmart('app.Tax (%s %s%s)', sprintf('Tax (%s %s%s)', $property->tax_name, $property->tax_value, '&#37;'), true, ['tax_name' => $property->tax_name, 'tax_value' => $property->tax_value,'symbol' => '&#37;'])}}
                            </span>
                        </b>
                    </td>
                    <td>
                        <span class="price">
                            {{CLDR::showPrice($subscription->tax($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
                        </span>
                    </td>
                </tr>
                @if($subscription->isDeposit())
                    <tr class="deposit">
                    <td>
                        <b>
                             <span class="name">
                               {{Translator::transSmart('app.Deposit', 'Deposit')}}
                             </span>
                        </b>
                    </td>
                    <td>
                        <span class="price">
                             {{CLDR::showPrice($subscription->deposit, $subscription->currency, Config::get('money.precision'))}}
                        </span>
                    </td>
                </tr>
                @endif
                <tr class="total">
                    <td>
                        <b>
                            <span class="name">
                                {{Translator::transSmart('app.Total', 'Total')}}
                            </span>
                        </b>
                    </td>
                    <td>
                        <b>
                         <span class="price">
                            {{CLDR::showPrice($subscription->grossPriceAndDeposit($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
                         </span>
                        </b>
                    </td>
                </tr>
                @if($subscription->isDeposit() && $price->is_collect_deposit_offline)
                    <tr class="total-charge">
                        <td>
                            <b>
                                <span class="name">
                                    {{Translator::transSmart('app.Total Charge for Now', 'Total Charge for Now')}}
                                </span>
                            </b>
                        </td>
                        <td>
                            <b>
                                <span class="price">
                                  {{CLDR::showPrice($subscription->grossPrice($property->tax_value), $subscription->currency, Config::get('money.precision'))}}
                                </span>
                            </b>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td colspan="2">
                        <span class="help-block">
                            <table>
                                <colgroup>
                                    <col width="3%">
                                    <col width="97%">
                                </colgroup>
                                <tr>
                                    <td valign="top">1.</td>
                                    <td valign="top">
                                        {{Translator::transSmart('app.This order summary is system generated based on %s timezone.', sprintf('This order summary is system generated based on %s timezone.', $property->timezone_name), false, ['timezone' => $property->timezone_name])}}

                                    </td>
                                </tr>
                                @if($subscription->isDeposit() && $price->is_collect_deposit_offline)
                                    <tr>
                                        <td valign="top">2.</td>
                                        <td valign="top">
                                            {{Translator::transSmart('app.Deposit for this package will be collected when you check-in.', 'Deposit for this package will be collected when you check-in.')}}
                                        </td>
                                    </tr>
                                @endif

                            </table>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>

@endsection