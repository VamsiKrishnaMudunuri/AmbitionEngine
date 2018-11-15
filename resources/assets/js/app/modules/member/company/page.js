$(function() {

    $module = $('.fanpage');

    var name = {
        icon : {
            uploadIconLoading: 'fa-spinner fa-pulse'
        },
        edit: {
            editable : 'editable',
        },
        form: {
            form: 'form',

        }
    }

    var cls = {
        container: {
            editableText : '.editable-text',
            banner: '.banner',
            box: '.box',
            profile: '.profile-info',
            section: '.section',
            body: '.body',
            websiteEditableInputContainer : '.website-editable-input-container'
        },
        icon: {
           uploadIcon: '.fa-camera'
        },
        message: {
           error : '.error'
        },
        img : {
            coverImage : '.cover-photo img',
            profileImage : '.profile-photo img'
        },
        text: {
            name : '.profile-info .info .name span.editable-text',
            industry : '.profile-info .info .industry span.editable-text',
            headline : '.profile-info .info .headline span.editable-text',
        },
        link: {
            owner: '.owner_company_link',
        },
        input: {
          textCore: '.text-core',
          tag : '.tags'
        },
        action : {
            uploadAction : '.upload-action',
            editableAction : '.editable-action',
            uploadCoverImage: '.upload-cover-photo',
            uploadProfileImage: '.upload-profile-photo',
            editProfile : '.edit-profile',
            saveProfile : '.save-profile',
            cancelProfile : '.cancel-profile',
            cancelSection: '.cancel-section',
            saveSection: '.save-section',
            inlineEditAction : '.inline-edit-action',
            websiteEditableInputAdd: '.website-editable-input-add',
            websiteEditableInputDelete : '.website-editable-input-delete',
            seeAll: '.see-all-members'
        }
    };

    var $websiteContainer = $(cls.container.websiteEditableInputContainer);
    var $websiteContainerForm = $websiteContainer.find('form');
    var websiteEditableInputSample = $websiteContainer.find('.sample').html();

    widget.bsToggle();

    $(sprintf('%s,%s', cls.action.uploadCoverImage, cls.action.uploadProfileImage)).click(function (e){

        e.preventDefault();

        var $a = $(this);
        var $icon = $a.find(cls.icon.uploadIcon);
        var url = $a.data('url');
        var field = $a.data('file-field');

        $fileInput = $('<input />').attr('type', 'file');

        $fileInput.on('change', function(event){

            var files = event.target.files;

            var data = new FormData();

            $.each(files, function(key, value)
            {
                data.append(field, value);
            });

            var _default = {
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

                    $a.attr('disabled', true);
                    $icon.addClass(name.icon.uploadIconLoading);

                }
            };

            $.ajax(_default).done(function(data, textStatus, jqXHR) {

                if(typeof data.cover_lg_url != 'undefined'){
                    $(cls.img.coverImage).attr('src', data.cover_lg_url);
                }


                if(typeof data.profile_xlg_url != 'undefined'){
                    $(cls.img.profileImage).attr('src', data.profile_xlg_url);
                }

            }).fail(function(jqXHR, textStatus, errorThrown){

                widget.notify(jqXHR);

            }).always(function(jqXHR, textStatus, error){

                $a.attr("disabled", false);
                $icon.removeClass(name.icon.uploadIconLoading);

            });

        });

        $fileInput.click();

    })

    $(cls.action.editProfile).click(function(e){
        e.preventDefault();
        $module.find(cls.container.banner).addClass(name.edit.editable);
    })

    $(cls.action.saveProfile).click(function(e){

        e.preventDefault();

        var $submit = $(this);
        var $profile = $module.find(sprintf('%s %s', cls.container.banner, cls.container.profile));
        var $form = $submit.closest(name.form.form);
        var $cancel = $form.find(cls.action.cancelProfile);
        var $error = $profile.find(sprintf('%s %s', cls.action.editableAction, cls.message.error));

        var options = {
            url: $form.attr('action'),
            dataType: 'json',
            data: $form.serialize(),
            xhrFields: {
                withCredentials: true
            }
        };

        widget.ajax.form($profile, $form, $submit, [$cancel], $error, options, function(data, textStatus, jqXHR) {

            $module.find(cls.text.name).html(data.name);
            $module.find(cls.text.industry).html(data.industry);
            $module.find(cls.text.headline).html(data.headline);

            $(cls.link.owner).attr('href', data.url);
            $module.find(cls.container.banner).removeClass(name.edit.editable);


        }, function(jqXHR, textStatus, errorThrown){



        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        }, false);

    })

    $(cls.action.cancelProfile).click(function(e){

        e.preventDefault();

        $module.find(cls.container.banner).removeClass(name.edit.editable);

    })

    $(cls.action.inlineEditAction).click(function(e){

        e.preventDefault();
        $section =  $(this).parents(cls.container.section);
        $section.addClass(name.edit.editable);
        $tags = $section.find(cls.input.tag);


        if($tags.length > 0) {
            $tags.each(function(){

                if($(this).parents(cls.input.textCore).length <= 0){

                    var $tag = $(this).textext({
                        //tagsItems: $(this).data('data'),
                        suggestions: $(this).data('suggestion'),
                        //plugins: 'autocomplete suggestions tags filter'
                        plugins: 'autocomplete suggestions tags'
                    });

                    $tag.textext()[0].tags().addTags($(this).data('data'));

                    $tag.blur(function(){
                        if ($(this).val().trim() != '') $(this).trigger('enterKeyPress').val('').blur();
                    });

                }

            })
        }

    })

    $(cls.action.saveSection).click(function(e){

        e.preventDefault();

        var $submit = $(this);
        var $section = $submit.parents(cls.container.section);
        var $editableText = $section.find(cls.container.body).children(cls.container.editableText);
        var $form = $submit.closest(name.form.form);
        var $cancel = $form.find(cls.action.cancelSection);
        var $error = $form.find(sprintf('%s %s', cls.action.editableAction, cls.message.error));

        var options = {
            url: $form.attr('action'),
            data: $form.serialize(),
            xhrFields: {
                withCredentials: true
            }
        };

        widget.ajax.form($form, $form, $submit, [$cancel], $error, options, function(data, textStatus, jqXHR) {

            $section.removeClass(name.edit.editable);
            $editableText.html(data);

        }, function(jqXHR, textStatus, errorThrown){



        }, function(firstJqXHR, firstTextStatus, firstError){

        }, function(firstJqXHR, firstTextStatus, firstError, hasError){

        }, false);

    })

    $(cls.action.cancelSection).click(function(e){

        e.preventDefault();
        $(this).parents(cls.container.section).removeClass(name.edit.editable);

    })

    $(cls.action.websiteEditableInputAdd).click(function(e){

        e.preventDefault();

        var $this = $(this);

        var start_index =  $this.data('start-index');
        var max_size = $this.data('max-size');
        var available_size = $this.data('current-available-size');


        start_index += 1;
        available_size -= 1;

        $websiteContainerForm.prepend(sprintf(websiteEditableInputSample, start_index, start_index, start_index, start_index, start_index, start_index));

        $this.data('start-index', start_index);
        $this.data('current-available-size', available_size);

        if(available_size <= 0){
            $this.attr('disabled', 'disabled');
        }

    })

    $(cls.action.seeAll).click(function(event) {

        event.preventDefault();

        var $a = $(this);

        var url = $a.data('url');

        var options = {
            url: url
        };

        widget.ajax.get($a, null, null, options, function (data, textStatus, jqXHR) {


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

    });

    $websiteContainer.on('click', cls.action.websiteEditableInputDelete, function(e){

        e.preventDefault();

        var $this = $(this);

        var $websiteEditableInputAdd = $(cls.action.websiteEditableInputAdd);
        var available_size = $websiteEditableInputAdd.data('current-available-size');
        available_size += 1;
        $this.closest('.row').remove();
        $websiteEditableInputAdd.data('current-available-size', available_size);

        if(available_size > 0){
            $websiteEditableInputAdd.removeAttr('disabled');
        }

    })

});