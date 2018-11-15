$(function() {

    var $module = $('.social-company.card');


    var $infinite = $module;

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'company-id', paging: $infinite.data('paging'), 'emptyText' : $infinite.data('empty-text'), 'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){

    }});


});