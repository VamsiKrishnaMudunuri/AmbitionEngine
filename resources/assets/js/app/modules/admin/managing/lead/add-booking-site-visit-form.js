$(function() {


    var $module = $('.admin-managing-lead-add-booking-site-visit');
    var $siteVisitForm = $module.find('.site-visit-booking-form');
    var $siteVisitSubmitButton = $siteVisitForm.find('.input-submit');

    $siteVisitSubmitButton.click(function(event){

        event.preventDefault();

        var $submit = $(this);
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



        widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {


        }, function(jqXHR, textStatus, errorThrown){


        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

            if(!hasError){

                leadStorage.setBookingSiteVisit(leadStorage.populateViewState(window.opener));
                widget.popup.close(true, null, 0);

            }

        });

    });



});