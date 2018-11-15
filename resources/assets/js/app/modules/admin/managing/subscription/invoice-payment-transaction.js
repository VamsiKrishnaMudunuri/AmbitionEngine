$(function() {


    var name = {
        paymentMethod : 'payment-method-'
    };

    var cls = {
        paymentMethod : '.' + name.paymentMethod
    };


    var $form = $('.invoice-payment-transaction-form');
    var $submitButton = $form.find('.submit');
    var $cancelButton = $form.find('.cancel');
    var $paymentMethod = $form.find('.method');


    $paymentMethod.change(function(){

        var selectedValue = $(this).val();

        widget.hide($form.find('[class*="' + name.paymentMethod + '"]'));

        var $paymentDetails = $form.find(cls.paymentMethod + selectedValue );

        if($paymentDetails.length > 0){

            widget.show($paymentDetails);

        }


    })

    $paymentMethod.trigger('change');


});