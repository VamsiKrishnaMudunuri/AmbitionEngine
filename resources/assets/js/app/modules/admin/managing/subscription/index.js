$(function() {

    $module = $('.admin-managing-subscription-index');
    $showPrice = $module.find('.show-price');

    $showPrice.click(function(e){
        e.preventDefault(); $(this).next('.modal').modal('show');
    })

});