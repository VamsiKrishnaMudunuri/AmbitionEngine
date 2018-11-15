$(function() {

    var $reportMatrix = $('.admin-managing-report-finance-salesoverview-occupany .report-matrix');

    $reportMatrix.find('.unit-toggle').click(function(e){

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

    $('.select-year').change(function(){

        var $this = $(this);
        var url = $this.data('url');

        window.location = url + '?year=' + $this.val();
    })

});