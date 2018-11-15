@extends('layouts.admin')
@section('title', Translator::transSmart('app.Deposit', 'Deposit'))

@section('center-justify', true)


@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
@endsection

@section('scripts')
    @parent
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
    {{ Html::skin('app/modules/admin/managing/subscription/check-in-deposit.js') }}
@endsection

@section('breadcrumb')
    {{

        Html::breadcrumb(array(

            [URL::getAdvancedLandingIntended('admin::managing::listing::index', null,  URL::route('admin::managing::listing::index', array())), Translator::transSmart('app.Managing', 'Managing'), [], ['title' => Translator::transSmart('app.Managing', 'Managing')]],

            ['admin::managing::property::index', $property->smart_name, ['property_id' => $property->getKey()], ['title' => $property->smart_name]],

            [URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))),  Translator::transSmart('app.Subscriptions', 'Subscriptions'), [], ['title' =>  Translator::transSmart('app.Subscriptions', 'Subscriptions')]],

            ['admin::managing::subscription::check-in', Translator::transSmart('app.Check-In', 'Check-In'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Check-In', 'Check-In')]],

            ['admin::managing::subscription::check-in-deposit', Translator::transSmart('app.Deposit', 'Deposit'), ['property_id' => $property->getKey(), 'subscription_id' => $subscription->getKey()], ['title' =>  Translator::transSmart('app.Deposit', 'Deposit')]]
        ))

    }}
@endsection


@section('content')

    <div class="admin-managing-subscription-check-in-deposit">

        <div class="row">

            <div class="col-sm-12">

                <div class="page-header">
                    <h3>
                        {{Translator::transSmart('app.Deposit', 'Deposit')}}
                    </h3>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <span class="help-block">
                     {{ Translator::transSmart('app.Please make sure you have collect the deposit before check-in.', 'Please make sure you have collect the deposit before check-in.') }}
                </span>

            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                    {{ Html::success() }}
                    {{ Html::error() }}

                    {{Html::validation($subscription_invoice, 'csrf_error')}}

                    {{ Form::open(array('route' => array('admin::managing::subscription::check-in-deposit', $property->getKey(), $subscription->getKey()), 'class' => 'form-horizontal payment-form')) }}

                        <div class="hide credit-card-payment-method" data-credit-card="{{Utility::constant('payment_method.2.slug')}}"></div>

                        @php
                            $balanceSheet = $subscription_invoice->summaryOfBalanceSheet->first();
                        @endphp

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
                                    {{Form::select($name, Utility::constant('payment_method', true, $excludePaymentMethod), $subscription_invoice_transaction_deposit->getAttribute($field), array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
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
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="btn-group">
                                    @php
                                        $submit_text = Translator::transSmart('app.Pay', 'Pay');
                                    @endphp
                                    {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
                                </div>
                                <div class="btn-group">
                                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::subscription::index', [$property->getKey()],  URL::route('admin::managing::subscription::index', array('property_id' => $property->getKey()))) . '"; return false;')) }}
                                </div>
                            </div>

                        </div>

                    {{ Form::close() }}
            </div>
        </div>

    </div>

@endsection