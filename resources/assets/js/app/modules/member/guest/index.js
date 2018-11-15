$(function () {


    $('.add-guest, .edit-guest').unbind().click(function (event) {

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true, function (data, textStatus, jqXHR) {

                $(this).remove();
                location.reload();
                return;

                var $modal = $(skin.modal.simple('', widget.json.toJson(data).message));

                $modal.modal('show');

                $modal.on('hidden.bs.modal', function (e) {
                    $(this).remove();
                    location.reload();
                })
            });

        }, function (jqXHR, textStatus, errorThrown) {

            widget.notify(jqXHR);

        }, function (jqXHR, textStatus, error) {

        }, function (jqXHR, textStatus, error, hasError) {

        });

    })


});


