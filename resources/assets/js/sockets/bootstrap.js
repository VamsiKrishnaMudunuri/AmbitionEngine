import Echo from "laravel-echo"

$(function() {

    try {

        window.Echo = new Echo({
            broadcaster: 'socket.io',
            host: app.socket_url
        });


        window.Echo.connector.socket.on('connect', function(){

            $.cookie('X-Socket-Id', window.Echo.socketId(), { expires : 365 , path : '/' });

            window.Echo.join(app.socket_online_channel)
                .here((users) => {
                   //console.log("here");
                })
                .joining((user) => {
                    //console.log("joining");
                })
                .leaving((user) => {
                    //console.log("leaving");
                });

            window.Echo.private('new-comment')
                .listen('NewCommentEvent', (e) => {
                    $(document).trigger('social-media-comment-new', {'post_id' : e.post_id, 'comment_id' : e.comment_id, 'view' : e.view});
                });

            window.Echo.private('new-feed-notification')
                .listen('NewFeedNotificationEvent', (e) => {
                    $(document).trigger('social-media-feed-new-notification', {'post_id' : e.id, 'type' : e.type, 'group_id' : e.group_id});
                });

            window.Echo.private(sprintf('new-notification-%s', window.Echo.socketId()))
                .listen('NewNotificationEvent', (e) => {
                    $(document).trigger('social-media-notification', {'count' : e.count, 'view' : e.view});
                });


            window.Echo.connector.socket.on('disconnect', function(){



            })

        })


    }catch(err){

        jQuery.ajaxSetup({
            beforeSend: (xhr) => {

            }
        });

        console.log(err);
    }

});