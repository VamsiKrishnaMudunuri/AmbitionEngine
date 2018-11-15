var braintreePayment = new function(){

    var that = this;
    that.cls = {};
    that.cls['container'] = {};
    that.cls['loading'] = '.fa-loading';
    that.cls['container']['payment'] = '.braintree-payment';
    that.cls['container']['error'] =  that.cls['container']['payment'] + ' .braintree-payment-errors';
    that.cls['container']['existingCardForm'] = '.braintree-payment-existing-card-form';
    that.cls['container']['newCardForm'] = '.braintree-payment-new-card-form';

    that.cls['existingToken'] = that.cls.container.payment + ' ' +  that.cls.container.existingCardForm   + ' .braintree-payment-existing-token';
    that.cls['existingTokenText'] = that.cls.container.payment + ' ' +  that.cls.container.existingCardForm  + ' .braintree-payment-existing-token-text';
    that.cls['existingTokenNumber'] = that.cls.container.payment + ' ' +  that.cls.container.existingCardForm  + ' .braintree-payment-existing-token-number';
    that.cls['existingTokenNumberText'] = that.cls.container.payment + ' ' +  that.cls.container.existingCardForm  + ' .braintree-payment-existing-token-number-text';

    that.cls['token'] = that.cls.container.payment + ' .braintree-payment-client-token';
    that.cls['nonce'] =  that.cls.container.payment + ' .braintree-payment-method-nonce';
    that.cls['errors'] = {
        create: that.cls.container.payment + ' ' + that.cls.container.error + ' .braintree-payment-create-error',
        hostedField: that.cls.container.payment + ' ' + that.cls.container.error + ' .braintree-payment-hosted-field-error',
        network: that.cls.container.payment + ' ' + that.cls.container.error + ' .braintree-payment-network-error',
        verify: that.cls.container.payment + ' ' + that.cls.container.error + ' .braintree-payment-verify-error',
        field: that.cls.container.payment + ' .braintree-payment-field-error',
        card: {
            number: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm  + ' .braintree-payment-credit-card-number-field-error',
            expirationDate: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm  + ' .braintree-payment-credit-card-expiry-field-error',
            cvv: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm + ' .braintree-payment-credit-card-cvc-field-error'
        }
    }

    that.id = {
        card: {
            number: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm  + ' #braintree-payment-credit-card-number',
            expirationDate: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm + ' #braintree-payment-credit-card-expiry',
            cvv: that.cls.container.payment  + ' ' +  that.cls.container.newCardForm + ' #braintree-payment-credit-card-cvc',
        }
    };

    that.data = {
        token: 'braintreePaymentClientToken'
    };

    that.token = null;
    that.loading = '<i class="fa fa-spinner fa-spin fa-fw fa-loading"></i>'
    that.submitButton = null;
    that.buttons = [];
    that.form = null;
    that.formEventHandler = null;
    that.hostedFieldsInstance = null;
    that.initialize = function(form, submitButton, buttons, token){
        that.form = form;
        that.submitButton = submitButton,
        that.buttons = buttons || [];
        that.token = token || that.form.querySelector(that.cls.token).dataset[that.data.token];
    };

    that.enableButton = function(){

        var disabled = 'disabled';

        var submitButtons = [];

        if(that.submitButton instanceof Array){

            submitButtons = that.submitButton;

        }else{
            submitButtons.push(that.submitButton);
        }

        for(var i = 0; i < submitButtons.length; i++){
            if(submitButtons[i]) {
                submitButtons[i].removeAttribute(disabled);
            }
        }

        if(that.buttons && that.buttons.length > 0){
            for(var i = 0; i < that.buttons.length; i++){
                that.buttons[i].removeAttribute(disabled);
            }
        }

    };

    that.disableButton = function(){

        var disabled = 'disabled';

        var submitButtons = [];

        if(that.submitButton instanceof Array){

            submitButtons = that.submitButton;

        }else{
            submitButtons.push(that.submitButton);
        }


        for(var i = 0; i < submitButtons.length; i++){
            if(submitButtons[i]) {
                submitButtons[i].setAttribute(disabled, disabled);
            }
        }

        if(that.buttons && that.buttons.length > 0){
            for(var i = 0; i < that.buttons.length; i++){
                that.buttons[i].setAttribute(disabled, disabled);
            }
        }
    };

    that.processing = function(){


        var submitButtons = [];

        if(that.submitButton instanceof Array){

            submitButtons = that.submitButton;

        }else{
            submitButtons.push(that.submitButton);
        }

        that.disableButton();

        for(var i = 0; i < submitButtons.length; i++){
            if(submitButtons[i]) {
                var loading = submitButtons[i].querySelector(that.cls.loading);
                if (!loading) {
                    submitButtons[i].insertAdjacentHTML('beforeend', that.loading);
                }
            }
        }

    };

    that.done = function(){

        var submitButtons = [];

        if(that.submitButton instanceof Array){

            submitButtons = that.submitButton;

        }else{
            submitButtons.push(that.submitButton);
        }

        that.enableButton();

        for(var i = 0; i < submitButtons.length; i++){

            if(submitButtons[i]) {
                var loading = submitButtons[i].querySelector(that.cls.loading);
                if (loading) {
                    submitButtons[i].removeChild(loading);
                }
            }

        }

    };

    that.hideErrors =function(){

        var errors = that.form.querySelectorAll(that.cls.container.error + ' > div');
        var field = that.form.querySelectorAll(that.cls.errors.field);

        errors.forEach(function(elem){
            elem.style.cssText = 'display:hide !important';
        });

        field.forEach(function(elem){
            elem.style.cssText = 'display:hide !important';
        });

    };

    that.showError = function(cls){

        var elem = that.form.querySelector(cls);

        if(elem){
            elem.style.cssText = 'display:block !important';
        }

    };

    that.existingPaymentTokenHandler = function(element){

        if(element.checked){
          that.hideNewPaymentForm();
        }else{
          that.showNewPaymentForm();
        }

    },

    that.isUseOfExistingTokenChosen = function(){
        var flag = false;
        var checkbox = that.form.querySelector(that.cls.existingToken);

        if(checkbox){
            flag = checkbox.checked;
        }

        return flag;
    };

    that.clearExistingPaymentFormElements = function(){
        var checkbox = that.form.querySelector(that.cls.existingToken);
        var tokenNumber = that.form.querySelector(that.cls.existingTokenNumber);
        var tokenText = that.form.querySelector(that.cls.existingTokenNumberText);

        if(checkbox){
            checkbox.checked = false;
        }
        if(tokenNumber){
            tokenNumber.style.cssText = 'display:none !important';
            if(tokenText){
                tokenText.innerHTML = '';
            }
        }
    };

    that.setCardNumberForExistingPaymentForm = function(cardNumber){

        var tokenNumber = that.form.querySelector(that.cls.existingTokenNumber);
        var tokenText = that.form.querySelector(that.cls.existingTokenNumberText);
        if(cardNumber) {
            if (tokenNumber && tokenText) {
                tokenNumber.style.cssText = 'display:inline-block !important';
                tokenText.innerHTML = cardNumber;
            }
        }
    };

    that.showExistingPaymentForm = function(){
        var elem = that.form.querySelector(that.cls.container.existingCardForm);

        if(elem){
            elem.style.cssText = 'display:block !important';

        }
    };

    that.hideExistingPaymentForm = function(){
        var elem = that.form.querySelector(that.cls.container.existingCardForm);
        var checkbox = that.form.querySelector(that.cls.existingToken);
        if(elem){
            elem.style.cssText = 'display:none !important';
        }
    };

    that.showNewPaymentForm = function(){
        var elem = that.form.querySelector(that.cls.container.newCardForm);
        if(elem){
            elem.style.cssText = 'display:block !important';
        }
    };

    that.hideNewPaymentForm = function(){
        var elem = that.form.querySelector(that.cls.container.newCardForm);
        if(elem){
            elem.style.cssText = 'display:none !important';
        }
    };

    that.teardown = function(cb){
        if(that.hostedFieldsInstance){
            that.hostedFieldsInstance.teardown(function (err) {

                if(err){
                    that.showError(that.cls.errors.hostedField);
                    cb(err)
                }else {
                    if (!err && that.formEventHandler) {
                        that.form.removeEventListener('submit', that.formEventHandler, false);
                        that.formEventHandler = null;
                    }
                }
            });
        }
    };

    that.create = function(cb){


        braintree.client.create({

            authorization: that.token

        }, function (clientErr, clientInstance) {

            if (clientErr) {

                that.showError(that.cls.errors.create);

                cb(clientErr)

            }else{

                cb(null, clientInstance);

            }

        });
    };

    that.hostedFields = function(clientInstance, cb){

        braintree.hostedFields.create({
            client: clientInstance,
            styles: {
                'input': {
                    'font-size': '14px',
                    'font-family': 'helvetica, tahoma, calibri, sans-serif',
                    'color': '#3a3a3a'
                },
                'input.invalid': {
                    'color': 'red'
                },
                'input.valid': {
                    'color': 'green'
                },
                ':focus': {
                    'color': 'black'
                },
                '::-moz-placeholder' : {
                    'color' : '#999',
                    'opacity': 1,
                },
                ':-ms-input-placeholder' : {
                    'color' : '#999',
                },
                '::-webkit-input-placeholder' : {
                    'color' : '#999',
                }
            },
            fields: {
                number: {
                    selector: that.id.card.number,
                    placeholder: '4111 1111 1111 1111'
                },
                expirationDate: {
                    selector: that.id.card.expirationDate,
                    placeholder: '10/2019'
                },
                cvv: {
                    selector: that.id.card.cvv,
                    placeholder: '123'
                }

            }
        }, function (hostedFieldsErr, hostedFieldsInstance) {

            if (hostedFieldsErr) {

                that.showError(that.cls.errors.hostedField);

                cb(hostedFieldsErr);

            }else{

                that.hostedFieldsInstance = hostedFieldsInstance;
                cb(null, hostedFieldsInstance);
            }

        });

    };

    that.tokenize = function(hostedFieldsInstance, cb){

        hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {

            if (tokenizeErr) {

                switch (tokenizeErr.code) {
                    case 'HOSTED_FIELDS_FIELDS_EMPTY':

                        for(var cls in that.cls.errors.card){
                            that.showError(that.cls.errors.card[cls]);
                        }

                        break;
                    case 'HOSTED_FIELDS_FIELDS_INVALID':

                        for(var i = 0; i < tokenizeErr.details.invalidFieldKeys.length; i++){
                            that.showError(that.cls.errors.card[tokenizeErr.details.invalidFieldKeys[i]])
                        }

                        break;
                    case 'HOSTED_FIELDS_FAILED_TOKENIZATION':
                        that.showError(that.cls.errors.verify);
                        break;
                    case 'HOSTED_FIELDS_TOKENIZATION_FAIL_ON_DUPLICATE':
                        // will only get here if you generate a client token with a customer ID
                        // with the fail on duplicate payment method option. See:
                        // https://developers.braintreepayments.com/reference/request/client-token/generate/#options.fail_on_duplicate_payment_method
                        that.showError(that.cls.errors.verify);
                        break;
                    case 'HOSTED_FIELDS_TOKENIZATION_CVV_VERIFICATION_FAILED':
                        // will only get here if you generate a client token with a customer ID
                        // with the verify card option or if you have credit card verification
                        // turned on in your Braintree Gateway. See
                        // https://developers.braintreepayments.com/reference/request/client-token/generate/#options.verify_card
                        that.showError(that.cls.errors.card.cvc);
                        break;
                    case 'HOSTED_FIELDS_TOKENIZATION_NETWORK_ERROR':
                        that.showError(that.cls.errors.network);
                        break;
                    default:
                        that.showError(that.cls.errors.verify);

                }

                cb(tokenizeErr)

            }else{

                that.form.querySelector(that.cls.nonce).value = payload.nonce;

                cb(null, payload);
            }

        });


    };

    that.submit = function(cb, doneCB, formBeforeCB, formDoneCB, formCB){

        that.processing();
        that.create(function(err, clientInstance){

            if(!err){

                that.hostedFields(clientInstance, function(err, hostedFieldsInstance){

                    if(!err){


                        that.formEventHandler =  function (event) {

                            event.preventDefault();

                            that.processing();
                            that.hideErrors();

                            if(formBeforeCB){
                                formBeforeCB();
                            }

                            if(!cb){
                                cb = function(inner_cb){
                                    inner_cb(false);
                                }
                            }

                            cb(function(force){

                                if(force){

                                    if(!formCB) {
                                        that.form.submit();
                                    }else{
                                        that.done();
                                        if(formDoneCB) {
                                            formDoneCB();
                                        }
                                        formCB();
                                    }

                                }else{

                                    if(that.isUseOfExistingTokenChosen()){
                                        if(!formCB) {
                                            that.form.submit();
                                        }else{
                                            that.done();
                                            if(formDoneCB) {
                                                formDoneCB();
                                            }
                                            formCB();
                                        }
                                    }else {

                                        that.tokenize(hostedFieldsInstance, function (tokenizeErr, payload) {

                                            if (tokenizeErr) {
                                                that.done();
                                                if(formDoneCB) {
                                                    formDoneCB();
                                                }
                                            } else {
                                                if (payload) {

                                                    if(!formCB) {
                                                        that.form.submit();
                                                    }else{
                                                        that.done();
                                                        if(formDoneCB) {
                                                            formDoneCB();
                                                        }
                                                        formCB();
                                                    }

                                                }
                                            }

                                        });
                                    }
                                }

                            })

                        },

                        that.form.addEventListener('submit', that.formEventHandler, false);

                        that.done();
                        if(doneCB) {
                            doneCB();
                        }
                    }
                    else{
                        that.done();
                        if(doneCB) {
                            doneCB();
                        }
                    }

                })

            }else{
                that.done();
                if(doneCB) {
                    doneCB();
                }
            }

        })

    };

}

