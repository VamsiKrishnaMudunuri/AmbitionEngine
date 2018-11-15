$(function() {


    var $module = $('.admin-managing-lead-edit');
    var $form = $module.find('.lead-form-edit');

    var $status = $form.find('.status');
    var $addSiteVisit = $form.find('.add-site-visit');
    var $editSiteVisit = $form.find('.edit-site-visit');
    var $deleteSiteVisit = $form.find('.delete-site-visit');
    var $addMember = $form.find('.add-member');
    var $userHidden = $form.find('.user_id_hidden');
    var $addSubscription = $form.find('.add-subscription');
    var $addSubscriptionRight = $addSubscription.data('right');
    var $subscriptionShowPrice = $form.find('.subscription-show-price');
    var $voidSubscription = $form.find('.void-subscription');

    var popupWidth = 1024;
    var popupHeight = 768;

    var func = {

        init: function(){
            this.updateRoute();
            this.updateStatus();

            if($addSubscriptionRight) {
                if ($userHidden.val() > 0) {
                    this.enableSubscription()
                } else {
                    this.disableSubscription();
                }
            }
        },

        updateRoute: function(){

            var $route = $form.find(sprintf('.route.%s', $status.val()));

            if($route.length > 0){
                $form.attr('action', $route.data('url'));
            }

        },
        updateStatus: function(){

            var selectedVal = $status.val();
            var $leadBody = $form.find('.lead-body-status');
            var $leadBodyTarget = $form.find(sprintf('.lead-body-%s', selectedVal));

            if($leadBodyTarget.length > 0){
                $leadBody.hide();
                $leadBodyTarget.show();
            }


        },
        enableSubscription: function(){
            $addSubscription.attr('disabled', false);
        },
        disableSubscription: function () {
            $addSubscription.attr('disabled', true);
        }
    };

    if(window.performance.navigation.type != 1){
        leadStorage.clearAll();
    }

    leadStorage.populateBookingSiteVisitFormFromViewState();
    leadStorage.populateSubscriptionFormFromViewState();
    leadStorage.clearAll();

    func.init();

    $status.change(function(event){
        event.preventDefault();
        $this = $(this);
        func.updateRoute();
        func.updateStatus();

    })

    $addSiteVisit.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        widget.popup.open(url, title, popupWidth, popupHeight);

    });

    $editSiteVisit.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        widget.popup.open(url, title, popupWidth, popupHeight);

    });

    $deleteSiteVisit.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var confirmMessage = $this.data('confirm-message');
        var title = $this.data('title');
        var url = $this.data('url');

        var options = {
            url: url,
            data: {_method: 'delete'}
        };

        if(confirm(confirmMessage)) {

            widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {

                $this.parents('tr').remove();

            }, function (jqXHR, textStatus, errorThrown) {

                widget.notify(jqXHR);

            }, function (jqXHR, textStatus, error) {

            }, function (jqXHR, textStatus, error, hasError) {


            });

        }

    });

    $form.find('.twitter-typeahead-user-with-management').each(function(){

        var $this = $(this);
        var target = $this.data('target');
        var $target =  $(sprintf('.%s_hidden', target));
        var displayField = 'name';
        var isMemberWriteRight = $this.data('member-write-right');
        var editTitle = $this.data('edit-title');
        var editWord = $this.data('edit-word');
        var editUrl = $this.data('edit-url');

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

                    var disabledEdit = (isMemberWriteRight) ? '' : 'disabled';

                    return sprintf('<div class="card"><a href="javascript:void(0);"><div class="profile-photo circle"><div class="frame"><img src="%s" /></div></div><div class="details member-skin"><div class="name">%s</div><div class="username">%s</div><div class="email">%s</div><div class="role">%s</div><div class="company">%s</div></div><div class="menu"><span class="edit-member %s" %s data-title="%s" data-url="%s">%s</span></div></a></div>',  item.avatar, item.name, item.username_alias, item.email, item.role_name, item.company, isMemberWriteRight, isMemberWriteRight,  editTitle, editUrl + '/' + item.id, editWord);


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
            func.enableSubscription();


        })
        .on('typeahead:change', function(event, item) {

            if( $.trim(item) !=  $.trim( $target.data(displayField)) ) {
                $target.val('');
                $target.data(displayField, '');
                func.disableSubscription();
            }
        });

    })

    $addMember.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        widget.popup.open(url, title, popupWidth, popupHeight);

    });

    $form.on('click', '.edit-member', function(event){

        event.preventDefault();

        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        widget.popup.open(url, title, popupWidth, popupHeight);

    });

    $subscriptionShowPrice.click(function(e){
        e.preventDefault(); $(this).next('.modal').modal('show');
    })

    $addSubscription.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var title = $this.data('title');
        var url = $this.data('url');
        url += '/' + $userHidden.val();
        widget.popup.open(url, title, popupWidth, popupHeight);

    });

    $voidSubscription.click(function(event){

        event.preventDefault();

        var $this = $(this);
        var confirmMessage = $this.data('confirm-message');
        var title = $this.data('title');
        var url = $this.data('url');

        var options = {
            url: url
        };

        if(confirm(confirmMessage)) {

            widget.ajax.post($this, null, null, options, function (data, textStatus, jqXHR) {


            }, function (jqXHR, textStatus, errorThrown) {

                widget.notify(jqXHR);

            }, function (jqXHR, textStatus, error) {

            }, function (jqXHR, textStatus, error, hasError) {

                if(!hasError){


                    leadStorage.setSubscription(leadStorage.populateViewState(window));
                    location.reload();

                }

            });

        }

    });


});