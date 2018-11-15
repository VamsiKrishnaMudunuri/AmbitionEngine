$(function() {


    var $which_module = $('.social-post-editor.edit-mode');
    var isEditMode = ($which_module.length > 0);
    var $module = (isEditMode) ? $which_module :  $('.social-post-editor');
    var $form = $module.find('form');
    var $message = $form.find('.message');
    var $textarea = $message.find('textarea');
    var $photos = $form.find('.photos');
    var $addPost = $form.find('.add-post');
    var $editPost = $form.find('.edit-post')
    var $addPhoto = $form.find('.add-photo');
    var $deletePhoto = $form.find('.delete-photo');

    var deletedPhotos = [];

    var photoUploadManager = {
        index: 0,
        files : {},
        uploadedFileds: {},
        add: function(obj){

            var newSize = this.index++;
            this.files[newSize] = obj;

            return newSize;

        },
        remove: function(index){
            delete this.files[index];
        },
        get: function(index){
            return this.files[index];
        },
        saveUploadedFiles: function(index){

            var obj = this.get(index);

            if(obj !== undefined){
                this.uploadedFileds[index] = obj.file;
            }

        },
        getUploadedFiles: function(index){

            var file = '';

            if(this.uploadedFileds[index] !== undefined){
                file = this.uploadedFileds[index];

            }

            return file;

        },
        hasUploading: function(){
            return _.size(this.files) > 0 ? true : false;
        }

    };

    var request;

    var func = {

        enablePostButton : function(){
            var $element = (isEditMode) ? $editPost : $addPost;
            $element.attr('disabled', false);
        },
        disablePostButton : function(){
            var $element = (isEditMode) ? $editPost : $addPost;
            $element.attr('disabled', true);
        },
        clearPhotoFrames: function(){
            $photos.html('');
        },
        hasMentionText: function(cb){

            var textarea = $message.find('textarea');
            textarea.mentiony('hasText', textarea, function(flag){
                cb(flag);

            });

        },
        getMention: function(cb){
            var textarea = $message.find('textarea');
            textarea.mentiony('markup', textarea, function(text){
                cb(text);

            });
        },
        clearMention: function(cb){
            var textarea = $message.find('textarea');
            textarea.mentiony('clear', textarea, function(){
                if(cb){
                    cb();
                }
            });
        }

    }

    var textarea = $textarea.mentiony({
        url: $textarea.data('mention-url'),
        applyInitialSize: false,
        triggerChar:  $textarea.data('mention-delimiter'),
        minChars:  $textarea.data('mention-length'),
        onTyping: function(event, elmInputBoxContent, text, length){
            if(length > 0){
                func.enablePostButton();
            }else{
                func.disablePostButton();
            }
        },
        debug: 0,
    });

    $addPhoto.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var url = $this.data('url');
        var fileField = $this.data('file-field');
        var fileUploadThreshold = $this.data('file-upload-threshold');
        var message = $this.data('message');

        if($photos.find('.frame').length >= fileUploadThreshold){
            $.notify(message.threshold, {
                type: 'danger'
            });
            return;
        }

        if (window.File && window.FileReader && window.FileList && window.Blob)
        {
            $fileInput = $('<input />').attr('type', 'file').attr('name', fileField);

            $fileInput.on('change', function(event){

                var files = event.target.files;
                var file = files[0];
                var reader = new FileReader();

                reader.onload = function (event) {

                    var data = new FormData();

                    $imageFrame = $('<div class="frame"><a href="javascript:void(0);"><i class="fa fa-close fa-lg close-frame" onclick="$(this).parent().parent().remove();"></i><div class="loading-layer"><i class="fa fa-spinner fa-spin fa-fw fa-loading"></i></div><img src="" /></a></div>');
                    $loading = $imageFrame.find('.loading-layer');
                    $image = $imageFrame.find('img');

                    func.disablePostButton();
                    $photos.append($imageFrame);

                    var index = photoUploadManager.add({imageFrame : $imageFrame, loading: $loading, image : $image, file: file});
                    $image.attr('src', event.target.result);
                    $image.data('index', index);

                    data.append(fileField, file);

                    var _default = {
                        index: index,
                        url: url,
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        cache: true,
                        processData: false,
                        contentType: false,
                        xhrFields: {
                            withCredentials: true
                        },
                        headers: {
                            'X-XSRF-TOKEN' : widget.getXsrfToken()
                        },
                        beforeSend: function(jqXHR, settings){



                        }
                    };

                    $.ajax(_default).done(function(data, textStatus, jqXHR) {


                        if($photos.find(photoUploadManager.get(this.index).imageFrame).length > 0){
                            photoUploadManager.saveUploadedFiles(this.index);
                        }


                    }).fail(function(jqXHR, textStatus, errorThrown){

                        widget.notify(jqXHR);

                        if($photos.find(photoUploadManager.get(this.index).imageFrame).length > 0){
                            photoUploadManager.get(this.index).imageFrame.remove()
                        }

                    }).always(function(jqXHR, textStatus, error){

                        if($photos.find(photoUploadManager.get(this.index).imageFrame).length > 0){
                            photoUploadManager.get(this.index).loading.remove();
                        }

                        photoUploadManager.remove(this.index);
                        if(!photoUploadManager.hasUploading()){
                            func.hasMentionText(function(flag){
                                if(flag){
                                    func.enablePostButton();
                                }
                            })

                        }

                    });
                }

                reader.readAsDataURL(file);

            });

            $fileInput.click();

        }
        else
        {
            $.notify(message.unsupported, {
                type: 'danger'
            });
        }
    })

    if(!isEditMode) {
        $addPost.click(function (event) {

            event.preventDefault();

            var $this = $(this);

            func.getMention(function (markup) {

                var $submit = $this;
                var $form = $submit.closest('form');
                var fileField = $submit.data('file-field');

                var data = new FormData();

                data.append('_token', $form.find('input[name="_token"]').val());
                data.append('message', markup.text);
                data.append('mentions', JSON.stringify(markup.id));
                $photos.find('.frame img').each(function () {
                    var index = $(this).data('index');
                    var file = photoUploadManager.getUploadedFiles(index);
                    if (file) {
                        data.append(sprintf('%s[]', fileField), file);
                    }
                })


                var options = {
                    url: $form.attr('action'),
                    data: data,
                    contentType: false,
                    processData: false
                };

                widget.ajax.form($form, $form, $submit, [$addPhoto], null, options, function (data, textStatus, jqXHR) {

                    $(document).trigger('social-media-feed-new', data);

                }, function (jqXHR, textStatus, errorThrown) {

                    widget.notify(jqXHR);

                }, function (firstJqXHR, firstTextStatus, firstError) {

                }, function (firstJqXHR, firstTextStatus, firstError, hasError) {

                    if (!hasError) {

                        func.clearMention(function () {

                            func.disablePostButton();
                            func.clearPhotoFrames();

                        });

                    }

                });
            });


        })
    }

    if(isEditMode){

        func.enablePostButton();

        $deletePhoto.click(function(event){

            event.preventDefault();

            var $this = $(this);

            var id = $this.data('id');

            deletedPhotos.push(id);

            $(this).parent().parent().remove();

        })

        $editPost.click(function (event) {

            event.preventDefault();

            var $this = $(this);
            var $modal = $this.parents('.modal');

            $modal.on('hidden.bs.modal', function(e){
                $(this).remove();
            })

            func.getMention(function (markup) {

                var $submit = $this;
                var $form = $submit.closest('form');
                var fileField = $submit.data('file-field');

                var data = new FormData();

                data.append('_token', $form.find('input[name="_token"]').val());
                data.append('message', markup.text);
                data.append('mentions', JSON.stringify(markup.id));
                $photos.find('.frame').not('.frame[data-existing]').find('img').each(function () {
                    var index = $(this).data('index');
                    var file = photoUploadManager.getUploadedFiles(index);
                    if (file) {
                        data.append(sprintf('%s[]', fileField), file);
                    }
                })

                for(var k in deletedPhotos){
                    data.append('_delete_files[]', deletedPhotos[k]);
                }


                var options = {
                    url: $form.attr('action'),
                    data: data,
                    contentType: false,
                    processData: false
                };

                widget.ajax.form($form, $form, $submit, [$addPhoto], null, options, function (data, textStatus, jqXHR) {

                    $(document).trigger('social-media-feed-edit', data);


                }, function (jqXHR, textStatus, errorThrown) {

                    widget.notify(jqXHR);

                }, function (firstJqXHR, firstTextStatus, firstError) {

                }, function (firstJqXHR, firstTextStatus, firstError, hasError) {

                    if (!hasError) {

                        func.clearMention(function () {

                            func.disablePostButton();
                            func.clearPhotoFrames();
                            $modal.modal('hide');

                        });

                    }

                });
            });


        })

    }

});