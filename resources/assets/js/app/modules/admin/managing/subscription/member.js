$(function() {

    var $module = $('.admin-managing-subscription-member');

    var $addMember = $module.find('.add-member');

    var $subscribers = $module.find('.toggle-subscriber');

    $addMember.click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                window.location.reload();

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){


        });
    })

    $subscribers.click(function(event){

        event.preventDefault();

        var $this = $(this);

        var url = $this.data('url');
        var status = $this.prop('checked');

        widget.ajax.post($this, $subscribers, null, {'url' : url}, function(data, textStatus, jqXHR){

            window.location.reload();

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, isError){

            if(isError){

                if(status){
                    $this.prop('checked', false);
                }else{
                    $this.prop('checked', true);
                }

            }

        });

    })

});