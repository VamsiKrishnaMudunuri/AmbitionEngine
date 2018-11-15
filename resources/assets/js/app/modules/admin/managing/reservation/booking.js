$(function() {

    var func = {
        isTaxable : function(){
            return $tax.data('is-taxable');
        },
        isDiscount : function(){
            return cs.toNumber($discount.val()) > 0;
        },
        taxValue : function(){
            return $tax.data('tax-value');
        },
        netPrice : function(){

            var price = $net_price.data('original-net-price');
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
        grossPriceCredit: function(){
            var quote_rate = $gross_price_credit.data('quote-rate');
            var wallet_unit = $gross_price_credit.data('wallet-unit');
            return cs.toNumber(cs.toFixed((this.grossPrice() * quote_rate) / wallet_unit  , 0));
        }

    };

    var $form = $('.booking-form');
    var $submitButton = $form.find('.submit');
    var $cancelButton = $form.find('.cancel');
    var $price = $form.find('.price');
    var $discount = $form.find('.discount');
    var $net_price = $form.find('.net_price');
    var $taxable_amount = $form.find('.taxable_amount');
    var $tax = $form.find('.tax');
    var $gross_price = $form.find('.gross_price');
    var $gross_price_credit = $form.find('.gross_price_credit');

    var $member = $form.find('.user_id');
    var $memberHidden = $form.find('.user_id_hidden');

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
                return sprintf('<div class="card" data-disabled="%s"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details member-skin"><div class="name">%s</div><div class="username">%s</div><div class="email">%s</div><div class="company">%s</div><div class="wallet">%s</div></div></a></div>', item.display_status,  item.profile_url, item.full_name, item.username_alias, item.email, item.company, item.balance);
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

        if(!item.display_status) {
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


    $discount.keyup(function(event){

        $(this).val(cs.discount($(this).val()));

        $net_price.val(cs.toLocalizeNumber(func.netPrice(), app.money_precision));
        $taxable_amount.val(cs.toLocalizeNumber(func.taxableAmount(), app.money_precision));
        $tax.val(cs.toLocalizeNumber(func.tax(), app.money_precision))
        $gross_price.val(cs.toLocalizeNumber(func.grossPrice(), app.money_precision));
        $gross_price_credit.val(cs.toLocalizeNumber(func.grossPriceCredit(), 0));

    })


});