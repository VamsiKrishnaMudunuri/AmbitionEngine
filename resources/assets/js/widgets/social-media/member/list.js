$(function() {

    var $module = $('.social-member.list');
    var $infinite = $module;
    var cls = {
        'removeMember': '.remove-member'
    };

    $infinite.infinite_loading_more({url : $infinite.data('url'), 'id' : 'member-id', paging: $infinite.data('paging'), 'moreText' : $infinite.data('more-text'), 'emptyText' : $infinite.data('empty-text'), 'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){

    }});

    $module.on('click', cls.removeMember, function(event) {

        event.preventDefault();

        var $this = $(this);

        var $a = $(this);

        var url = $a.data('url');

        var confirmMessage = $this.data('confirm-message');

        var options = {
            url: url,
            data: {_method: 'delete'},
            dataType : 'json'
        };

        if (confirm(confirmMessage)) {
            widget.ajax.post($a, null, null, options, function (data, textStatus, jqXHR) {

                if (data.status === 'success') {
                    if (data.count === 0) {
                        $this.closest('.social-member.list').html('<div class="infinite-item empty">Not have member</div>');
                    } else {
                        $this.closest('.section').parent().remove();
                    }
                }

            }, function(jqXHR, textStatus, errorThrown){

                widget.notify(jqXHR);

            }, function(jqXHR, textStatus, error){

            }, function(jqXHR, textStatus, error, hasError){


            });
        }
    });

});