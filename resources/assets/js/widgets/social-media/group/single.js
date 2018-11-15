$(function() {

    var name = {
        'group': 'social-group',
        'dashboard' : 'single',
    };

    var cls = {
        'group': sprintf('.%s.%s', name.group, name.dashboard),
        'delete' : '.delete',
        'edit' : '.edit',
        'invite' : '.invite',
        'seeAllMembers' : '.see-all-members'
    };

    var func = {

        edit: function($element){

            if($element.length > 0){
                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);
                        var $group = $this.parents(cls.group);
                        var url = $this.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                                var $data = $(data);
                                func.bind($data);
                                $group.replaceWith($data);

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

                                data = widget.json.toJson(data);
                                window.location.href = data.url;

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

    $(cls.group).each(function(){

        var $this = $(this);

        func.bind($this);

    });


});