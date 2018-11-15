$(function() {

    $module = $('.admin-member-security');


    $('.save-right').click(function(e){

        e.preventDefault();

        var $submit = $(this);
        var $form = $submit.closest('form');

        var options = {
            url: $form.attr('action'),
            dataType: 'json',
            data: $form.serialize(),
        };

        widget.ajax.form($form, $form, $submit, null, null, options, function(data, textStatus, jqXHR) {

            widget.notify(jqXHR, data.message);

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        }, false);

    })


});