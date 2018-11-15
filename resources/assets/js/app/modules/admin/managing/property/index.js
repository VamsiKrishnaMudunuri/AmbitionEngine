$(function() {

    var $module = $('.admin-managing-property-index');


    var cls = {
        item: '.item',
        add : '.add',
        edit : '.edit',
        invite : '.invite',
        approve : '.approve',
        disapprove : '.disapprove',
        delete: '.delete',
        show: '.toggle-show',
        view : '.view-detail',
        empty : '.empty',
        event: '.section-board .event',
        eventPending: '.section-board .event-pending',
        group: '.section-board .group',
        guest: '.section-board .guest'
    };

    var func = {
        edit: function($element){
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

                                var id = $a.data('id');
                                var $item = $a.parents(sprintf('%s[data-id="%s"]', cls.item, id));
                                var $data = $(data);
                                func.bind($data);
                                $item.replaceWith($data);

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
        approve: function($element){
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


                        widget.ajax.post($a, null, null, options, function (data, textStatus, jqXHR) {


                            widget.hide($a.parent('li'));
                            widget.show($a.parent('li').next('li'));

                        }, function (jqXHR, textStatus, errorThrown) {

                            widget.notify(jqXHR);

                        }, function (jqXHR, textStatus, error) {

                        }, function (jqXHR, textStatus, error, hasError) {


                        });

                    })

                })
            }
        },
        disapprove: function($element){
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


                        widget.ajax.post($a, null, null, options, function (data, textStatus, jqXHR) {


                            widget.show($a.parent('li').prev('li'));
                            widget.hide($a.parent('li'));

                        }, function (jqXHR, textStatus, errorThrown) {

                            widget.notify(jqXHR);

                        }, function (jqXHR, textStatus, error) {

                        }, function (jqXHR, textStatus, error, hasError) {


                        });

                    })

                })
            }
        },
        delete: function($element){
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

                                var id = $this.data('id');
                                var $item = $this.parents(sprintf('%s[data-id="%s"]', cls.item, id));

                                $item.remove();

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
            this.invite($element.find(cls.invite));
            this.approve($element.find(cls.approve));
            this.disapprove($element.find(cls.disapprove));
            this.delete($element.find(cls.delete));
        }
    };

    $module.find(cls.show).click(function(e){

        e.preventDefault();
        var $a = $(this);
        var $i = $a.children('i.fa');
        var $class = $($a.data('toggle'));

        $a.attr('disabled', 'disabled');

        if($class.length > 0) {
            $class.slideToggle('fast', function () {


                var $this = $(this);

                if ($this.is(':visible')) {
                    $i.toggleClass('fa-plus fa-minus');
                } else {
                    $i.toggleClass('fa-minus fa-plus');
                }

                $a.removeAttr('disabled');


            });
        }

    })

    $module.on('click', cls.view, function(e){

        e.preventDefault();

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

    $module.find(cls.add).click(function(e){

        e.preventDefault();

        var $a = $(this);

        var $container =  $($a.data('container'));

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){


                var $data = $(data);
                func.bind($data);
                $container.prepend($data);

                var $empty = $container.prev(cls.empty);
                if($empty.length > 0){
                    $empty.remove();
                }


            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){


        });



    })

    $(cls.event).each(function(){
        var $this = $(this);
        func.bind($this);
    })

    $(cls.eventPending).each(function(){
        var $this = $(this);
        func.bind($this);
    })

    $(cls.group).each(function(){
        var $this = $(this);
        func.bind($this);
    })

    $(cls.guest).each(function(){
        var $this = $(this);
        func.bind($this);
    })

    var ctx = document.getElementById('occupancy-chart').getContext('2d');

    var occupancy = $('#occupancy-chart');
    var stats =  occupancy.data('stats');
    var ctx = occupancy.get(0).getContext('2d');

    var chart = new Chart(ctx, {
        // The type of chart we want to create
        type: 'line',

        // The data for our dataset
        data: stats,

        options: {
            responsive: true,
            legend: {
                labels: {
                    fontColor: '#333333',
                    fontSize: 16
                }
            },
            title:{
                display:true,
                text:''
            },
            tooltips: {
                enabled: true,
                mode: 'single',
                callbacks: {
                    label: function(tooltipItems, data) {
                        return sprintf(' %s/%s', tooltipItems.yLabel, data['datasets'][tooltipItems.datasetIndex]['percentage'][tooltipItems.index]) + '%';
                    }
                }
            },

        }
    });

});