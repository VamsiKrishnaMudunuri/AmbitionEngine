$(function() {


    var $form = $('.invite-form');

    var $member = $form.find('.receivers');
    var $memberHidden = $form.find('.receivers_hidden');
    var $submit = $form.find('.submit');

    var max  = $member.data('max');
    var items = [];
    var selected = {};

    $member.val('');

    var $tag = $member.textext({

        plugins: 'autocomplete ajax tags',
        ajax: {
            url : $member.data('url'),
            typeDelay: 0.3,
            delay: 0,
            loadingMessage: $member.data('loading'),
            dataCallback : function(query){
                return {'query' : query}
            }
        },
        ext: {
            itemManager: {

                stringToItem: function(str){

                    var item  = {};

                    if(items[str]){
                        item = items[str];
                    }

                    return item;

                },
                itemToString: function (item) {

                    return item.name;

                },
                itemToID: function(item){
                    items[item.id] = item;
                    return item.id;
                }

            }
        },
        autocomplete: {
            render: function(item){

                var buf = '<div class="simple-card">';

                        buf += '<a href="javascript:void(0);">';
                            buf += '<div class="image-frame">';
                                buf += '<img src="' + item.avatar  + '" />'
                            buf += '</div>'
                            buf += '<div class="details">';
                                buf += '<div class="name">';
                                    buf +=  item.name;
                                buf += '</div>';
                                buf += '<div class="username">';
                                    buf +=  item.username_alias;
                                buf += '</div>';
                            buf += '</div>' ;
                        buf += '</a>';

                buf += '</div>';

                return  buf;
            }
        }

    }).bind('isTagAllowed', function(e, data)
    {
        if(_.size(data.tag) <= 0 || _.size(selected) >= max || selected[data.tag.id]){

            data.result = false;

        }

    }).bind('tagPreRender', function(e, data){

        if(!selected[data.tag.id]){
            selected[data.tag.id] = data.tag;
        }

    })
    .bind('tagRemove', function(e, item, element){

        if(selected[item.id]){
           delete selected[item.id];
        }

    })

    $submit.click(function(e){
        e.preventDefault();

        if(_.size(selected) > 0){
            $memberHidden.val(Object.keys(selected).join());
        }
    })



});