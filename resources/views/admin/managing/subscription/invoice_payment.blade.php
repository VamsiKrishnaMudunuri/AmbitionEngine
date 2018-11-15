@extends('layouts.admin')
@section('title', Translator::transSmart('app.Payment for Invoice', 'Payment for Invoice'))

@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
    {{ Html::skin('app/modules/admin/managing/subscription/payment.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))),  Translator::transSmart('app.Invoices', 'Invoices'), [], ['title' =>  Translator::transSmart('app.Invoices', 'Invoices')]],

            ['admin::managing::subscription::invoice-payment', Translator::transSmart('app.Pay', 'Pay'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey(), 'subscription_invoice_id' => $subscription_invoice->getKey()], ['title' =>  Translator::transSmart('app.Pay', 'Pay')]]

        ))

    }}
@endsection

@section('content')

    <div class="admin-managing-subscription-invoice-payment">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Invoice', 'Invoice')}}
                    </h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                {{ Html::success() }}
                {{ Html::error() }}

                {{Html::validation($subscription_invoice, 'csrf_error')}}

                {{ Form::open(array('route' => array('admin::managing::subscription::invoice-payment', $property->getKey(), $subscription->getKey(), $subscription_invoice->getKey()), 'class' => 'form-horizontal payment-form')) }}

                    <div class="form-group">
                        <?php
                        $field = 'ref';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Invoice No.', 'Invoice No.');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <p class="form-control-static">
                                {{$subscription_invoice->getAttribute($field)}}
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                            $field = 'start_date';
                            $field1 = 'end_date';
                            $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                            $name1 = sprintf('%s[%s]', $subscription_invoice->getTable(), $field1);
                            $translate = Translator::transSmart('app.Duration', 'Duration');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            {{Html::validation($subscription_invoice, $field1)}}
                            <p class="form-control-static">

                                {{
                                    sprintf('%s - %s', CLDR::showDateTime($subscription_invoice->getAttribute($field), config('app.datetime.datetime.format_timezone'), $property->timezone), CLDR::showDateTime($subscription_invoice->getAttribute($field1), config('app.datetime.datetime.format_timezone'), $property->timezone))
                                }}

                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'price';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Regular Price', 'Regular Price');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'prorated_price';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Prorated Price', 'Prorated Price');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->proratedPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'discount';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Discount', 'Discount');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                {{Form::text($name, $subscription_invoice->getAttribute($field) , array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control integer-value', $field), 'title' => $translate))}}
                                <span class="input-group-addon">&#37;</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'net_price';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Net Price', 'Net Price');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->netPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'taxable_amount';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Taxable Amount', 'Taxable Amount');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->taxableAmount(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'tax';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($subscription_invoice->tax_value)), true, ['tax' => CLDR::showTax($subscription_invoice->tax_value)])
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->tax($subscription_invoice->tax_value), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'data-is-taxable' => $subscription_invoice->is_taxable, 'data-tax-value' => $subscription_invoice->tax_value,'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'deposit';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Desposit', 'Deposit');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        $field = 'gross_price_and_deposit';
                        $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                        $translate = Translator::transSmart('app.Total', 'Total');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice, $field)}}
                            <div class="input-group">
                                <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                {{Form::text($name, CLDR::number($subscription_invoice->grossPriceAndDeposit(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                            </div>
                        </div>
                    </div>

                    <div class="hide credit-card-payment-method" data-credit-card="{{Utility::constant('payment_method.2.slug')}}"></div>

                    @php
                        $balanceSheet = $subscription_invoice->summaryOfBalanceSheet->first();
                    @endphp

                    @if($balanceSheet->hasBalanceDueForPackage() && $balanceSheet->hasBalanceDueForDeposit())

                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Package', 'Package')}}
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="payment-section">
                            <div class="form-group">
                                <?php
                                $field = 'package_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Total is inclusive of tax if any.', 'Total is inclusive of tax if any.')}}">
                                        <i class="fa fa-question-circle fa-lg"></i>
                                    </a>
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForPackage(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php
                                $field = 'method';
                                $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                                $translate = Translator::transSmart('app.Method', 'Method');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($subscription_invoice_transaction_package, $field)}}
                                    @php
                                        $excludePaymentMethod = array();
                                        if(!config('features.payment.method.credit-card')){
                                            $excludePaymentMethod[] = Utility::constant('payment_method.2.slug');
                                        }
                                    @endphp
                                    {{Form::select($name, Utility::constant('payment_method', true, $excludePaymentMethod) , $subscription_invoice_transaction_package->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                                <div class="form-group required">
                                    <?php
                                    $field = 'check_number';
                                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                                    $translate = Translator::transSmart('app.Check Number', 'Check Number');
                                    ?>
                                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                    <div class="col-sm-10">
                                        {{Html::validation($subscription_invoice_transaction_package, $field)}}
                                        {{Form::text($name, $subscription_invoice_transaction_package->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_package->getMaxRuleValue($field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                                @include('templates.widget.braintree.credit_card_horizontal', array('transaction' => $transaction))
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Deposit', 'Deposit')}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Deposit will only be applied if the balance due is more than zero.', 'Deposit will only be applied if the balance due is more than zero.')}}">
                                        <i class="fa fa-question-circle"></i>
                                    </a>
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="deposit-section">
                            <div class="form-group">
                                <?php
                                $field = 'deposit_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForDeposit(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <?php
                                    $field = '_different_deposit_method';
                                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                                    $translate = Translator::transSmart('app.Pay deposit with different payment method?', 'Pay deposit with different payment method?');
                                    ?>
                                    <div class="checkbox">
                                        <label>
                                            {{Form::checkbox($name, 1, null, array('class' => sprintf('%s', $field), 'data-message' => Translator::transSmart("app.For your information, this can only be applied if deposits' balance due  is more than zero.", "For your information, this can only be applied if deposits' balance due is more than zero.")))}} {{$translate}}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="deposit-section-payment-method hide">
                                <div class="form-group required">
                                    <?php
                                    $field = 'method';
                                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                                    $translate = Translator::transSmart('app.Method', 'Method');
                                    ?>
                                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                    <div class="col-sm-10">
                                        {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                                        {{Form::select($name, Utility::constant('payment_method', true, [Utility::constant('payment_method.2.slug')]) , $subscription_invoice_transaction_deposit->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                                <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                                    <div class="form-group required">
                                        <?php
                                        $field = 'check_number';
                                        $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                                        $translate = Translator::transSmart('app.Check Number', 'Check Number');
                                        ?>
                                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                        <div class="col-sm-10">
                                            {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                                            {{Form::text($name, $subscription_invoice_transaction_deposit->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_deposit->getMaxRuleValue($field), 'title' => $translate))}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif($balanceSheet->balanceDueForPackage())

                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Package', 'Package')}}
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="payment-section">
                            <div class="form-group">
                                <?php
                                $field = 'package_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Total is inclusive of tax if any.', 'Total is inclusive of tax if any.')}}">
                                        <i class="fa fa-question-circle fa-lg"></i>
                                    </a>
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForPackage(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php
                                $field = 'method';
                                $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                                $translate = Translator::transSmart('app.Method', 'Method');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($subscription_invoice_transaction_package, $field)}}
                                    @php
                                        $excludePaymentMethod = array();
                                        if(!config('features.payment.method.credit-card')){
                                            $excludePaymentMethod[] = Utility::constant('payment_method.2.slug');
                                        }
                                    @endphp
                                    {{Form::select($name, Utility::constant('payment_method', true, $excludePaymentMethod ) , $subscription_invoice_transaction_package->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s  payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                                <div class="form-group required">
                                    <?php
                                    $field = 'check_number';
                                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                                    $translate = Translator::transSmart('app.Check Number', 'Check Number');
                                    ?>
                                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                    <div class="col-sm-10">
                                        {{Html::validation($subscription_invoice_transaction_package, $field)}}
                                        {{Form::text($name, $subscription_invoice_transaction_package->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_package->getMaxRuleValue($field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                                @include('templates.widget.braintree.credit_card_horizontal', array('transaction' => $transaction))
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Deposit', 'Deposit')}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Deposit will only be applied if the balance due is more than zero.', 'Deposit will only be applied if the balance due is more than zero.')}}">
                                        <i class="fa fa-question-circle"></i>
                                    </a>
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="deposit-section">
                            <div class="form-group">
                                <?php
                                $field = 'deposit_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForDeposit(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                        </div>


                    @elseif($balanceSheet->balanceDueForDeposit())

                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Package', 'Package')}}
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="payment-section">
                            <div class="form-group">
                                <?php
                                $field = 'package_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Total is inclusive of tax if any.', 'Total is inclusive of tax if any.')}}">
                                        <i class="fa fa-question-circle fa-lg"></i>
                                    </a>
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'package_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForPackage(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <hr />
                                <h3>
                                    {{Translator::transSmart('app.Deposit', 'Deposit')}}
                                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Deposit will only be applied if the balance due is more than zero.', 'Deposit will only be applied if the balance due is more than zero.')}}">
                                        <i class="fa fa-question-circle"></i>
                                    </a>
                                </h3>
                                <hr />
                            </div>
                        </div>
                        <div class="deposit-section">
                            <div class="form-group">
                                <?php
                                $field = 'deposit_charge';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Total', 'Total');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_paid';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Paid', 'Paid');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?php
                                $field = 'deposit_due';
                                $name = sprintf('%s[%s]', $balanceSheet->getTable(), $field);
                                $translate = Translator::transSmart('app.Balance Due', 'Balance Due');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">
                                    {{$translate}}
                                </label>

                                <div class="col-sm-10">
                                    {{Html::validation($balanceSheet, $field)}}
                                    <div class="input-group">
                                        <span class="input-group-addon">{{$subscription_invoice->currency}}</span>
                                        {{Form::text($name, CLDR::number($balanceSheet->balanceDueForDeposit(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group required">
                                <?php
                                $field = 'method';
                                $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                                $translate = Translator::transSmart('app.Method', 'Method');
                                ?>
                                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                <div class="col-sm-10">
                                    {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                                    @php
                                        $excludePaymentMethod = array();
                                        if(!config('features.payment.method.credit-card')){
                                            $excludePaymentMethod[] = Utility::constant('payment_method.2.slug');
                                        }
                                    @endphp
                                    {{Form::select($name, Utility::constant('payment_method', true, $excludePaymentMethod) , $subscription_invoice_transaction_deposit->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                                <div class="form-group required">
                                    <?php
                                    $field = 'check_number';
                                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                                    $translate = Translator::transSmart('app.Check Number', 'Check Number');
                                    ?>
                                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                                    <div class="col-sm-10">
                                        {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                                        {{Form::text($name, $subscription_invoice_transaction_deposit->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_deposit->getMaxRuleValue($field), 'title' => $translate))}}
                                    </div>
                                </div>
                            </div>
                            <div class="{{sprintf('hide payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                                @include('templates.widget.braintree.credit_card_horizontal', array('transaction' => $transaction))
                            </div>
                        </div>

                    @endif

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="btn-group">
                                @php
                                    $submit_text = Translator::transSmart('app.Pay', 'Pay');
                                @endphp
                                {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
                            </div>
                            <div class="btn-group">


                                {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::subscription::invoice', [$property->getKey(), $subscription->getKey()],  URL::route('admin::managing::subscription::invoice', array('property_id' => $property->getKey(), 'subscription_id' =>  $subscription->getKey()))) . '"; return false;')) }}

                            </div>
                        </div>

                    </div>
                {{ Form::close() }}

            </div>
        </div>
    </div>

@endsection