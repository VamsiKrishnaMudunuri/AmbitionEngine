$(function() {

    var $module = $('.mix-board');

    var $toggle = $module.find('.toggle');
    var $feedContainer = $module.find('.feed-container');
    var $member = $module.find('.member-container.infinite-more');
    var $company = $module.find('.company-container.infinite-more');

    $toggle.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var $container = $this.data('target');

        $toggle.removeClass('active');
        $this.addClass('active');
        $feedContainer.hide();
        $('.' + $container).show();


    })

    $member.infinite_loading_more({url : $member.data('url'), 'id' : 'feed-id', 'is_paging_method' : true, paging: $member.data('paging'), 'loadingSkin': skin.loading.sm, 'emptyText' : $member.data('empty-text'), 'moreText' : $member.data('more-text'), 'endingText' : $member.data('ending-text'), 'complete' : function(response, feeds, lastID){

    }});

    $company.infinite_loading_more({url : $company.data('url'), 'id' : 'feed-id', 'is_paging_method' : true, paging: $company.data('paging'), 'loadingSkin': skin.loading.sm, 'emptyText' : $company.data('empty-text'), 'moreText' : $company.data('more-text'), 'endingText' : $company.data('ending-text'), 'complete' : function(response, feeds, lastID){

    }});

});