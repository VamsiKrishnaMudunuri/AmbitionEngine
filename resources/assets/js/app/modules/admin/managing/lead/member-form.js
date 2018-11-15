$(function() {


    var $module = $('.admin-managing-lead-add-member, .admin-managing-lead-edit-member');
    var $form = $module.find('.member-form');
    var $submitButton = $form.find('.input-submit');

    $submitButton.click(function(event){

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
            data: new FormData($form[0]),
            contentType: false,
            processData: false
        };


        widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {

            data = widget.json.toJson(data).arr;

            var $memberHidden = window.opener.$(sprintf('.%s', 'user_id_hidden'));
            var $memberInputs = window.opener.$(sprintf('.%s', '_user_id'));
            $memberHidden.val(data.id);

            $memberInputs.each(function(){
                $(this).val(data.full_name);
            })


        }, function(jqXHR, textStatus, errorThrown){


        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

            if(!hasError){


                leadStorage.setSubscription(leadStorage.populateViewState(window.opener));
                widget.popup.close(true, null, 0);

            }

        });

    });



});