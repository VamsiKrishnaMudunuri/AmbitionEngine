$(function() {


    var $module = $('.admin-managing-lead-book-subscription-package, .admin-managing-lead-book-subscription-facility');
    var $form = $module.find('.booking-form');
    var $submitButton = $form.find('.submit');

    $(document).on('managing_subscription_package_success', function(event, data){

        leadStorage.setSubscription(leadStorage.populateViewState(window.opener));
        widget.popup.close(true, null, 0);

    });


});