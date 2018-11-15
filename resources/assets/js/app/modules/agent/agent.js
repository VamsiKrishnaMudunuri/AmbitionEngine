$(function() {

    var name = {
        agent: 'agent',
        layout_agent: 'layout-agent'
    };

    var cls = {
        agent: sprintf('.%s.%s', name.agent, name.layout_agent),
        copy_text: '.copy-text',
        refer_friend: '.refer-friend-referral'
    };

    var func = {
        copy_text: function ($element) {
            $element.click(function() {
                copyTextToClipboard($(this));
            });

        },
        refer_friend: function($element) {

            if ($element.length > 0) {

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);
                        // var $group = $this.parents(cls.group);
                        var url = $this.data('url');
                        var options = {
                            url: url
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){
                            //
                            //     var $data = $(data);
                            //     func.bind($data);
                            //
                            //     // $group.replaceWith($data);
                            //
                            });

                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){



                        });

                    })

                })
            }

        },
        bind: function ($element) {
            this.copy_text($element.find(cls.copy_text));
            // this.refer_friend($element.find(cls.refer_friend));
        }
    };

    $(cls.agent).each(function () {
        var $this = $(this);
        func.bind($this);
    });

});

function copyTextToClipboard(context, elem) {
    var copyText = elem ? context.find(elem) : context.closest('.input-group').find('.referral-link-input');
    copyText.select();
    document.execCommand("copy");
}

