$(function() {

    $(document).on('click', '.social-button', function (e) {

        e.preventDefault();

        widget.popup.open($(this).prop('href'));


    });
});