$(function() {

    var $module = $('.job-form');
    var $company_name_input = $module.find('.company_name-input');
    var $company_input_hidden = $module.find('.company_id-input-hidden');
    var displayField = 'name';
    var $tags = $module.find('.tags');

    var dataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s',  $company_name_input.data('url'), '%QUERY'),
            wildcard: '%QUERY'
        }
    });

    widget.integerValueOnly();

    $company_name_input.typeahead({highlight : true, hint : true, minLength : 1}, {
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
            var loading = $company_name_input.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length <= 0) {
                $company_name_input.parents('.twitter-typeahead-container').append(skin.loading.sm);
            }

        })
        .on('typeahead:asynccancel typeahead:asyncreceive', function() {
            var loading = $company_name_input.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length > 0) {
                loading.remove();
            }
        })
        .on('typeahead:select', function(event, item) {


            $company_input_hidden.val(item.id);
            $company_input_hidden.data(displayField, item[displayField]);


        })
        .on('typeahead:change', function(event, item) {

            if( $.trim(item) != $.trim($company_input_hidden.data(displayField))) {
                $company_input_hidden.val('');
                $company_input_hidden.data(displayField, '');
            }

        });

    $tags.each(function(){

        if($(this).parents('.text-core').length <= 0){

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

});