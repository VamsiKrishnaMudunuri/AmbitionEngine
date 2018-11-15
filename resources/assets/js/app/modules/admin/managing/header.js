$(function() {

    var $tab = $('.managing-menu .tabs');
    var $tab_dropdowns = $tab.find('.dropdown');

    $tab_dropdowns.on("hide.bs.dropdown", function(){
        $tab.removeClass('no-overflow');
    });
    $tab_dropdowns.on("show.bs.dropdown", function(){
        $tab.addClass('no-overflow');
    });

});