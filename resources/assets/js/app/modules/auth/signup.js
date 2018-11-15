$(function() {

    var $module = $('.auth-signup');
    var $propertySelect = $module.find('.property-select');
    var $propertyChosen = $module.find('.property-chosen');
    var $propertyPackage = $module.find('.property-package');
    var $signupTab = $module.find('[data-tab="sign-up-form"]');
    var $packageTab = $module.find('[data-tab="package-form"]');
    var $paymentTab = $module.find('[data-tab="payment-form"]');
    var $signupForm = $module.find('.sign-up-form');
    var $packageForm = $module.find('.package-form');
    var $paymentForm = $module.find('.payment-form');
    var $infoBox = $module.find('.info-box');
    var $welcomeBox = $module.find('.welcome-box');
    var $companyHidden = $signupForm.find('.company-hidden');
    var $company = $signupForm.find('.company');
    var $order = $('.order');


    var func = {
        prev: function($this){
            var $prev = $this;
            var $form = $prev.closest('form');
            var prev = $prev.data('prev');

            $li = $module.find('.tabs li[data-tab="' + prev + '"]');

            if($li.length > 0) {
                $module.find('.tabs li.active').addClass('visited');
                $module.find('.tabs li').removeClass('active');
                $li.addClass('active');
                $li.addClass('visited');
            }

            $form.hide();
            if(prev) {
                $prevForm = $module.find('form.' + prev);
                if ($prevForm.length > 0) {
                    $prevForm.show();
                }
            }


        },
        submit : function($this, done){

            $submit = $this;
            var $form = $submit.closest('form');
            var $prev = $form.find('.prev');
            var disabled = [];
            $messageBox = $form.find('.message-box');


            if($prev.length > 0){

                disabled.push($prev);
            }

            var options = {
                url: $form.attr('action'),
                data: $form.serialize(),
                beforeSend: function(jqXHR, settings){

                    $module.find('.tabs li a').attr('disabled', true);

                    if($paymentForm.is($form)){
                        $infoBox.show();
                    }



                }

            };

            widget.ajax.form($form, $form, $submit, disabled, $messageBox, options, function(data, textStatus, jqXHR) {


                if(done){

                    done(data, textStatus, jqXHR)

                }else{

                    var next = $submit.data('next');
                    $li = $module.find('.tabs li[data-tab="' + next + '"]');

                    if($li.length > 0) {
                        $module.find('.tabs li.active').addClass('visited');
                        $module.find('.tabs li').removeClass('active');
                        $li.addClass('active');
                        $li.addClass('visited');
                    }

                    $form.hide();
                    if(next) {
                        $nextForm = $module.find('form.' + next);
                        if ($nextForm.length > 0) {
                            $nextForm.show();
                        }
                    }
                }


            }, function(jqXHR, textStatus, errorThrown){



            }, function(firstJqXHR, firstTextStatus, firstError){

            }, function(firstJqXHR, firstTextStatus, firstError, hasError){

                if($paymentForm.is($form)){
                    $infoBox.hide();
                }

                if(hasError){

                    $module.find('.tabs li a').attr('disabled', false);

                    if($paymentForm.is($form)){
                        var response = window.widget.response.json(firstJqXHR);
                        if($signupForm.data('table') in response){
                            window.widget.error.show(firstJqXHR, $signupForm.find('.message-box'), $signupForm);
                            $module.find('.tabs li').removeClass('active');
                            $signupTab.addClass('active');
                            $module.find('form').hide();
                            $module.find('form.' + $signupTab.data('tab')).show();
                        }else if($packageForm.data('table') in response){
                            window.widget.error.show(firstJqXHR, $packageForm.find('.message-box'), $packageForm);
                            $module.find('.tabs li').removeClass('active');
                            $packageTab.addClass('active');
                            $module.find('form').hide();
                            $module.find('form.' + $packageTab.data('tab')).show();
                        }
                    }
                }else{

                    if($paymentForm.is($form)){

                        widget.link.loading.add($welcomeBox);
                        var loginUrl = $welcomeBox.data('url');
                        $welcomeBox.show();

                        setTimeout(function(){
                            window.location.href = loginUrl;
                        }, 5000)

                    }else{

                        $module.find('.tabs li a').attr('disabled', false);

                    }

                }

            }, false)
        }
    }


    /**
    $module.on('click', '.tabs li.visited a:not(:disabled)', function(event){

        event.preventDefault();

        $a = $(this);
        $currentLi = $module.find('.tabs li.active');
        $currentForm = $module.find('form.' +  $currentLi.data('tab'));
        $nextLi = $a.parent('li');

        if($paymentForm.is($currentForm) || !$currentLi.hasClass('visited')){

            $module.find('.tabs li').removeClass('active');
            $nextLi.addClass('active');

            $module.find('form').hide();
            $nextForm = $module.find('form.' + $nextLi.data('tab'));
            if($nextForm.length > 0){
              $nextForm.show();
            }

        }else{

            func.submit($currentForm.find('.submit'), function(data, textStatus, jqXHR){

                $module.find('.tabs li').removeClass('active');
                $nextLi.addClass('active');

                $module.find('form').hide();

                $nextForm = $module.find('form.' + $nextLi.data('tab'));
                if($nextForm.length > 0){
                    $nextForm.show();
                }


            });


        }




    })

   **/

    $signupForm.find('.submit').click(function(event){

        event.preventDefault();



        func.submit($(this));



    })

    $packageForm.find('.prev').click(function(event){

        event.preventDefault();

        func.prev($(this));

    });

    $packageForm.find('.submit').click(function(event){

        event.preventDefault();


        func.submit($(this));



    })

    $paymentForm.find('.prev').click(function(event){

        event.preventDefault();

        func.prev($(this));

    });

    $propertySelect.change(function(event){

        event.preventDefault();

        var $this = $(this);
        var $form = $this.closest('form');
        var $prev = $form.find('.prev');
        var $submit = $form.find('.submit');
        var id = $this.val();

        if(id) {

            var $data;
            $messageBox = $propertyPackage.prev('.package-message-box');

            var options = {
                url: $propertyPackage.data('url') + '/' + id,
                beforeSend: function(jqXHR, settings){
                    $propertyPackage.html('');
                }
            };

            widget.ajax.get($this, [$prev, $submit], $messageBox, options, function (data, textStatus, jqXHR) {

                $data = $(data);
                $data.hide();

            }, function (jqXHR, textStatus, errorThrown) {


            }, function (jqXHR, textStatus, error){

            }, function(jqXHR, textStatus, error, hasError){

                if(!hasError) {
                    $data.show();
                    $propertyPackage.append($data);
                }

            });

        }


    })

    $propertyPackage.on('click', 'tr.package', function(event){

        $this = $(this);
        $radios = $this.find('input:radio:not(:disabled)');

        if($radios.length > 0) {
            $radios.prop("checked", true);
            $radios.trigger('change');
        }

    })

    $propertyPackage.on('change', 'tr.package input:radio:not(:disabled)', function(event){

        var $this = $(this);
        var $form = $this.closest('form');
        var $prev = $form.find('.prev');
        var $submit = $form.find('.submit');
        var url = $this.data('url');

        var $data;
        $summary = $order.find('.summary');
        $messageBox =  $summary.find('.order-message-box');
        $tableEmpty =  $summary.find('.table-empty');
        $tableNoEmpty =  $summary.find('.table-no-empty');

        $this.data('location-loading', $summary.data('location-loading-place'));

        var options = {
            url: url,
            beforeSend: function(jqXHR, settings){
                $propertyChosen.val('');
                $tableEmpty.hide();
                $tableNoEmpty.html('');
            }
        };

        widget.ajax.get($this, [$propertySelect, $propertyPackage.find('tr.package input:radio:not(:disabled)'), $prev, $submit], $messageBox, options, function (data, textStatus, jqXHR) {

            $data = $(data);
            $data.hide();


        }, function (jqXHR, textStatus, errorThrown) {


        }, function (jqXHR, textStatus, error){

        }, function(jqXHR, textStatus, error, hasError){

            if(!hasError) {
                $propertyChosen.val($this.val());
                $data.show();
                $tableNoEmpty.append($data);
            }else{
                $tableEmpty.show();
            }

        });



    });

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

    braintreePayment.initialize($paymentForm.get(0), [$signupForm.find('.submit').get(0)], [$propertySelect.get(0)] );

    braintreePayment.submit(null, function(){

        var office = cs.getQueryString('office');

        if(office) {
            $propertySelect.val(office);
            $propertySelect.trigger('change');
        }


    }, function(){
        $module.find('.tabs li a').attr('disabled', true);
    }, function(){
        $module.find('.tabs li a').attr('disabled', false);
    }, function(){
        func.submit($paymentForm.find('.submit'));
    });


});