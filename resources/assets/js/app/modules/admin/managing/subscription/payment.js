$(function() {

    var credit_card_slug = '';

    var name = {
        paymentMethod : 'payment-method-'
    };

    var cls = {
        paymentMethod : '.' + name.paymentMethod
    };


    var $form = $('.payment-form');
    var $creditCardPaymentMethod = $form.find('.credit-card-payment-method');
    var $submitButton = $form.find('.submit');
    var $cancelButton = $form.find('.cancel');

    var $paymentForm = $form.find('.payment-section');
    var $depositForm = $form.find('.deposit-section');

    var $paymentMethod = $paymentForm.find('.method');
    var $depositMethod = $depositForm.find('select.method');

    credit_card_slug = $creditCardPaymentMethod.data('credit-card');

    if($paymentMethod.length > 0 && $depositMethod.length > 0){

        var pdfunc = {
            autoUseDifferentDepositMethod: function ($element, isNeedAlert) {
                if ($depositMethod.length <= 0) {
                    if (isNeedAlert) {
                        alert($element.data('message'));
                    }
                    $element.prop('checked', false);
                    widget.hide($depositFormPaymentMethod);
                }
            },
            isUseDifferentDepositMethod: function ($element, isNeedAlert) {

                this.autoUseDifferentDepositMethod($element, isNeedAlert);

                if ($element.prop('checked')) {
                    $depositMethod.trigger('change');
                    widget.show( $depositFormPaymentMethod);
                } else {
                    widget.hide( $depositFormPaymentMethod);
                }

            },
        };

        var $differentDepositMethod = $form.find('._different_deposit_method');
        var $depositFormPaymentMethod = $form.find('.deposit-section-payment-method');

        braintreePayment.initialize($form.get(0), $submitButton.get(0), [$cancelButton.get(0)]);
        braintreePayment.submit(function(cb){
            if($paymentMethod.val() == credit_card_slug){
                cb(false);
            }else{
                cb(true);
            }

        });

        $paymentMethod.change(function(){

            var selectedValue = $(this).val();

            widget.hide($paymentForm.find('[class*="' + name.paymentMethod + '"]'));

            $paymentDetails = $paymentForm.find(cls.paymentMethod + selectedValue );

            if($paymentDetails.length > 0){

                widget.show($paymentDetails);

            }

        })

        $depositMethod.change(function(){

            var selectedValue = $(this).val();

            widget.hide($depositForm.find('[class*="' + name.paymentMethod + '"]'));

            $paymentDetails = $depositForm.find(cls.paymentMethod + selectedValue );

            if($paymentDetails.length > 0){

                widget.show($paymentDetails);

            }

        })

        $differentDepositMethod.click(function(event){

            var $this = $(this);

            pdfunc.isUseDifferentDepositMethod($this, true);

        });

        pdfunc.isUseDifferentDepositMethod($differentDepositMethod);

        $paymentMethod.trigger('change');

    }else if($paymentMethod.length > 0){

        braintreePayment.initialize($form.get(0), $submitButton.get(0), [$cancelButton.get(0)]);
        braintreePayment.submit(function(cb){
            if($paymentMethod.val() == credit_card_slug){
                cb(false);
            }else{
                cb(true);
            }

        });

        $paymentMethod.change(function(){

            var selectedValue = $(this).val();

            widget.hide($paymentForm.find('[class*="' + name.paymentMethod + '"]'));

            $paymentDetails = $paymentForm.find(cls.paymentMethod + selectedValue );

            if($paymentDetails.length > 0){

                widget.show($paymentDetails);

            }

        })

        $paymentMethod.trigger('change');


    }else if($depositMethod.length > 0){

        braintreePayment.initialize($form.get(0), $submitButton.get(0), [$cancelButton.get(0)]);
        braintreePayment.submit(function(cb){

            if($depositMethod.val() == credit_card_slug){
                cb(false);
            }else{
                cb(true);
            }

        });

        $depositMethod.change(function(){

            var selectedValue = $(this).val();

            widget.hide($depositForm.find('[class*="' + name.paymentMethod + '"]'));

            $paymentDetails = $depositForm.find(cls.paymentMethod + selectedValue );

            if($paymentDetails.length > 0){

                widget.show($paymentDetails);

            }

        })

        $depositMethod.trigger('change');


    }




});