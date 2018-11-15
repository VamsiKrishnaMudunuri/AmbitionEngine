$(function() {

    var $form = $('.group-form');
    var $tag = $form.find('.tags');

    var $tagTextText = $tag.textext({
        //tagsItems: $tag.data('data'),
        suggestions: $tag.data('suggestion'),
        //plugins: 'autocomplete suggestions tags filter'
        plugins: 'autocomplete suggestions tags'
    });

    $tagTextText.textext()[0].tags().addTags($tag.data('data'));

    $tagTextText.blur(function(){
        if ($(this).val().trim() != '') $(this).trigger('enterKeyPress').val('').blur();
    });

});