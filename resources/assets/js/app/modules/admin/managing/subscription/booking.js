$(function() {

    var credit_card_slug = '';

    var name = {
        paymentMethod : 'payment-method-'
    };

    var cls = {
        paymentMethod : '.' + name.paymentMethod
    };

    var func = {
        autoUseDifferentDepositMethod: function($element, isNeedAlert){
            if (this.deposit() <= 0) {
                if(isNeedAlert) {
                    alert($element.data('message'));
                }
                $element.prop('checked', false);
                widget.hide($depositForm);
            }
        },
        isUseDifferentDepositMethod: function($element, isNeedAlert){

            this.autoUseDifferentDepositMethod($element, isNeedAlert);

            if($element.prop('checked')){
                $depositMethod.trigger('change');
                widget.show($depositForm);
            }else{
                widget.hide($depositForm);
            }

        },
        isTaxable : function(){
            return $tax.data('is-taxable');
        },
        isDiscount : function(){
            return cs.toNumber($discount.val()) > 0;
        },
        taxValue : function(){
            return $tax.data('tax-value');
        },
        deposit: function(){
            return cs.toNumber(cs.toFixed(cs.toNumber($deposit.val()), app.money_precision));
        },
        netPrice : function(){

            var price = cs.toNumber($proratedPrice.val());
            var discount = cs.toNumber($discount.val());

            if(this.isDiscount()){
                price = price - (price * discount / 100);
            }

            return cs.toNumber(cs.toFixed(price, app.money_precision));
        },
        taxableAmount: function(){
            return this.isTaxable() ? cs.toNumber(cs.toFixed(this.netPrice(), app.money_precision)) : 0;
        },
        tax: function(){
            return cs.toNumber(cs.toFixed(this.taxableAmount() * this.taxValue() / 100, app.money_precision));
        },
        grossPrice: function(){
            return cs.toNumber(cs.toFixed(this.netPrice() + this.tax(), app.money_precision));
        },
        grossPriceAndDeposit: function(){
            return cs.toNumber(cs.toFixed(this.netPrice() + this.deposit() + this.tax(), app.money_precision));
        }

    };

    var $form = $('.booking-form');
    var $creditCardPaymentMethod = $form.find('.credit-card-payment-method');
    var $submitButton = $form.find('.submit');
    var $cancelButton = $form.find('.cancel');
    var $deposit = $form.find('.deposit');
    var $price = $form.find('.price');
    var $proratedPrice = $form.find('.prorated_price');
    var $discount = $form.find('.discount');
    var $net_price = $form.find('.net_price');
    var $taxable_amount = $form.find('.taxable_amount');
    var $tax = $form.find('.tax');
    var $gross_price_and_deposit = $form.find('.gross_price_and_deposit');
    var $gross_price = $form.find('.gross_price');
    var $gross_deposit = $form.find('.gross_deposit');

    var $member = $form.find('.user_id');
    var $memberHidden = $form.find('.user_id_hidden');
    var $differentDepositMethod = $form.find('._different_deposit_method');
    var $paymentForm = $form.find('.payment-section');
    var $depositForm = $form.find('.deposit-section');
    var $paymentMethod = $paymentForm.find('.method');
    var $depositMethod = $depositForm.find('select.method');

    var isAjaxSubmit = $submitButton.data('is-ajax-submit');

    credit_card_slug = $creditCardPaymentMethod.data('credit-card');

    braintreePayment.initialize($form.get(0), $submitButton.get(0), [$cancelButton.get(0)]);

    if(!isAjaxSubmit){

        braintreePayment.submit(function(cb){

            if($paymentMethod.val() == credit_card_slug){
                cb(false);
            }else{
                cb(true);
            }

        });

    }else{

        braintreePayment.submit(function(cb){

            if($paymentMethod.val() == credit_card_slug){
                cb(false);
            }else{
                cb(true);
            }

        }, function(){}, function(){}, function(){}, function(){

            var $submit = $submitButton;
            var $form = $submit.closest('form');
            var $messageBox = $form.find('.message-box');
            var $success = $messageBox;
            var $error = $messageBox;
            var data = '';

            data = $form.serialize();

            var options = {
                url: $form.attr('action'),
                data: data
            };

            widget.ajax.form($form, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {


            }, function(jqXHR, textStatus, errorThrown){


            }, function(firstJqXHR, firstTextStatus, firstError){

            }, function(firstJqXHR, firstTextStatus, firstError, hasError){

                if(!hasError){

                    $(document).trigger('managing_subscription_package_success');

                }

            });

        });

    }


    var dataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s',  $member.data('url'), '%QUERY'),
            wildcard: '%QUERY'
        }
    });

    var displayField = 'full_name';

    $member.typeahead({highlight : true, hint : true, minLength : 1}, {
        name: 'members',
        display: displayField,
        limit: 41,
        source: dataSource,
        templates : {

            notFound: sprintf('<div class="empty">%s</div>', $member.data('no-found')),
            suggestion: function(item){
                return sprintf('<div class="card" data-disabled="%s"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details member-skin"><div class="name">%s</div><div class="username">%s</div><div class="email">%s</div><div class="company">%s</div><div class="hint"><small>%s</small></div></div></a></div>', item.subscription_status,  item.profile_url, item.full_name, item.username_alias, item.email, item.company, item.subscription_message);
            }

        }
    })
    .on('typeahead:asyncrequest', function() {
        var loading = $member.parents('.twitter-typeahead-container').find('.fa-loading');
        if(loading.length <= 0) {
            $member.parents('.twitter-typeahead-container').append(skin.loading.sm);
        }

    })
    .on('typeahead:asynccancel typeahead:asyncreceive', function() {
        var loading = $member.parents('.twitter-typeahead-container').find('.fa-loading');
        if(loading.length > 0) {
            loading.remove();
        }
    })
    .on('typeahead:select', function(event, item) {

        if(!item.subscription_status) {
            $memberHidden.val(item.id);
            $memberHidden.data(displayField, item[displayField]);
        }

    })
    .on('typeahead:change', function(event, item) {

        if( $.trim(item) !=  $.trim($memberHidden.data(displayField))) {
            $memberHidden.val('');
            $memberHidden.data(displayField, '');
        }
    });


    $paymentMethod.change(function(event){

        var selectedValue = $(this).val();

        widget.hide($paymentForm.find('[class*="' + name.paymentMethod + '"]'));

        $paymentDetails = $paymentForm.find(cls.paymentMethod + selectedValue );

        if($paymentDetails.length > 0){

            widget.show($paymentDetails);

        }

    })

    $depositMethod.change(function(event){

        var selectedValue = $(this).val();

        widget.hide($depositForm.find('[class*="' + name.paymentMethod + '"]'));

        $paymentDetails = $depositForm.find(cls.paymentMethod + selectedValue );

        if($paymentDetails.length > 0){

            widget.show($paymentDetails);

        }

    })

    $differentDepositMethod.click(function(event){

        var $this = $(this);

        func.isUseDifferentDepositMethod($this, true);

    });

    $paymentMethod.trigger('change');
    func.isUseDifferentDepositMethod($differentDepositMethod);

    $deposit.keyup(function(event){

        $gross_deposit.val(cs.toLocalizeNumber(func.deposit(), app.money_precision));
        $gross_price_and_deposit.val(cs.toLocalizeNumber(func.grossPriceAndDeposit(), app.money_precision));

        func.autoUseDifferentDepositMethod($differentDepositMethod);


    })

    $discount.keyup(function(event){

        $(this).val(cs.discount($(this).val()));

        $net_price.val(cs.toLocalizeNumber(func.netPrice(), app.money_precision));
        $taxable_amount.val(cs.toLocalizeNumber(func.taxableAmount(), app.money_precision));
        $tax.val(cs.toLocalizeNumber(func.tax(), app.money_precision));

        $gross_price.val(cs.toLocalizeNumber(func.grossPrice(), app.money_precision));
        $gross_price_and_deposit.val(cs.toLocalizeNumber(func.grossPriceAndDeposit(), app.money_precision));

    })


});