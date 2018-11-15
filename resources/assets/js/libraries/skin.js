var skin = {

    loading: {

        'sm': '<i class="fa fa-spinner fa-spin fa-fw fa-loading"></i>',
        'md': '<i class="fa fa-spinner fa-spin fa-2x fa-loading"></i>',
        'lg': '<i class="fa fa-spinner fa-spin fa-3x fa-loading"></i>'

    },

    locationLoading : '<div class="location-loading text-center"><div><br/></div><div><i class="fa fa-spinner fa-spin fa-2x fa-loading"></i></div><div><br/></div></div>',

    pageLoading : '<div class="page-loading"><i class="fa fa-spinner fa-spin fa-3x fa-loading"></i></div>',

    message: {

        'error': function(message){

            var str = '<ul>';

            if(typeof message === 'string'){

                str += '<li>' + cs.removeQuotes(message) + '</li>';

            }else if(typeof message === 'object'){

                for(var key in message){
                    str += '<li>' + cs.removeQuotes(message[key]) + '</li>';
                }

            }else{

                for(i = 0; i < message.length; i++){

                    str += '<li>' + cs.removeQuotes(message[i]) + '</li>';

                }
            }

            str += '</ul>';

            return str;

        },

    },

    alert: {

        'error': function(message, skin){


            var str = '<div class="alert alert-danger text-left ' + (skin ? skin : '') + '">';

            str += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';

            str += '<ul>';

            if(typeof message === 'string'){

                str += '<li>' + cs.removeQuotes(message) + '</li>';

            }else if(typeof message === 'object'){

                for(var key in message){
                    str += '<li>' + cs.removeQuotes(message[key]) + '</li>';
                }

            }else{

                for(i = 0; i < message.length; i++){

                    str += '<li>' + cs.removeQuotes(message[i]) + '</li>';

                }
            }

            str += '</ul>';

            str += '</div>';

            return str;

        },

        'success': function(message, skin){


            var str = '<div class="alert alert-success text-left ' + (skin ? skin : '') + '">';

            str += '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';

            str += '<ul>';

            if(typeof message === 'string'){

                str += '<li>' + cs.removeQuotes(message) + '</li>';

            }else if(typeof message === 'object'){

                for(var key in message){
                    str += '<li>' + cs.removeQuotes(message[key]) + '</li>';
                }

            }else{

                for(i = 0; i < message.length; i++){

                    str += '<li>' + cs.removeQuotes(message[i]) + '</li>';

                }
            }

            str += '</ul>';

            str += '</div>';

            return str;

        }

    },

    badge: {
        nice: function(count){
            return (count <= 0) ?  '' : '<span class="nice-badge">' + count + '</span>';
        }
    },

    modal: {

        simple: function(header, body, footer){

            var buf = '';
            var header= header || '';
            var body = body || '';
            var footer = footer || '';


            buf += "<div class=\"modal fade\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"gridSystemModalLabel\">" +
                        "<div class=\"modal-dialog\" role=\"document\">" +
                            "<div class=\"modal-content\">";

                                header = "<div class=\"modal-header\">" +
                                                "<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>"+
                                                 header +
                                            "</div>";

                                body = "<div class=\"modal-body\">" +
                                            body +
                                         "</div>";

                                footer = "<div class=\"modal-footer\">" +
                                            footer +
                                          "</div>";

                                buf += header + body + footer;


            buf += 	        "</div>" +
                         "</div>" +
                    "</div>";

            return buf;


        }
    },

    businessHours: '<div class="dayContainer">' +
        '<div data-original-title="" class="colorBox">' +
        '   <input type="checkbox" class="invisible operationState">' +
        '</div>' +
        '<div class="weekday"></div>' +
        '<div class="operationDayTimeContainer">' +
            '<div class="operationTime input-group">' +
                '<span class="input-group-addon"><i class="fa fa-sun-o"></i></span>' +
                '<input type="text" name="startTime" class="mini-time form-control operationTimeFrom" value="">' +
            '</div>' +
            '<div class="operationTime input-group">' +
                '<span class="input-group-addon"><i class="fa fa-moon-o"></i></span>' +
                '<input type="text" name="endTime" class="mini-time form-control operationTimeTill" value="">' +
            '</div>' +
        '</div>' +
    '</div>'

}