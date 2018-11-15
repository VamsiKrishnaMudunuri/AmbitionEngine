var leadStorage = {

        cls : {
            'form' : 'lead-form-edit'
        },

        skeys: {
            'siteVisit': 'booking-site-visit',
            'subscription': 'subscription'
        },

        populateViewState : function(windowOpener){

            var inputData = {};

            if(windowOpener) {

                var $formEdit = windowOpener.$(sprintf('.%s', this.cls.form));
                var $inputs = $formEdit.find('input, select, textarea');

                $inputs.each(function(){

                    var $this = $(this);
                    var name = $this.attr('name');

                    if(name == '_token'){
                        return;
                    }

                    var val = $this.val();

                    if(name){
                        inputData[name] = val;
                    }



                });

            }

            return inputData;
        },

        populateForm: function(data){

            if(data){

                var $formEdit = $(sprintf('.%s', this.cls.form));
                for (var key in data){
                    var $input = $formEdit.find(sprintf('[name="%s"]', key));
                    if($input.length > 0){
                        $input.val(data[key]);
                    }
                }


            }

        },

        populateBookingSiteVisitFormFromViewState: function(){

            var data = this.getBookingSiteVisit();

            this.populateForm(data);

        },

        populateSubscriptionFormFromViewState: function(){

            var data = this.getSubscription();

            this.populateForm(data);
        },

        setBookingSiteVisit: function(data){

            window.localStorage.setItem(this.skeys.siteVisit, JSON.stringify(data));

        },

        getBookingSiteVisit: function(){

            return JSON.parse(window.localStorage.getItem(this.skeys.siteVisit));

        },

        setSubscription: function(data){

            window.localStorage.setItem(this.skeys.subscription, JSON.stringify(data));

        },

        getSubscription: function(){

            return JSON.parse(window.localStorage.getItem(this.skeys.subscription));

        },

        clear: function(skey){

            window.localStorage.removeItem(skey);

        },

        clearAll: function(){

            for(var key in this.skeys){

                window.localStorage.removeItem(this.skeys[key]);

            }
        }


};