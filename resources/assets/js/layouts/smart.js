$(function() {

    $.fn.modal.Constructor.DEFAULTS.backdrop = 'static';
    $.fn.modal.Constructor.DEFAULTS.keyword = false;

    widget.offcanvas($('.navbar-toggle'));

    widget.datePicker();

    widget.dateTimePicker();

    widget.inputFile();

    widget.activatePopup();

    widget.alphanumericOnly();

    widget.integerValueOnly();

    widget.priceValueOnly();

    widget.coordinateValueOnly();

    widget.helpBox();

    widget.formSearch();

});