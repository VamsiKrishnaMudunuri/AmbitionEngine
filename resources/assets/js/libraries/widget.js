var widget = {

    jquery: {
        browser: function(){
            var matched, browser;

            jQuery.uaMatch = function( ua ) {
                ua = ua.toLowerCase();

                var match = /(chrome)[ \/]([\w.]+)/.exec( ua ) ||
                    /(webkit)[ \/]([\w.]+)/.exec( ua ) ||
                    /(opera)(?:.*version|)[ \/]([\w.]+)/.exec( ua ) ||
                    /(msie)[\s?]([\w.]+)/.exec( ua ) ||
                    /(trident)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
                    ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec( ua ) ||
                    [];

                return {
                    browser: match[ 1 ] || "",
                    version: match[ 2 ] || "0"
                };
            };

            matched = jQuery.uaMatch( navigator.userAgent );
            //IE 11+ fix (Trident)
            matched.browser = matched.browser == 'trident' ? 'msie' : matched.browser;
            browser = {};

            if ( matched.browser ) {
                browser[ matched.browser ] = true;
                browser.version = matched.version;
            }

            // Chrome is Webkit, but Webkit is also Safari.
            if ( browser.chrome ) {
                browser.webkit = true;
            } else if ( browser.webkit ) {
                browser.safari = true;
            }

            jQuery.browser = browser;
        }
    },

    link: {

        loading: {

            skin: skin.loading.sm,
            loading: '.fa-loading',
            skinLocation: skin.locationLoading,
            loadingLocation: '.location-loading',
            skinPage: skin.pageLoading,
            loadingPage: '.page-loading',
            add: function(element){
                if(element.length > 0) {

                    element.attr("disabled", true);

                    if (element.data('page-loading')) {

                        $('body').append($(this.skinPage));

                    }else if (element.data('location-loading')){

                        $('[data-location-loading-place="' + element.data('location-loading') + '"]').prepend($(this.skinLocation));

                    }else if (element.data('inline-loading')){

                        $('[data-inline-loading-place="' + element.data('inline-loading') + '"]').append($(this.skin));

                    }else{
                        element.append($(this.skin));
                    }

                }
            },
            remove: function(element){
                if(element.length > 0) {
                    element.attr("disabled", false);

                    if(element.data('page-loading')){
                        $skin = $('body').find(this.loadingPage);

                    }else if (element.data('location-loading')){

                        $skin = $('[data-location-loading-place="' + element.data('location-loading') + '"]').find(this.loadingLocation);

                    }else if (element.data('inline-loading')){

                        $skin = $('[data-inline-loading-place="' + element.data('inline-loading') + '"]').find(this.loading);

                    }else {
                        $skin = element.find(this.loading);

                    }

                    if ($skin.length > 0) {
                        $skin.remove();
                    }

                }
            }
        }

    },

    json: {

        isJson: function(str){

            var flag = false;


            try {

                $.parseJSON(str);
                flag = true;

            } catch (e) {

            }


            return flag;

        },

        toJson: function(str){

            var val = {};


            try {

                val = $.parseJSON(str);

            } catch (e) {

            }


            return val;
        }

    },

    response: {
        key: {
            version : 'version',
            error: 'errors'
        },
        json: function(jqXHR){

            var response = {};

            try{

                if(jqXHR.hasOwnProperty('alternativeResponseText')){
                    response = window.widget.json.toJson(jqXHR.alternativeResponseText);
                }else{
                    response = window.widget.json.toJson(jqXHR.responseText);
                }


            }catch(err){

            }

            return response;

        },

        jsonOrString: function(jqXHR){

            var response;

            try{

                if(jqXHR.hasOwnProperty('alternativeResponseText')){
                    if(!window.widget.json.isJson(jqXHR.alternativeResponseText)){

                        response = jqXHR.alternativeResponseText;

                    }else{
                        response = window.widget.json.toJson(jqXHR.alternativeResponseText);
                    }

                }else{

                    if(!window.widget.json.isJson(jqXHR.responseText)){

                        response = jqXHR.responseText;

                    }else{
                        response = window.widget.json.toJson(jqXHR.responseText);
                    }


                }


            }catch(err){

            }

            return response;

        },

        code: function(jqXHR){
            return jqXHR.status;
        },

        hasError: function(jqXHR){

            var code = '200';

            if(typeof jqXHR !== 'string' && jqXHR.promise){
                code = this.code(jqXHR);
            }

            return code != 200;

        },

        hasVersionError: function(jqXHR){

            var flag = false;
            var response = this.json(jqXHR);
            var code = this.code(jqXHR);

            if(response && code == 422){

                flag = this.key.version in response;

            }

            return flag;

        },

        hasGenericError: function(jqXHR){

            var flag = false;
            var response = this.json(jqXHR);
            var code = this.code(jqXHR);

            if(response && code == 422){

                flag = this.key.error in response;

            }

            return flag;

        }

    },

    error: {

        key: 'alert-danger',

        skin: 'alert-skin',

        get: function(domElement){
            var dom = domElement || $('body');
            return dom.find('.' + this.key);
        },

        show: function(jqXHR, header, form, isNeedMessageOnly){

            var code = window.widget.response.code(jqXHR)
            var response = window.widget.response.jsonOrString(jqXHR);


            switch(code){

                case 422:

                    if(response) {

                        if(window.widget.response.hasVersionError(jqXHR)){
                            if(isNeedMessageOnly){
                                return  $(skin.message.error(response[window.widget.response.key.version]));
                            }else {
                                var template = $(skin.alert.error(response[window.widget.response.key.version], header.data(this.skin)));
                                header.after(template);
                            }
                        }else if(window.widget.response.hasGenericError(jqXHR)) {
                            if(isNeedMessageOnly){
                                return  $(skin.message.error(response[window.widget.response.key.error]));
                            }else {
                                var template = $(skin.alert.error(response[window.widget.response.key.error], header.data(this.skin)));
                                header.after(template);
                            }
                        }else {
                            var other = {};
                            for (var model in response) {

                                for(var index in response[model] )
                                {
                                    for(var field in response[model][index] )
                                    {
                                        if(isNeedMessageOnly){
                                            other[field] = response[model][index][field];
                                        }else{
                                            var template = $(skin.alert.error(response[model][index][field], form.data(this.skin)));

                                            var element = (form != null && form.length > 0) ? form.find("[data-validation-name='" + field + "']") : '';

                                            if(element.length <= 0){
                                                element = (form != null && form.length > 0) ? form.find('[name="' + model + '[' + field + ']' + '"]') : '';
                                            }

                                            if(element.length <= 0){
                                                element = (form != null && form.length > 0) ? form.find('[name="' + field + '"]') : '';
                                            }

                                            if (element.length > 0) {
                                                element.before(template);
                                            } else {
                                                other[field] = response[model][index][field];
                                            }
                                        }

                                    }

                                }

                            }

                            if(Object.keys(other).length > 0){

                                if(isNeedMessageOnly){
                                    return  $(skin.message.error(other));
                                }else {
                                    var template = $(skin.alert.error(other), header.data(this.skin));
                                    header.after(template);
                                }

                            }

                        }
                    }


                    break;

                default:

                    if(isNeedMessageOnly){
                        return $(skin.message.error(response));
                    }else {
                        var template = $(skin.alert.error(response, header.data(this.skin)));
                        header.after(template);
                    }

                    break;

            }

        },

        message: function(jqXHR){
            return this.show(jqXHR, null, null, true);
        },

        notify: function(jqXHR){

            if(widget.response.hasError(jqXHR)){

                $.notify(this.message(jqXHR)[0].outerHTML, {
                    type: 'danger'
                });

            }

        }

    },

    notify: function(jqXHR, data){

        if(widget.response.hasError(jqXHR)){

            $.notify(widget.error.message(jqXHR)[0].outerHTML, {
                type: 'danger'
            });

        }else{

            if(data){

                $.notify(data, {
                    type: 'success'
                });

            }

        }

    },

    alert: {
        clear: function(){
            $('.alert').remove();
        },

        smartClear: function(){
            $('.alert').not('.alert-keep').remove();
        }
    },

    badge: {

        nice: {
            key: 'nice-badge',

            has: function(element){
                return (element.find('.' + this.key).length > 0) ? true : false;
            },

            addIfNecessary: function(element, count){

                var badge = '';

                if(this.has(element)){

                    badge = element.find('.' + this.key);

                    if(count > 0){
                        badge.html(count);
                    }else{
                        badge.remove();
                    }

                }else{

                    badge = window.skin.badge.nice(count);

                    if(badge){
                        element.append(badge)
                    }

                }

            }

        }

    },

    getXsrfToken : function() {

        var cookies = document.cookie.split(';');
        var token = '';

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].split('=');
            if(cookie[0].trim() == 'XSRF-TOKEN') {
                token = decodeURIComponent(cookie[1]);
            }
        }

        return token;
    },

    getXSocketId : function() {

        var cookies = document.cookie.split(';');
        var token = '';

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i].split('=');
            if(cookie[0].trim() == 'X-Socket-Id') {
                token = decodeURIComponent(cookie[1]);
            }
        }

        return token;
    },

    xhr: {

        form: function(parent, form, clickElements, disabledElements, errorElement, options, done, fail, always, clean, isFormReset){

            var beforeSend = null;
            var clickElems = [];
            var disabledElems = [];

            if(clickElements !== null) {
                if (clickElements instanceof Array) {
                    clickElems = clickElements;
                } else {
                    clickElems.push(clickElements);
                }
            }

            if(disabledElements !== null) {
                if (disabledElements instanceof Array) {
                    disabledElems = disabledElements;
                } else {
                    disabledElems.push(disabledElements);
                }
            }

            if (options && options.beforeSend){
                beforeSend = options.beforeSend;
                delete options.beforeSend;
            }

            var _default = {
                url: '',
                type: 'POST',
                data: '',
                dataType: '',
                withCredentials: false,
                asynchronous: true,
                cache: true
            };

            $.extend(_default, options);

            var xhr = new XMLHttpRequest();
            xhr.withCredentials = _default.withCredentials;
            xhr.responseType = _default.dataType;
            xhr.open(_default.type, _default.url, _default.asynchronous);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('X-XSRF-TOKEN', widget.getXsrfToken());

            var socket_id = widget.getXSocketId();
            if(socket_id){
                xhr.setRequestHeader('X-Socket-Id', socket_id);
            }

            if(beforeSend){

                beforeSend(xhr, _default);

            }

            for(i = 0; i < clickElems.length; i++){
                window.widget.link.loading.add(clickElems[i]);
            }

            for(i = 0; i < disabledElems.length; i++){
                disabledElems[i].attr("disabled", true);
            }

            window.widget.alert.smartClear();

            xhr.onload = function(e) {

                var jqXHR = this;

                jqXHR.promise = true;

                if (jqXHR.status == 200) {

                    done(jqXHR.response, jqXHR.statusText, jqXHR);
                    complete(jqXHR, jqXHR.statusText, jqXHR.response.error);

                }else{

                    var responseType = jqXHR.responseType || '';

                    if(responseType.toLowerCase() == 'blob'){

                        var reader = new FileReader();
                        reader.addEventListener('load', function() {

                            jqXHR.alternativeResponse = reader.result;
                            jqXHR.alternativeResponseText = reader.result;

                            fail(jqXHR, jqXHR.statusText, jqXHR.response.error);
                            complete(jqXHR, jqXHR.statusText, jqXHR.response.error);

                        }, false);

                        reader.readAsBinaryString(jqXHR.response);

                    }else{

                        fail(jqXHR, jqXHR.statusText, jqXHR.response.error);
                        complete(jqXHR, jqXHR.statusText, jqXHR.response.error);

                    }

                }



            };

            function complete(firstJqXHR, firstTextStatus, firstError) {

                if(typeof grecaptcha != 'undefined'){

                    try {

                        grecaptcha.reset();

                    }catch (e){

                    }

                }

                if (errorElement && window.widget.response.hasError(firstJqXHR)) {

                    if (window.widget.response.hasVersionError(firstJqXHR)) {

                        window.widget.ajax.get(null, null, null, {url: _default.url}, function (data, textStatus, jqXHR) {

                            var $data = $(data);
                            var errElement = $data.find(errorElement.selector);
                            window.widget.error.show(firstJqXHR, errElement);
                            parent.replaceWith($data);


                        }, function (jqXHR, textStatus, errorThrown) {


                        }, function (jqXHR, textStatus, error) {

                            if (always) {
                                always(firstJqXHR, firstTextStatus, firstError)
                            }

                            for(i = 0; i < disabledElems.length; i++){
                                disabledElems[i].attr("disabled", false);
                            }
                            for(i = 0; i < clickElems.length; i++){
                                window.widget.link.loading.remove(clickElems[i]);
                            }

                            if (clean) {
                                clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                            }

                        });

                    } else {

                        if (always) {
                            always(firstJqXHR, firstTextStatus, firstError)
                        }

                        window.widget.error.show(firstJqXHR, errorElement, form);

                        for(i = 0; i < disabledElems.length; i++){
                            disabledElems[i].attr("disabled", false);
                        }
                        for(i = 0; i < clickElems.length; i++){
                            window.widget.link.loading.remove(clickElems[i]);
                        }

                        if (clean) {
                            clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                        }
                    }

                } else {

                    if (always) {
                        always(firstJqXHR, firstTextStatus, firstError)
                    }

                    for(i = 0; i < disabledElems.length; i++){
                        disabledElems[i].attr("disabled", false);
                    }
                    for(i = 0; i < clickElems.length; i++){
                        window.widget.link.loading.remove(clickElems[i]);
                    }

                    if (clean) {
                        clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                    }

                    if(!window.widget.response.hasError(firstJqXHR)) {
                        if (typeof isFormReset == 'undefined' || (typeof isFormReset == "boolean" && isFormReset)) {
                            if (form.length > 0) {
                                form[0].reset();
                            }
                        }
                    }
                }
            }

            xhr.send(_default.data);

        }

    },

    ajax: {

        get: function(clickElements, disabledElements, errorElement, options, done, fail, always, clean){

            var beforeSend = null;
            var clickElems = [];
            var disabledElems = [];

            if(clickElements !== null) {
                if (clickElements instanceof Array) {
                    clickElems = clickElements;
                } else {
                    clickElems.push(clickElements);
                }
            }

            if(disabledElements !== null) {
                if (disabledElements instanceof Array) {
                    disabledElems = disabledElements;
                } else {
                    disabledElems.push(disabledElements);
                }
            }

            if (options && options.beforeSend){
                beforeSend = options.beforeSend;
                delete options.beforeSend;
            }

            var _default = {
                url: '',
                type: 'GET',
                data: '',
                dataType: 'html',
                cache: true,
                headers: {},
                beforeSend: function(jqXHR, settings){

                    if(beforeSend){
                        beforeSend(jqXHR, settings);
                    }

                    for(i = 0; i < clickElems.length; i++){
                        window.widget.link.loading.add(clickElems[i]);
                    }

                    for(i = 0; i < disabledElems.length; i++){
                        disabledElems[i].attr("disabled", true);
                    }

                    if(errorElement != null) {
                        window.widget.alert.smartClear();
                    }


                }
            }

            var socket_id = widget.getXSocketId();
            if(socket_id){
                _default.headers['X-Socket-Id'] = socket_id;
            }
            _default.headers['X-XSRF-TOKEN'] = widget.getXsrfToken();

            $.extend(_default, options);

            $.ajax(_default).done(function(data, textStatus, jqXHR) {

                if(done){
                    done(data, textStatus, jqXHR)
                }

            }).fail(function(jqXHR, textStatus, errorThrown){


                if(fail){
                    fail(jqXHR, textStatus, errorThrown)
                }

                if(errorElement) {
                    window.widget.error.show(jqXHR, errorElement);
                }

            }).always(function(jqXHR, textStatus, error){

                if(always){
                    always(jqXHR, textStatus, error)
                }
                for(i = 0; i < disabledElems.length; i++){
                    disabledElems[i].attr("disabled", false);
                }
                for(i = 0; i < clickElems.length; i++){
                    window.widget.link.loading.remove(clickElems[i]);
                }

                if(clean){
                    clean(jqXHR, textStatus, error, window.widget.response.hasError(jqXHR));
                }

            });
        },

        post: function(clickElements, disabledElements, errorElement, options, done, fail, always, clean){

            var beforeSend = null;
            var clickElems = [];
            var disabledElems = [];

            if(clickElements !== null) {
                if (clickElements instanceof Array) {
                    clickElems = clickElements;
                } else {
                    clickElems.push(clickElements);
                }
            }

            if(disabledElements !== null) {
                if (disabledElements instanceof Array) {
                    disabledElems = disabledElements;
                } else {
                    disabledElems.push(disabledElements);
                }
            }

            if (options && options.beforeSend){
                beforeSend = options.beforeSend;
                delete options.beforeSend;
            }

            var _default = {
                url: '',
                type: 'POST',
                data: '',
                dataType: 'html',
                cache: true,
                headers: {},
                beforeSend: function(jqXHR, settings){

                    if(beforeSend){
                        beforeSend(jqXHR, settings);
                    }

                    for(i = 0; i < clickElems.length; i++){
                        window.widget.link.loading.add(clickElems[i]);
                    }

                    for(i = 0; i < disabledElems.length; i++){
                        disabledElems[i].attr("disabled", true);
                    }

                    if(errorElement != null) {
                        window.widget.alert.smartClear();
                    }

                }
            }

            var socket_id = widget.getXSocketId();
            if(socket_id){
                _default.headers['X-Socket-Id'] = socket_id;
            }
            _default.headers['X-XSRF-TOKEN'] = widget.getXsrfToken();
            _default.headers['X-Requested-With'] = 'XMLHttpRequest';

            $.extend(_default, options);

            $.ajax(_default).done(function(data, textStatus, jqXHR) {

                if(done){
                    done(data, textStatus, jqXHR)
                }

            }).fail(function(jqXHR, textStatus, errorThrown){


                if(fail){
                    fail(jqXHR, textStatus, errorThrown)
                }

                if(errorElement) {
                    window.widget.error.show(jqXHR, errorElement);
                }

            }).always(function(jqXHR, textStatus, error){

                if(always){
                    always(jqXHR, textStatus, error)
                }
                for(i = 0; i < disabledElems.length; i++){
                    disabledElems[i].attr("disabled", false);
                }
                for(i = 0; i < clickElems.length; i++){
                    window.widget.link.loading.remove(clickElems[i]);
                }

                if(clean){
                    clean(jqXHR, textStatus, error, window.widget.response.hasError(jqXHR));
                }

            });
        },

        form: function(parent, form, clickElements, disabledElements, errorElement, options, done, fail, always, clean, isFormReset){

            var beforeSend = null;
            var clickElems = [];
            var disabledElems = [];

            if(clickElements !== null) {
                if (clickElements instanceof Array) {
                    clickElems = clickElements;
                } else {
                    clickElems.push(clickElements);
                }
            }

            if(disabledElements !== null) {
                if (disabledElements instanceof Array) {
                    disabledElems = disabledElements;
                } else {
                    disabledElems.push(disabledElements);
                }
            }

            if (options && options.beforeSend){
                beforeSend = options.beforeSend;
                delete options.beforeSend;
            }

            var _default = {
                url: '',
                type: 'POST',
                data: '',
                dataType: 'html',
                cache: true,
                headers: {},
                beforeSend: function(jqXHR, settings){

                    if(beforeSend){
                        beforeSend(jqXHR, settings);
                    }

                    for(i = 0; i < clickElems.length; i++){
                        window.widget.link.loading.add(clickElems[i]);
                    }
                    for(i = 0; i < disabledElems.length; i++){
                        disabledElems[i].attr("disabled", true);
                    }

                    window.widget.alert.smartClear();

                }
            }

            var socket_id = widget.getXSocketId();
            if(socket_id){
                _default.headers['X-Socket-Id'] = socket_id;
            }
            _default.headers['X-XSRF-TOKEN'] = widget.getXsrfToken();
            _default.headers['X-Requested-With'] = 'XMLHttpRequest';

            $.extend(_default, options);

            $.ajax(_default).done(function(data, textStatus, jqXHR) {

                if(done){
                    done(data, textStatus, jqXHR)
                }

            }).fail(function(jqXHR, textStatus, errorThrown){

                if(fail){
                    fail(jqXHR, textStatus, errorThrown)
                }

            }).always(function(firstJqXHR, firstTextStatus, firstError){

                if(typeof grecaptcha != 'undefined'){

                    try {

                        grecaptcha.reset();

                    }catch (e){

                    }

                }

                if(errorElement && window.widget.response.hasError(firstJqXHR)) {

                    if(window.widget.response.hasVersionError(firstJqXHR)){

                        window.widget.ajax.get(null, null, null, {url: _default.url}, function(data, textStatus, jqXHR) {

                            var $data = $(data);
                            var errElement = $data.find(errorElement.selector);
                            window.widget.error.show(firstJqXHR, errElement);
                            parent.replaceWith($data);


                        }, function(jqXHR, textStatus, errorThrown){


                        },function(jqXHR, textStatus, error){

                            if(always){
                                always(firstJqXHR, firstTextStatus, firstError)
                            }

                            for(i = 0; i < disabledElems.length; i++){
                                disabledElems[i].attr("disabled", false);
                            }
                            for(i = 0; i < clickElems.length; i++){
                                window.widget.link.loading.remove(clickElems[i]);
                            }

                            if(clean){
                                clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                            }

                        });

                    }else{

                        if(always){
                            always(firstJqXHR, firstTextStatus, firstError)
                        }

                        window.widget.error.show(firstJqXHR, errorElement, form);

                        for(i = 0; i < disabledElems.length; i++){
                            disabledElems[i].attr("disabled", false);
                        }
                        for(i = 0; i < clickElems.length; i++){
                            window.widget.link.loading.remove(clickElems[i]);
                        }

                        if(clean){
                            clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                        }

                    }

                }else{

                    if(always){
                        always(firstJqXHR, firstTextStatus, firstError)
                    }

                    for(i = 0; i < disabledElems.length; i++){
                        disabledElems[i].attr("disabled", false);
                    }
                    for(i = 0; i < clickElems.length; i++){
                        window.widget.link.loading.remove(clickElems[i]);
                    }

                    if(clean){
                        clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                    }

                    if(!window.widget.response.hasError(firstJqXHR)) {

                        if (typeof isFormReset == 'undefined' || (typeof isFormReset == "boolean" && isFormReset)) {
                            if (form.length > 0) {
                                form[0].reset();

                            }
                        }

                    }
                }

            });

        },

        formInModal: function($modal, options, isMultipart, done, fail, always, clean, isFormReset){

            $modal.modal('show');

            $modal.on('shown.bs.modal', function (e) {
                if(isMultipart) {
                    widget.inputFile();
                }
            });

            $modal.on('hidden.bs.modal', function(e){
                $(this).remove();
            })

            $modal.on('click', '.cancel', function(e){
                e.preventDefault();
                $modal.modal('hide');
            });

            $modal.on('click', '.submit', function(e){

                e.preventDefault();

                var $submit = $(this);
                var $form = $submit.closest('form');
                var $messageBoard = $form.find('.message-board');
                var $cancel = $form.find('.cancel');

                var _default = {
                    url: $form.attr('action')
                };

                $.extend(_default, options);

                if(isMultipart){

                    $.extend(_default, {
                        data: new FormData($form[0]),
                        contentType: false,
                        processData: false}
                    )

                }else{

                    $.extend(_default, {
                        data: $form.serialize()
                    })

                }

                widget.ajax.form($modal, $form, $submit, [$cancel], (($messageBoard.length > 0) ? $messageBoard : null), _default, function(data, textStatus, jqXHR) {

                    if(done){
                        done(data, textStatus, jqXHR);
                    }

                    $modal.modal('hide');

                }, function(jqXHR, textStatus, errorThrown){

                    if($messageBoard.length <= 0){
                        widget.notify(jqXHR);
                    }

                    if(fail){
                        fail(jqXHR, textStatus, errorThrown);
                    }

                }, function (firstJqXHR, firstTextStatus, firstError){

                    if(always){
                        always(firstJqXHR, firstTextStatus, firstError)
                    }

                }, function(firstJqXHR, firstTextStatus, firstError, hasError){

                    if(clean){
                        clean(firstJqXHR, firstTextStatus, firstError, window.widget.response.hasError(firstJqXHR));
                    }

                }, isFormReset);

            });
        }
    },

    popup: {
        win: null,
        open : function (obj, title, w, h, name, param) {

            var url = (typeof obj === 'string') ? obj : obj.getAttribute('href');
            if (typeof w == 'undefined') w = 800;
            if (typeof h == 'undefined') h = 600;
            if (typeof name == 'undefined') name = '_blank';
            if (typeof param == 'undefined') param = 'directories=no,  toolbar=no, menubar=no, location=yes, status=yes, scrollbars=yes, resizable=yes'

            param += ', width=' + w + ', height=' + h;

            var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
            var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
            var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            if (param.indexOf('left') == -1) {
                var left = ((width / 2) - (w / 2)) + dualScreenLeft;
                param += ',left=' + left;
            }
            if (param.indexOf('top') == -1) {
                var top =  ((height / 2) - (h / 2)) + dualScreenTop;
                param += ',top=' + top;
            }

            if (this.win && !this.win.closed) {
                this.win.location.href = url;
            } else {
                this.win = window.open(url, name, param);
            }

            if (this.win) {
                if(title) {
                    this.win.document.title = title;
                }
                this.win.focus();
            }

            return false;

        },

        close : function closePopupWindow(reloadParent, reloadUrl, timer) {

            if (typeof (timer) == 'undefined') {
                timer = 5000;
            }

            setTimeout(function () {

                if(window.opener != null) {
                    if(reloadParent) {
                        if(reloadUrl){
                            window.opener.location.href= reloadUrl;
                        }else {
                            window.opener.location.reload();
                        }
                    }
                }

                window.close();

            }, timer);

        },

        unload: function(cb){

            if(this.win){
                this.win.onunload = function(){
                    if(cb) {
                        return cb();
                    }
                }
            }

        }
    },

    time: {
        parse: function(str){

            var obj = {
                hours : 0,
                minutes : 0,
                seconds : 0,
            };

            var time = str.match(/(\d+)(?::(\d\d))?(?::(\d\d))?\s*(p?)/i);

            if(time) {
                obj.hours = parseInt(time[1], 10);
                obj.minutes = parseInt(time[2], 10) || 0;
                obj.seconds = parseInt(time[3], 10) || 0;
            }

            return obj;
        }
    },

    date: {

        dayOnCurrentWeek: function(day){

            var date = new Date();
            var daytoset = day;
            var currentDay = date.getDay();
            var distance = 0;

            if(currentDay == 0){
                date.setDate(date.getDate() - 7);
            }else{
                date.setDate(date.getDate() - currentDay);
            }

            if(daytoset == 0){
                distance = 7;
            }else{
                distance = daytoset;
            }

            date.setDate(date.getDate() + distance);


            return date;

        }

    },

    offcanvas: function($element){

        $element.click(function(e){

            e.preventDefault();

            $body = $('body');
            var cookie = 'offcanvas';
            var toggleSubmenu = 'a.toggle-submenu';
            var submenu = 'ul.submenu';
            var direction = $element.data('toggle-direction');
            var static =  $element.data('toggle-static');
            var cls = (direction == 'right') ? 'open-right-sidebar' : 'open-left-sidebar';

            if(static){
                cls += ' static';
            }

            var $toggleSubmenu = $body.find(toggleSubmenu);

            $body.toggleClass(cls).promise().done(function(){

                if($body.hasClass(cls)){

                    var state =  ($.cookie(cookie)) ? $.cookie(cookie).split(',') : [];

                    $toggleSubmenu.next(submenu).find('li a.active').each(function(){
                        $(this).parents(submenu).css('display', 'block');
                        $(this).parents(submenu).prev(toggleSubmenu).addClass('active');
                    })


                    $toggleSubmenu.on('click', function(event){
                        event.preventDefault();
                        var $nextSubMenu = $(this).next(submenu);
                        var title = $(this).attr('title');

                        $nextSubMenu.slideToggle(300, function() {
                            var flag = ($nextSubMenu.is(':visible')) ? true : false;

                            var found = state.indexOf(title);

                            if(flag){
                                if(found < 0){
                                    state.push(title);
                                }
                            }else{
                                if(found >= 0) {
                                    state.splice(found, 1);
                                }
                            }

                            $.cookie(cookie, state, { expires : 365 , path : '/' });
                        });

                    });

                    $toggleSubmenu.each(function(){

                        if(state.indexOf($(this).attr('title')) >= 0){
                            $(this).next(submenu).css('display', 'block');
                        }

                        if($(this).hasClass('auto')){
                            if(!$(this).next(submenu).is(':visible')) {
                                $(this).trigger("click");
                            }
                        }

                    })


                }else{
                    $toggleSubmenu.off('click');
                }


            });

        })

    },

    activatePopup: function(){

        $('.popup').click(function(event){

            event.preventDefault();

            var a = $(this);
            var url = a.attr('href');
            var title = a.attr('title');

            widget.popup.open(url, title);

        });

    },

    animate: function(element, animationName, done){
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';

        if(element.length > 0) {
            element.addClass('animated ' + animationName).one(animationEnd, function () {

                var $this = $(this);
                $this.removeClass('animated ' + animationName);

                if(done){
                    done($this);
                }

            });
        }
    },

    inputFile: function(){

        var inputFileTrigger = '.input-file-trigger';
        var inputFile = '.input-file';
        var inputFileText = '.input-file-text';

        $(inputFileTrigger).click(function(event){
            event.preventDefault();
            $inputFile = $(this).prev(inputFile);
            $inputFileText = $(this).next(inputFileText);
            var image = $(this).data('image');
            $inputFile.off('change').on('change', function(event){

                if( typeof  image !== 'undefined' && image.length > 0) {

                    $image = eval(image);

                    var reader = new FileReader();

                    reader.onload = function (event) {
                        $image.attr('src', event.target.result);
                    }

                    reader.readAsDataURL(event.target.files[0]);
                }

                $inputFileText.text(event.target.files[0].name);

            })
            $inputFile.click();
        })
    },

    datePicker: function(options, $element){

        $element = $element || $(".datepicker");

        var arr = [];

        var _default = {
            dateFormat: app.datetime.date.format,
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                    document.getElementById(this.id).value = '';
                }
            }
        };

        $.extend(_default, options);


        $element.each(function(){

            $this = $(this);

            var additionalOptions = $this.data("datepicker");

            var _this_default = $.extend({}, _default, additionalOptions);

            $picker = $this.datepicker( _this_default);

            if($this.val()){
                var date = (new Date($this.val()))
                if(date.toString()  !== "Invalid Date") {
                    $picker.datepicker('setDate', date);
                }
            }

            arr.push($picker);

        })

        return (arr.length == 1) ? arr[0] : arr;

    },

    dateTimePicker: function(options, $element){

        $element = $element || $(".date-time-picker");

        var arr = [];

        var _default = {
            format: app.datetime.datetime.format,
            autoclose: true,
            todayBtn: true,
            showMeridian: true,
            pickerPosition: "bottom-left"
        };

        $.extend(_default, options);


        $element.each(function(){

            $this = $(this);

            var additionalOptions = $this.data("date-time-picker");
            var _this_default = $.extend({}, _default, additionalOptions);
            _this_default['container'] = $this.parent();


            $picker = $this.datetimepicker(_this_default);

            if($this.val()){
                $picker.datetimepicker('setValue');
            }

            $picker.on("hide", function() { $(this).blur(); });

            arr.push($picker);

        })

        return (arr.length == 1) ? arr[0] : arr;

    },

    bsToggle: function(){

        $('.toggle-checkbox').off('change').on('change', function(){

            $this = $(this);
            var $toggleContainer = $this.parents('.toggle');
            var toggle = 'bs.toggle';
            var url = $this.data('url');
            var status = $this.prop('checked');

            var options =  {'url' : url};
            var data = $this.data('post');

            if(data){

                options['data'] = data;
            }

            widget.ajax.post($toggleContainer, $this, null, options, function(data, textStatus, jqXHR){

            }, function(jqXHR, textStatus, errorThrown){

                widget.notify(jqXHR);

            }, function(jqXHR, textStatus, error){

            }, function(jqXHR, textStatus, error, isError){

                if(isError){


                    if(status){
                        $this.data(toggle).off(true);
                    }else{
                        $this.data(toggle).on(true);
                    }

                }

            });

        })

    },

    followByScrolling: function(element, top_margin, stop_at_screen_dimension){

        element.css('position', 'relative');

        var originalY = element.offset().top;

        var topMargin = top_margin || 100;

        $(window).on('scroll', function(event) {

            var scrollTop = $(window).scrollTop();

            element.stop(false, false).animate({
                top: (stop_at_screen_dimension && $(window).width() < stop_at_screen_dimension) ? 0 : scrollTop < originalY ? 0 : scrollTop - originalY + topMargin
            }, 300);

        });
    },

    integerValueOnly: function(){

        $element = $(".integer-value");

        $element.keydown(function(event)
        {
            var ctrlDown = event.ctrlKey || event.metaKey;

            if( !(event.keyCode == 8                                // backspace
                || event.keyCode == 9                               // tab
                || event.keyCode == 46                              // delete
                || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
                || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
                || (event.keyCode >= 96 && event.keyCode <= 105)
                || (ctrlDown && event.keyCode == 67)
                || (ctrlDown && event.keyCode == 86)
                || (ctrlDown && event.keyCode == 88))
            ) {
                event.preventDefault();     // Prevent character input
            }

        });

    },

    alphanumericOnly: function(){

        $element = $(".alphanumeric-value");

        $element.keydown(function(event)
        {
            var ctrlDown = event.ctrlKey || event.metaKey;

            if( !(event.keyCode == 8                               // backspace
                || event.keyCode == 9                              // tab
                || event.keyCode == 46                             // delete
                || (event.keyCode >= 35 && event.keyCode <= 40)    // arrow keys/home/end
                || (event.keyCode >= 48 && event.keyCode <= 57)    // numbers on keyboard
                || (event.keyCode >= 96 && event.keyCode <= 105)   // number on keypad
                || (event.keyCode >= 65 && event.keyCode <= 90)    // alphabet
                || (ctrlDown && event.keyCode == 67)
                || (ctrlDown && event.keyCode == 86)
                || (ctrlDown && event.keyCode == 88))
            ) {
                event.preventDefault();     // Prevent character input
            }

        });


    },

    priceValueOnly: function(){

        $element = $(".price-value");

        $element.keydown(function(event)
        {
            var ctrlDown = event.ctrlKey || event.metaKey;

            if( !(event.keyCode == 8                                // backspace
                || event.keyCode == 9                               // tab
                || event.keyCode == 46                              // delete
                || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
                || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
                || (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
                ||  event.keyCode == 110                            // period on keypad
                || event.keyCode == 190                             // period
                || (ctrlDown && event.keyCode == 67)
                || (ctrlDown && event.keyCode == 86)
                || (ctrlDown && event.keyCode == 88)
                )
            ) {
                event.preventDefault();     // Prevent character input
            }

        });

    },

    coordinateValueOnly: function(){

        $element = $(".coordinate-value");

        $element.keydown(function(event)
        {
            var ctrlDown = event.ctrlKey || event.metaKey;

            if( !(event.keyCode == 8                                // backspace
                    || event.keyCode == 9                               // tab
                    || event.keyCode == 46                              // delete
                    || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
                    || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
                    || (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
                    ||  event.keyCode == 110                            // period on keypad
                    || event.keyCode == 190                             // period
                    || (ctrlDown && event.keyCode == 67)
                    || (ctrlDown && event.keyCode == 86)
                    || (ctrlDown && event.keyCode == 88)
                )
            ) {
                event.preventDefault();     // Prevent character input
            }

        });

    },

    helpBox: function(){
        $('.help-box').popover()
    },

    sortable: function(element){

        var $element = element;
        var url = $element.data('url');

        $element.sortable(

            {
                distance: 5,
                delay: 300,
                opacity: 0.6,
                helper: function fixWidthHelper(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width());
                    });
                    return ui;
                },
                start: function(event, ui){

                },
                update: function(event, ui){

                    $this = $(this);

                    var $messageBoard = ui.item.find('.message-board');

                    var options = {
                        url: url,
                        data: {'id' : ui.item.attr('data-id'), 'position' : ui.item.index(), 'total' :  $this.children().length - 1},
                        dataType: 'json'
                    };

                    widget.ajax.post($this, null, (($messageBoard.length > 0) ? $messageBoard : null), options, function(data, textStatus, jqXHR) {



                    }, function(jqXHR, textStatus, errorThrown){

                        if($messageBoard.length <= 0){
                            widget.notify(jqXHR);
                        }

                        $element.sortable( "cancel" );

                    }, function(firstJqXHR, firstTextStatus, firstError){

                    }, function(firstJqXHR, firstTextStatus, firstError, hasError){

                    });

                },

            }

        ).disableSelection();

    },

    creditCardInputFormat: function(){
        $('input.credit-card-number:text').payment('formatCardNumber');
        $('input.credit-card-expiry:text').payment('formatCardExpiry');
        $('input.credit-card-cvc:text').payment('formatCardCVC');
    },

    show: function(element){
        var display = element.data('display-show') || "block";
        element.attr("style",  sprintf('display: %s !important', display));
    },

    hide: function(element){
        var display = element.data('display-hidde') || "none";
        element.attr("style", sprintf('display: %s !important', display));
    },

    headerFromLayout: function(){

        return $('body > .nav-main > header');

    },

    formSearch: function(){

        $formSearch = $('.form-search');

        if($formSearch.length > 0){

            $formSearch.on('keypress', function(e){
                var code = e.keyCode || e.which;
                if(code == 13){
                    e.preventDefault();
                    $(this).find('.search-btn').trigger('click');
                }
            })

        }
    }


}