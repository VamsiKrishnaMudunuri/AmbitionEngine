$(function() {

    var cls = {
        action : {
            join : '.join',
            leave: '.leave',
        }
    };

    var $body =  $('body');

    $body.on('click', cls.action.join, function(e){
        e.preventDefault();

        var $a = $(this);
        var id =  $a.data('id');
        var $leave = $a.next(cls.action.leave);
        var url = $a.data('url');

        var options = {
            url: url,
            dataType: 'json',
            xhrFields: {
                withCredentials: true
            }
        };

        widget.ajax.post($a, null, null, options, function(data, textStatus, jqXHR) {

            vertex.stats(1, id, data);
            $a.toggleClass('hide');
            $leave.toggleClass('hide');


        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        });
    })

    $body.on('click', cls.action.leave, function(e){

        e.preventDefault();

        var $a = $(this);
        var id =  $a.data('id');
        var $join = $a.prev(cls.action.join);
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
            $join.toggleClass('hide');
            vertex.stats(0, id, data);


        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        });
    })


});