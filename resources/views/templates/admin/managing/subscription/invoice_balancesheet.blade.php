<table class="table table-bordered table-condensed table-crowded">

    <tr>
        <th>{{Translator::transSmart('app.Date', 'Date')}}</th>
        <th>{{Translator::transSmart('app.Description', 'Description')}}</th>
        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.1.name'), $subscription->currency)}}</th>
        <th>{{sprintf('%s (%s)', Utility::constant('payment_mode.0.name'), $subscription->currency)}}</th>
    </tr>

    @if($invoice_transactions->isEmpty())

        <tr>
            <td colspan="4">
                {{ Translator::transSmart('app.No Transaction', 'No Transaction') }}
            </td>
        </tr>

    @endif

    @php
        $debit = 0;
        $credit = 0;
    @endphp
    @foreach($invoice_transactions as $transaction)

        <tr>

            <td>{{CLDR::showDate($property->localDate($transaction->getAttribute($transaction->getCreatedAtColumn()))->toDateTimeString(), config('app.datetime.date.format'))}}</td>
            </td>

            <td>
                @if($transaction->type == Utility::constant('subscription_invoice_transaction_status.0.slug'))
                    {{
                        sprintf(
                        '%s (%s - %s)',
                        $subscription->package_name,
                        CLDR::showDate($property->localDate($transaction->start_date)->toDateTimeString(), config('app.datetime.date.format')),
                        CLDR::showDate($property->localDate($transaction->end_date)->toDateTimeString(), config('app.datetime.date.format'))
                        )
                    }}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.1.slug'))
                    {{sprintf('%s (%s)', Translator::transSmart('app.Discount', 'Discount'), CLDR::showDiscount($invoice->discount))}}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.2.slug'))
                    {{sprintf('%s (%s %s)', Translator::transSmart('app.Tax', 'Tax'), $invoice->tax_name, CLDR::showTax($invoice->tax_value))}}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.3.slug'))
                    {{sprintf('%s', Translator::transSmart('app.Deposit', 'Deposit'))}}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.4.slug'))
                    {{sprintf('%s', Translator::transSmart('app.Package Paid', 'Package Paid'))}}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.5.slug'))
                    {{sprintf('%s', Translator::transSmart('app.Deposit Paid', 'Deposit Paid'))}}
                @elseif($transaction->type == Utility::constant('subscription_invoice_transaction_status.6.slug'))
                    {{sprintf('%s', Translator::transSmart('app.Deposit Refund', 'Deposit Refund'))}}
                @endif
            </td>

            <td>
                @if($transaction->mode == Utility::constant('payment_mode.1.slug'))
                    @php $debit += $transaction->amount @endphp
                    {{CLDR::showPrice($transaction->amount, null, Config::get('money.precision'))}}
                @endif
            </td>

            <td>
                @if($transaction->mode == Utility::constant('payment_mode.0.slug'))
                    @php $credit += $transaction->amount @endphp
                    {{CLDR::showPrice($transaction->amount, null, Config::get('money.precision'))}}
                @endif
            </td>

        </tr>

    @endforeach

    <tr class="hide">
        <td></td>
        <td></td>
        <td>{{$debit}}</td>
        <td>{{$credit}}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td><b>{{Translator::transSmart('app.Balance Due', 'Balance Due')}}</b></td>
        <td>{{CLDR::showPrice($invoice_balancesheet->balanceDue(), null, Config::get('money.precision'))}}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td><b>{{Translator::transSmart('app.Total Paid', 'Total Paid')}}</b></td>
        <td>{{CLDR::showPrice($invoice_balancesheet->totalPaid(), null, Config::get('money.precision'))}}</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td><b>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</b></td>
        <td>{{CLDR::showPrice($invoice_balancesheet->overpaid(), null, Config::get('money.precision'))}}</td>
    </tr>


</table>


@if(isset($refund) && !is_null($refund) && $refund->exists)

    <h3>
        {{Translator::transSmart('app.Refund', 'Refund')}}
    </h3>

    <table class="table table-bordered table-condensed table-crowded">
        <tr>
            <th>{{Translator::transSmart('app.Deposit', 'Deposit')}}</th>
            <th>{{Translator::transSmart('app.Overpaid', 'Overpaid')}}</th>
            <th>{{Translator::transSmart('app.Total', 'Total')}}</th>
            <th>{{Translator::transSmart('app.Refund', 'Refund')}}</th>
        </tr>
        <tr>
            <td>{{CLDR::showPrice($refund_balancesheet->deposit_paid, $invoice->currency, Config::get('money.precision'))}}</td>
            <td>{{CLDR::showPrice($refund_balancesheet->overpaidForPackage(), $invoice->currency, Config::get('money.precision'))}}</td>
            <td>{{CLDR::showPrice($refund_balancesheet->overpaid(), $invoice->currency, Config::get('money.precision'))}}</td>
            <td>{{CLDR::showPrice($refund->amount, $invoice->currency, Config::get('money.precision'))}}</td>
        </tr>
    </table>

@endif