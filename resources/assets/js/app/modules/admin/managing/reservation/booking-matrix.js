$(function() {

    var $module = $('.admin-managing-reservation-check-availability');

    var $pricingRule = $module.find('.pricing_rule');
    var $startTime = $module.find('.start_time');
    var $endTime = $module.find('.end_time');
    var $bookingMatrix = $module.find('.booking-matrix');

    $bookingMatrix.find('.unit-toggle').click(function(e){

        e.preventDefault();
        var $a = $(this);
        var $i =  $a.children('i');
        var unit = $a.data('unit');
        var $unit = $a.parents('.unit').next('tr.facilities[data-unit="' +  unit + '"]');

        $a.attr('disabled', 'disabled');

        if($unit.length > 0) {
            $unit.slideToggle('fast', function () {


                var $this = $(this);

                if ($this.is(':visible')) {
                    $i.toggleClass('fa-plus fa-minus');
                } else {
                    $i.toggleClass('fa-minus fa-plus');
                }

                $a.removeAttr('disabled');


            });
        }

    })

    $pricingRule.change(function(e){
        e.preventDefault();
        $this = $(this);
        var val = $this.val();
        var hour = $this.data('pricing-rule');

        if(val == hour){
            widget.show($startTime.parents('.form-group'));
            widget.show($endTime.parents('.form-group'));
        }else{
            widget.hide($startTime.parents('.form-group'));
            widget.hide($endTime.parents('.form-group'));
        }

    })

    $startTime.timepicker({
        'timeFormat': app.timepicker.time.format,
        'step': $startTime.data('minutes')
    });

    $endTime.timepicker({
        'timeFormat': app.timepicker.time.format,
        'step': $endTime.data('minutes')
    });

});