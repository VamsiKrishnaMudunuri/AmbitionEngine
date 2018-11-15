$(function() {

    $('.copy-link').click(function(e){

        e.preventDefault();

        $this = $(this);
        var link = $this.data('relative-url');

        if(cs.isEmpty(link)){
           link = $this.data('absolute-url');
        }


        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(link).select();
        document.execCommand("copy");
        $temp.remove();


    });

});