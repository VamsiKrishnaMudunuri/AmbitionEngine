$(function() {

    var cls = {
      container: {
          cover: '.cover-gallery',
          profile: '.profile-gallery'

      },
      action : {
          addCover : '.add-cover',
          addProfile: '.add-profile',
          editPhoto : '.edit-photo',
          deletePhoto : '.delete-photo'
      }
    };

    $module = $('.admin-managing-gallery-index');
    $coverGallery = $module.find(cls.container.cover);
    $profileGallery = $module.find(cls.container.profile);
    $addCover = $module.find(cls.action.addCover);
    $addProfile = $module.find(cls.action.addProfile);

    if($coverGallery.data('write')) {
        widget.sortable($coverGallery);
    }

    if($profileGallery.data('write')){
        widget.sortable($profileGallery);
    }


    $addCover.click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                $coverGallery.append(data);

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });
    })

    $addProfile.click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                $profileGallery.append(data);

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });
    })

    $module.on('click', cls.action.editPhoto, function(e){

        event.preventDefault();

        var $a = $(this);

        var $li = $a.parents('li');

        var $disabled = [$li.find(cls.action.deletePhoto)];

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

    $module.on('click', cls.action.deletePhoto, function(e){

        event.preventDefault();

        var $a = $(this);

        var $li = $a.parents('li');

        var $disabled = [$li.find(cls.action.editPhoto)];

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