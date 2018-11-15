$(function() {

    var $module = $('.member-feed-index');
    var $infinite = $module.find('.infinite');
    var $formSearch = $module.find('.form-search');
    var $formSearchContainer = $formSearch.find('.form-search-container');
    var $formSearchInputContainer = $formSearchContainer.find('.form-search-input-container');
    var $searchInput = $formSearchInputContainer.find('.form-search-input');
    var searchInputPlaceholder = $searchInput.data('placeholder')

    var tagInputHiddenData = [];
    var tagInputHiddenDataPrevCount = 0;

    $infinite.infinite_loading({url : $infinite.data('url'), 'id' : 'feed-id', paging: $infinite.data('paging'), 'emptyText' : $infinite.data('empty-text'),  'endingText' : $infinite.data('ending-text'), 'complete' : function(response, feeds, lastID){
        $(document).trigger('social-media-feed-infinite-loading', lastID)
     }});

    var $tagTextText = $searchInput.textext({
        tagsItems: $searchInput.data('query'),
        plugins: 'tags, prompt'
    }).bind('setFormData', function(e, data){

        var dataSize = data.length;

        if(dataSize != tagInputHiddenDataPrevCount){

            setTimeout(function(){
                var padding =  $searchInput.css('padding');
                $searchInput.prev('.tt-hint').css({
                    'padding' :  padding
                });
                tagInputHiddenDataPrevCount = dataSize;
            }, 0);


        }

    }).bind('isTagAllowed', function(e, data){

        var formData = $(e.target).textext()[0].tags()._formData,
            list = eval(formData);

        if (formData.length && list.indexOf(data.tag) >= 0) {

            data.result = false;

        }

    });

    $tagTextText.blur(function(e){
        if ($(this).val().trim() != ''){
            $(this).trigger('enterKeyPress').val('').blur();
            $searchInput.typeahead('val', '');
        }
    });


    var countries = $searchInput.data('location');

    var source = [];


    for(var c in countries){

        source.push(
            {
                name: 'countries',
                display: 'keyword',
                limit: 20,
                source: new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('keyword', 'other_keyword'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    local: countries[c]['state']['all_states']
                }).ttAdapter(),
                templates: {

                    notFound: '',
                    header: sprintf('<div class="tt-header"><div><span>%s</span></div></div>', countries[c]['state']['title']),
                    suggestion: function (item) {

                        var template = '';

                        template = sprintf('<div class="list-item"><a href="javascript:void(0);">%s</a></div>', item.keyword);


                        return template;

                    }

                }
            }
        )

        for(var s in countries[c]['state']['states']){

            if(countries[c]['state']['states'][s]['property']['properties'].length <= 0){
                continue;
            }

            source.push(
                {
                    name: 'offices',
                    display: 'keyword',
                    limit: 21,
                    source: new Bloodhound({
                        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('keyword', 'other_keyword'),
                        queryTokenizer: Bloodhound.tokenizers.whitespace,
                        local: countries[c]['state']['states'][s]['property']['properties']
                    }).ttAdapter(),
                    templates: {

                        notFound: '',
                        header: sprintf('<div class="tt-header"><div><span>%s</span></div></div>', countries[c]['state']['states'][s]['property']['title']),
                        suggestion: function (item) {

                            var template = '';

                            template = sprintf('<div class="list-item"><a href="javascript:void(0);">%s</a></div>', item.keyword);


                            return template;

                        }

                    }
                }
            )

        }

    }

    $searchInput.typeahead({highlight : true, hint : true, minLength : 1}, source)
        .on('typeahead:asyncrequest', function() {

            var loading = $searchInput.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length <= 0) {
                $searchInput.parents('.twitter-typeahead-container').append(skin.loading.sm);
            }

        })
        .on('typeahead:asynccancel typeahead:asyncreceive', function() {

            var loading = $searchInput.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length > 0) {
                loading.remove();
            }

        })
        .on('typeahead:select', function(event, item) {

            console.log(item);
            $tagTextText.textext()[0].tags().addTags([item.keyword]);
            $searchInput.typeahead('val', '');

        })

        .on('typeahead:change', function(event, item) {

        });

});