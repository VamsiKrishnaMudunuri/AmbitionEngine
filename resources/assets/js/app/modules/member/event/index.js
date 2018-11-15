$(function() {

    var $module = $('.member-event-index');

    var $infinite = $module.find('.infinite');

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'feed-id', 'is_paging_method' : true, paging: $infinite.data('paging'), 'emptyText' : $infinite.data('empty-text'),  'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){
        $(document).trigger('social-media-feed-infinite-loading', lastID)
     }});

});