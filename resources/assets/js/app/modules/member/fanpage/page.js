$(function() {

    var $module = $('.fanpage');

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
            navProfileImage : '.navbar .profile .photo img',
            coverImage : '.cover-photo img',
            profileImage : '.profile-photo img'
        },
        text: {
            navProfileFullName : '.navbar .profile .name',
            fullName : '.profile-info .info .name span.editable-text',
            jobAndCompany: '.profile-info .info .company span.editable-text'
        },
        input: {
          textCore: '.text-core',
          tag : '.tags',
          companyHidden: '.company-input-hidden',
          company: '.company-input'

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

                if(typeof data.profile_sm_url != 'undefined'){
                    $(cls.img.navProfileImage).attr('src', data.profile_sm_url);
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

            $(cls.text.navProfileFullName).html(data.full_name);
            $module.find(cls.text.fullName).html(data.full_name);
            $module.find(cls.text.jobAndCompany).html(data.job_and_company);
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

    var $company = $(cls.input.company);
    var $companyHidden = $(cls.input.companyHidden);

    var dataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s',  $company.data('url'), '%QUERY'),
            wildcard: '%QUERY'
        }
    });

    var displayField = 'name';

    $company.typeahead({highlight : true, hint : true, minLength : 1}, {
        name: 'companies',
        display: displayField,
        limit: 41,
        source: dataSource,
        templates : {

            notFound: '', //sprintf('<div class="empty">%s</div>', $company.data('no-found')),
            suggestion: function(item){
                return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details company-skin"><div class="name">%s</div><div class="headline">%s</div><div class="address">%s</div></div></a></div>', item.logo, item.name, (item.headline) ? item.headline : '', item.address);
            }

        }
    })
    .on('typeahead:asyncrequest', function() {
        var loading = $company.parents('.twitter-typeahead-container').find('.fa-loading');
        if(loading.length <= 0) {
            $company.parents('.twitter-typeahead-container').append(skin.loading.sm);
        }

    })
    .on('typeahead:asynccancel typeahead:asyncreceive', function() {
        var loading = $company.parents('.twitter-typeahead-container').find('.fa-loading');
        if(loading.length > 0) {
            loading.remove();
        }
    })
    .on('typeahead:select', function(event, item) {


        $companyHidden.val(item.id);
        $companyHidden.data(displayField, item[displayField]);


    })
    .on('typeahead:change', function(event, item) {

        if( $.trim(item) !=  $.trim($companyHidden.data(displayField))) {
            $companyHidden.val('');
            $companyHidden.data(displayField, '');
        }
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