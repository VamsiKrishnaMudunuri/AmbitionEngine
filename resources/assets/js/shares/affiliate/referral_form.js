var $container = $('.package-affiliate-container');
var $btnAddMorePackage = $container.find('.addMorePackage');
var $packageAffiliateListing = $('.package-affiliate-listing');
var $referralForm = $('.referral-form');

var $removeBtnTemplate = '<div class="form-group"><label class="control-label">&nbsp;</label><br/><button class="btn btn-theme btn-remove-package"><i class="fa fa-trash"></i></button></div>';

function resetCounter() {
    var allClonedElements = $packageAffiliateListing.find('.toClone');

    allClonedElements.each(function(index, elem) {
        var inputs = $(elem).find(':input:not("button")');

        inputs.each(function(i, e) {

            var reference = $(e).data('reference').split('-');

            $(e).attr('name', function(latest, old) {
                return reference[0] + '[' + index + '][' + reference[1] + ']';
            });
            $(e).attr('id', function(latest, old) {
                return reference[0] + '[' + index + '][' + reference[1] + ']';
            })
        });

    });
}

$btnAddMorePackage.click(function() {

    var allClonedElements = $packageAffiliateListing.find('.toClone');
    var $parentContainer = $(this).closest('.package-affiliate-container');
    var $firstElement = $parentContainer.find('.toClone').first();

    // Check for limit
    if (allClonedElements.length === 3) return;

    if ($firstElement.length) {
        var $clonedElement = $firstElement.clone();

        $clonedElement.find('.btnContainer').html($removeBtnTemplate);
        $clonedElement.find(':input').val('');
        $clonedElement.find('.alert.alert-danger').remove();
        $parentContainer.find('.package-affiliate-listing').append($clonedElement);
    }

    resetCounter();
});

$packageAffiliateListing.on('click', '.btn-remove-package', function(e) {
    e.preventDefault();
    $(this).closest('.toClone').remove();
    resetCounter();
});

$referralForm.on('click', '.input-submit-referral', function(event){

    event.preventDefault();

    var $module = $referralForm;
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

        // if ($submit.data('shouldRedirect')) {
        //     location.href = $submit.data('shouldRedirect');
        // } else {
        //     $success.after($(skin.alert.success(data)));
        // }

    }, function(jqXHR, textStatus, errorThrown){

    });

});