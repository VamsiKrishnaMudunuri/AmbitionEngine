$(function() {

    var $module = $('.admin-profile-edit');

    var cls = {
        input: {
            textCore: '.text-core',
            tag : '.tags',

        },
    };

    $module.find( cls.input.tag ).each(function(){

        if($(this).parents(cls.input.textCore).length <= 0){

            var $tag = $(this).textext({
                tagsItems: $(this).data('data'),
                suggestions: $(this).data('suggestion'),
                //plugins: 'autocomplete suggestions tags filter'
                plugins: 'autocomplete suggestions tags'
            });

            $tag.textext()[0].tags().addTags($(this).data('data'));

            $tag.blur(function(){
                if ($(this).val().trim() != '') $(this).trigger('enterKeyPress').val('').blur();
            });

        }

    })

});