$(function() {


    var name = {
        'feed' : 'social-feed',
        'newFeedNotification' : 'new-feed-notification',
        'active' : 'active',
        'hasPhoto' : 'has-photo'
    };

    var cls = {
        'container': '.feed-container',
        'feed': '.' + name.feed,
        'add' : '.add-new-post',
        'delete' : '.delete',
        'editPost' : '.edit-post',
        'editEvent' : '.edit-event',
        'invite' : '.invite',
        'time' : '.top .profile .details .time',
        'photo' : '.top .message-container .photos',
        'message' : '.top .message-container .message',
        'actionEdge' : '.activity .action .edge-action',
        'actionComment' : '.activity .action .comment',
        'stats' : '.stats',
        'statsInfo': '.stats-info',
        'newFeedNotification' : '.' + name.newFeedNotification,
        'newFeedNotificationLink' : '.' + name.newFeedNotification + ' a',
        'newFeedNotificationText' : '.' + name.newFeedNotification + ' .text',
        'commentContainer' : '.comment-container',
        'commentsContainer' : '.comments',
        'comment' : '.comment',
        'editComment' : '.edit-comment',
        'deleteComment' : '.delete-comment',
        'moreCommentContainer' : '.more',
        'moreComment' : '.more-comment',
        'newCommentEditor' : '.comment-container .social-comment-editor textarea',
        'commentEditor' : '.social-comment-editor',
        'commentEditorCancel' : '.cancel-container .cancel',
        'commentContentEditor' : '.comment-container .mentions-input-box .mentiony-container .mentiony-content'
    };

    var ids = {
        'filteredPost' : '#filtered-post'
    };

    var func = {

        photo: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    var photos = $this.data('photos');
                    var cells = $this.data('cells');

                    $this.imagesGrid({
                        images: photos,
                        cells: cells,
                        align: true,
                        loading: skin.loading.sm,
                        getViewAllText: function (imagesCount) {
                            return sprintf('+%d', Math.max(0, _.size(photos) - cells));
                        }
                    });
                });

            }


        },

        del: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);

                        var confirmMessage = $this.data('confirm-message');

                        var url = $this.data('url');

                        var options = {
                            url: url,
                            data: {_method: 'delete'}
                        };

                        if(confirm(confirmMessage)) {

                            widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {

                                _.defer(function () {

                                    $template = $this.parents(cls.feed);
                                    widget.animate($template, 'zoomOut', function ($this) {
                                        $this.remove();
                                    })

                                });

                            }, function (jqXHR, textStatus, errorThrown) {

                                widget.notify(jqXHR);

                            }, function (jqXHR, textStatus, error) {

                            }, function (jqXHR, textStatus, error, hasError) {


                            });
                        }

                    })

                })
            }

        },

        editPost: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var $feed = $this.parents(cls.feed);

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

                            $(data).modal('show');

                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){


                        });


                    })

                })
            }

        },

        editEvent: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var $feed = $this.parents(cls.feed);

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                                var $data = $(data);
                                $feed.replaceWith($data);
                                func.bindAllForFeed($data);
                                func.bindAllForFeedComment($data);


                            });

                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){


                        });


                    })

                })
            }

        },

        invite: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

                            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){




                            });

                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){


                        });


                    })

                })
            }

        },

        addNewPost: function($element){

            var $template = $element;

            if($template.hasClass(name.feed)) {

                $(cls.container).prepend($template);

                _.defer(function () {

                    widget.animate($template, 'zoomIn', function($this){

                        func.bindAllForFeed($this);
                        func.bindAllForFeedComment($this);

                    })

                });


            }
        },

        actionEdge: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);

                        var edge_url = $this.data('edge-url');
                        var edge_text = $this.data('edge-text');
                        var edge_delete_url = $this.data('edge-delete-url');
                        var edge_delete_text = $this.data('edge-delete-text');
                        var edge_info = $this.data('edge-info');


                        var isActive = $this.hasClass(name.active);

                        var options = {
                            url: (!isActive) ? edge_url : edge_delete_url
                        };

                        widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {

                            $this.toggleClass(name.active);
                            if (!isActive) {
                                $this.attr('title', edge_delete_text);
                                $this.html(edge_delete_text);
                            } else {
                                $this.attr('title', edge_text);
                                $this.html(edge_text);
                            }

                            var $stats = $this.parents(cls.feed).find(cls.stats);
                            var $statsEdge = $stats.find('.' + edge_info);

                            data = widget.json.toJson(data);
                            var text =  data.text.long;
                            $statsEdge.attr('title', text);
                            $statsEdge.html(text);
                            if (data.count > 0) {
                                $stats.parent().removeClass('hide');
                            } else {
                                $stats.parent().addClass('hide');
                            }
                        }, function (jqXHR, textStatus, errorThrown) {

                            widget.notify(jqXHR);

                        }, function (jqXHR, textStatus, error) {

                        }, function (jqXHR, textStatus, error, hasError) {


                        });

                    })

                });
            }

        },

        actionInfo: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);

                        var url = $this.data('url');

                        var options = {
                            url: url
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {


                            var $modal = $(data);
                            $modal.modal('show');

                            $modal.on('hidden.bs.modal', function (e) {
                                $(this).remove();
                            })


                        }, function (jqXHR, textStatus, errorThrown) {

                            widget.notify(jqXHR);

                        }, function (jqXHR, textStatus, error) {

                        }, function (jqXHR, textStatus, error, hasError) {


                        });


                    })

                });
            }

        },

        actionComment: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $this = $(this);
                        $contentEditor = $this.parents(cls.feed).find(cls.commentContentEditor);

                        $contentEditor.mentiony('focus', $contentEditor);


                    })
                });
            }

        },

        newCommentEditor: function($element){

            if($element.length > 0) {

                $element.each(function () {

                    var $this = $(this);

                    var textarea = $this.mentiony({
                        url: $this.data('mention-url'),
                        applyInitialSize: false,
                        triggerChar: $this.data('mention-delimiter'),
                        minChars: $this.data('mention-length'),
                        isAltEnterToNewLine: true,
                        isEnterToSubmit: true,
                        onEnterToSubmit: function (event, elmInputBoxContent, text, length, done) {

                            var $form = elmInputBoxContent.parents('form');
                            var textarea = elmInputBoxContent.prev('textarea');

                            textarea.mentiony('markup', textarea, function (markup) {

                                var data = new FormData();

                                data.append('_token', $form.find('input[name="_token"]').val());
                                data.append('message', markup.text);
                                data.append('mentions', JSON.stringify(markup.id));

                                var options = {
                                    url: $form.attr('action'),
                                    data: data,
                                    contentType: false,
                                    processData: false
                                };

                                widget.ajax.form($form, $form, null, null, null, options, function (data, textStatus, jqXHR) {

                                    var $editor = elmInputBoxContent.parents(cls.commentEditor);

                                    var $template = $(data);

                                    if($editor.data('edit-mode')){

                                        $editor.parents(cls.comment).replaceWith($template)
                                        func.bindAllForFeedComment($template);

                                    }else {

                                        var $template = $(data);
                                        var $commentContainer = elmInputBoxContent.parents(cls.commentContainer).find(cls.commentsContainer);

                                        $commentContainer.prepend($template);
                                        func.bindAllForFeedComment($template);
                                    }


                                }, function (jqXHR, textStatus, errorThrown) {

                                    widget.notify(jqXHR);

                                }, function (firstJqXHR, firstTextStatus, firstError) {

                                }, function (firstJqXHR, firstTextStatus, firstError, hasError) {

                                    if (!hasError) {

                                        textarea.mentiony('clear', textarea, function () {
                                            done();
                                        });

                                    } else {
                                        done();
                                    }

                                });

                            });
                        },
                        debug: 0,
                    });

                });

            }
        },

        removeOneCommentEditor: function ($element) {

            var $editor = $element;
            var $comment = $editor.next();
            $editor.remove();
            $comment.show();

        },

        removeAllCommentEditors: function(){
            $(cls.commentsContainer + ' ' + cls.commentEditor).each(function(){
                var $this = $(this);
                var $comment = $this.next();
                $this.remove();
                $comment.show();
            })

        },

        cancelComment: function($element){
            if($element.length > 0) {

                $element.each(function () {

                    var $this = $(this);
                    var $cancel = $this.find(cls.commentEditorCancel);

                    if($cancel.length > 0){

                        $cancel.click(function(event){

                            event.preventDefault();

                            var $editor = $cancel.parents(cls.commentEditor);

                            func.removeOneCommentEditor($editor);

                        })

                    }

                });

            }

        },

        editComment: function($element){

            if($element.length > 0){

                $element.each(function() {

                    var $this = $(this);

                    $this.click(function (event) {

                        event.preventDefault();

                        var $a = $(this);

                        var url = $a.data('url');

                        var $comment = $this.parents(cls.comment);
                        var $firstChild = $comment.children();

                        var options = {
                            url: url
                        };

                        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {


                            var $data = $(data);


                            func.removeAllCommentEditors();

                            $firstChild.hide();
                            $comment.prepend($data);
                            func.cancelComment($data);
                            func.newCommentEditor($data.find('textarea'));

                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){

                        }, function(jqXHR, textStatus, error, hasError){


                        });


                    })

                })
            }

        },

        delComment: function($element){
            if($element.length > 0){

                $element.each(function(){
                    $(this).click(function(event){

                        event.preventDefault();

                        var $this = $(this);
                        var $menu = $this.parent();
                        var $commentsContainer = $this.parents(cls.commentsContainer);
                        var $comments = $commentsContainer.find(cls.comment);

                        var url = $this.data('url');

                        var options = {
                            url: url,
                            data: {_method: 'delete'},
                            beforeSend: function(jqXHR, settings){

                                $menu.addClass('loading');

                            }
                        };

                        widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {

                            _.defer(function () {

                                var $template = $this.parents(cls.comment);
                                $template.remove();

                            });

                        }, function (jqXHR, textStatus, errorThrown) {

                            widget.notify(jqXHR);

                        }, function (jqXHR, textStatus, error) {

                        }, function (jqXHR, textStatus, error, hasError) {

                            if(!hasError){

                            }

                            $menu.removeClass('loading');

                        });

                    })
                });
            }
        },

        moreComment: function($element){
            if($element.length > 0) {

                $element.each(function(){

                    $(this).click(function (event) {

                        event.preventDefault();

                        var $this = $(this);
                        var $commentsContainer = $this.parents(cls.commentContainer).find(cls.commentsContainer);
                        var id = 'comment-id';
                        var text = $(this).attr('title');
                        var url = $this.data('url');
                        var paging = $this.data('paging');
                        var total = $this.data('total');
                        var offset = $this.data('offset');
                        var remaining = 0;
                        var lastID = $this.data('last-id');

                        var options = {
                            url: url,
                            data: {
                                'comment-id' : lastID,
                            }
                        };

                        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {

                            var $data = $(data);
                            var hasComment = false;

                            var $comments = $data.filter(sprintf('[data-%s]', id));

                            if($comments.length > 0) {

                                if ($comments.length > paging) {
                                    $comments = $comments.slice(0, -1);
                                }

                                offset = offset + $comments.length;
                                remaining = Math.max(0, total - offset);

                                if(text){
                                    text = text.replace(/\b([0-9].*?)\b/g, remaining)
                                    $this.attr('title', text);
                                    $this.html(text);
                                }

                                $this.data('last-id', $comments.last().data(id));
                                $this.data('offset', offset);

                                $commentsContainer.append($comments)
                                func.bindAllForFeedComment($comments);

                                if(remaining <= 0){
                                    $this.parent().addClass('hide');
                                }

                            }




                        }, function(jqXHR, textStatus, errorThrown){

                            widget.notify(jqXHR);

                        }, function(jqXHR, textStatus, error){




                        }, function(jqXHR, textStatus, error, hasError){



                        });
                    });

                });

            }
        },

        notificationText: function($element, figure){

            var text = sprintf(
                '%s %s %s',
                figure,
                $element.data('new-text'),
                (figure) <= 1 ? $element.data('singular-text') : $element.data('plural-text')
            )

            return text;

        },

        bindAllForFeed:function($element){

            this.photo($element.find(cls.photo));
            this.del($element.find(cls.delete));
            this.editPost($element.find(cls.editPost));
            this.editEvent($element.find(cls.editEvent));
            this.invite($element.find(cls.invite));
            this.actionEdge($element.find(cls.actionEdge));
            this.actionInfo($element.find(cls.statsInfo));
            this.actionComment($element.find(cls.actionComment));
            this.newCommentEditor($element.find(cls.newCommentEditor));
        },

        bindAllForFeedComment: function($element){
            this.editComment($element.find(cls.editComment));
            this.delComment($element.find(cls.deleteComment));
            this.moreComment($element.find(cls.moreComment))
        },

    };

    var newFeedNotificationLoading = false;

    var filteredPost = $(ids.filteredPost);

    if(filteredPost.length > 0){

        var gap = 15;
        var header = widget.headerFromLayout();

        if(header.length > 0){
            gap += header.height();
        }

        $('html, body').stop().animate({scrollTop: filteredPost.offset().top - gap}, 500, 'swing', function() {

        })

    }

    $(cls.newFeedNotificationLink).click(function(event){

        event.preventDefault();

        if(newFeedNotificationLoading){
            return;
        }

        newFeedNotificationLoading = true;

        var $this = $(this);
        var $newFeedNotificationText = $(cls.newFeedNotificationText);
        var url = $this.data('url');
        var paging = $this.data('paging');
        var figure = $this.data('figure');
        var lastID = $this.data('feed-id');

        var options = {
            url: url,
            data: {
                'feed-id' : lastID,
            }
        };



        widget.ajax.get($this, null, null, options, function (data, textStatus, jqXHR) {


        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

            if(!widget.response.hasError(jqXHR)){

                var data = jqXHR;
                var $newFeedNotificationContainer = $(cls.newFeedNotification);
                var hasMore = false;
                var $firstFeed;
                var $feeds = $(data).filter('[data-feed-id]');

                if ($feeds.length > paging) {
                    $firstFeed = $feeds.first();
                    $feeds = $feeds.slice(1);
                    hasMore = true;
                }

                $('html, body').stop().animate({scrollTop:0}, 500, 'swing', function() {

                }).promise().then(function() {
                    if(hasMore){
                        figure = Math.max(0, figure - $feeds.length);
                        $this.data('feed-id', $firstFeed.data('feed-id'));
                        $this.data('figure', figure);
                        $newFeedNotificationText.html(func.notificationText($this, figure));
                        newFeedNotificationLoading = false;
                    }else{
                        widget.animate($newFeedNotificationContainer, 'bounceOutUp', function() {
                            $newFeedNotificationContainer.addClass('hide');
                            $this.data('feed-id', '');
                            $this.data('figure', 0);
                            newFeedNotificationLoading = false;
                        })
                    }

                    func.addNewPost($($('<div></div>').append($feeds).html()));
                });


            }else{
                newFeedNotificationLoading = false;
            }


        }, function(jqXHR, textStatus, error, hasError){



        });

    });

    $(cls.add).click(function(event){

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){

                //var $data = $(data);
                //func.addNewPost($($('<div></div>').append($data).html()));

                var $modal = $(skin.modal.simple('', widget.json.toJson(data).message));

                $modal.modal('show');

                $modal.on('hidden.bs.modal', function(e){
                    $(this).remove();
                })

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){


        });

    })

    $(cls.feed).each(function(){

       var $this = $(this);

       func.bindAllForFeed($this);
       func.bindAllForFeedComment($this);

    });

    $(document).on('social-media-feed-new', function(event, template){

        var $template = $(template);

        func.addNewPost($template);

    });

    $(document).on('social-media-feed-edit', function(event, data){

        data = widget.json.toJson(data);
        var $feed = $(sprintf('%s[data-feed-id="%s"]', cls.feed, data.id));

        var $time = $feed.find(cls.time);
        var $message = $feed.find(cls.message);
        var $photos = $feed.find(cls.photo);

        if($feed.length > 0){
            $time.attr('title', data.time)
            $time.html(data.time);
            $message.html(data.message);
            if(data.photos.length > 0){
                $photos.addClass(name.hasPhoto);
            }else{
                $photos.removeClass(name.hasPhoto);
            }
            $photos.data('photos', data.photos);
            $photos.data('cells', data.layout);
            $photos.html('');
            func.photo($feed.find(cls.photo));
        }

    });

    $(document).on('social-media-feed-new-notification', function(event, data){

        _.defer(function () {

            var id = data.post_id;
            var type = data.type;
            var group_id = data.group_id;
            var $post = $(sprintf('%s[data-feed-id="%s"]', cls.feed, id));


            var $newFeedNotificationContainer = $(cls.newFeedNotification);
            var $newFeedNotificationText = $(cls.newFeedNotificationText);
            var $newFeedNotificationLink = $(cls.newFeedNotificationLink);
            var figure = $newFeedNotificationLink.data('figure');


            var dataType = $newFeedNotificationContainer.data('type');
            var dataGroupId = $newFeedNotificationContainer.data('group-id');

            if(dataType != type || ( dataGroupId && dataGroupId != group_id) || $post.length > 0){

                return;

            }


            figure++;

            $newFeedNotificationLink.data('figure', figure);
            $newFeedNotificationText.html(func.notificationText($newFeedNotificationLink, figure));

            if($newFeedNotificationContainer.hasClass('hide')) {
                $newFeedNotificationLink.data('feed-id', id);
                $newFeedNotificationContainer.removeClass('hide');
                widget.animate($newFeedNotificationContainer, 'bounceInDown', function () {
                })
            }

        });

    });

    $(document).on('social-media-comment-new', function(event, data){


        var $post = $(sprintf('%s[data-feed-id="%s"]', cls.feed, data.post_id));

        if($post.length > 0){

            var $commentContainer = $post.find(cls.commentsContainer);
            var $comment = $commentContainer.find( $(sprintf('%s[data-comment-id="%s"]', cls.comment, data.comment_id)));

            if($comment.length <= 0) {
                var $template = $(data.view);
                $commentContainer.prepend($template);
                func.bindAllForFeedComment($template);
            }

        }

    });

    $(document).on('social-media-feed-infinite-loading', function(event, lastID){

        var $feeds = $(cls.container).find(sprintf('%s[data-feed-id="%s"]',  cls.feed, lastID)).nextAll(cls.feed);

        func.bindAllForFeed($feeds);
        func.bindAllForFeedComment($feeds);


    });


});