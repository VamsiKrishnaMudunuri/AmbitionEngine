$(function() {


    $form = $('.member-room-booking-form');
    $duration = $form.find('.duration');
    $order = $form.find('.order');

    $schedule = $order.find('.schedule');
    $cost = $order.find('.cost');
    $costText = $cost.find('.text');
    $complimentary = $order.find('.complimentary');
    $charge = $order.find('.charge');
    $chargeText = $charge.find('.text');

    $start_date_input = $schedule.find('.start_date');
    $end_date_input = $schedule.find('.end_date');

    $start_date = $schedule.find('.start-date');
    $start_time = $schedule.find('.start-time');
    $end_time = $schedule.find('.end-time');

    var reservation = $order.data('reservation');
    var creditUnit = $order.data('unit');
    var creditArr = $order.data('credit').split('|');
    var minuteInterval = $order.data('minute-interval');

    var am = $schedule.data('am');
    var pm = $schedule.data('pm');

    var start_date = $start_date.data('date');
    var start_time = $start_time.data('time');
    var end_time = $end_time.data('time');

    var subscription_complimentary_remaining = $complimentary.data('complimentary');

    var start_time_arr = start_time.split(':');
    var end_time_arr = end_time.split(':');
    var start_time_min = (cs.toNumber(start_time_arr[0]) * 60) + cs.toNumber(start_time_arr[1]);
    var end_time_min = (cs.toNumber(end_time_arr[0]) * 60) + cs.toNumber(end_time_arr[1]);

    var func = {
        total: function(){

            var price = reservation.price;

            var time = end_time_min - start_time_min;

            price = (price / 60) * time;

            return cs.toNumber(cs.toFixed(price, app.money_precision));
        },

        isDiscount : function(){
            return reservation.discount > 0;
        },
        discountAmount : function(){

            var price = this.total();
            var amount = 0;

            if(this.isDiscount()){
                amount = price * reservation.discount / 100;
            }

            return cs.toNumber(cs.toFixed(amount, app.money_precision));
        },
        netPrice : function(){

            var price = this.total() - this.discountAmount();

            return cs.toNumber(cs.toFixed(price, app.money_precision));
        },
        taxableAmount: function(){
            return  reservation.is_taxable ? cs.toNumber(cs.toFixed(this.netPrice(), app.money_precision)) : 0;
        },
        tax: function(){
            return cs.toNumber(cs.toFixed(this.taxableAmount() * reservation.tax_value / 100, app.money_precision));
        },
        grossPrice: function(){
            return cs.toNumber(cs.toFixed(this.netPrice() + this.tax(), app.money_precision));
        },
        grossPriceInCredits: function(){
            return cs.toNumber(cs.toFixed((this.grossPrice() * reservation.quote_rate) / creditUnit, 0));
        },
        grossPriceInCreditsIfNeedToApplySubscriptionComplimentary: function(){
            var price = this.grossPriceInCredits();
            if(subscription_complimentary_remaining > 0){
                price = Math.max(0, price - subscription_complimentary_remaining);
            }
            return cs.toNumber(cs.toFixed(price, 0));
        }


    };

    $duration.change(function(event){

        $this = $(this);

        end_time_min = (cs.toNumber(start_time_arr[0]) * 60) + cs.toNumber(start_time_arr[1]) +  cs.toNumber($this.val());

        var end_hour = Math.floor(end_time_min / 60);
        var end_min = end_time_min % 60;
        var end_sec = 0;


        var start_time_string = sprintf('%s:%s %s', (end_hour > 12) ? end_hour - 12 : end_hour, (end_min < 10) ? '0' + end_min : end_min, (end_hour < 12) ? am : pm );
        var end_hour_string = (end_hour < 10) ? '0' + end_hour : end_hour;
        var end_min_string = (end_min < 10) ? '0' + end_min : end_min;
        var end_sec_string = (end_sec < 10) ? '0' + end_sec : end_sec;


        $end_time.html(start_time_string);
        $end_date_input.val(sprintf('%s %s:%s:%s', start_date, end_hour_string, end_min_string, end_sec_string));


        var credit_need = func.grossPriceInCredits();
        var credit_charge = func.grossPriceInCreditsIfNeedToApplySubscriptionComplimentary();

        $costText.html(sprintf('%s %s', credit_need, (credit_need <= 1) ? creditArr[0] : creditArr[1]));
        $chargeText.html(sprintf('%s %s', credit_charge, (credit_charge <= 1) ? creditArr[0] : creditArr[1]));


    })

});