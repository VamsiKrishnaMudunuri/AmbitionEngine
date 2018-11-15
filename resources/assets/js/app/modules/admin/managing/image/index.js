$(function() {

    var cls = {
      container: {
          gallery: '.gallery',
      },
      action : {
          add : '.add-photo',
          edit : '.edit-photo',
          delete : '.delete-photo'
      }
    };

    $module = $('.admin-managing-image-index');
    $gallery = $module.find(cls.container.gallery);
    $add = $module.find(cls.action.add);

    $add.click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                $gallery.append(data);

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });
    })

    $module.on('click', cls.action.edit, function(e){

        event.preventDefault();

        var $a = $(this);

        var $li = $a.parents('li');

        var $disabled = [$li.find(cls.action.delete)];

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, $disabled, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                $li.replaceWith(data);

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });

    });

    $module.on('click', cls.action.delete, function(e){

        event.preventDefault();

        var $a = $(this);

        var $li = $a.parents('li');

        var $disabled = [$li.find(cls.action.edit)];

        var confirmMessage = $a.data('confirm-message');
        var url = $a.data('url');

        var options = {
            url: url,
            data: {_method: 'delete'}
        };

        if(confirm(confirmMessage)) {

            widget.ajax.post($a, $disabled, null, options, function (data, textStatus, jqXHR) {

                $li.remove();

            }, function (jqXHR, textStatus, errorThrown) {

                widget.notify(jqXHR);

            }, function (jqXHR, textStatus, error) {

            }, function (jqXHR, textStatus, error, hasError) {


            });

        }

    });

});