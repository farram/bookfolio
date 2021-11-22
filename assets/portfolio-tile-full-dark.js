// ################# CSS FILES ############################

import './tpl/tile-full-dark/css/libs/bootstrap.min.css';
import './tpl/tile-full-dark/css/libs/vuetify-custom.css';
import './tpl/tile-full-dark/css/font-icons/font-awesome.min.css';
import './tpl/tile-full-dark/css/font-icons/pe-icon-7-stroke.css';
import './tpl/tile-full-dark/css/libs/jquery.flipster.css';
import './tpl/tile-full-dark/css/libs/default-skin.css';
import './tpl/tile-full-dark/css/libs/justifiedGallery.min.css';
import './tpl/tile-full-dark/css/libs/photoswipe.css';
import './tpl/tile-full-dark/css/libs/animate.css';
import './tpl/tile-full-dark/css/main.css';

// ################# JS FILES ############################

//import $ from 'jquery';
import 'bootstrap';
import './tpl/tile-full-dark/js/jquery.min.js';
import './tpl/tile-full-dark/js/jquery.flexslider.js';
import './tpl/tile-full-dark/js/jquery.magnific-popup.min.js';
import './tpl/tile-full-dark/js/jquery.mb.YTPlayer.src.js';
import './tpl/tile-full-dark/js/jquery.matchHeight-min.js';
import './tpl/tile-full-dark/js/jquery.fitvids.js';
import './tpl/tile-full-dark/js/jquery-migrate.min.js';
import './tpl/tile-full-dark/js/jquery.justifiedGallery.min.js';
import './tpl/tile-full-dark/js/jquery.gridrotator.js';
import './tpl/tile-full-dark/js/jquery.multiscroll.min.js';
import './tpl/tile-full-dark/js/foxlazy.js';
import './tpl/tile-full-dark/js/script.js';
import './tpl/tile-full-dark/js/main.js';
import './vuejs/tile-full-dark/main.js';

$(document).contextmenu(function () {
    return false;
});

;
(function ($, window, document, undefined) {
    'use strict';

    var $window = $(window);

    /*Calculate paddings for main wrapper*/
    function calcPaddingMainWrapper() {
        if (!$("#footer.fix-bottom").length) {
            var paddValue = $('footer').outerHeight();
            $('.main-wrapper').css('padding-bottom', paddValue);
        }
    }
    calcPaddingMainWrapper();

    //fixed menu
    function addFixedHeader() {
        var topHeader = $('.header_top_bg.enable_fixed'),
            heightHeader = topHeader.height();

        $window.on('scroll', function () {
            if ($window.scrollTop() > 0) {
                topHeader.addClass('fixed');
                $('.main-wrapper').css('padding-top', heightHeader);
            } else {
                topHeader.removeClass('fixed');
                $('.main-wrapper').css('padding-top', '0');
            }
        });
    }
    addFixedHeader();

})(jQuery, window, document);
