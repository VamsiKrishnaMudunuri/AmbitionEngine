(function ($, skin, _) {

    var InfiniteLoadingMore = function(options){

        var $target;
        var $more;

        var isLoading = false;
        var isEnding = false;
        var lastScrollPosition = 0;
        var scrollDirection = 0;

        var lastID;

        var defaults = {
            url: '',
            id : 'id',
            threshold : 300,
            is_paging_method : false,
            paging_no_id : 'page-no',
            paging_no : 1,
            paging: 20,
            moreText: '',
            emptyText: '',
            endingText: '',
            moreSkin: '<a href="javascript:void(0);" class="infinite-item loading-more"></a>',
            loadingSkin: skin.loading.md,
            emptySkin: '<div class="infinite-item empty"></div>',
            endingSkin: '<div class="infinite-item ending"></div>',
            complete: function(response, lastID){

            }

        };

        defaults = $.extend({}, defaults, options);

        function init(element){

            $target = $(element);

            var $feeds = $target.find('[data-' + defaults.id +  ']');
            lastID = $target.find('[data-' + defaults.id +  ']').last().data(defaults.id);

            if($feeds.length <= 0 && defaults.emptyText){

                var $emptySkin = $(defaults.emptySkin);
                $emptySkin.html(defaults.emptyText);
                $target.append($emptySkin);
                isEnding = true;

            }else if($feeds.length <= defaults.paging){

                var $endingSkin = $(defaults.endingSkin);
                $endingSkin.html(defaults.endingText);
                $target.append($endingSkin);
                isEnding = true;

            }else{

                $more = $(defaults.moreSkin);
                $more.html(defaults.moreText);
                $more.bind('click', click);
                $target.append($more);

            }


        }


        function click(e){


            if(!isLoading && !isEnding){

                    var $endingSkin = $(defaults.endingSkin);
                    isLoading = true;
                    $endingSkin.html(defaults.endingText);

                    var data = {};
                    data[defaults.id] = lastID;


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
                            $more.html(defaults.loadingSkin);
                        }
                    };

                    $.ajax(_default).done(function(data, textStatus, jqXHR) {




                    }).fail(function(jqXHR, textStatus, errorThrown){




                    }).always(function(jqXHR, textStatus, error){

                        if(!widget.response.hasError(jqXHR)){

                            var response = jqXHR;
                            var $feeds = $(response).filter('[data-' + defaults.id +  ']');
                            var jobs = [];


                            if($feeds.length > defaults.paging){
                                $feeds = $feeds.slice(0, -1);
                            }else{
                                isEnding = true;
                            }


                            jobs.push($feeds.insertBefore($more));

                            if(defaults.complete) {
                                jobs.push(defaults.complete(response, $feeds, lastID));
                            }

                            if(isEnding){
                                jobs.push([$more.hide()]);
                                jobs.push([$endingSkin.insertBefore($more)]);
                            }


                            $.when.apply($, jobs).done(function() {
                                defaults.paging_no += 1;
                                lastID = $feeds.last().data(defaults.id);
                                isLoading = false;
                                $more.html(defaults.moreText);
                            });


                        }else{


                            $more.html(defaults.moreText);
                            isLoading = false;

                        }

                    });

                }


        }



        return {
            init: function (element) {
                init(element);
            },
        }

    };

    jQuery.fn.infinite_loading_more = function (method, options) {

        var outerArguments = arguments;

        if (typeof method === 'object' || !method) {
            options = method;
        }

        return this.each(function () {

            var instance = $.data(this, 'infinite_loading_more') || $.data(this, 'infinite_loading_more', new InfiniteLoadingMore(options));

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