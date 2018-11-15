$(function() {

    $module = $('.member-creditcard-edit');
    $form = $module.find('form');
    $info = $form.find('.info-box')
    $submit = $form.find('.submit');
    $cancel = $form.find('.cancel');

    braintreePayment.initialize($form.get(0), $submit.get(0), [$cancel.get(0)] );

    braintreePayment.submit(null, function(){

    }, function(){

        widget.show($info);

    });


});