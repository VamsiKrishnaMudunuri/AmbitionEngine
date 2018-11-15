$(function () {


    widget.integerValueOnly();
    var today = new Date();

    widget.dateTimePicker({
        minDate: 0,
        pickerPosition: "top-left",
        todayBtn: true
    });

    var attr = {
        email: '.field-email',
        name: '.field-name'
    };

    function resetNumbering(event) {
        $(event.target).find('tr').each(function(index, element) {

            $(element).find(attr.name).attr('name', function () {
                return "guest_list[" + index + "][name]";
            });

            $(element).find(attr.email).attr('name', function () {
                return "guest_list[" + index + "][email]";
            });

        });
    }

    $('.guest-list').on('reset-numbering', resetNumbering);

    var i = 1;

    $('.add-guest').click(function(e){

        e.preventDefault();

        i = $('.guest-list tr').length + 1;

        if (i<6){
            $('.guest-list').append('<tr valign="top"><td><input type="text" class="form-control field-name" id="name" name="guest_list[]" value="" placeholder="Name" /><td><input type="text" class="form-control field-email" id="email" name="guest_list[]" value="" placeholder="Email" /> &nbsp;</td> &nbsp;</td><td><button type="button" class="btn btn-theme btn-block remove-guest">Remove</button> &nbsp;</td></tr>');
            i++;
            $('.guest-list').trigger('reset-numbering');
            return false;
        }

    });

    $(document).on('click', '.remove-guest', function () {
        $(this).parent().parent().remove();
        $('.guest-list').trigger('reset-numbering');
    });

});


