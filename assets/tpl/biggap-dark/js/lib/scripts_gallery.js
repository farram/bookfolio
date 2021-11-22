;(function($, window, document, undefined) {
    'use strict';


    $('.hover span').on('click', function() {
        var $t = $(this);
        $t.siblings('.list').slideToggle(300);
        $t.toggleClass('active');
    });

    var lastClass = '';

    $('.hover .list li').on('click', function() {
        var tx = $(this).text();
        var hoverClass = $(this).attr('data-hover');
        if($('.portfolio.simple').length){
            $('.portfolio.simple').each(function(index){
                $(this).find('.item-link').removeClass(lastClass);
                lastClass = hoverClass;
                $(this).find('.item-link').addClass(hoverClass);
            })
        }
        $('.hover span').text(tx);
        $(this).parents('.list').toggle();
        $('.hover span').toggleClass('active');
    });


})(jQuery, window, document);