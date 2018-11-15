$(function() {

    var $module = $('.member-notification-index');

    var $infinite = $module.find('.infinite');

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'notification-id', paging: $infinite.data('paging'), 'emptyText' : $infinite.data('empty-text'), 'endingText' : $infinite.data('ending-text'), 'complete' : function(response, lastID){} });

});