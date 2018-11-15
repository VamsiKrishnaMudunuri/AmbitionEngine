@section('styles')
    @parent
    {{ Html::skin('widgets/braintree/payment.css') }}
    {{ Html::skin('app/modules/admin/managing/subscription/booking.css') }}
@endsection
@section('scripts')
    @parent
    {{ Html::skinForVendor('braintree-web/all.js') }}
    {{ Html::skin('widgets/braintree-payment.js') }}
    {{ Html::skin('app/modules/admin/managing/subscription/booking.js') }}
@endsection

{{ Html::success() }}
{{ Html::error() }}

{{Html::validation($subscription, 'csrf_error')}}

{{ Form::open(array('route' => $route, 'class' => 'form-horizontal booking-form')) }}

    <div class="message-box"></div>

    <div class="row">
        @if($is_facility)
            <div class="{{$is_facility ? 'col-sm-2' : 'hide'}}">
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
        @endif
        <div class="{{$is_facility ? 'col-sm-10 booking-form-right' : 'col-sm-12'}}">
            <div class="form-group">
                <?php
                $field = 'start_date';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Subscription Date', 'Subscription Date');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <p class="form-control-static">
                        {{CLDR::showDate($subscription->getAttribute($field), config('app.datetime.date.format'))}}
                    </p>
                    {{Form::hidden($name, $subscription->getAttribute($field))}}
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'start_date';
                $field1 = 'end_date';
                $name = sprintf('%s[%s]', $subscription_invoice->getTable(), $field);
                $name1 = sprintf('%s[%s]', $subscription_invoice->getTable(), $field1);
                $translate = Translator::transSmart('app.Invoice Date', 'Invoice Date');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription_invoice, [$field, $field1])}}
                    <p class="form-control-static">
                        {{
                            sprintf('%s - %s',
                            CLDR::showDate($subscription_invoice->getAttribute($field), config('app.datetime.date.format')),
                            CLDR::showDate($subscription_invoice->getAttribute($field1), config('app.datetime.date.format')))
                         }}
                    </p>
                    {{Form::hidden($name, $subscription_invoice->getAttribute($field))}}
                    {{Form::hidden($name1, $subscription_invoice->getAttribute($field1))}}
                </div>
            </div>
            @if($is_facility)
                <div class="form-group">
                    <?php
                    $field = 'name';
                    $name = sprintf('%s[%s]', $facility->getTable(), $field);
                    $translate = Translator::transSmart('app.Package Name', 'Package Name');
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
                    $translate = Translator::transSmart('app.Package Category', 'Package Category');
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
                <div class="form-group required">
                    <?php
                    $field = 'contract_month';
                    $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                    $translate = Translator::transSmart('app.Contract Month', 'Contract Month');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($subscription, $field)}}
                        @php
                            $contract_months = [];
                            for($i = 1; $i <= 12; $i++){
                             $contract_months[$i] = $i;
                            }
                        @endphp
                        {{Form::select($name, $contract_months, 3, array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                    </div>
                </div>
            @else
                <div class="form-group">
                    <?php
                    $field = 'name';
                    $name = sprintf('%s[%s]', $package->getTable(), $field);
                    $translate = Translator::transSmart('app.Package Name', 'Package Name');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($package, $field)}}
                        <p class="form-control-static">{{$package->getAttribute($field)}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    $field = 'category_name';
                    $name = sprintf('%s[%s]', $package->getTable(), $field);
                    $translate = Translator::transSmart('app.Package Category', 'Package Category');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($package, $field)}}
                        <p class="form-control-static">{{$package->getAttribute($field)}}</p>
                    </div>
                </div>

                <div class="form-group required">
                    <?php
                    $field = 'contract_month';
                    $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                    $translate = Translator::transSmart('app.Contract Month', 'Contract Month');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($subscription, $field)}}
                        @php
                            $contract_months = [];
                            for($i = 1; $i <= 12; $i++){
                             $contract_months[$i] = $i;
                            }
                        @endphp
                        {{Form::select($name, $contract_months, 12, array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                    </div>
                </div>
            @endif
            <div class="form-group">
                <?php
                $field = 'price';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Regular Price', 'Regular Price');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'prorated_price';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Prorated Price', 'Prorated Price');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->proratedPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'discount';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Discount', 'Discount');
                $translate1 = Translator::transSmart('app.Only allow integer value.', 'Only allow integer value.');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        {{Form::text($name, $subscription->getAttribute($field) , array('id' => $name, 'class' => sprintf('%s form-control integer-value', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                        <span class="input-group-addon">&#37;</span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'net_price';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Net Price', 'Net Price');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->netPrice(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'taxable_amount';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Taxable Amount', 'Taxable Amount');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->taxableAmount(), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'tax';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Tax (%s)', sprintf('Tax (%s)', CLDR::showTax($property->tax_value)), true, ['tax' => CLDR::showTax($property->tax_value)])
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->tax($property->tax_value), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'data-is-taxable' => $subscription->is_taxable, 'data-tax-value' => $property->tax_value,'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group required">
                <?php
                $field = 'deposit';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Deposit', 'Deposit');
                $translate1 = Translator::transSmart('app.Only allow this format "#.##".', 'Only allow this format "#.##".');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    {{Form::text($name, null , array('id' => $name, 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate, 'placeholder' => $translate1))}}
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'gross_price_and_deposit';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Total', 'Total');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->grossPriceAndDeposit($property->tax_value), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <hr />
                    <h3>
                        {{Translator::transSmart('app.Package', 'Package')}}
                    </h3>
                    <hr />
                </div>
            </div>

            <div class="hide credit-card-payment-method" data-credit-card="{{Utility::constant('payment_method.2.slug')}}"></div>
            <div class="form-group">
                <?php
                $field = 'gross_price';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Package', 'Package');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">
                    {{$translate}}
                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Package is inclusive of tax if any.', 'Package is inclusive of tax if any.')}}">
                        <i class="fa fa-question-circle fa-lg"></i>
                    </a>
                </label>

                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->grossPrice($property->tax_value), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'deposit';
                $field1 = 'gross_deposit';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field1);
                $translate = Translator::transSmart('app.Deposit', 'Deposit');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">
                    {{$translate}}

                    <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Deposit will be applied if amount is more than zero.', 'Deposit will be applied if amount is more than zero.')}}">
                        <i class="fa fa-question-circle fa-lg"></i>
                    </a>

                </label>
                <div class="col-sm-10">
                    {{Html::validation($subscription, $field1)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->getAttribute($field), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field1), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php
                $field = 'gross_price_and_deposit';
                $name = sprintf('%s[%s]', $subscription->getTable(), $field);
                $translate = Translator::transSmart('app.Total', 'Total');
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">
                    {{$translate}}
                </label>

                <div class="col-sm-10">
                    {{Html::validation($subscription, $field)}}
                    <div class="input-group">
                        <span class="input-group-addon">{{$property->currency}}</span>
                        {{Form::text($name, CLDR::number($subscription->grossPrice($property->tax_value), Config::get('money.precision')), array('id' => $name, 'readonly' => 'readonly', 'class' => sprintf('%s form-control price-value', $field), 'title' => $translate))}}
                    </div>
                </div>
            </div>
            <div class="form-group required">
                <?php
                $field = 'user_id';
                $name = sprintf('%s[%s]',  $subscription_user->getTable(), $field);
                $name1 = sprintf('%s[%s]', 'typeahead', $field);
                $translate = Translator::transSmart('app.Member', 'Member');
                $route = '';

                if($is_facility){
                    $route = URL::route('admin::managing::member::subscription-facility', array('property_id' => $property->getKey(), 'facility_id' => $facility->getKey(), 'facility_unit_id' => $facility_unit->getKey()));
                }else{
                    $route = URL::route('admin::managing::member::subscription-package', array('property_id' => $property->getKey(), 'package_id' => $package->getKey()));
                }
                ?>
                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation( $subscription_user, $field)}}
                    {{Form::hidden($name, $subscription_user->getAttribute($field), array('class' => sprintf('%s_hidden form-control', $field)))}}

                    <div class="twitter-typeahead-container">
                        {{Form::text($name1, ($subscription_user->user) ? $subscription_user->user->full_name : null, array('id' => $name1, 'class' => sprintf('%s form-control', $field), 'data-url' => $route, 'data-no-found' => Translator::transSmart('app.No Found.', 'No Found'),  'autocomplete' => 'off',  'title' => $translate, 'placeholder' => Translator::transSmart('app.Search by name, username or email.', 'Search by name, username or email.')))}}
                    </div>

                </div>
            </div>
            <div class="payment-section">

                <div class="form-group required">
                    <?php
                    $field = 'method';
                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                    $translate = Translator::transSmart('app.Payment', 'Payment');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">
                        <span>
                            {{$translate}}

                        </span>
                        <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="top" data-html="true" data-content="{{Translator::transSmart('app.Recurring billing will be triggered if the payment is credit card.', 'Recurring billing will be triggered if the payment is credit card.')}}">
                            <i class="fa fa-question-circle fa-lg"></i>
                        </a>
                    </label>
                    <div class="col-sm-10">
                        {{Html::validation($subscription_invoice_transaction_package, $field)}}
                        @php
                            $excludePaymentMethod = array();
                            if(!config('features.payment.method.credit-card')){
                                $excludePaymentMethod[] = Utility::constant('payment_method.2.slug');
                            }
                        @endphp
                        {{Form::select($name, Utility::constant('payment_method', true, $excludePaymentMethod) , null, array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                    </div>
                </div>
                <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                    <div class="form-group required">
                        <?php
                        $field = 'check_number';
                        $name = sprintf('%s[%s]', $subscription_invoice_transaction_package->getTable(), $field);
                        $translate = Translator::transSmart('app.Reference Number', 'Reference Number');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice_transaction_package, $field)}}
                            {{Form::text($name, null , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_package->getMaxRuleValue($field), 'title' => $translate))}}
                        </div>
                    </div>
                </div>
                <div class="{{sprintf('hide payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                    @include('templates.widget.braintree.credit_card_horizontal', array('transaction' => $transaction))
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
                            {{Form::checkbox($name, 1, null, array('class' => sprintf('%s', $field), 'data-message' => Translator::transSmart('app.For your information, this can only be applied if deposit amount is more than zero.', 'For your information, this can only be applied if deposit amount is more than zero.')))}} {{$translate}}
                        </label>
                    </div>
                </div>
            </div>
            <div class="deposit-section hide">

                <div class="form-group required">
                    <?php
                    $field = 'method';
                    $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                    $translate = Translator::transSmart('app.Payment', 'Payment');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                        {{Form::select($name, Utility::constant('payment_method', true, [Utility::constant('payment_method.2.slug')]) , null, array('id' => $name, 'class' => sprintf('%s form-control', $field), 'title' => $translate))}}
                    </div>
                </div>
                <div class="{{sprintf('hide payment-method-%s payment-method-%s', Utility::constant('payment_method.1.slug'), Utility::constant('payment_method.3.slug'))}}">
                    <div class="form-group required">
                        <?php
                        $field = 'check_number';
                        $name = sprintf('%s[%s]', $subscription_invoice_transaction_deposit->getTable(), $field);
                        $translate = Translator::transSmart('app.Reference Number', 'Reference Number');
                        ?>
                        <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                        <div class="col-sm-10">
                            {{Html::validation($subscription_invoice_transaction_deposit, $field)}}
                            {{Form::text($name, null , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $subscription_invoice_transaction_deposit->getMaxRuleValue($field), 'title' => $translate))}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="btn-group">
                        @php
                            $submit_text = Translator::transSmart('app.Pay', 'Pay');
                        @endphp
                        {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit', 'data-is-ajax-submit' => (isset($is_ajax_submit) && $is_ajax_submit) ? 1 : 0))}}
                    </div>
                    <div class="btn-group">
                        @if(isset($is_from_lead) && $is_from_lead)
        
                            <a href="javascript:void(0);"
                               title = "{{Translator::transSmart('app.Cancel', 'Cancel')}}"
                               class="btn btn-theme btn-block cancel" onclick = "javascript:widget.popup.close(false, null, 0)" >
                                {{Translator::transSmart('app.Cancel', 'Cancel')}}
                            </a>
                            
                        @else

                            {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . URL::getAdvancedLandingIntended('admin::managing::subscription::check-availability', [$property->getKey()],  URL::route('admin::managing::subscription::check-availability', array('property_id' => $property->getKey()))) . '"; return false;')) }}
                            
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>


{{ Form::close() }}
