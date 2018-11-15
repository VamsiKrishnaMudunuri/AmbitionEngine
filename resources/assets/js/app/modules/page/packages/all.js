$(function() {

    $module = $('.page-packages');
    $signUp = $module.find('.sign-up-trigger');
    $packageContainer =  $module.find('.package-container');
    $packageBox =  $packageContainer.find('.package-box .box');
    $priceDefault = $packageBox.find('.price-default');
    $priceBox = $packageBox.find('.price');

    $locationSelection = $packageBox.find('.select-city');
    $allowScreenSize = 768;
    $options = {
        'parent' :  $module,
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

    $signUp.click(function(event){

        event.preventDefault();

        var a = $(this);
        var url = a.data('url');
        var $select = a.parent().find('select.page-booking-location');

        if($select.length > 0){
            url += '?office=' + $select.val();
        }

        window.location.href = url;

    })

    $locationSelection.change(function(event){

        event.preventDefault();

        var $select = $(this);
        var property = $select.val();
        var category = $select.data('category');
        var url = sprintf('%s/%s/%s', $select.data('url'), property, category)

        var options = {
            url: url
        };

        if(!property){

            $priceBox.html($priceDefault.html());

        }else {


            widget.ajax.get($select, null, null, options, function (data, textStatus, jqXHR) {

                $priceBox.html(data);

            }, function (jqXHR, textStatus, errorThrown) {


            }, function (jqXHR, textStatus, error) {

            });
        }

    })

});