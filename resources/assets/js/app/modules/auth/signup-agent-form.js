$(function() {

    var $module = $('.signup-agent-form');


    var cls = {
        input: {
            textCore: '.text-core',
            tag : '.tags',

        },
    };

    var $companyHidden = $module.find('.company-hidden');
    var $companyInput = $module.find('.company-input');

    $module.find( cls.input.tag ).each(function(){

        if($(this).parents(cls.input.textCore).length <= 0){

            $(this).val('');

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

    var dataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s',  $companyInput.data('url'), '%QUERY'),
            wildcard: '%QUERY'
        }
    });

    var displayField = 'name';

    $companyInput.typeahead({highlight : true, hint : true, minLength : 1}, {
        name: 'companies',
        display: displayField,
        limit: 41,
        source: dataSource,
        templates : {

            notFound: '', //sprintf('<div class="empty">%s</div>', $companyInput.data('no-found')),
            suggestion: function(item){

                return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details has-menu company-skin"><div class="name">%s</div><div class="registration-number">%s</div><div class="headline">%s</div><div class="address">%s</div></div></a></div>', item.logo, item.name, item.registration_number, (item.headline) ? item.headline : '', item.address);

            }

        }
    })
    .on('typeahead:asyncrequest', function() {
        var typeheadContainer = $companyInput.parents('.twitter-typeahead-container');
        var loading = typeheadContainer.find('.fa-loading');
        if(loading.length <= 0) {
            $companyInput.parents('.twitter-typeahead-container').append(skin.loading.sm);
        }

    })
    .on('typeahead:asynccancel typeahead:asyncreceive', function() {
        var loading = $companyInput.parents('.twitter-typeahead-container').find('.fa-loading');
        if(loading.length > 0) {
            loading.remove();
        }
    })
    .on('typeahead:select', function(event, item) {

        $companyHidden.val(item.id);
        $companyHidden.data(displayField, item[displayField]);

        for(k in item){

            var $input = $module.find(sprintf('input[name="companies[%s]"], select[name="companies[%s]"]', k, k));
            if ($input.length > 0) {
                if($input.get(0) === $companyInput.get(0)){
                    continue;
                }
                $input.val(item[k]);
                $input.attr('disabled','disabled');
            }

        }

    })
    .on('typeahead:change', function(event, item) {

        if( $.trim(item) !=  $.trim($companyHidden.data(displayField))) {
            $companyHidden.val('');
            $companyHidden.data(displayField, '');

            var $input = $module.find(sprintf('input[name*="companies"], select[name*="companies"]'));
            $input.removeAttr('disabled');

        }
    });


    $module.on('click', '.input-submit', function(event){

        event.preventDefault();

        var $submit = $(this);
        var $form = $submit.closest('form');
        var $messageBox = $form.find('.message-box');
        var $success = $messageBox;
        var $error = $messageBox;

        var options = {
            url: $form.attr('action'),
            data: $form.serialize()
        };

        widget.ajax.form($module, $form, $submit, null, $error, options, function(data, textStatus, jqXHR) {

            var data = widget.json.toJson(data);

            if ($submit.data('shouldRedirect')) {
                location.href = $submit.data('shouldRedirect');
            } else {
                $success.after($(skin.alert.success(data.message)));
            }

        }, function(jqXHR, textStatus, errorThrown){

            setTimeout(function() {
                $oldElement = $form.find('#handphone_number').prev().detach();
                $module.find('.btm-divider').after($oldElement);
            },1);
        });

    });



});