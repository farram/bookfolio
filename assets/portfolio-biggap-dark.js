// ################# CSS FILES ############################

import './tpl/biggap-dark/css/libs/vuetify-custom.css';
import './tpl/biggap-dark/css/libs/bootstrap.min.css';
import './tpl/biggap-dark/css/font-icons/font-awesome.min.css';
import './tpl/biggap-dark/css/font-icons/pe-icon-7-stroke.css';
import './tpl/biggap-dark/css/libs/jquery.flipster.css';
import './tpl/biggap-dark/css/libs/default-skin.css';
import './tpl/biggap-dark/css/libs/justifiedGallery.min.css';
import './tpl/biggap-dark/css/libs/photoswipe.css';
import './tpl/biggap-dark/css/libs/animate.css';
import './tpl/biggap-dark/css/main.css';
import './tpl/biggap-dark/css/style.css';

// ################# JS FILES ############################

//import $ from 'jquery';
import 'bootstrap';
import './tpl/biggap-dark/js/jquery.min.js';
import './tpl/biggap-dark/js/jquery.flexslider.js';
import './tpl/biggap-dark/js/jquery.magnific-popup.min.js';
import './tpl/biggap-dark/js/jquery.mb.YTPlayer.src.js';
import './tpl/biggap-dark/js/jquery.matchHeight-min.js';
import './tpl/biggap-dark/js/jquery.fitvids.js';
import './tpl/biggap-dark/js/jquery-migrate.min.js';
import './tpl/biggap-dark/js/jquery.justifiedGallery.min.js';
import './tpl/biggap-dark/js/jquery.gridrotator.js';
import './tpl/biggap-dark/js/jquery.multiscroll.min.js';
import './tpl/biggap-dark/js/foxlazy.js';
import './tpl/biggap-dark/js/main.js';
import './vuejs/biggap-dark/main.js';

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
