$(function() {

    $module = $('.member-wallet-top-up');
    $form = $module.find('form');
    $info = $form.find('.info-box');
    $submitButton = $form.find('.submit');
    $cancelButton = $form.find('.cancel');

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

    var disabledElements = [$cancelButton.get(0)];

    $('.credit-package').each(function(){
        disabledElements.push($(this).get(0));
    })

    braintreePayment.initialize($form.get(0), $submitButton.get(0), disabledElements);

    braintreePayment.submit(null, function(){

    }, function(){

        widget.show($info);

    });


});