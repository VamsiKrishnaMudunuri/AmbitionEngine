$(function() {

    var $module = $('.member-business-opportunity-suggestion');

    var $infinite = $module.find('.infinite');

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'feed-id', paging: $infinite.data('paging'), 'emptyText' : $infinite.data('empty-text'),  'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){
        $(document).trigger('social-media-business-opportunity-dashboard-infinite-loading', lastID)
     }});

});