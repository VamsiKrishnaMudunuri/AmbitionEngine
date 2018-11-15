<div class="braintree-payment vertical">
    <div class="form-group hide">
        <div class="col-sm-12">
            <div class="braintree-payment-client-token" data-braintree-payment-client-token="{{$transaction->client_token}}"></div>
                <?php
                $field = $transaction->paymentMethodNonceField;
                $name = sprintf('%s[%s]', $transaction->getTable(), $field);
                ?>
            {{Form::hidden($name, $transaction->getAttribute($field), array('class' => 'braintree-payment-method-nonce'))}}
            <div class="braintree-payment-errors">
                {{Html::errorBox(Translator::transSmart("app.We couldn't create credit card payment form at this moment. Please try again by refresh your browser.", "We couldn't create credit card payment form at this moment. Please try again by refresh your browser."),['braintree-payment-create-error', 'alert-keep',  'hide'], false) }}
                {{Html::errorBox(Translator::transSmart("app.We couldn't create credit card payment form at this moment. Please try again by refresh your browser.", "We couldn't create credit card payment form at this moment. Please try again by refresh your browser."),['braintree-payment-hosted-field-error', 'alert-keep',  'hide'], false) }}
                {{Html::errorBox(Translator::transSmart("app.There was some network issues while verifying your credit card. Please try again.", "There was some network issues while verifying your credit card. Please try again."), ['braintree-payment-network-error', 'alert-keep',  'hide'], false) }}
                {{Html::errorBox(Translator::transSmart("app.We couldn't verify your credit card due to the reason of invalid card number, CVC/CVV,  expiry date, or all of them.", "We couldn't verify your credit card due to the reason of invalid card number, CVC/CVV,  expiry date, or both of them."), ['braintree-payment-verify-error', 'alert-keep',  'hide'], false) }}
            </div>
        </div>

    </div>
    @if($transaction->isAlreadyEnableUseOfExistingTokenForm())
        <div class="braintree-payment-existing-card-form">
            <div class="form-group {{isset($no_show_required_symbol) ? '' : 'required'}}">
                <div>
                    @php
                        $field = $transaction->paymentMethodTokenField;
                        $name = sprintf('%s[%s]', $transaction->getTable(), $field);
                        $translate = Translator::transSmart('app.Use existing card', 'Use existing card');
                    @endphp
                    {{Html::validation($transaction, $field)}}
                    {{
                        Form::checkbox($name, Utility::constant('status.1.slug'), $transaction->getAttribute($field), array('class' => 'braintree-payment-existing-token', 'onclick' => 'braintreePayment.existingPaymentTokenHandler(this);' ))
                    }}

                    <span class="braintree-payment-existing-token-text">{{$translate}}</span>
                    <span class="braintree-payment-existing-token-number {{$transaction->hasCardNumber() ? '' : 'hide'}}">
                        (<span class="braintree-payment-existing-token-number-text">{{$transaction->getAttribute($transaction->paymentMethodCardNumber)}}</span>)
                    </span>

                </div>
            </div>
        </div>
    @endif
    @php

        $newCardFormClass = '';

        if($transaction->isAlreadyEnableUseOfExistingTokenForm()){
                if(Request::old(sprintf('%s.%s', $transaction->getTable(), $transaction->paymentMethodTokenField))){
                    $newCardFormClass = 'hide';
                }
        }

    @endphp

    <div class="braintree-payment-new-card-form {{$newCardFormClass}}">
        <div class="form-group {{isset($no_show_required_symbol) ? '' : 'required'}}">
            <label for="credit-card-number" class="control-label">
                <div class="credit-card-types">
                    <span><i class="fa fa-cc-mastercard fa-fw fa-2x"></i></span>
                    <span><i class="fa fa-cc-visa fa-fw fa-2x"></i></span>
                    <span><i class="fa fa-cc-amex fa-fw fa-2x"></i></span>
                </div>
                {{Translator::transSmart('app.Card Number', 'Card Number')}}
            </label>
            <div>
                {{Html::errorBox(Translator::transSmart('validation.ccn'), ['braintree-payment-field-error',  'braintree-payment-credit-card-number-field-error', 'alert-keep', 'hide'], false)}}
                <div class="form-control braintree-payment-hosted-field" id="braintree-payment-credit-card-number"></div>
            </div>
        </div>
        <div class="form-group {{isset($no_show_required_symbol) ? '' : 'required'}}">
            <div class="row">
                <div class="col-sm-6">
                    <label for="credit-card-expiry" class="control-label">{{Translator::transSmart('app.Card Expiry Date', 'Card Expiry Date')}}</label>
                    {{Html::errorBox(Translator::transSmart('validation.ccd'), ['braintree-payment-field-error', 'braintree-payment-credit-card-expiry-field-error',  'alert-keep', 'hide'], false)}}
                    <div class="form-control braintree-payment-hosted-field" id="braintree-payment-credit-card-expiry"></div>
                </div>
                <div class="col-sm-6">
                    @php

                        $cvc_info = sprintf('<div class="ccjs-csc-diagram"><div class="ccjs-barcode"></div><div class="ccjs-signature">%s #</div><div class="ccjs-card-code">123</div><div class="ccjs-explanation">%s</div></div>', Translator::transSmart('app.Signature and digits from card', 'Signature and digits from card'), Translator::transSmart('app.On most cards, the 3-digit security code is on the back, to the right of the signature.', 'On most cards, the 3-digit security code is on the back, to the right of the signature.'));

                        $cvc_info .= sprintf('<div class="ccjs-csc-diagram-amex"><div class="ccjs-card-number">XXXX XXXXXX XXXXX</div><div class="ccjs-explanation">%s</div><div class="ccjs-card-code">1234</div></div>', Translator::transSmart('app.On American Express cards, the 4-digit security code is on the front, to the top-right of the card number.', 'On American Express cards, the 4-digit security code is on the front, to the top-right of the card number.'))
                    @endphp
                    <label for="credit-card-cvc" class="control-label">
                        {{Translator::transSmart('app.Card CVC/CVV', 'Card CVC/CVV')}}
                        <a href="javascript:void(0);" class='help-box' data-container="body" data-toggle="popover" data-trigger="focus"  data-placement="bottom" data-html="true" data-content='{{$cvc_info}}'>
                            <i class="fa fa fa-info-circle fa-lg"></i>
                        </a>

                    </label>

                    {{Html::errorBox(Translator::transSmart('validation.cvc'), ['braintree-payment-field-error',  'braintree-payment-credit-card-cvc-field-error', 'alert-keep', 'hide'], false)}}
                    <div class="form-control braintree-payment-hosted-field" id="braintree-payment-credit-card-cvc"></div>
                </div>
            </div>
        </div>
    </div>
</div>