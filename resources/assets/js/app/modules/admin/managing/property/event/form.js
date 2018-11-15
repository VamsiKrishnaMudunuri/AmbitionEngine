$(function() {

    var $form = $('.event-form');
    var $location = $form.find('.location');
    var $addressInput = $location.find('.address_or_name');
    var $addressHiddenInput = $location.find('.place-hidden');
    var addressUrl =  $addressInput.data('url');
    var addressMatch = $addressInput.data('mapping');
    var $tag = $form.find('.tags');

    widget.integerValueOnly();
    widget.helpBox();
    widget.dateTimePicker({  pickerPosition: "top-left"});

    var dataSource = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: sprintf('%s?query=%s', addressUrl, '%QUERY'),
            wildcard: '%QUERY'

        }
    });

    $addressInput.typeahead({highlight : true, hint : true, minLength : 1}, {
        name: 'addresses',
        display: 'display_field',
        limit: 41,
        source: dataSource,
        templates : {

            notFound: '',
            suggestion: function(item){
                return sprintf('<div class="card"><a href="javascript:void(0);"><div class="details property-skin"><div class="address-container"><div class="location">%s</div><div class="address">%s</div></div></div></a></div>', item.location, item.display_address);
            }

        }
    })
        .on('typeahead:asyncrequest', function() {
            var loading = $addressInput.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length <= 0) {
                $addressInput.parents('.twitter-typeahead-container').append(skin.loading.sm);
            }

        })
        .on('typeahead:asynccancel typeahead:asyncreceive', function() {
            var loading = $addressInput.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length > 0) {
                loading.remove();
            }
        })
        .on('typeahead:select', function(event, item) {


            for(var field in addressMatch){
                if(item[field]){
                    var pfield = addressMatch[field];
                    var data = item[field];
                    var input = $addressHiddenInput.filter(sprintf('[data-field="%s"]', pfield));
                    if(input.length > 0){
                        input.val(data);
                    }
                }
            }

        })
        .on('typeahead:change', function(event, item) {

            $addressHiddenInputForAddress =  $addressHiddenInput.filter(sprintf('[data-field="%s"]', 'address'));
            if($.trim(item) != $.trim($addressHiddenInputForAddress.val()) ) {
                $addressHiddenInput.each(function () {
                    $(this).val('');
                })
            }

            $addressHiddenInputForAddress.val($addressInput.val());

        });

        var $tagTextText = $tag.textext({
            //tagsItems: $tag.data('data'),
            suggestions: $tag.data('suggestion'),
            //plugins: 'autocomplete suggestions tags filter'
            plugins: 'autocomplete suggestions tags'
        });

        $tagTextText.textext()[0].tags().addTags($tag.data('data'));

        $tagTextText.blur(function(){
            if ($(this).val().trim() != '') $(this).trigger('enterKeyPress').val('').blur();
        });
});