$(function() {

    $module = $('.member-workspace-index');

    $calendar = $module.find('.calendar');

    $property = $module.find('.property_select_list');

    $reservation = $module.find('.reservation');

    $lightbox = $module.find('.lightbox');

    $toggle = $module.find('.info-box .toggle a');

    $noAllowCancel = $module.find('.no-allow-cancel');

    $book = $module.find('.book');

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

    $lightbox.each(function(){
        $(this).simpleLightbox({
            sourceAttr: 'data-url',
            showCounter: false,
            nav: false,
            animationSlide: false,
            disableScroll: false
        });
    })

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


    $book.click(function(event){

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