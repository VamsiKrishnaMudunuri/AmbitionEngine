$(function() {

    var $body =  $('body');
    var $searchContainer = $body.find('.navbar-menu .navbar-form.search-form');
    var $search = $body.find('.navbar-menu .navbar-form.search-form .smart-search-input');
    var $searchIconContainer = $body.find('.navbar-menu .navbar-form.search-form .search-container');
    var $searchIcon = $body.find('.navbar-menu .navbar-form.search-form .search-container .fa-search');

    var memberSearchUrl  =  $search.data('member-search-url');
    var companySearchUrl  =  $search.data('company-search-url');
    var apiSearchMemberUrl = $search.data('api-search-member-url');
    var apiSearchCompanyUrl = $search.data('api-search-company-url');
    var text = $search.data('text');


    if($searchContainer.length <= 0){
        return;
    }

    var membersDataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s', apiSearchMemberUrl, '%QUERY'),
            wildcard: '%QUERY'

        }
    });

    var companiesDataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s', apiSearchCompanyUrl, '%QUERY'),
            wildcard: '%QUERY'
        }
    });

    var memberHeaderSkin =  sprintf('<div class="tt-header"><div><span>%s</span><a href="javascript:void(0);" data-url="%s">%s</a></div></div>', text.members, memberSearchUrl, text.show);

    var companyHeaderSkin =  sprintf('<div class="tt-header"><div><span>%s</span><a href="javascript:void(0);" data-url="%s">%s</a></div></div>', text.companies, companySearchUrl, text.show);

    $searchIconContainer.click(function(e){

        e.preventDefault();

        var $this = $(this);
        var current_search_url = $this.data('url');

        var loading = $search.parents('.twitter-typeahead-container').find('.fa-loading');

        widget.hide($searchIcon);
        if(loading.length <= 0) {
            $search.parents('.twitter-typeahead-container').append(skin.loading.sm);
        }

        if( $search.val() ){
            current_search_url += '?requery=' + $search.val();
        }
        window.location.href = current_search_url;

    })

    $searchContainer.on('click', '.tt-header a', function(e){

        e.preventDefault();

        var url = $(this).data('url');
        if( $search.val() ){
            url += '?requery=' + $search.val();
        }

        window.location.href = url;

    })

    $search.typeahead({highlight : true, hint : true, minLength : 1}, {
        name: 'members',
        display: 'name',
        limit: 41,
        source: membersDataSource,
        templates : {

           notFound: sprintf('%s<div class="empty">%s</div>', memberHeaderSkin, text.empty),
           header: memberHeaderSkin,
           suggestion: function(item){
               return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details member-skin"><div class="name">%s</div><div class="username">%s</div></div></a></div>', item.avatar, item.name, item.username_alias);
           }

        }
    }, {
        name: 'companies',
        display: 'name',
        limit: 20,
        source: companiesDataSource,
        templates : {
            notFound: sprintf('%s<div class="empty">%s</div>', companyHeaderSkin, text.empty),
            header: companyHeaderSkin,
            suggestion: function(item){
                return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details company-skin"><div class="name">%s</div><div class="headline">%s</div></div></a></div>', item.avatar, item.name, item.headline);
            }

        }
    })
    .on('typeahead:asyncrequest', function() {

        var loading = $search.parents('.twitter-typeahead-container').find('.fa-loading');
        widget.hide($searchIcon);
        if(loading.length <= 0) {
            $search.parents('.twitter-typeahead-container').append(skin.loading.sm);
        }

    })
    .on('typeahead:asynccancel typeahead:asyncreceive', function() {

        var loading = $search.parents('.twitter-typeahead-container').find('.fa-loading');
        widget.show($searchIcon);
        if(loading.length > 0) {
            loading.remove();
        }

    })
    .on('typeahead:select', function(event, item) {

        if(item['url']) {
            window.location = item['url'];
        }

    })
    .on('typeahead:change', function(event, item) {

    });


});