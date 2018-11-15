$(function() {

    var name = {
        'businessOpportunity': 'social-business-opportunity',
        'dashboard' : 'dashboard',
    };

    var cls = {
        'container' : '.business-opportunity-container',
        'businessOpportunity': sprintf('.%s.%s', name.businessOpportunity, name.dashboard),
        'add' : '.add-business-opportunity',
        'delete' : '.delete',
        'edit' : '.edit'
    };

    var func = {
        add: function($element){

            var $template = $element;

            if($template.hasClass(sprintf('%s %s', name.businessOpportunity, name.dashboard))) {

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
                        var $businessOpportunity = $this.parents(cls.businessOpportunity);
                        var url = $this.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                                var $data = $(data);
                                func.bind($data);
                                $businessOpportunity.replaceWith($data);

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

                                    $template = $this.parents(cls.businessOpportunity);
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
        bind: function($element){
            this.edit($element.find(cls.edit));
            this.del($element.find(cls.delete));
        }
    };

    $(cls.businessOpportunity).each(function(){

        var $this = $(this);

        func.bind($this);

    });

    $(cls.add).click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                func.add($(data));

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){


        });

    })


    $(document).on('social-media-business-opportunity-dashboard-infinite-loading', function(event, lastID){

        var $feeds = $(cls.container).find(sprintf('%s[data-feed-id="%s"]',  cls.businessOpportunity, lastID)).nextAll(cls.businessOpportunity);

        func.bind($feeds);

    });

});