;
(function ($, window, document, undefined) {
    'use strict';
    var $window = $(window);
    
    $('.mob-nav').on('click', function () {
        $(this).toggleClass('active');
        $(this).find('i').toggleClass('mdi-menu mdi-close');
        $('#topmenu').slideToggle();
        $('.header_top_bg.header_trans-fixed').toggleClass('open');
        $('body, html').toggleClass('no-scroll');
        return false;
    });

    $(document).contextmenu(function() {
        return false;
    });

})(jQuery, window, document);