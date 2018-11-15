$(function() {

    var name = {
        'event': 'social-event',
        'eventList' : 'event-list',
    };

    var cls = {
        'container' : '.group-container',
        'event': sprintf('.%s.%s', name.event, name.eventList),
        'add' : '.add-event',
        'delete' : '.delete',
        'edit' : '.edit',
        'invite' : '.invite',
        'seeAllMembers' : '.see-all-members'
    };

    var func = {
        add: function($element){

            var $template = $element;

            if($template.hasClass(sprintf('%s %s', name.event, name.dashboard))) {

                $(cls.container).prepend($template);

                _.defer(function () {

                    widget.animate($template, 'zoomIn', function($this){

                        func.bind($this);

                    })

                });


            }
        },
        edit: function($element){

            if($element.length > 0){
                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);
                        var $event = $this.parents(cls.event);
                        var url = $this.data('url');
                        var options = {
                            url: url
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                                if ($this.data('isRefresh')) {
                                    widget.notify(jqXHR, widget.json.toJson(data).message);
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }

                                var $data = $(data);
                                func.bind($data);

                                $event.replaceWith($data);

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
        invite: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){




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
        del: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);

                        var confirmMessage = $this.data('confirm-message');

                        var url = $this.data('url');

                        var options = {
                            url: url,
                            data: {_method: 'delete'}
                        };

                        if(confirm(confirmMessage)) {

                            widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {

                                _.defer(function () {

                                    $template = $this.parents(cls.event);
                                    widget.animate($template, 'zoomOut', function ($this) {
                                        $this.remove();
                                    })

                                });

                            }, function (jqXHR, textStatus, errorThrown) {

                                widget.notify(jqXHR);

                            }, function (jqXHR, textStatus, error) {

                            }, function (jqXHR, textStatus, error, hasError) {


                            });

                        }

                    })

                })
            }

        },
        seeAllMember: function($element){
            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function(event){

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {


                            var $modal = $(data);
                            $modal.modal('show');

                            $modal.on('hidden.bs.modal', function(e){
                                $(this).remove();
                            })



                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){


                        });

                    })


                })

            }

        },
        bind: function($element){
            this.edit($element.find(cls.edit));
            this.invite($element.find(cls.invite));
            this.del($element.find(cls.delete));
            this.seeAllMember($element.find(cls.seeAllMembers));
        }
    };

    $(cls.event).each(function() {
        var $this = $(this);
        func.bind($this);
    });

    $(cls.add).click(function(event){

        event.preventDefault();

        var $this = $(this);
        var $a = $(this);
        var url = $a.data('url');
        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                var $modal = $(skin.modal.simple('', widget.json.toJson(data).message));

                $modal.modal('show');

                if ($this.data('isRefresh')) {
                    setTimeout(function() {
                        window.location.reload();
                    }, 3000);
                }

                $modal.on('hidden.bs.modal', function(e){
                    $(this).remove();
                })

            });

        }, function(jqXHR, textStatus, errorThrown){
            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){

        });

    })

    widget.bsToggle();
});