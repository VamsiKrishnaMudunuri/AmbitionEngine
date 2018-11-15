$(function() {

    var $body =  $('body');

    var $notification = $body.find('header .notification');
    var hasNotificationBroadcastReceived = false;
    var hasNotificationLoaded = false;

    var func = {

        addNotification: function(template){
            if($notification){
                if(template) {
                    var $menu = $notification.next().find('.notification-feeds ul');
                    $menu.prepend(template);
                }
            }
        }

    };

    if($notification.length > 0){

        if($notification.find('.figure').parent().is(':visible')){
            hasNotificationBroadcastReceived = true;
        }

        $notification.click(function(event){

            event.preventDefault();

            var $this = $(this);
            var latest_url = $this.data('url');
            var reset_stats_url = $this.data('reset-stats-url');

            var $figure = $this.find('.figure');

            $figure.html('').parent().addClass('hide');

            if(hasNotificationBroadcastReceived){

                widget.ajax.post(null, null, null, {url: reset_stats_url}, function (data, textStatus, jqXHR) {

                    hasNotificationBroadcastReceived = false;

                }, function (jqXHR, textStatus, errorThrown) {



                }, function (jqXHR, textStatus, error) {

                }, function (jqXHR, textStatus, error, hasError) {


                });


            }

            if(hasNotificationLoaded){

               $this.parent().toggleClass('open');

            }else{

                widget.ajax.get($this, null, null, {url : latest_url}, function (data, textStatus, jqXHR) {

                    hasNotificationLoaded = true;
                    func.addNotification(data);
                    $this.parent().toggleClass('open');

                }, function(jqXHR, textStatus, errorThrown){

                    widget.notify(jqXHR);

                }, function(jqXHR, textStatus, error){

                }, function(jqXHR, textStatus, error, hasError){



                });
            }

        })

        $('body, header a').click( function(event){
            if($notification.parent().hasClass('open')) {
                var $trigger = $notification;
                if ($trigger.get(0) !== event.target && !$trigger.has(event.target).length) {
                    $notification.parent().toggleClass('open')
                }

            }
        });

    }

    $(document).on('social-media-notification', function(event, data){

        if($notification.length > 0){
            if(data.count <= 0 || !data.view){
                return;
            }
            hasNotificationLoaded = true;
            hasNotificationBroadcastReceived = true;
            var $figure = $notification.find('.figure');
            func.addNotification(data.view);
            $figure.html(data.count).parent().removeClass('hide');
        }

    });

});