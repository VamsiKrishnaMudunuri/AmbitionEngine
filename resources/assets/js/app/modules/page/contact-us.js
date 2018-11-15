$(function() {

    $module = $('.contact-us-form');

    $module.on('click', '.input-submit', function(event){

        event.preventDefault();

        var $submit = $(this);
        var $form = $submit.closest('form');
        var $messageBox = $form.find('.message-box');
        var $success = $messageBox;
        var $error = $messageBox;

        var options = {
            url: $form.attr('action'),
            data: $form.serialize()
        };

        widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {
            // $form.removeClass('show').addClass('hide');
            // $form.prev('.feedback-thank-you').removeClass('hide').addClass('show')
            if ($submit.data('shouldRedirect')) {
                location.href = $submit.data('shouldRedirect');
            } else {
                $success.after($success.after($(skin.alert.success(data))));
            }

        }, function(jqXHR, textStatus, errorThrown){

            setTimeout(function() {
                $oldElementContactNumber = $form.find('#contact_number').prev().detach();
                $form.find('.btm-divider').after($oldElementContactNumber);
            }, 1);

        });

    });

});