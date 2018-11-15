$(function() {

    var $module = $('.auth-signup-prime-member');
    var $signupForm = $module.find('.sign-up-form');
    var $companyHidden = $signupForm.find('.company-hidden');
    var $company = $signupForm.find('.company');



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

});

var tempSelect = {
    value: '',
    text: ''
};


$('select.country-code').change(function(e) {
    tempSelect.text = $(this).find('option:selected').text();
    tempSelect.value = $(this).find('option:selected').val();

    $(this).find('option:selected').text('+' + $(this).find('option:selected').val())
    //console.log($(this).find('option:selected').text())
});

$('select.country-code').on('click',function() {
    $(this).find('option[value="' + tempSelect.value + '"]').text(tempSelect.text)

})