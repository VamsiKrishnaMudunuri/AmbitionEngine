$(function() {

    var credit_card_slug = '';

    var name = {
        paymentMethod : 'payment-method-'
    };

    var cls = {
        paymentMethod : '.' + name.paymentMethod
    };

    $form = $('.payment-form');
    $creditCardPaymentMethod = $form.find('.credit-card-payment-method');
    $submitButton = $form.find('.submit');
    $cancelButton = $form.find('.cancel');

    $depositForm = $form.find('.deposit-section');
    $depositMethod = $depositForm.find('select.method');

    credit_card_slug = $creditCardPaymentMethod.data('credit-card');


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

});