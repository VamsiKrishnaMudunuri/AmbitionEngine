$(function() {

    var cls = {
        container: {
            followers: '.followers-info'
        },
        text: {
            followingInfo : '.following-info',
            followerInfo : '.follower-info',
            figure : '.figure',
            text : '.text',

        },
        action : {
            follow : '.follow',
            following: '.following',
        }
    };

    var func = {
        updateInfo : function($link, stats){

            var $from = $($link.data('source-info'))
            var $to = $($link.data('target-info'))

            var obj = {'from' : $from, 'to' : $to};

            for(var o in obj){
                if(obj[o].length > 0){

                    var $element = obj[o];
                    var statsData = stats.stats[o];

                    $followingInfo = $element.find(cls.text.followingInfo);
                    $followingFigure = $followingInfo.find(cls.text.figure);
                    $followingText = $followingInfo.find(cls.text.text);
                    $followerInfo = $element.find(cls.text.followerInfo);
                    $followerFigure = $followerInfo.find(cls.text.figure);
                    $followerText = $followerInfo.find(cls.text.text);

                    $followingInfo.attr('title', statsData.followings_full_text);
                    $followingFigure.html(statsData.followings)
                    $followingText.html(statsData.followings_short_text);

                    $followerInfo.attr('title', statsData.followers_full_text);
                    $followerFigure.html(statsData.followers)
                    $followerText.html(statsData.followers_short_text);

                }
            }


        }
    }

    var $body =  $('body');

    $body.on('click', cls.action.follow, function(e){
        e.preventDefault();

        var $a = $(this);
        var $following = $a.next(cls.action.following);
        var url = $a.data('url');

        var options = {
            url: url,
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            }
        };

        widget.ajax.post($a, null, null, options, function(data, textStatus, jqXHR) {

            $a.toggleClass('hide');
            $following.toggleClass('hide');
            func.updateInfo($a, data);

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        });
    })

    $body.on('click', cls.action.following, function(e){

        e.preventDefault();

        var $a = $(this);
        var $follow = $a.prev(cls.action.follow);
        var url = $a.data('url');

        var options = {
            url: url,
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            }
        };

        widget.ajax.post($a, null, null, options, function(data, textStatus, jqXHR) {

            $a.toggleClass('hide');
            $follow.toggleClass('hide');
            func.updateInfo($a, data);

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        });
    })


});