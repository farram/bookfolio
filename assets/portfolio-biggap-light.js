// ################# CSS FILES ############################

import './tpl/biggap-light/css/libs/vuetify-custom.css';
import './tpl/biggap-light/css/libs/bootstrap.min.css';
import './tpl/biggap-light/css/font-icons/font-awesome.min.css';
import './tpl/biggap-light/css/font-icons/pe-icon-7-stroke.css';
import './tpl/biggap-light/css/libs/jquery.flipster.css';
import './tpl/biggap-light/css/libs/default-skin.css';
import './tpl/biggap-light/css/libs/justifiedGallery.min.css';
import './tpl/biggap-light/css/libs/photoswipe.css';
import './tpl/biggap-light/css/libs/animate.css';
import './tpl/biggap-light/css/main.css';
import './tpl/biggap-light/css/style.css';

// ################# JS FILES ############################

//import $ from 'jquery';
import 'bootstrap';
import './tpl/biggap-light/js/jquery.min.js';
import './tpl/biggap-light/js/jquery.flexslider.js';
import './tpl/biggap-light/js/jquery.magnific-popup.min.js';
import './tpl/biggap-light/js/jquery.mb.YTPlayer.src.js';
import './tpl/biggap-light/js/jquery.matchHeight-min.js';
import './tpl/biggap-light/js/jquery.fitvids.js';
import './tpl/biggap-light/js/jquery-migrate.min.js';
import './tpl/biggap-light/js/jquery.justifiedGallery.min.js';
import './tpl/biggap-light/js/jquery.gridrotator.js';
import './tpl/biggap-light/js/jquery.multiscroll.min.js';
import './tpl/biggap-light/js/foxlazy.js';
import './tpl/biggap-light/js/main.js';
import './vuejs/biggap-light/main.js';

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
