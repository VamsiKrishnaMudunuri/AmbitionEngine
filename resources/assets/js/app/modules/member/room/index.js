$(function() {

    $module = $('.member-room-index');

    $calendar = $module.find('.calendar');

    $property = $module.find('.property_select_list');

    $reservation = $module.find('.reservation');


    $toggle = $module.find('.info-box .toggle a');

    $noAllowCancel = $module.find('.no-allow-cancel');

    $room = $module.find('.room');

    var url = $reservation.data('url');
    var today = new Date();
    var startDate = $reservation.data('start-date') ? $reservation.data('start-date') : today ;

    var func = {
        getDateStringFromDatetimepicker: function(){

            var date = $datetimepicker.datetimepicker('getDate');
            var dateString = sprintf('%s-%s-%s', date.getFullYear(), date.getMonth() + 1, date.getDate());

            return dateString;

        },

        submit: function(){

            var property_id = $property.val();
            var dateString = this.getDateStringFromDatetimepicker();
            if(property_id){
                window.location.href = sprintf('%s/%s/%s', url, property_id, dateString);
            }
        }

    };

    $datetimepicker = widget.dateTimePicker({
        'format': app.datetime.date.format,
        'minView' : 2,
        'autoclose' : false,
        'startDate' : today,
        initialDate: startDate
    }, $calendar);

    $datetimepicker.on('changeDate', function(ev){
        func.submit();
    });

    $property.change(function(event){

        event.preventDefault();

        func.submit();

    })

    $toggle.click(function(event){

        event.preventDefault();
        $this = $(this);
        $content = $this.parent().next('.content');
        $content.toggle('fast', function(){

            if($content.is(':visible')){
                $this.html($this.data('hide'));
            }else{
                $this.html($this.data('show'));
            }
        });

    })

    $room.each(function(event){
        $this = $(this);

        $this.timeSchedule(

            _.extend({
                startTime: $this.data('start-time'),
                endTime: $this.data('end-time'),
                headTimeBorder: 0,
                timeBorder: 0,
                dataWidth: 105,
                widthTimeX: 28,
                timeLineY: 100,
                widthTime: $this.data('second'),
                rows: $this.data('row'),
                change: function(node,data){

                },
                init_data: function(node,data){
                },
                click: function(node,data){

                },
                append: function(node,data){

                },
                time_click: function(element, time, timeline, data){

                    $this = $(element);

                    var options = {
                        url: sprintf('%s?start_date=%s %s:00', data.url, func.getDateStringFromDatetimepicker(), time),
                        beforeSend: function(jqXHR, settings){
                            $this.siblings().not('.off').addClass('disabled');
                            $this.addClass('disabled loading');
                        }
                    };

                    widget.ajax.get(null, null, null, options, function (data, textStatus, jqXHR) {

                        widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                            window.location.reload();

                        });

                    }, function(jqXHR, textStatus, errorThrown){

                        widget.notify(jqXHR);

                    }, function(jqXHR, textStatus, error){

                        $this.siblings().not('.off').removeClass('disabled');
                        $this.removeClass('disabled loading');


                    }, function(jqXHR, textStatus, error, hasError){



                    });
                },
            }, $this.data('option'))

        );

    })

    $noAllowCancel.click(function(event) {

        event.preventDefault();

        $this = $(this);
        var message = $this.data('message');

        $.notify(message, {
            type: 'warning',
            delay: 8000
        });

    });

});