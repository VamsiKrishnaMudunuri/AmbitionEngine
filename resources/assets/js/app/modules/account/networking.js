$(function() {

    var $module = $('.account-networking');
    var $view = $module.find('.view');
    var $wifi = $module.find('.wifi');
    var $wifiFirstLayout = $wifi.find('.first-layout');
    var $wifiSecondLayout = $wifi.find('.second-layout');
    var $wifiUsername = $wifiSecondLayout.find('.username').children('span');
    var $wifiPassword = $wifiSecondLayout.find('.password').children('span');
    var $printer = $module.find('.printer');
    var $printerFirstLayout = $printer.find('.first-layout');
    var $printerSecondLayout = $printer.find('.second-layout');
    var $printerUsername = $printerSecondLayout.find('.username').children('span');
    var $printerPassword = $printerSecondLayout.find('.password').children('span');


    $view.click(function(e){

        e.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                data = widget.json.toJson(data);


                $wifiUsername.html(data.wifi.username);
                $wifiPassword.html(data.wifi.password);
                $printerUsername.html(data.printer.username);
                $printerPassword.html(data.printer.password);

                widget.hide($wifiFirstLayout);
                widget.show($wifiSecondLayout);

                widget.hide($printerFirstLayout);
                widget.show($printerSecondLayout);

                setTimeout(function(){

                    $wifiUsername.html('');
                    $wifiPassword.html('');
                    $printerUsername.html('');
                    $printerPassword.html('');


                    widget.show($wifiFirstLayout);
                    widget.hide($wifiSecondLayout);

                    widget.show($printerFirstLayout);
                    widget.hide($printerSecondLayout);

                }, 180000)

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });

    })


});