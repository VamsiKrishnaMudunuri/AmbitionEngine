$(function() {

    $form = $('.facility-item-form');
    $submit = $form.find('.submit');
    $businessHoursInput = $form.find('.business-hours-input');
    $businessHoursContainer = $form.find('.business-hours');

    var inputExistingVal = JSON.parse($businessHoursInput.val());

    var businessHoursOptions = {
        postInit: function () {
            $('.operationTimeFrom, .operationTimeTill').timepicker({
                'timeFormat': app.timepicker.time.format,
                'step': $businessHoursInput.data('minutes')
            });
        },
        dayTmpl: skin.businessHours
    };



    if(inputExistingVal.length > 0){
        businessHoursOptions['operationTime'] =  inputExistingVal;
    }

    $businessHours = $businessHoursContainer.businessHours(businessHoursOptions);

    $form.submit(function(event){
        $businessHoursInput.val(JSON.stringify($businessHours.serialize()));
    })

});