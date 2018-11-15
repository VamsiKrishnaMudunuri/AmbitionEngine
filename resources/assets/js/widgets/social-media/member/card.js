$(function() {

    var $module = $('.social-member.card');


    var $infinite = $module;

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'member-id', paging: $infinite.data('paging'), 'is_slice_paging' : $infinite.data('is-slice-paging'), 'emptyText' : $infinite.data('empty-text'), 'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){

    }});


});