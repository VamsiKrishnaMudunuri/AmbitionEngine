(function ($, skin, _) {

    var InfiniteLoading = function(options){

        var $target;
        var $win = $(window);
        var $doc = $(document);

        var isLoading = false;
        var isEnding = false;
        var lastScrollPosition = 0;
        var scrollDirection = 0;

        var pagingLastID;
        var elementLastID;

        var defaults = {
            url: '',
            id : 'id',
            ads : '.feed-advertisement,.feed-recommendation',
            threshold : 300,
            is_paging_method : false,
            paging_no_id : 'page-no',
            paging_no : 1,
            paging: 20,
            is_slice_paging: false,
            emptyText: '',
            endingText: '',
            loadingSkin: sprintf('<div class="infinite-item loading">%s</div>', skin.loading.md),
            emptySkin: '<div class="infinite-item empty"></div>',
            endingSkin: '<div class="infinite-item ending"></div>',
            complete: function(response, lastID){

            }

        };

        defaults = $.extend({}, defaults, options);

        function init(element){

            $target = $(element);

            var $allFeeds = $target.find('[data-' + defaults.id +  ']');
            var $feeds = $allFeeds.not(defaults.ads);
            pagingLastID = $feeds.last().data(defaults.id);
            elementLastID = $allFeeds.last().data(defaults.id);

            if($allFeeds.length <= 0 && defaults.emptyText){
                var $emptySkin = $(defaults.emptySkin);
                $emptySkin.html(defaults.emptyText);
                $target.append($emptySkin);
                isEnding = true;
            }else if($feeds.length <= defaults.paging){
                var $endingSkin = $(defaults.endingSkin);
                $endingSkin.html(defaults.endingText);
                $target.append($endingSkin);
                isEnding = true;
            }

            $win.bind('scroll', onScrolling);
        }


        function onScrolling(e){

            var currentScrollPosition = $win.scrollTop();

            if(currentScrollPosition > lastScrollPosition){
                scrollDirection = 1;
            }else{
                scrollDirection = 0;
            }

            lastScrollPosition = currentScrollPosition;

            if (isScrollToDownDirection() && lastScrollPosition >= ($doc.height() - $win.height() - defaults.threshold)) {

                if(!isLoading && !isEnding){

                    var $loadingSkin = $(defaults.loadingSkin);
                    var $endingSkin = $(defaults.endingSkin);

                    isLoading = true;

                    $endingSkin.html(defaults.endingText);

                    var data = {};
                    data[defaults.id] = pagingLastID;

                    if(defaults.is_paging_method){

                        data[defaults.paging_no_id] = defaults.paging_no + 1;

                    }

                    var _default = {
                        url: defaults.url,
                        type: 'GET',
                        data: data,
                        dataType: 'html',
                        cache: true,
                        xhrFields: {
                            withCredentials: true
                        },
                        headers: {
                            'X-XSRF-TOKEN' : widget.getXsrfToken()
                        },
                        beforeSend: function(jqXHR, settings){
                            $target.append($loadingSkin);
                        }
                    };

                    $.ajax(_default).done(function(data, textStatus, jqXHR) {




                    }).fail(function(jqXHR, textStatus, errorThrown){




                    }).always(function(jqXHR, textStatus, error){

                        if(!widget.response.hasError(jqXHR)){

                            var response = jqXHR;
                            var $allFeeds = $(response).filter('[data-' + defaults.id +  ']');
                            var $feeds = $allFeeds.not(defaults.ads);
                            var $filterFeeds;
                            var jobs = [];


                            if($feeds.length > defaults.paging){

                                if(defaults.is_slice_paging){

                                    $filterFeeds =  $feeds.slice(0, -1);

                                }else{

                                    $filterFeeds =  $allFeeds.not('[data-' + defaults.id + "='" + $feeds.last().data(defaults.id) + "'" +  ']');
                                }


                            }else{

                                $filterFeeds = $allFeeds;
                                isEnding = true;

                            }


                            jobs.push($filterFeeds.insertBefore($loadingSkin));

                            if(defaults.complete) {
                                jobs.push(defaults.complete(response, $filterFeeds , elementLastID));
                            }

                            if(isEnding){
                                jobs.push([$endingSkin.insertBefore($loadingSkin)]);
                            }


                            $.when.apply($, jobs).done(function() {
                                defaults.paging_no += 1;
                                pagingLastID = $filterFeeds.not(defaults.ads).last().data(defaults.id);
                                elementLastID = $allFeeds.last().data(defaults.id);
                                $loadingSkin.remove();
                                isLoading = false;
                            });


                        }else{

                            $loadingSkin.remove();
                            isLoading = false;

                        }

                    });

                }

            }

        }

        function isScrollToUpDirection(){

            return scrollDirection <= 0;
        }

        function isScrollToDownDirection(){

            return scrollDirection > 0;
        }

        return {
            init: function (element) {
                init(element);
            },
        }

    };

    jQuery.fn.infinite_loading = function (method, options) {

        var outerArguments = arguments;

        if (typeof method === 'object' || !method) {
            options = method;
        }

        return this.each(function () {

            var instance = $.data(this, 'infinite_loading') || $.data(this, 'infinite_loading', new InfiniteLoading(options));

            if (typeof instance[method] === 'function') {
                return instance[method].apply(this, Array.prototype.slice.call(outerArguments, 1));
            } else if (typeof method === 'object' || !method) {
                return instance.init.call(this, this);
            } else {
                $.error('Method ' + method + ' does not exist');
            }
        });

    };

}(jQuery, skin, _));