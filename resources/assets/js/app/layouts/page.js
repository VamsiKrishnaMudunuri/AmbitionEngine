$(function() {

    var $body =  $('body');

    var $pageBookingHeaderAutoTrigger = $('.page-booking-header-auto-trigger');
    var $pageBookingAutoTrigger = $('.page-booking-auto-trigger');

    var $pageLocationState = $('.page-location-state');

    var is_show_auto_booking = cs.getQueryString('booking');

    var buttonTriggerVisibility = '.triggerVisibility';


    $body.on('click', '.page-booking-auto-trigger, .page-booking-trigger', function(event){

        event.preventDefault();

        var a = $(this);
        var url = a.data('url');
        var $select = a.parent().find('select.page-booking-location');
        var package = a.data('page-booking-package');
        var action =  a.data('page-booking-action');

        if($select.length > 0){
            url += '/' + $select.val() + '/true';
        }

        var $modal = null;

        var options = {
            url: url
        };


        widget.ajax.get(a, null, null, options, function(data, textStatus, jqXHR) {

            var $modal = $(data);
            var $module = $modal;

            $pageBookingLocation = $modal.find('.page-booking-location');
            $pageBookingPackagePax = $modal.find('.page-booking-package-pax');
            $pageBookingPackage = $modal.find('.page-booking-package');

            $schedule = $modal.find('.schedule');

            $pageBookingPackage.val(package);

            $modal.modal('show');

            $modal.on('hidden.bs.modal', function(e){
                $(this).remove();
            })

            $modal.on('click', '.input-submit', function(event){

                event.preventDefault();

                var $submit = $(this);
                var $form = $submit.closest('form');
                var $messageBox = $form.find('.message-box');
                var $success = $messageBox;
                var $error = $messageBox;
                var data = '';

                data = $form.serialize();

                data += '&type=' + action;


                var options = {
                    url: $form.attr('action'),
                    data: data
                };

                var location_selected_text =  $pageBookingLocation.find('option:selected').text();
                var package_pax_selected_text =   $pageBookingPackagePax.find('option:selected').text();
                var package_selected_text =   $pageBookingPackage.find('option:selected').text();

                $pageBookingLocation.find('option:first').attr('selected','selected');


                widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {

                    //$modal.find('.page-booking').replaceWith(data);

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

        }, function(jqXHR, textStatus, errorThrown){



        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){

            if( is_show_auto_booking ){
                $pageBookingHeaderAutoTrigger.data('page-loading', false);
                $pageBookingAutoTrigger.data('page-loading', false);
            }

        });

    });

    if(is_show_auto_booking){

        if($pageLocationState.length > 0 && $pageBookingHeaderAutoTrigger.length > 0){

            $pageBookingHeaderAutoTrigger.data('page-loading', true);
            $pageBookingHeaderAutoTrigger.trigger('click');

        }else if($pageBookingAutoTrigger.length > 0){

            $pageBookingAutoTrigger.data('page-loading', true);
            $pageBookingAutoTrigger.trigger('click');

        }else{

            is_show_auto_booking = 0;

        }


    }


    $subscribe = $('.page-subscribe-form');

    $subscribe.on('click', '.input-submit', function(event){

        event.preventDefault();

        var $module = $subscribe;
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

            if ($submit.data('shouldRedirect')) {
                location.href = $submit.data('shouldRedirect');
            } else {
                $success.after($(skin.alert.success(data)));
            }

        }, function(jqXHR, textStatus, errorThrown){

        });

    });

    $body.on('click', buttonTriggerVisibility, function(event) {
        event.preventDefault();

        var $this = $(this);
        var target = $this.data('visibilityTargetId');
        var targetContainerClass = $this.data('visibilityContainerClass');

        $body.find('.' + targetContainerClass).removeClass('show').addClass('hide');

        $body.find('#' + target).removeClass('hide').addClass('show');
    });

    navBarState();

    $(window).scroll(navBarState);

    function navBarState() {
        var scroll = $(window).scrollTop();

        if (scroll >= 100) {
            if (!$('.navbar-fixed-top.cms').hasClass('nav-state-inverse')) {
                $('.navbar-fixed-top.cms').addClass('nav-state-inverse');
            }
        } else {
            if (scroll <= 100) {
                if ($('.navbar-fixed-top.cms').hasClass('nav-state-inverse')) {
                    $('.navbar-fixed-top.cms').removeClass('nav-state-inverse');
                }
            }
        }
    }

    var toggle = function($select, $classToRemoveIfDisable, $classToRemoveIfEnable) {

        if ($select.data('buttonState')) {
            if ($select.val().trim()) {
                if ($($select.data('buttonState')).is(':disabled')) {
                    $($select.data('buttonState'))
                        .prop('disabled', false)
                        .removeClass($classToRemoveIfDisable)
                        .addClass($classToRemoveIfEnable)
                }
            } else {
                $($select.data('buttonState'))
                    .prop('disabled', true)
                    .removeClass($classToRemoveIfEnable)
                    .addClass($classToRemoveIfDisable)
            }
        }

    }

    $body.find('.change-btn-state').change(function() {

        var $select = $(this);
        var classToRemoveIfDisabled = $select.data('classDisabled');
        var classToRemoveIfEnabled = $select.data('classEnabled');

        toggle($select, classToRemoveIfDisabled, classToRemoveIfEnabled);

    });

    /**

    var coming_soon = 'coming-soon';

    var hasComingSoon =  ($.cookie(coming_soon)) ? $.cookie(coming_soon) : '';

    if(!hasComingSoon) {
        $('.coming-soon-modal').modal('show');
        var date = new Date();
        date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
        $.cookie(coming_soon, true, { expires : date , path : '/' });
    }

   **/


});

// Global function to make the image(s) clickable for preview
function clickableImg(mainContainer, dataAttribute, settings) {

    var options = {};

    $('body').find(mainContainer).each(function() {

        var photo = [
            $(this).data(dataAttribute ? dataAttribute : 'clickable-img')
        ];

        options = {
            images: photo,
            cells: 1,
            align: false,
            loading: skin.loading.sm,
            onGridRendered: function($grid) {

                var gridImage = $grid.find('.imgs-grid-image');
                var imgWrap = gridImage.find('.image-wrap');
                var img = imgWrap.find('img');

                $(gridImage).addClass('h-100-stretch');
                $(imgWrap).addClass('h-100-stretch');
                $(img).addClass('h-100-stretch');
            }
        };

        options = Object.assign(options, (typeof settings  === 'undefined' ? {} : settings));

        $(this).imagesGrid(options);

    });

}

function isEmpty(obj) {
    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
}