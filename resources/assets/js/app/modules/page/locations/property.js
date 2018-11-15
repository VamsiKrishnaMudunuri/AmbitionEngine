$(function() {

    /**

    $module = $('.page-location-property');
    $packageContainer =  $module.find('.office-container');
    $packageBox =  $packageContainer.find('.office-box .box-content');
    $allowScreenSize = 768;
    $options = {
        'parent' :  $packageContainer,
        offset_top: 85
    };

    if($(window).width() >= $allowScreenSize) {
        $packageBox.stick_in_parent($options);
    }

    $( window ).resize(function() {

        if($(window).width() < $allowScreenSize){
            $packageBox.trigger('sticky_kit:detach')
        }else{
            $packageBox.stick_in_parent($options);
        }

    })

    **/

    $('.input-submit').click(function(event){

        event.preventDefault();

        var $submit = $(this);
        var $form = $submit.closest('form');
        var $messageBox = $form.find('.message-box');
        var $pageBookingLocation = $form.find('.page-booking-location');
        var $pageBookingPackagePax =$form.find('.page-booking-package-pax');
        var $pageBookingPackage = $form.find('.page-booking-package');
        var $success = $messageBox;
        var $error = $messageBox;
        var data = '';

        var location_selected_text =  $pageBookingLocation.find('option:selected').text();
        var package_pax_selected_text =   $pageBookingPackagePax.find('option:selected').text();
        var package_selected_text =   $pageBookingPackage.find('option:selected').text();

        data = $form.serialize();

        var options = {
            url: $form.attr('action'),
            data: data
        };


        widget.ajax.form($error, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {



        }, function(jqXHR, textStatus, errorThrown){


        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

            if(!hasError){


                var redirectUrl = widget.json.toJson(firstJqXHR).link;
                var pixel_url = sprintf("//pixel.mathtag.com/event/js?mt_id=1328800&mt_adid=211124&mt_exem=&mt_excl=&v1=%s&v2=%s&v3=%s", location_selected_text, package_pax_selected_text, package_selected_text);

                $.getScript( pixel_url )
                    .done(function( script, textStatus ) {
                       window.location.href = redirectUrl;
                    })
                    .fail(function( jqxhr, settings, exception ) {
                       window.location.href = redirectUrl;
                    });



            }

        });


    });




});