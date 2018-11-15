<div class="{{$container_class}} top-up-wallet">

    <div class="row">

        <div class="col-sm-12">

            <div class="page-header">
                <h3>
                    {{Translator::transSmart('app.Top Up', 'Top Up')}}
                </h3>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">

            {{ Html::success() }}
            {{ Html::error() }}

            {{Html::validation($wallet_transaction, 'csrf_error')}}

            {{ Form::open(array('route' => $form_route, 'class' => 'form-horizontal payment-form')) }}

            <div class="form-group">

                <?php
                    $field = '_credit';
                    $name = sprintf('%s[%s]', $wallet->getTable(), $field);
                ?>

                {{Html::validation($wallet, $field)}}
                {{Form::hidden($name, '', array('class' => $field))}}
                @foreach(Config::get('wallet.top_up_credit') as $credit)
                    <div class="col-sm-3">
                        <div class="credit-package md" data-credit="{{$credit}}">

                            <div class="top">
                               <span class="credit">
                                    {{sprintf('%s %s', CLDR::number($credit, 0), trans_choice('plural.credit', intval($credit)))}}
                               </span>
                                <span class="price">
                                     {{CLDR::showPrice($base_currency->convert($quote_currency, $wallet->creditToBaseAmount($credit)), $quote_currency->quote, 0)}}
                                </span>
                            </div>
                            <div class="bottom">
                                <a href="javascript:void(0);" class="buy" data-select="{{Translator::transSmart('app.SELECT', 'SELECT')}}" data-selected="{{Translator::transSmart('app.SELECTED', 'SELECTED')}}">
                                    {{Translator::transSmart('app.SELECT', 'SELECT')}}
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach

            </div>

            <div class="form-group">
                <label  class="col-sm-2 control-label">
                    {{Translator::transSmart('app.Balance', 'Balance')}}
                </label>

                <div class="col-sm-10">
                    <p class="form-control-static">
                        {{$wallet->current_credit_word}}
                    </p>
                </div>
            </div>

            <div class="hide credit-card-payment-method"
                 data-credit-card="{{Utility::constant('payment_method.2.slug')}}"></div>

            <div class="form-group required">
                <?php
                $field = 'method';
                $name = sprintf('%s[%s]', $wallet_transaction->getTable(), $field);
                $translate = Translator::transSmart('app.Method', 'Method');
                ?>

                <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                <div class="col-sm-10">
                    {{Html::validation($wallet_transaction, $field)}}
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
                    $name = sprintf('%s[%s]', $wallet_transaction->getTable(), $field);
                    $translate = Translator::transSmart('app.Reference Number', 'Reference Number');
                    ?>
                    <label for="{{$name}}" class="col-sm-2 control-label">{{$translate}}</label>
                    <div class="col-sm-10">
                        {{Html::validation($wallet_transaction, $field)}}
                        {{Form::text($name, null , array('id' => $name, 'class' => sprintf('%s form-control', $field), 'maxlength' => $wallet_transaction->getMaxRuleValue($field), 'title' => $translate))}}
                    </div>
                </div>
            </div>

            <div class="{{sprintf('hide payment-method-%s', Utility::constant('payment_method.2.slug'))}}">
                @include('templates.widget.braintree.credit_card_horizontal', array('transaction' => $transaction))
            </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="info-box">
                        <span class="help-block">
                              {{Translator::transSmart('app.Please do not refresh the page and wait while we are processing your payment.', 'Please do not refresh the page and wait while we are processing your payment.')}}
                        </span>
                    </div>
                    <div class="btn-group">
                        @php
                            $submit_text = Translator::transSmart('app.Top Up', 'Top Up');
                        @endphp
                        {{Form::button($submit_text, array('type' => 'submit', 'title' => $submit_text, 'class' => 'btn btn-theme btn-block submit'))}}
                    </div>
                    <div class="btn-group">

                        {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block cancel', 'onclick' => 'location.href="' . $cancel_route . '"; return false;')) }}

                    </div>
                </div>
            </div>

            {{ Form::close() }}

        </div>
    </div>

</div>