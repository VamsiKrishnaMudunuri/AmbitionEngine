$(function() {


    var name = {
        paymentMethod : 'payment-method-'
    };

    var cls = {
        paymentMethod : '.' + name.paymentMethod
    };


    var $form = $('.top-up-wallet .payment-form');
    var $info = $form.find('.info-box');
    var $submitButton = $form.find('.submit');
    var $cancelButton = $form.find('.cancel');
    var $creditCardPaymentMethod = $form.find('.credit-card-payment-method');
    var $paymentMethod = $form.find('.method');

    var credit_card_slug = $creditCardPaymentMethod.data('credit-card');


    var existingCredit = $('._credit').val();
    if(existingCredit){
        var $package = $(sprintf('.credit-package[data-credit="%s"]', existingCredit))
        if($package.length > 0){
            $package.addClass('active');
        }
    }


    $('.credit-package').click(function(e){

        e.preventDefault();

        var $this = $(this);
        var $buy = $this.find('.buy');

        if($this.attr('disabled')){
            return false;
        }

        var $active = $('.credit-package.active');

        if($active.length > 0){
            var $activeBuy = $active.find('.buy');
            $active.removeClass('active');
            $activeBuy.html($activeBuy.data('select'));
        }


        $this.addClass('active');
        $buy.html($buy.data('selected'));
        $('._credit').val($this.data('credit'));

    })

    $paymentMethod.change(function(){

        var selectedValue = $(this).val();

        widget.hide($form.find('[class*="' + name.paymentMethod + '"]'));

        var $paymentDetails = $form.find(cls.paymentMethod + selectedValue );

        if($paymentDetails.length > 0){

            widget.show($paymentDetails);

        }


    })

    $paymentMethod.trigger('change');

    var disabledElements = [$cancelButton.get(0)];

    $('.credit-package').each(function(){
        disabledElements.push($(this).get(0));
    })

    braintreePayment.initialize($form.get(0), $submitButton.get(0), disabledElements);

    braintreePayment.submit(function(cb){

        if($paymentMethod.val() == credit_card_slug){
            cb(false);
        }else{
            cb(true);
        }

    }, function(){

    }, function(){

        widget.show($info);

    });


});