$(function() {


    var $form = $('.lead-form');
    var $submit = $form.find('.submit');

    $form.find('.twitter-typeahead-user').each(function(){

        var $this = $(this);
        var target = $this.data('target');
        var $target =  $(sprintf('.%s_hidden', target));
        var displayField = 'name';

        var dataSource = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: sprintf('%s?query=%s',  $this.data('url'), '%QUERY'),
                wildcard: '%QUERY'
            }
        });

        $this.typeahead({highlight : true, hint : true, minLength : 1}, {
            name: 'users',
            display: displayField,
            limit: 41,
            source: dataSource,
            templates : {

                notFound: sprintf('<div class="empty">%s</div>', $this.data('no-found')),
                suggestion: function(item){

                    return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details member-skin"><div class="name">%s</div><div class="username">%s</div><div class="email">%s</div><div class="role">%s</div><div class="company">%s</div></div></a></div>',  item.avatar, item.name, item.username_alias, item.email, item.role_name, item.company);


                }

            }
        })
        .on('typeahead:asyncrequest', function() {
            var typeheadContainer = $this.parents('.twitter-typeahead-container');
            var loading = typeheadContainer.find('.fa-loading');
            if(loading.length <= 0) {
                $this.parents('.twitter-typeahead-container').append(skin.loading.sm);
            }

        })
        .on('typeahead:asynccancel typeahead:asyncreceive', function() {
            var loading = $this.parents('.twitter-typeahead-container').find('.fa-loading');
            if(loading.length > 0) {
                loading.remove();
            }
        })
        .on('typeahead:select', function(event, item) {



            $target.val(item.id);
            $target.data(displayField, item[displayField]);


        })
        .on('typeahead:change', function(event, item) {

            if( $.trim(item) !=  $.trim( $target.data(displayField)) ) {
                $target.val('');
                $target.data(displayField, '');
            }
        });

    })

    $form.find('.activity').click(function(e){
        e.preventDefault();
        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        widget.popup.open(url, title);
    })

    $submit.click(function(e){

        e.preventDefault();

        var proceed = true;
        var $status = $form.find('.status');
        var $route = $form.find(sprintf('.route.%s', $status.val()));
        var confirmMessage = $route.data('confirm-message');

        var $modelFooter = $form.find('.modal-prompt-footer');
        var $remarkContainer = $form.find('._remark-container');
        var $remark = $form.find('._remark');

        if($route.length > 0){

            if(confirmMessage){
                proceed = confirm(confirmMessage);
            }

        }


        if(!proceed){

            return false;

        }else{

            var $modal = $(skin.modal.simple('', $remarkContainer.get(0).outerHTML, $modelFooter.html()));
            var $modalRemark = $modal.find('._remark');
            var $modelValidationErrorContainer = $modal.find('._remark-validation-container');
            var remarkValidationMessage = $modalRemark.data('validation-message');

            $modalRemark.val($remark.val());

            $modal.modal('show');

            $modal.on('hidden.bs.modal', function(e){
                $remark.val($modalRemark.val());
                $(this).remove();
            })

            $modal.on('click', '.modal-cancel', function(e){
                e.preventDefault();
                $modal.modal('hide');
            });

            $modal.on('click', '.modal-submit', function(event){

                event.preventDefault();



                if(!$modalRemark.val()){


                    $remark.val($modalRemark.val());
                    $modelValidationErrorContainer.html(skin.alert.error( remarkValidationMessage ));

                }else{

                    $remark.val($modalRemark.val());
                    $form.submit();

                }

            });

        }

    })

});