$(function() {

    var $module = $('.agreement-form');
    var $inlineText = $module.find('.inline-text');


    $inlineText.keyup(function(){
        var $this = $(this);
        var field = $this.data('field');
        var $span = $(sprintf('.%s_inline_text', field));

        if($span.length > 0){
            $span.html($this.val());
        }
    })


});