var $jobContainer = $('.page-jobs');
var findOutMoreBtn = $jobContainer.find('.find-out-more');
var jobPopup = $jobContainer.find('.job-popup');
var closeBtn = jobPopup.find('.close-btn');

findOutMoreBtn.click('click', function(elem) {

    event.preventDefault();

    var $a = $(this);
    var url = $a.data('url');
    var options = {
        url: url
    };

    widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

        var jobWrapper = jobPopup.find('.job-wrapper');
        jobWrapper.html(data);
        jobPopup.removeClass('hide');

    }, function(jqXHR, textStatus, errorThrown){
        widget.notify(jqXHR);

    }, function(jqXHR, textStatus, error){

    }, function(jqXHR, textStatus, error, hasError){

    });
});

closeBtn.click(function(e) {
    e.stopPropagation();
    e.preventDefault();

    jobPopup.addClass('hide');
});



$module = $('.post-job-appointment');

$module.on('click', '.input-submit', function(event){

    event.preventDefault();

    var $submit = $(this);
    var $form = $submit.closest('form');
    var $messageBox = $form.find('.message-box');
    var $success = $messageBox;
    var $error = $messageBox;

    var options = {
        url: $form.attr('action'),
        data: $form.serialize()
    };

    widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {

        if ($submit.data('shouldRedirect')) {
            location.href = $submit.data('shouldRedirect');
        } else {
            $success.after($success.after($(skin.alert.success(data))));
        }

    }, function(jqXHR, textStatus, errorThrown){

    });
});
