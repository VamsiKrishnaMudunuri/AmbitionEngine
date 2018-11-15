$(function() {


    var $form = $('.member-form');
    var $companyHidden = $form.find('.company-hidden');
    var $company = $form.find('.company');
    var $addCompany = $form.find('.add-company');
    var hasMemberRights = $company.data('member-rights');

    var func = {
      clearTypeaheadSuggestion : function(){
          var $suggestion = $('.twitter-typeahead-container .twitter-typeahead .tt-menu .tt-dataset');

          if($suggestion.length > 0){
              $suggestion.html('');
          }
      },
      updatePreHint: function(name){
          var $pre = $('.twitter-typeahead-container .twitter-typeahead pre');

          if($pre.length > 0){
              $pre.html(name)
          }
      }
    };

    var cls = {
        input: {
            textCore: '.text-core',
            tag : '.tags',

        },
    };


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


                var disabledCompanyEdit = (hasMemberRights) ? '' : 'disabled';

                return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details has-menu company-skin"><div class="name">%s</div><div class="registration-number">%s</div><div class="headline">%s</div><div class="address">%s</div></div><div class="menu"><span class="company-edit %s" %s data-url="%s">%s</span></div></a></div>', item.logo, item.name, item.registration_number, (item.headline) ? item.headline : '', item.address, disabledCompanyEdit, disabledCompanyEdit, $company.data('edit-url') + '/' + item.id, $company.data('edit-word'));

            }

        }
    })
    .on('typeahead:asyncrequest', function() {
        var typeheadContainer = $company.parents('.twitter-typeahead-container');
        var loading = typeheadContainer.find('.fa-loading');
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

    $addCompany.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var url = $this.data('url');

        var options = {
            url: url
        };

        widget.ajax.get([$this],  [$company], null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){


                var item = widget.json.toJson(data).company;
                $companyHidden.val(item.id);
                $companyHidden.data(displayField, item[displayField]);
                $company.val(item[displayField]);
                func.updatePreHint(item[displayField]);
                func.clearTypeaheadSuggestion();

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });

    });

    $form.find( cls.input.tag ).each(function(){


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

    });

    $form.on('click', '.company-edit', function(event){

        event.preventDefault();

        var $this = $(this);
        var url = $this.data('url');

        var options = {
            url: url
        };

        widget.ajax.get([$this], [$company], null, options, function (data, textStatus, jqXHR) {

            widget.ajax.formInModal($(data), {}, true,  function (data, textStatus, jqXHR){


                var item = widget.json.toJson(data).company;
                $companyHidden.val(item.id);
                $companyHidden.data(displayField, item[displayField]);
                $company.val(item[displayField]);
                func.updatePreHint(item[displayField]);
                func.clearTypeaheadSuggestion();

            });

        }, function(jqXHR, textStatus, errorThrown){

            widget.notify(jqXHR);

        }, function(jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){



        });


    });




});