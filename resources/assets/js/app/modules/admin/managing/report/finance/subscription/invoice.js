$(function() {

    $module = $('.admin-managing-report-finance-subscription-invoice');
    $showPrice = $module.find('.show-price');

    $showPrice.click(function(e){
        e.preventDefault(); $(this).next('.modal').modal('show');
    })

});