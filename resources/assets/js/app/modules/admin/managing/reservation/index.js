$(function() {

    $module = $('.admin-managing-reservation-index');
    $showPrice = $module.find('.show-price');

    $showPrice.click(function(e){
        e.preventDefault(); $(this).next('.modal').modal('show');
    })

});