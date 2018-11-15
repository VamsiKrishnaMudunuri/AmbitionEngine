$(function() {

    var $module = $('.member-group-group');

    var $infiniteGroupFeed = $module.find('.feed-container.infinite');

    var $infiniteMoreEvent = $module.find('.event-container').find('.listing-container.infinite-more');

    var $addNewEvent = $module.find('.add-new-group-event');

    var cls = {
        'eventContainer' : '.event-container',
        'event' : '.item',
        'editEvent': '.edit-group-event',
        'deleteEvent': '.delete-group-event'
    };

    var func = {

        editEvent: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var $event = $this.parents(cls.event);

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                                var $data = $(data);
                                $event.replaceWith($data);
                                func.bindAllForEvent($data);


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

        delEvent: function($element){

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

        bindAllForEvent:function($element){
            this.editEvent($element.find(cls.editEvent));
            this.delEvent($element.find(cls.deleteEvent));

        },


    };

    $(cls.event).each(function(){

        var $this = $(this);

        func.bindAllForEvent($this);

    });

    $infiniteGroupFeed.infinite_loading({url : $infiniteGroupFeed.data('url'), 'id' : 'feed-id', paging: $infiniteGroupFeed.data('paging'), 'emptyText' : $infiniteGroupFeed.data('empty-text'),  'endingText' : $infiniteGroupFeed.data('ending-text'), 'complete' : function(response, feeds, lastID){
        $(document).trigger('social-media-feed-infinite-loading', lastID)
    }});

    $infiniteMoreEvent.infinite_loading_more({url : $infiniteMoreEvent.data('url'), 'id' : 'feed-id', 'is_paging_method' : true, paging: $infiniteMoreEvent.data('paging'), 'emptyText' : $infiniteMoreEvent.data('empty-text'), 'moreText' : $infiniteMoreEvent.data('more-text'),  'endingText' : $infiniteMoreEvent.data('ending-text'), 'complete' : function(response, feeds, lastID){
        $(document).trigger('social-media-event-infinite-more', lastID)
    }});

    $module.find('.member-container .see-all-members').click(function(event) {

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {


            var $modal = $(data);
            $modal.modal('show');

            $modal.on('hidden.bs.modal', function (e) {
                $(this).remove();
            })


        }, function (jqXHR, textStatus, errorThrown) {

            widget.notify(jqXHR);

        }, function (jqXHR, textStatus, error) {

        }, function (jqXHR, textStatus, error, hasError) {


        });
    });

    $addNewEvent.click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                var $modal = $(skin.modal.simple('', widget.json.toJson(data).message));

                $modal.modal('show');

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

    $(document).on('social-media-event-infinite-more', function(event, lastID){

        var $feeds = $(cls.eventContainer).find(sprintf('%s[data-feed-id="%s"]',  cls.event, lastID)).nextAll(cls.feed);

        func.bindAllForEvent($feeds);


    });

});