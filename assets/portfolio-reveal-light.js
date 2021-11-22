// ################# CSS FILES ############################

import './tpl/reveal-light/css/vuetify-custom.css';
import './tpl/reveal-light/css/vendors.css';
import './tpl/reveal-light/css/style.css';
import './tpl/reveal-light/css/main.css';

// ################# JS FILES ############################


import 'bootstrap';
import "velocity-animate";
import verge from "verge";
//import './tpl/reveal-light/js/vendors.min.js';
import './vuejs/reveal-light/main.js';

(function ($) {

    $.extend(verge);

    /* google map
    ----------------------------------------------*/
    var $gmap = $("#gmap , .gmap");
    if ($gmap.length > 0) {
        $gmap.each(function () {
            var $gmap = $(this);
            var address = $gmap.data('address') || 'Footscray VIC 3011 Australia',
                maxZoom = parseInt($gmap.data('map-max-zoom')) || 15,
                initZoom = parseInt($gmap.data('map-initial-zoom')) || 10;

            $gmap.gmap3({
                address: address,
                zoom: initZoom,
                maxZoom: maxZoom,
                streetViewControl: false,
                mapTypeControl: false,
                mapTypeId: 'mystyle',
            })
                .overlay({
                    address: address,
                    content: '<div id="map-marker"></div>',
                    y: -65,
                    x: -20
                })
                .styledmaptype(
                    "mystyle",
                    [{
                        featureType: "all",
                        stylers: [
                            { saturation: -100 },
                            { lightness: 0.9 }
                        ]
                    }],
                    { name: "mystyle" }
                );
        });
    }


    /* Justified Gallery
     ----------------------------------------------*/
    var emJustifiedGallery = {
        init: function ($gallery) {
            var self = this;
            self.$gallery = $gallery;
            self.$items = $gallery.find('.gallery-item');
            self.$imgs = self.$items.find('img');
            self.lazyFlg = $gallery.attr('data-lazy-mode') ? $gallery.attr('data-lazy-mode') : true;
            self.infiniteScrollFlg = self.$gallery.hasClass('em-infinite-gallery');

            self.bindUIActions();
            self.run();
            self.runInfiniteScroll();
        },
        bindUIActions: function () {
            var self = this;

            self.$gallery.on('jg.complete', function (e) {
                if (self.lazyFlg) {
                    lazyloadImages(self.$imgs);
                }
                if (self.infiniteScrollFlg) {
                    self.infiniteScroll.checkNow();
                }
                createObjInstance('.ol-animate._wait:not(.animated)', revealAnimate);
            });

            self.$gallery.on('em-new-items', function (e, arg1) {
                self.newItemsHandler(arg1.items);
            });

        },
        newItemsHandler: function ($newItems) {
            var self = this;

            $newItems.appendTo(self.$gallery);
            self.$items = self.$items.add($newItems);
            self.$gallery.justifiedGallery('norewind');

            self.$gallery.trigger('appended-new-items');

        },
        runInfiniteScroll: function () {
            var self = this;
            if (self.infiniteScrollFlg) {
                self.infiniteScroll = Object.create(infiniteScroll);
                self.infiniteScroll.init(self.$gallery);
            }
        },
        run: function () {
            var self = this;
            self.$gallery.justifiedGallery({
                lastRow: 'nojustify',
                margins: parseInt(self.$gallery.data('jg-margin')),
                waitThumbnailsLoad: false,
                selector: '.gallery-item',
                captions: false,
                rowHeight: self.$gallery.data('jg-rowheight') || 200,
                maxRowHeight: self.$gallery.data('jg-maxrowheight') || "150%"
            });
        }
    }
    /* Gallery base object
    ----------------------------------------------*/
    var emGallery = {
        init: function ($galleryWrapper) {
            var self = this;
            self.$galleryWrapper = $galleryWrapper;
            self.$gallery = $galleryWrapper.find('.em-gallery');
            self.$items = self.$gallery.find('.gallery-item');
            self.$imgs = self.$items.find('img');
            self.lazyFlg = self.$gallery.attr('data-lazy-mode') ? self.$gallery.attr('data-lazy-mode') : true;
            self.infiniteScrollFlg = self.$gallery.hasClass('em-infinite-gallery');
            self._init($galleryWrapper);
            self.prepareItems();
            self.bindUIActions();
            self.runIsotope();

        },
        runInfiniteScroll: function () {
            var self = this;
            if (self.infiniteScrollFlg) {
                self.infiniteScroll = Object.create(infiniteScroll);
                self.infiniteScroll.init(self.$gallery);
                self.infiniteScroll.checkNow();
            }

            self.$gallery.on('layoutComplete', function (filteredItems) {
                if (self.infiniteScrollFlg) {
                    self.infiniteScroll.checkNow();
                }
            });


        },
        newItemsHandler: function ($newItems) {
            var self = this;

            $newItems.each(function () {
                if ($(this).hasClass('ol-animate')) {
                    revealAnimate.init($(this));
                }
            });
            $newItems.appendTo(self.$gallery);
            self.$items = self.$items.add($newItems);
            self.prepareItems($newItems);
            self.$gallery.isotope('appended', $newItems).isotope('layout');


            self.$gallery.trigger('appended-new-items');
            $(window).trigger('resize');

        },
        bindUIActions: function () {
            var self = this;

            self.$gallery.on('arrangeComplete', function () {
                if (self.lazyFlg) {
                    lazyloadImages(self.$imgs);
                }
                $(window).trigger('scroll');
            });


            self.$gallery.on('em-new-items', function (e, arg1) {
                self.newItemsHandler(arg1.items);
            });


            self.$gallery.one('arrangeComplete', function () {
                self.$galleryWrapper.addClass('isotope-loaded');
                setTimeout(function () {
                    self.runInfiniteScroll();
                    $(window).trigger('isotope-init');
                }, 50);
            });

            $(window).on('resize', function () {
                self.prepareItems();
            });
        }
    }

    /* Horizontal Galleries
    ----------------------------------------------*/
    var horizontalGallery = Object.create(emGallery);
    horizontalGallery._init =
        function ($galleryWrapper) {
            var self = this;
            self.$galleryWrapper = $galleryWrapper;
            self.mode = self.$gallery.data('mode') ? self.$gallery.data('mode') : 'auto';
            self.defaultWidth = self.$gallery.attr('data-default-width') ? self.$gallery.attr('data-default-width') : 400;
            self.HGridFlg = $galleryWrapper.hasClass('type-grid');
            self.HMasonryFlg = $galleryWrapper.hasClass('type-masonry');
        };
    horizontalGallery.prepareItems =
        function ($items) {
            var self = this;
            $items = ($items) ? $items : self.$items;

            //Masonry or simple horizontal in auto width mode
            if (self.HMasonryFlg || (self.$galleryWrapper.hasClass('type-simple') && self.mode == 'auto')) {

                $items.each(function () {
                    var $this = $(this),
                        ratio = 1;

                    ratio = parseInt($this.data('width')) / parseInt($this.data('height'));

                    $this.width($this.height() * ratio);
                });
            }

            if (self.HGridFlg) {
                var $firstItem = $items.eq(0),
                    ratio = 1;

                if ($firstItem.hasClass('ratio-portrait'))
                    ratio = 0.75;
                if ($firstItem.hasClass('ratio-landscape'))
                    ratio = 1.43;

                $items.width($firstItem.height() * ratio)
            }

            if (self.mode == 'fixed' || self.HGridFlg) {
                $items.each(function () {
                    imageFill($(this));
                });
            }
            if (self.mode == 'fixed') {
                self.$items.each(function () {
                    var $item = $(this);
                    $item.find('img').first().on('lazyimage-loaded', function () {
                        imageFill($item);
                    });
                });
            }

        };
    horizontalGallery.runIsotope =
        function () {
            var self = this;
            var $gallery = self.$gallery.isotope({
                layoutMode: 'masonryHorizontal',
                itemSelector: '.gallery-item',
                transitionDuration: 0,
                masonryHorizontal: {
                    gutter: 0,
                }
            });
        };

    /* Galleries
    ----------------------------------------------*/
    var verticalGallery = Object.create(emGallery);
    verticalGallery._init =
        function ($galleryWrapper) {
            var self = this;
            self.filter();
            self.layoutMode = $galleryWrapper.hasClass('type-grid') ? 'grid' : 'masonry';
            self.portfolioFilters();
        };
    verticalGallery.prepareItems =
        function ($items) {
            var self = this;
            $items = ($items) ? $items : self.$items;

            if (self.layoutMode == 'masonry') {

                $items.each(function () {
                    var $item = $(this),
                        $glwrapper = $item.find('.gl-wrapper'),
                        ratio = 1;

                    ratio = parseInt($item.data('height')) / parseInt($item.data('width'));

                    $glwrapper.height($glwrapper.width() * ratio);
                });
            }
            if (self.layoutMode == 'grid') {
                self.$items.each(function () {
                    imageFill($(this));
                });
            }
        };
    verticalGallery.runIsotope =
        function () {
            var self = this;
            var widthClass = (self.$gallery.find('.grid-sizer').length) ? '.grid-sizer' : '.gallery-item';
            self.$gallery.isotope({
                layoutMode: 'masonry',
                itemSelector: '.gallery-item',
                transitionDuration: 0,
                masonry: {
                    // use outer width of grid-sizer for columnWidth
                    columnWidth: widthClass
                }
            });
        };
    verticalGallery.filter =
        function () {
            var self = this;
            $('.grid-filters').on('click', 'a', function (e) {
                e.preventDefault();

                var $this = $(this);
                var filterValue = $this.attr('data-filter');
                self.$gallery.isotope({ filter: filterValue });

                $this.parent().siblings().find('a').removeClass('active');
                $this.addClass('active');
                return false;
            });
        }
    verticalGallery.portfolioFilters =
        function () {
            var self = this;
            $("#portfolio-filters").on('click', 'li a', function (e) {
                e.preventDefault();
                var $this = $(this);
                var filterValue = $(this).parent('li').data('filter');

                self.$gallery.isotope({ filter: filterValue });
                $this.parent().siblings().removeClass('active');
                $this.parent().addClass('active');
                return false;
            });
        }
    /* Simple Vertical Gallery
     ----------------------------------------------*/
    var simpleVerticalGallery = {
        init: function ($galleryWrapper) {
            var self = this;
            self.$gallery = $galleryWrapper.find('.em-gallery');
            self.lazyFlg = self.$gallery.attr('data-lazy-mode') ? self.$gallery.attr('data-lazy-mode') : true;
            self.fillFlag = self.$gallery.hasClass('fill-mode');
            self.infiniteScrollFlg = self.$gallery.hasClass('em-infinite-gallery');
            self.$items = self.$gallery.find('.gallery-item');
            self.$imgs = self.$items.find('img');

            this.prepare();
            this.bindUIActions();
        },
        prepare: function () {
            var self = this;
            if (self.fillFlag) {
                self.$items.each(function () {
                    imageFill($(this));
                });
            }
            if (self.lazyFlg) {
                lazyloadImages(self.$imgs);
            }
            self.runInfiniteScroll();
        },
        runInfiniteScroll: function () {
            var self = this;

            if (self.infiniteScrollFlg) {
                self.infiniteScroll = Object.create(infiniteScroll);
                self.infiniteScroll.init(self.$gallery);
            }
        },
        bindUIActions: function () {
            var self = this;

            self.$gallery.on('em-new-items', function (e, arg1) {
                self.newItemsHandler(arg1.items);
            });

        },
        newItemsHandler: function ($newItems) {
            var self = this;

            $newItems.each(function () {
                if ($(this).hasClass('ol-animate')) {
                    revealAnimate.init($(this));
                }
            });

            $newItems.appendTo(self.$gallery);
            self.$items = self.$items.add($newItems);

            self.$gallery.trigger('appended-new-items');

        },
    };

    /* Infinite Scroll for galleries
    ----------------------------------------------*/
    var infiniteScroll = {
        init: function ($gallery) {

            // don't infinite scroll at cusomizer preview
            if (typeof wp.customize != 'undefined')
                return false;

            this.$gallery = $gallery;
            this.pageCounter = 1;
            this.totalPageNum = this.$gallery.data('pagecount');
            this.generateLoader();
            this.infiniteOffset = 150;
            this.inFetchingFlag = false;
            this.$horizontalWrapper = this.$gallery.parents('.gallery-wrapper.direction-horizontal');
            this.$gallerySide = this.$horizontalWrapper.find('.gallery-side');
            this.isHorizontal = (this.$horizontalWrapper.length);
            this.setVariables();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;
            if ($.og.touchDevice && self.isHorizontal) {
                $.og.$scrollWrapper.scroll(function () {
                    if ($(this).scrollLeft() >= self.touchScrollEdge) {
                        setTimeout(self.fetchItems(), 1000);
                    }
                });
            } else {
                $.og.$scrollWrapper.scroll(function () {
                    if ($(this).scrollTop() >= self.mouseScrollEdge) {
                        setTimeout(self.fetchItems(), 1000);
                    }
                });
            }

            $(window).on('resize', function () {
                self.setVariables();
            });


        },
        setVariables: function () {
            var self = this;
            self.sideWidth = self.$gallerySide.length ? self.$gallerySide.width() : 0;
            self.touchScrollEdge = self.$gallery.width() + self.sideWidth - $(window).width() - self.infiniteOffset;
            self.mouseScrollEdge = $(document).height() - $(window).height() - self.infiniteOffset;
        },
        getAction: function () {
            var self = this;

            var ajaxAction = 'eram_gallery_infinite_scroll';
            if (self.$gallery.hasClass('is-portfolio'))
                ajaxAction = 'eram_portfolio_infinite_scroll';

            return ajaxAction;
        },
        fetchItems: function () {
            var self = this;
            if (self.inFetchingFlag) { return false; }
            if (self.pageCounter > self.totalPageNum) {
                return false;
            }
            self.showLoader();
            self.inFetchingFlag = true;
            $.ajax({
                url: eram_vars.adminAjaxUrl,
                type: 'POST',
                data: {
                    'action': self.getAction(),
                    'page_no': self.pageCounter,
                    'post_id': self.$gallery.data('postid'),
                    'page_is': eram_vars.page_is,
                    'options': eram_vars.queried_options
                },
                success: function (html) {
                    self.updateIsotope(html);
                    self.pageCounter++;
                    self.inFetchingFlag = false;
                }
            });

        },
        updateIsotope: function (data) {
            var self = this;
            var $items = $('<div>').html(data).find('.gallery-item');

            var arg = {
                items: $items
            };

            self.$gallery.trigger('em-new-items', arg);
            self.hideLoader();
        },
        generateLoader: function () {
            var self = this;

            self.loaderElem = $('<div>').addClass('em-infinite-loader layout-margin').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>').appendTo(self.$gallery);

        },
        showLoader: function () {
            var self = this;

            self.loaderElem.fadeIn();

        },
        hideLoader: function () {
            this.loaderElem.fadeOut();
        },
        checkPageScroll: function () {
            var self = this;
            if (self.isHorizontal) {
                return (self.$gallery.width() + self.sideWidth < $(window).width());
            }
            return (self.$gallery.height() + self.$gallery.offset().top < $(window).height());
        },
        checkNow: function () {
            // don't infinite scroll at cusomizer preview
            if (typeof wp.customize != 'undefined')
                return false;

            var self = this;
            setTimeout(function () {
                if (self.checkPageScroll()) {
                    self.fetchItems();
                }
            }, 500);

        }
    };

    /* Simple masonry
    /* Blog
    /* Clients
    /* WooCommerce categories
    ----------------------------------------------*/
    var simpleMasonry = {
        init: function ($elem, options) {

            var self = this;

            self.$elem = $elem;
            self.options = options;


            if (self.options.setHeight) {
                self.$items = $elem.find(self.options.itemSelector);
                self.prepare();
            }

            self.runIsotope();
            self.bindUIActions();
        },
        prepare: function () {
            var self = this;

            self.$items.each(function () {
                var $item = $(this),
                    ratio = 1;

                ratio = parseInt($item.data('height')) / parseInt($item.data('width'));

                $item.height($item.width() * ratio);
            });
        },
        bindUIActions: function () {
            var self = this;

            if (self.options.setHeight) {

                $(window).on('resize', function () {
                    self.prepare();
                    self.$elem.isotope('layout');
                });

            }

            $(window).on('load', function () {
                self.$elem.isotope('layout');
            });
        },
        runIsotope: function () {
            var self = this;
            self.$elem.isotope({
                layoutMode: 'masonry',
                itemSelector: self.options.itemSelector,
                transitionDuration: 0,
            })
        }
    }

    /* Reveal Animation on scroll
    ----------------------------------------------*/
    var revealAnimate = {
        init: function ($elem) {
            this.$elem = $elem;
            this.disableMobile = true;
            this.offset = $elem.data('offset') ? $elem.data('offset') : 0;
            if (this.offset == 'inview') {
                this.offset = -Math.min($(window).height() / 2, this.$elem.outerHeight());
            }
            this.animatedFlag = false;

            this.animOptions = {
                duration: $elem.data('duration'),
                delay: $elem.data('delay'),
                iteration: $elem.data('iteration')
            };

            this.prepare();
            this.bindUIActions();
            this.checkInView();

        },
        prepare: function () {
            var self = this;
            var cssObj = {};

            self.animOptions.name = self.$elem.css('animation-name');

            for (var option in self.animOptions) {
                if (self.animOptions[option]) {
                    cssObj['animation-' + option] = self.animOptions[option];
                    cssObj['-webkit-animation-' + option] = self.animOptions[option];
                    self.$elem.css(cssObj);
                }
            }

            self.$elem.css('visibility', 'hidden').css('opacity', '0').css('animation-name', 'none');
        },
        bindUIActions: function () {
            var self = this;

            $.og.$scrollWrapper.on('scroll', function () {
                self.checkInView();
            });

            $(window).on('isotope-init', function () {
                self.checkInView();
            });


        },
        checkInView: function () {
            var self = this;

            if (self.animatedFlag) return false;


            var check = verge.inY(self.$elem, self.offset);
            if (self.$elem.hasClass('_animates_horizontally')) {
                check = verge.inX(self.$elem, self.offset);
            }
            if (check) {
                self.animatedFlag = true;
                self.$elem.css('animation-name', self.animOptions.name);

                if (self.animOptions.delay) {
                    setTimeout(function () {
                        self.setAnimations();
                    }, parseFloat(self.animOptions.delay) * 1000);
                } else {
                    self.setAnimations();
                }

            }
        },
        setAnimations: function () {
            this.$elem.css('visibility', 'visible').css('opacity', '1').addClass('animated');
        }
    };

    var handleSocialSharing = {
        init: function () {
            this.$elem = $('#social-sharing');
            this.$triggerElem = this.$elem.find('.share-trigger');
            this.$liItems = this.$elem.find('li');

            this.inAnimation = false;
            this.animMode = "show";

            this.prepareLinks();
            this.bindUIActions();

        },
        prepareLinks: function () {
            var self = this;

            var pageTitle = document.title,
                pageUrl = document.URL,
                pageMedia = '';

            var $firstImage = $('.em-gallery').find('img').first();

            if ($firstImage.data('original') != undefined) {
                pageMedia = $firstImage.data('original');
            } else {
                pageMedia = $firstImage.attr('src') || '';
            }



            self.$elem.find('a').each(function () {
                var $this = $(this),
                    link = $this.attr('href') || '';

                link = link.replace('{title}', pageTitle);
                link = link.replace('{url}', pageUrl);
                link = link.replace('{img}', pageMedia);

                $this.attr('href', link);
            });
        },
        bindUIActions: function () {
            var self = this,
                $listItems = self.$elem.find('li'),
                $sharingIcon = self.$elem.find('.sharing-icons');


            self.$triggerElem.on('click', function () {
                if (self.inAnimation) {
                    return false;
                }
                self.animateIcons();
            })
        },
        animateIcons: function () {
            var self = this;
            var multiplier = -1;

            if (self.animMode == 'show') {
                self.$elem.addClass('active');
                self.$triggerElem.addClass('close-trigger');
                self.$liItems.each(function (i) {
                    var $this = $(this);
                    $this.velocity({ translateX: multiplier * 35 * (i + 1) }, {
                        duration: 250, delay: i * 40, easing: [.36, .79, .69, 1.22], complete: function () {
                            self.inAnimation = false;
                            self.animMode = 'hide';
                        }
                    });
                });
            } else {
                self.$elem.removeClass('active');
                self.$triggerElem.removeClass('close-trigger');
                self.$liItems.each(function (i) {
                    var $this = $(this);
                    $this.velocity({ translateX: 0 }, {
                        duration: 250, easing: [.36, .79, .68, .99], complete: function () {
                            self.inAnimation = false;
                            self.animMode = 'show';
                        }
                    });
                });
            }
        }
    };
    /* Initialize Ken-Burns sliders
    ----------------------------------------------*/
    function runKenburnSlider() {
        var $kbsliders = $('.em-gallery-kenburn');
        $kbsliders.each(function () {
            var $this = $(this),
                zoom = $this.data('kb-zoom') ? $this.data('kb-zoom') : 1.1,
                duration = $this.data('kb-duration') ? $this.data('kb-duration') : 7;

            $this.kenburnIt({
                itemClass: '.gallery-item',
                zoom: zoom,
                duration: duration
            });
        });
    };

    /* MasterSlider handler
    ----------------------------------------------*/
    var msSlider = {

        //This is the function that controlls all masterslider instances or atleast it tries to do!
        init: function ($elem) {
            this.$elem = $elem;
            var self = this;

            //Some basic settings
            var msOptions = {
                ewidth: $elem.data('width') || $elem.width(),
                eheight: $elem.data('height') || $elem.height(),
                layout: $elem.data('layout') || 'boxed',
                view: $elem.data('view') || 'basic',
                dir: $elem.data('dir') || 'h',
                showCounter: $elem.data('counter') || false,
                isGallery: $elem.data('gallery') || false,
                autoHeight: $elem.data('autoheight') || false,
                hasPlay: $elem.data('playbtn') || true,
                timerBar: $elem.data('timer') || false,
                galleryWrapper: $elem.parents('.tj-ms-gallery'),
                mouse: ($elem.data('mouse') || true) == true,
                fillMode: $elem.data('fillmode') || 'fill',
                initialPlay: $elem.data('autoplay') || false,
            }

            self.runSlider(msOptions);

        },
        runSlider: function (options) {

            var self = this;

            var slider = new MasterSlider(),
                elemID = self.$elem.attr('id') || '';


            slider.setup(elemID, {
                width: options.ewidth,
                height: options.eheight,
                layout: options.layout,
                view: options.view,
                dir: options.dir,
                autoHeight: options.autoHeight,
                space: 0,
                preload: 5,
                keyboard: true,
                loop: true,
                centerControls: false,
                mouse: options.mouse,
                fillMode: options.fillMode,
                overPause: false,
                autoplay: false
            });

            slider.control('arrows', { autohide: false });

            if (options.timerBar) {
                slider.control('timebar', { autohide: false });
            }

            if (options.isGallery) {
                self.makeGallery(slider);
            }


            slider.api.addEventListener(MSSliderEvent.INIT, function () {
                var $controlsWrapper = self.$elem.find('.ms-container'),
                    $controlUI = $('<div class="tj-controlls tj-controlls-' + options.dir + 'mode"></div>').appendTo($controlsWrapper);

                if (options.isGallery) {
                    self.$elem.find('.ms-thumb-list').wrapAll('<div class="tj-thumb-wrapper"></div>');
                }

                //Play pause button
                if (options.hasPlay) {
                    var playBtn = $('<div></div>').addClass('tj-playbtn').appendTo($controlUI);
                    self.addPlay(slider, playBtn, options);

                }

                //Next & Prev buttons
                $controlUI.append($controlsWrapper.find('.ms-nav-next'));
                //First one is prev and last next add anything in betwen(eg.counter)




                if (options.showCounter) {
                    var counterMarkup = '<div class="tj-ms-counter">' +
                        '<span class="counter-current">' + (slider.api.index() + 1) + '</span>' +
                        '<span class="counter-divider">/</span>' +
                        '<span class="counter-total">' + slider.api.count() + '</span>' +
                        '</div>';
                    var $counterMarkup = $(counterMarkup).appendTo($controlUI),
                        $current = $counterMarkup.find('.counter-current');
                    self.sliderCounter(slider, $current);
                }

                $controlUI.append($controlsWrapper.find('.ms-nav-prev'));


                $(window).trigger('resize');
            });


        }, sliderCounter: function (slider, $current) {
            var self = this;

            slider.api.addEventListener(MSSliderEvent.CHANGE_START, function () {
                //slider slide change listener
                $current.html('' + (slider.api.index() + 1) + '');
            });

        }, makeGallery: function (slider) {
            var self = this;
            //Do the gallery things here... 
            var direction = (self.$elem.hasClass('tj-vertical-gallery')) ? 'v' : 'h';

            slider.control('thumblist', { autohide: false, dir: direction });


        }, addPlay: function (slider, playbtn, options) {
            if (options.initialPlay) {
                slider.api.resume();
                playbtn.addClass('btn-pause');
            }
            playbtn.click(function () {
                if (slider.api.paused) {
                    slider.api.resume();
                    playbtn.addClass('btn-pause');
                } else {
                    slider.api.pause();
                    playbtn.removeClass('btn-pause');
                }
            });
        }
    };


    /* gallery proofing handler
   ----------------------------------------------*/
    var galleryProofing = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$counter = $('.accepted-count');
            this.$proofForm = $('form.proofing-submission');
            this.$imgIDsInput = this.$proofForm.find('#accepted-photos');
            this.$notificationElem = $("#feedback");
            this.generateLoader();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;

            self.$elem.on('click', '.em-proofing-controls', function () {
                var $item = $(this).parents('.gallery-item');
                $item.toggleClass('em-accepted');

                self.update($item);
            });

            self.$proofForm.on('submit', function (e) {
                e.preventDefault();
                self.saveSelectedIDs(self.$imgIDsInput.val());
            });
        },
        update: function ($item) {
            var self = this,
                acceptedCount = parseInt(self.$counter.text()),
                imgValues = self.$imgIDsInput.val().split(","),
                itemID = $item.data('id').toString();

            if ($item.hasClass('em-accepted')) {
                imgValues.push(itemID);
                acceptedCount++;
            } else {
                imgValues.splice(imgValues.indexOf(itemID), 1);
                acceptedCount--;
            }
            imgValues = self.cleanArray(imgValues);
            self.$imgIDsInput.val(imgValues.join(","));
            self.$counter.stop().text(acceptedCount).hide(0).velocity('transition.slideDownIn', 500);
        },
        saveSelectedIDs: function (imgIDs) {
            var self = this;

            self.showLoader();
            $.ajax({
                url: eram_vars.adminAjaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    'action': "eram_gallery_proofing_update",
                    'post_id': self.$elem.find('.em-gallery').data('postid'),
                    'img_ids': imgIDs
                },
                success: function (ret) {
                    if (ret.status) {
                        self.$notificationElem.removeClass('error').addClass('success').find('.message').html(ret.msg);
                    } else {
                        self.$notificationElem.removeClass('success').addClass('error').find('.message').html(ret.msg);
                    }
                    self.showNotification();
                    self.hideLoader();
                },
                error: function (request, status, error) {
                    self.$notificationElem.removeClass('success').addClass('error').find('.error-message').html(request.responseText);
                    self.showNotification();
                }
            });
        },
        showNotification: function () {
            var self = this;

            self.$notificationElem
                .velocity("transition.slideRightBigIn", 500)
                .delay(3000)
                .velocity("transition.slideRightBigOut", 500)
        },
        generateLoader: function () {
            var self = this;

            self.loaderElem = $('<div>').addClass('em-infinite-loader').html('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>').appendTo(self.$proofForm);

        },
        showLoader: function () {
            var self = this;

            self.loaderElem.fadeIn();

        },
        hideLoader: function () {
            this.loaderElem.fadeOut();
        },
        cleanArray: function (actual) {
            var newArray = new Array();
            for (var i = 0; i < actual.length; i++) {
                if (actual[i]) {
                    newArray.push(actual[i]);
                }
            }
            return newArray;
        },
    }

    /* lightgallery  handler
    ----------------------------------------------*/
    var lightGallery = {
        init: function ($elem) {
            this.$elem = $elem;
            this.makeLightGallery();
            this.lightboxExtraClasses = '';

            var self = this;

            if ($elem.data('lightbox-mode') == 'advanced' && $.og.$window.width() > 992) {
                self.initAdvancedMode();
            }

            if ($.og.$body.hasClass('eram-lightbox-dark')) {
                self.lightboxExtraClasses = 'eram-theme-dark';
            } else {
                self.lightboxExtraClasses = 'eram-theme-light';
            }


            this.bindGeneralEvents();
        },
        initAdvancedMode: function () {
            var self = this;

            self.$sidebar = $('<div id="ol-lightbox-sidebar"></div>');
            self.$contentWrapper = $('<div class="content-wrapper"></div>').appendTo(self.$sidebar);
            self.$sidebar.append($('<div class="em-loader"><div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div></div>'));

            self.parseTemplate();
            self.bindAdvancedEvents();
        },
        bindGeneralEvents: function () {
            var self = this;

            self.$elem.on('onAfterOpen.lg', function (event) {
                $('.lg-outer').addClass(self.lightboxExtraClasses);
            });

            self.$elem.on('appended-new-items', function () {
                self.updateLightGallery();
            });
        },
        bindAdvancedEvents: function () {
            var self = this;

            self.$elem.on('onAfterOpen.lg', function (event) {
                self.appendSidebar();
                $.og.$body.addClass('ol-advanced-lightbox');
            });

            self.$elem.on('onBeforeSlide.lg', function (event, prevIndex, index, fromTouch, fromThumb) {
                self.loadContents(index);
            });

            self.$elem.on('onAfterClose.lg', function (event) {
                $.og.$body.removeClass('ol-advanced-lightbox');
            });

        },
        makeLightGallery: function () {
            var self = this;
            self.$elem.lightGallery({
                selector: '.lightbox-item',
                loadYoutubeThumbnail: false,
                loadVimeoThumbnail: false,
                mode: eram_vars.lg_mode || 'lg-fade',
                download: eram_vars.lg_download,
                counter: eram_vars.lg_counter,
                controls: eram_vars.lg_controls,
                keyPress: eram_vars.lg_keyPress,
                escKey: eram_vars.lg_escKey == '1',
                loop: eram_vars.lg_loop,
                thumbnail: eram_vars.lg_thumbnail,
                showThumbByDefault: eram_vars.lg_showThumbByDefault,
                autoplay: eram_vars.lg_autoplay,
                progressBar: eram_vars.lg_progressBar,
                zoom: eram_vars.lg_zoom,
            });
        },
        updateLightGallery: function () {
            var self = this;

            self.$elem.data('lightGallery').destroy(true);
            self.makeLightGallery();
        },
        appendSidebar: function ($lgWrapper) {
            var self = this;
            $('.lg-outer').append(self.$sidebar);
        },
        parseTemplate: function () {
            var self = this;

            self.template = $('#ol-lightbox-sidebar-template').html();
            Mustache.parse(self.template);
        },
        getPostId: function (slideIndex) {
            var self = this,
                postId = 0,
                $currentItem;

            // get the corresponding element
            $currentItem = self.$elem.find('.gallery-item:eq(' + slideIndex + ')');

            if ($currentItem.length) {
                postId = $currentItem.data('id');
            }

            return postId;
        },
        loadContents: function (slideIndex) {
            var self = this,
                postId = 0;

            self.showLoader();

            postId = self.getPostId(slideIndex);

            $.ajax({
                url: eram_vars.adminAjaxUrl,
                type: 'POST',

                data: {
                    action: 'eramlightboxajax',
                    'post_id': postId,
                },
                dataType: "json",
                success: function (results) {
                    var rendered = Mustache.render(self.template, results);
                    self.$contentWrapper.html(rendered);
                    self.hideLoader();
                    $.og.$window.trigger('eram.lightboxContent');
                },
                error: function (error) {
                    alert(error);
                }
            });
        },
        showLoader: function () {
            this.$sidebar.addClass('show-loader');
        },
        hideLoader: function () {
            this.$sidebar.removeClass('show-loader');
        }
    }

    /* Invert scroll
    ----------------------------------------------*/
    function runInvertScroll() {

        if ($.og.touchDevice) {
            return false;
        }

        var $galleryWrapper = $('.gallery-wrapper.direction-horizontal'),
            hasSide = $galleryWrapper.find('.gallery-side').length,
            optionObj = {};


        if (hasSide) {
            var sideHandler = Object.create(gallerySideHandler);
            sideHandler.init($galleryWrapper);

            $(window).on('scroll', function () {
                sideHandler.onScroll($(this).scrollTop());
            });
        }

        $galleryWrapper.find('.em-gallery').olInvertScroll({
            $fixedElem: $galleryWrapper
        });

    };

    var gallerySideHandler = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$gallery = this.$elem.find('.em-gallery');
            this.$gallerySide = $elem.find('.gallery-side');
            this.sideWidth = this.$gallerySide.width();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;
            $.og.$window.on('resize', function () {
                self.sideWidth = self.$gallerySide.css('width', '').width();
            });
        },
        onScroll: function (scrollPos) {
            var self = this,
                tempWidth = Math.max(0, self.sideWidth - (Math.abs(scrollPos) * 1.2));

            self.$gallerySide.css({
                width: tempWidth,
                opacity: tempWidth / self.sideWidth
            })
        }
    }



    /* lazy load
    ----------------------------------------------*/
    function lazyloadImages($elems, $container) {
        if ($container == undefined) {
            $container = $.og.horizontalTouch ? $.og.$scrollWrapper : window;
        }
        $elems.filter(function () {
            return !$(this).hasClass('image-loaded');
        }).lazyload({
            failure_limit: Math.max($(this).length - 1, 0),
            threshold: 300,
            container: $container,
            skip_invisible: false,
            load: function () {
                var $this = $(this);

                $this.addClass('image-loaded');
                $this.parent().removeClass('loading-lazy');

                if ($this.hasClass('lazyBg')) {

                    $this.removeClass('lazyBg');
                    var src = $this.attr('src') || '';

                    $this.parents('.set-bg').first().css({
                        'background': 'url(' + src + ') no-repeat 50% 50%',
                        'background-size': 'cover'
                    });

                    $this.remove();

                }
                $this.trigger('lazyimage-loaded');

            }
        });

    }

    /* cover images in a container
    ----------------------------------------------*/
    function imageFill($container, callback) {

        setFillCSS($container, callback);

        $(window).on('resize', function () {
            setFillCSS($container, callback);
        });
    }

    function setFillCSS($container, callback) {
        var containerWidth = $container.width(),
            containerHeight = $container.height(),
            containerRatio = containerWidth / containerHeight,
            imgRatio;

        $container.find('img').each(function () {
            var img = $(this);
            imgRatio = parseInt(img.attr('width')) / parseInt(img.attr('height'));

            if (img.css('position') == 'static') {
                img.css('position', 'relative');
            }
            if (containerRatio < imgRatio) {
                // taller
                img.css({
                    width: 'auto',
                    height: containerHeight,
                    top: 0,
                    left: -(containerHeight * imgRatio - containerWidth) / 2
                });
            } else {
                // wider
                img.css({
                    width: containerWidth,
                    height: 'auto',
                    top: -(containerWidth / imgRatio - containerHeight) / 2,
                    left: 0
                });
            }
        });

        if (typeof (callback) == 'function') {
            callback();
        }
    }

    /* Fit images in a container
    ----------------------------------------------*/
    var imageFit = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$img = $elem.children('img').first();

            this.fit();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;
            $.og.$window.on('resize', function () {
                self.fit();
            });
        },
        fit: function () {
            var self = this,
                imgRatio = self.$img.attr('width') / self.$img.attr('height'),
                elemRatio = self.$elem.width() / self.$elem.height(),
                $mode;

            $mode = (imgRatio >= elemRatio) ? "wide" : "tall";

            self.$elem.attr('data-mode', $mode);
        }
    }




    /* Handles sidebar 
    ----------------------------------------------*/
    var sideHeader = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$menu = $elem.find('#slide-menu');
            this.$content = $elem.find('.side-content');
            this.$trigger = $('.menu-trigger');
            this.$menuOpenFlg = $.og.$body.hasClass('show-menu');
            this.inAnimation = false;
            this.openMenus = 0;

            if (eram_vars.mobile_menu_is == "primary-menu") {
                this.handleResponsiveMenus();
            }

            if (!$.og.touchDevice) {
                var $scrollWrapper = (this.$elem.attr('id') == "side-header") ? this.$elem.find('.em-scroll-wrapper') : this.$content;
                $scrollWrapper.niceScroll({
                    cursorcolor: '#a9a9a9',
                    cursorwidth: 5,
                    cursorborder: 'none',
                    background: '',
                    horizrailenabled: false,
                    nativeparentscrolling: false

                });
            }

            this.setStaggers();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;

            self.$elem.on('click', 'li.menu-item-has-children >a', function (e) {
                e.preventDefault();
                var $parentLi = $(this).parent('li');


                self.closeAll($parentLi.siblings());
                self.animateOne($parentLi, $parentLi.hasClass('active'));
            });

            self.$trigger.on('click', function (e) {
                e.preventDefault();
                if (self.inAnimation)
                    return;
                self.sideAnimate();

            });

            // customizer cahnge menu
            $(document).on('customize-preview-menu-refreshed', function (e, params) {

                self.$menu = self.$elem.find('#slide-menu');
                self.setStaggers();
            });


        },
        animateOne: function ($elem, closeFlag) {
            var $parentLi = $elem,
                $subMenu = $elem.children('.sub-menu'),
                $subLis = $subMenu.children('li'),
                sequence;


            if (closeFlag) {
                $subLis.velocity("transition.slideUpOut", { duration: 150, display: "block", visibility: "hidden" });
                $subMenu.velocity("slideUp", { duration: 150, easing: "easeOut" });
                $elem.removeClass('active');
            } else {
                sequence = [
                    {
                        e: $subMenu,
                        p: "slideDown",
                        o: { duration: 200 }
                    },
                    {
                        e: $subLis,
                        p: "transition.fadeIn",
                        o: { duration: 500, visibility: "visible" }
                    },

                ];
                $.Velocity.RunSequence(sequence);
                $elem.addClass('active');
            }
            this.handleOpenMenus($elem, closeFlag);

        },
        closeAll: function ($elems) {
            //close all sub-menus in given elems
            var self = this;
            $elems.each(function () {
                var $this = $(this);

                if (!$this.hasClass('active'))
                    return;

                var $subMenu = $this.children('.sub-menu'),
                    $subLis = $subMenu.children('li');

                $subLis.velocity("transition.slideUpOut", { duration: 150, display: "block", visibility: "hidden" });
                $subMenu.velocity("slideUp", { duration: 150, easing: "easeOut" });
                $this.removeClass('active');
                self.handleOpenMenus($this, true);
            });
        },
        sideAnimate: function () {
            var self = this;
            self.inAnimation = true;
            self.$trigger.children('.hamburger').toggleClass("is-active");


            if (self.menuOpenFlg) {
                //stagger all elems to the left and close the sidebar
                self.menuOpenFlg = false;
                self.$animationStaggers.velocity("transition.slideLeftOut", {
                    delay: 150, duration: 100, stagger: 10, backwards: true, display: "block", visibility: "hidden", easing: "easeOut", complete: function () {
                        self.$content.hide();
                        self.inAnimation = false;
                    }
                });
            }
            else {
                self.$content.show();
                self.menuOpenFlg = true;
                self.$animationStaggers.velocity("transition.slideLeftIn", {
                    delay: 200, duration: 300, stagger: 80, display: "block", visibility: "visible", complete: function () {

                        self.inAnimation = false;
                    }
                });
            }
            $.og.$body.toggleClass('show-menu');

        },
        setStaggers: function () {
            this.$animationStaggers = $();

            this.$animationStaggers = this.$animationStaggers.add(this.$menu.children('li'));
            this.$animationStaggers = this.$animationStaggers.add(this.$elem.find('.stagger-animation'));
        },
        handleOpenMenus: function ($li, closeMode) {
            var self = this;
            if ($li.parent('ul.menu').length) {
                if (closeMode) {
                    self.openMenus--;
                } else {
                    self.openMenus++;
                }
                if (self.openMenus > 0) {
                    this.$menu.addClass('has-open-submenu');
                } else {
                    this.$menu.removeClass('has-open-submenu');
                }
            }
        },
        handleResponsiveMenus: function () {
            var self = this,
                mobileMode,
                isMobile;


            mobileMode = false;
            self.$menuOriginal = self.$menu;
            self.$menuClones = $('#classic-menu,#list-menu').clone().removeAttr('id').addClass('menu-clone');
            $('#side-area .slide-menu-wrapper').append(self.$menuClones).addClass('has-cloned-menu');

            if (checkMobile()) {
                self.$menu = self.$menuClones;
                self.mobileFlg = true;
            }

            $(window).on('resize', function () {
                if (self.menuOpenFlg) {
                    self.sideAnimate();
                }
                isMobile = checkMobile();
                if (isMobile && !mobileMode) {
                    self.$menu = self.$menuClones;
                    self.setStaggers();
                    mobileMode = true;
                } else if (!isMobile && mobileMode) {
                    self.$menu = self.$menuOriginal;
                    self.setStaggers();
                    mobileMode = false;
                }

            });
        }

    }


    /* Parallax Backgrounds
     ----------------------------------------------*/
    var parallaxer = {
        settings: function () {
            return {
                // zoom in move
                "mode-1": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1);",
                    'end': "transform:translate3d(0px, %distance%px, 0.1px) scale(1.4);",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'distance'
                },
                // zoom out move
                "mode-2": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1.4);",
                    'end': "transform:translate3d(0px, %distance%px, 0.1px) scale(1);",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'distance'
                },
                // zoom in
                "mode-3": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1);",
                    'end': "transform:translate3d(0px, 0px, 0.1px) scale(1.4);",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'none'
                },
                //zoom out
                "mode-4": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1.4);",
                    'end': "transform:translate3d(0px, 0px, 0.1px) scale(1);",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'none'
                },
                // zoom in opacity up
                "mode-5": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1); opacity:0;",
                    'end': "transform:translate3d(0px, 0px, 0.1px) scale(1.4); opacity:2",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'none'
                },

                // zoom out opacity down
                "mode-6": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1); opacity:2;",
                    'end': "transform:translate3d(0px, 0px, 0.1px) scale(1.4); opacity:-1",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'none'
                },
                "mode-title": {
                    'start': "transform:translate3d(0px, 0px, 0.1px) scale(1); opacity:2;",
                    'end': "transform:translate3d(0px, 0px, 0.1px) scale(1.4); opacity:-1",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'none'
                },
                "mode-header-content": {
                    'start': "transform:translate3d(0px, 0px, 0.1px);opacity:1",
                    'end': "transform:translate3d(0px, %height%px, 0.1px);opacity:-0.5",
                    'data-anchor-target': ".page-head",
                    'type': 'height'
                },
                "mode-header-demo-2": {
                    'start': "transform:translate3d(0px, 0px, 0.1px);opacity:1",
                    'end': "transform:translate3d(0px, %height%px, 0.1px);opacity:-1",
                    'data-anchor-target': ".page-head",
                    'type': 'height'
                },
                "default": {
                    'start': "transform:translate3d(0px, 0px, 0.1px);",
                    'end': "transform:translate3d(0px, %distance%px, 0.1px);",
                    'data-anchor-target': "." + this.$elem.UniqueClass,
                    'type': 'distance'
                }
            }
        },
        init: function ($elem) {
            this.$elem = $elem;
            this.$elem.readyState = false;
            this.attsObj = {};
            this.parallaxMode = this.$elem.data('parallax-mode') ? this.$elem.data('parallax-mode') : 'default';

            this.setAttributes();


            //wait for the prepare to be done.
            var self = this;

            this.prepare(function () {
                self.$elem.readyState = true;
                self.$elem.trigger('parallaxReady');
                self.$layer.addClass("parallax-" + self.parallaxMode);

                self.getAnimations();
                self.setAnimations();


            });

            this.bindUIActions();

        },
        setAttributes: function () {
            this.elementOffsetTop = this.$elem.offset().top;
            this.elemHeight = this.$elem.outerHeight();
            this.elemWidth = this.$elem.outerWidth();
            this.windowHeight = $.og.$window.height();
        },
        bindUIActions: function () {
            var self = this;

        },
        prepare: function (callback) {
            var self = this;

            var prefix = "ol-para-bg-";
            self.$elem.removeClassPrefix(prefix);
            self.$elem.UniqueClass = prefix + makeid();
            this.$elem.addClass(this.$elem.UniqueClass);

            // get imagesrc
            var imgSrc = self.$elem.data('img-src');
            if (!imgSrc) {
                self.$layer = self.$elem;
                callback();
                return false;
            }

            // create imageLayer
            self.$imageLayer = $('<div></div>').addClass('parallax-bg-elem');

            // add image to it's background
            self.$imageLayer.css('background-image', 'url(' + imgSrc + ')');


            //append imageLayer to the container
            self.$elem.append(self.$imageLayer);

            // get image size
            self.getImageSize(imgSrc, function (imgDimesnion) {


                //set bg element's height and enshure that it is bigger than the container so can be parallaxed
                self.$imageLayer.height = self.elemWidth / (imgDimesnion.width / imgDimesnion.height);
                self.$imageLayer.height = Math.max(self.elemHeight * 1.5, self.$imageLayer.height);

                // set height
                self.$imageLayer.css({
                    height: self.$imageLayer.height,
                    // in case we want to center align
                    top: -(self.$imageLayer.height - self.elemHeight) / 2
                });

                self.$layer = self.$imageLayer;
                // we are done!
                callback();

            });
        },
        getAnimations: function () {
            var self = this;

            self.attsObj = self.settings()[self.parallaxMode];
            self.attsObj = self.assignVariables(self.attsObj);

            //Detect start and end range
            self.attsObj = self.assignRange(self.attsObj);
        },
        setAnimations: function () {
            var self = this;
            self.$layer.attr(self.attsObj);
        },
        destroyAnimations: function () {
            var self = this;
            self.removeDataAttributes(self.$layer);
        },
        getImageSize: function (imgSrc, callback) {
            var img = new Image();
            img.src = imgSrc;
            img.onload = function () {
                var imgDimesnion = {
                    width: img.width,
                    height: img.height
                };
                callback(imgDimesnion);
            }
        },
        assignRange: function (attsObj) {
            var self = this,
                start, end, obj = attsObj;

            if (self.elementOffsetTop > self.windowHeight / 2) {
                start = 'data-bottom-top';
                end = 'data-top-bottom'
            } else {
                start = 'data-top-top';
                end = 'data-top-bottom'
            }

            if (attsObj['start']) {
                obj[start] = attsObj['start'];
                delete obj['start'];
            }

            if (attsObj['end']) {
                obj[end] = attsObj['end'];
                delete obj['end'];
            }
            return obj;
        },
        calcDistance: function () {
            var self = this,
                distance;

            if (self.elementOffsetTop > self.windowHeight) {
                distance = self.windowHeight + self.elemHeight;
            } else {
                distance = self.elemHeight + self.elementOffsetTop;
            }
            var maxRatio = (self.$layer.height - self.elemHeight) / (2 * distance);
            self.elemBgRatio = self.$elem.data('bg-parallax-factor') ? self.$elem.data('bg-parallax-factor') : Math.min(maxRatio, 0.6);

            var ratio = Math.max(Math.min(Math.abs(self.elemBgRatio), maxRatio), 0.05);

            return distance * ratio * Math.sign(self.elemBgRatio);
        },
        assignVariables: function (attsObj) {
            var self = this;

            if (attsObj['type'] == 'height') {
                attsObj['end'] = attsObj['end'].replace(/%\w+%/g, parseInt($('.page-head').height() / 2));
            } else if (attsObj['type'] == 'distance') {
                attsObj['end'] = attsObj['end'].replace(/%\w+%/g, parseInt(self.calcDistance()));
            }

            return attsObj;
        },
        removeDataAttributes: function ($target) {
            var i,
                attrName,
                dataAttrsToDelete = [],
                dataAttrs = $target.get(0).attributes,
                dataAttrsLen = dataAttrs.length;

            for (i = 0; i < dataAttrsLen; i++) {
                if ('data-' === dataAttrs[i].name.substring(0, 5)) {
                    dataAttrsToDelete.push(dataAttrs[i].name);
                }
            }
            $.each(dataAttrsToDelete, function (index, attrName) {
                // remove attr from element
                $target.removeAttr(attrName);
                // remove data
                $target.removeData(attrName.substr(5));
            });
        }
    };

    /* Skrollr Parallax
     ----------------------------------------------*/
    var skrollrHandler = {
        init: function () {
            this.skrollrFlg = false;

            if (olIsTouchDevice()) return false;

            this.makeDecision();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;
            $(window).on('debouncedresize', function () {
                self.makeDecision();
            });
        },
        makeDecision: function () {
            var self = this;
            if ($(window).width() > 767) {
                if (!self.skrollrFlg) {
                    self.initSkrollr();
                    self.skrollrFlg = true;
                }
            } else {
                self.destroy();
            }
        },
        initSkrollr: function () {
            skrollr.init({
                forceHeight: false
            });
        },
        destroy: function () {

            skrollr.get() && skrollr.get().destroy();
            self.skrollrFlg = false;
        },
        updateSkrollr: function () {
            var self = this;
            if (self.skrollrFlg) {
                self.destroy();
                self.initSkrollr();
            }
        }
    };

    /* Handles Background aware elements for
     * darkness and lightness
     * relies on background-check.js
     ----------------------------------------------*/
    var bgLightnessCheck = {
        init: function () {
            this.targets = '.em-bg-aware';
            this.run();
            this.bindUIActions();
        },
        run: function () {
            var self = this;
            BackgroundCheck.init({
                targets: self.targets
            });
        },
        bindUIActions: function () {
            var self = this;
            $(window).on('kbs.slideStart', function () {
                self.refresh();
            });
        },
        refresh: function () {
            BackgroundCheck.refresh();
        }
    }

    /*  Sticky elements handler
    ----------------------------------------------*/
    var sticky_relocate = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$anchor = $elem.children('.sticky-anchor');
            this.$stickyEl = $elem.children('.sticky-elem');
            this.elHeight = this.$stickyEl.outerHeight();
            this.offset = $elem.data('offset') || 20;
            this.threshold = this.$stickyEl.offset().top + this.elHeight + this.offset;
            this.isSticky = false;

            this.stickyCore();
            this.bindUIActions();
        },
        bindUIActions: function () {
            var self = this;
            $.og.$window.on('scroll', function () {
                self.stickyCore();
            });
            $.og.$window.on('resize', function () {
                self.elHeight = self.$stickyEl.outerHeight();
                self.threshold = self.$stickyEl.offset().top + self.elHeight + self.offset;
                self.stickyCore();
            });
        },
        stickyCore: function () {
            var self = this,
                windowTop = $.og.$window.scrollTop();

            if (windowTop > self.threshold) {
                if (!self.isSticky) {
                    self.isSticky = true;
                    self.$stickyEl.addClass('is-sticky layout-padding');
                    self.$anchor.height(self.elHeight);
                }
            } else {
                if (self.isSticky) {
                    self.isSticky = false;
                    self.$stickyEl.removeClass('is-sticky layout-padding');
                    self.$anchor.height(0);
                }

            }

        }
    }


    /* Run DoubleCarousel gallery and projects
    ----------------------------------------------*/
    var doubleCarouselHandler = {
        init: function ($elem) {
            var self = this;
            self.$elem = $elem;
            self.mode = $elem.hasClass('type-project-carousel') ? 'projects' : 'gallery';

            if (self.mode == "projects") {
                self.prepare();
                self.projectCarousel();
            } else {
                self.galleryCarousel();
            }
        },
        galleryCarousel: function () {
            var self = this;
            self.$elem.DoubleCarousel({
                itemSelector: '.gallery-item',
                autoplay: true,
                bulletControll: self.$elem.data('show-bullet'),
                leftSideDuration: self.$elem.data('transition-duration'),
                rightSideDuration: self.$elem.data('transition-duration'),
                mobileMergeSides: true,
                touchSwipe: true
            });
        },
        projectCarousel: function () {
            var self = this;
            self.$elem.DoubleCarousel({
                rightSideDir: "down",
                leftSideDir: "up",
                leftSideDuration: self.$elem.data('duration'),
                rightSideDuration: self.$elem.data('duration'),
                mouse: self.$elem.data('mouse'),
                keyboard: self.$elem.data('keyboard'),
                touchSwipe: self.$elem.data('touch'),
                bulletControll: self.$elem.data('bullet'),
                bulletNumber: false,
                bulletCenter: "vertical",
                autoplay: self.$elem.data('autoplay'),
                duration: self.$elem.data('auto-duration')
            });
        },
        prepare: function () {
            var self = this,
                $leftWrapper = self.$elem.find('.left-side-wrapper'),
                $rightWrapper = self.$elem.find('.right-side-wrapper'),
                $leftItems = self.$elem.find('.dc-item .dc-left-side'),
                $rightItems = self.$elem.find('.dc-item .dc-right-side');

            $leftItems.each(function () {
                $leftWrapper.append($(this).html());
            });
            $rightItems.each(function () {
                $rightWrapper.append($(this).html());
            });
            self.$elem.find('.dc-item').remove();
        }
    }

    /* single Image, gallery and video lightbox
    ----------------------------------------------*/
    var lightBox = {

        init: function () {
            var self = this,
                descMode;

            self.localvideo = {
                autoPlay: false,
                preload: 'metadata',
                webm: true,
                ogv: false
            };

            $('.ol-lightbox').each(function () {
                var $this = $(this);
                if (!$this.parents('.ol-lightbox-gallery').length)
                    self.singleBox($this);
            });


            $('.ol-lightbox-gallery').each(function () {
                self.galleryBox($(this));
            });

            this.bindUIActions();


        },
        generateVideo: function (src, poster) {
            var self = this;
            //here we generate video markup for html5 local video
            var basePath = src.substr(0, src.lastIndexOf('.mp4'));
            var headOptions = '';
            if (self.localvideo.autoPlay) {
                headOptions += ' autoplay';
            }
            headOptions += 'preload="' + self.localvideo.preload + '"';

            var markup = '<video class="mejs-player popup-mejs video-html5" controls ' + headOptions + ' poster="' + poster + '">' +
                '<source src="' + src + '" type="video/mp4" />';

            if (self.localvideo.webm) {
                markup += '<source src="' + basePath + '.webm" type="video/webm" />';
            }

            if (self.localvideo.ogv) {
                markup += '<source src="' + basePath + '.ogv" type="video/ogg" />';
            }
            markup += '</video>' + '<div class="mfp-close"></div>';

            return markup;

        },
        generatedesc: function (descMode) {
            var self = this,
                markup = '<div class="container">' +
                    '<div class="mfp-figure ' + descMode + '">' +
                    '<button title="Close (Esc)" type="button" class="mfp-close">x</button>' +
                    '<figure>' +
                    '<div class="wrapper">' +
                    '<div class="mfp-description">' +
                    '<figcaption class="mfp-figcaption">' +
                    '<div class="mfp-title"></div>' +
                    '<div class="mfp-desc"></div>' +
                    '</figcaption>' +
                    '</div>' +
                    '<div class="mfp-content-container">' +
                    '<div class="mfp-img"></div>' +
                    '</div>' +
                    '</div>' +
                    '</figure>' +
                    '</div>' +
                    '</div>';
            return markup;
        },
        bindUIActions: function () {
            var self = this,
                $body = $('body');



            $('body').on('click', '.mfp-container', function (e) {
                if (e.target !== this)
                    return;
                $(this).find('.mfp-close').trigger('click');
            });


        },
        singleBox: function ($elem) {
            var self = this;
            $elem.magnificPopup({
                type: 'image',
                closeOnContentClick: false,
                closeOnBgClick: false,
                mainClass: 'mfp-fade',
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">' +
                        '<div class="mfp-close"></div>' +
                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                        '<div class="mfp-title"></div>' +
                        '</div>'
                },
                image: {
                    verticalFit: true,
                },
                callbacks: {
                    elementParse: function (item) {
                        var popType = item.el.attr('data-type') || 'image';
                        if (popType == 'descriptive') {
                            item.type = 'image';
                            if (item.el.attr('data-type') == 'descriptive') {
                                descMode = 'with-desc';
                                if (item.el.hasClass('horizontal')) {
                                    descMode.concat('horizontal');
                                }
                                this.st.image.markup = self.generatedesc(descMode);
                            }
                        } else {
                            item.type = popType;
                        }
                    },
                    markupParse: function (template, values, item) {
                        if (item.el.attr('title')) {
                            values.title = '<h3 class="title">' + item.el.attr('title') + '</h3>';
                        }
                        if (item.el.attr('desc')) {
                            values.desc = item.el.attr('desc');
                        }
                    },
                    open: function () {
                    }
                }
            });

        },
        galleryBox: function ($elem) {
            var self = this,
                $this = $elem,
                itemsArray = [];

            $elem.magnificPopup({
                delegate: '.ol-lightbox',
                closeOnBgClick: false,
                closeOnContentClick: false,
                removalDelay: 300,
                mainClass: 'mfp-fade',
                iframe: {
                    markup: '<div class="mfp-iframe-scaler">' +
                        '<div class="mfp-close"></div>' +
                        '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
                        '<div class="mfp-title"></div>' +
                        '<div class="mfp-counter"></div>' +
                        '</div>'
                },
                gallery: {
                    enabled: true,
                    tPrev: 'Previous',
                    tNext: 'Next',
                    tCounter: '%curr% / %total%',
                    arrowMarkup: '<a class="tj-mp-action tj-mp-arrow-%dir% mfp-prevent-close" title="%title%"><i class="fa fa-angle-%dir%"></i></a>',
                },
                callbacks: {
                    elementParse: function (item) {

                        var popType = item.el.attr('data-type') || 'image',
                            source = item.el.attr('href');

                        if (popType == 'descriptive') {
                            item.type = 'image';
                            if (item.el.attr('data-type') == 'descriptive') {
                                descMode = 'with-desc';
                                if (item.el.hasClass('horizontal')) {
                                    descMode.concat('horizontal');
                                }
                                this.st.image.markup = self.generatedesc(descMode);
                            }
                        } else {
                            item.type = popType;
                        }
                    },
                    markupParse: function (template, values, item) {
                        if (item.el.attr('title')) {
                            values.title = '<h3 class="title">' + item.el.attr('title') + '</h3>';
                        }
                        if (item.el.attr('desc')) {
                            values.desc = item.el.attr('desc');
                        }
                    },
                    open: function () {
                    },
                    change: function () {
                        if (this.isOpen) {
                            this.wrap.addClass('mfp-open');
                        }
                    }
                },
                type: 'image' // this is a default type
            });

            itemsArray = [];
        }
    };

    /* Retina Images Handler
    ----------------------------------------------*/
    var retina = {
        init: function ($elem) {
            this.$elem = $elem;

            if (!isRetinaDisplay()) return false;

            var imgSrc = $elem.attr('src');
            if (!imgSrc) return false;

            //Generate retina image path based on the suffix
            var retinaSrc = $elem.data('retina');
            if (!retinaSrc) return false;

            this.setRetina(retinaSrc);

        },
        setRetina: function (retinaSrc) {
            var self = this;
            self.$elem.attr('src', retinaSrc);
            self.$elem.attr('width', parseInt(self.$elem.data('retina-width') / 2));
            self.$elem.attr('height', parseInt(self.$elem.data('retina-height') / 2));
        }
    };


    var handleFullScreen = {
        init: function ($elems) {
            this.$elems = $elems;

            this.setHeight();
            this.bindUIActions();
        },
        setHeight: function () {
            var $mainArea = $('#main-area'),
                height = $(window).height() - parseInt($mainArea.css('padding-top')) - parseInt($mainArea.css('padding-bottom'));
            this.$elems.each(function () {
                var $this = $(this);
                if ($this.hasClass('min-height-full')) {
                    $this.css('min-height', height);
                } else {
                    $this.height(height);
                }

            });
        },
        bindUIActions: function () {
            var self = this;
            $(window).on('resize', function () {
                self.setHeight();
            });
        }
    }

    var handleMaskReveal = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$maskElem = $elem.find('.mask-elem');


            var self = this,
                $maskWrapper = $elem.find('.mask-wrapper');

            $elem.find('.mask-clone').imagesLoaded(function () {
                $maskWrapper.addClass('ol-animate');
                self.set();
                createObjInstance($maskWrapper, revealAnimate);
            });

            this.bindUIActions();
        },
        set: function () {
            var self = this;
            self.$maskElem.width(this.$elem.width());
            self.$maskElem.height(this.$elem.height());
        },
        bindUIActions: function () {
            var self = this;
            $(window).on('resize', function () {
                self.$maskElem.width('');
                self.$maskElem.height('');
                self.set();
            });
        }
    }

    var goFullScreen = {
        init: function ($elem) {
            this.$elem = $elem;
            this.$wrapper = $elem.parents('.fullscreen-wrapper').first();

            this.bindUIActions();
        },
        bindUIActions: function ($elem) {
            var self = this;

            self.$elem.on('click', function () {
                self.$wrapper.toggleClass('full-view');
            });

            $(document).keydown(function (e) {
                if (e.which == 27 && self.$wrapper.hasClass('full-view')) {
                    self.$wrapper.removeClass('full-view');
                }
            });
        }
    }

    var stickyTransparentHeader = {
        init: function ($elem) {
            this.$elem = $elem;
            this.threshold = 300;
            this.hasBg = false;

            this.bindEvents();
        },
        bindEvents: function () {
            var self = this;
            $(window).on('scroll', function () {
                var thresholdFlag = ($(this).scrollTop() > self.threshold);
                if (thresholdFlag && !self.hasBg) {
                    self.$elem.addClass('em-sticky-background');
                    self.hasBg = true;
                } else if (!thresholdFlag && self.hasBg) {
                    self.$elem.removeClass('em-sticky-background');
                    self.hasBg = false;
                }
            });
        }
    }

    /* Columizes the main menu 
    *  at top header site layout
    ----------------------------------------------*/
    function topMainMenuHandler() {
        var $topMenu = $('#list-menu'),
            liCount = $topMenu.find('li').length;
        $topMenu.columnize({
            width: Math.ceil($topMenu.width() / liCount * 4) + 50,
            height: $topMenu.height() - 30
        });
    }

    /* Create Object Instance
    ----------------------------------------------*/
    function createObjInstance(selector, objName, options) {
        $(selector).each(function () {
            var obj = Object.create(objName);
            obj.init($(this), options);

        });
    }

    /* Carousel for gallery
     ----------------------------------------------*/
    function galleryCarouselHandler() {
        //Owl carousel
        $(".gallery-carousel").owlCarousel({

            navigation: true,
            slideSpeed: 300,
            pagination: false,
            paginationSpeed: 400,
            singleItem: true,
            itemsScaleUp: true,
            navigationText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
            autoHeight: true

        });
    }


    /* trigger background videos
     ----------------------------------------------*/
    function videobg() {
        $('.owl-videobg:not(.manual-trigger)').owlVideoBg({

            autoGenerate: {
                posterImageFormat: 'png'
            },
            preload: 'auto'

        });
    }

    function copyrightNotifier() {

        if (eram_vars.show_copyright_note == '0')
            return false;

        $('body').on('contextmenu', function (e) {
            e.preventDefault();
            if (eram_vars.copyright_notice.length > 1) {
                $('.copy-tip').hide();
                var $tooltip = $("<div>" + eram_vars.copyright_notice + "</div>")
                    .addClass('copy-tip')
                    .css({ left: e.pageX, top: e.pageY });
                $('body').append($tooltip);

                $tooltip.fadeIn();
                setTimeout(function () {
                    $tooltip.fadeOut('fast', function () {
                        $tooltip.remove();
                    });
                }, 1000);

                return false;
            }
        });
    }

    function runRrailCarousel() {
        var $carousel = $('.ol-rail-carousel'),
            hasDynamicBg,
            hasDynamicBgImage,
            enableParallax;

        if ($carousel.data('dynamic-bg') === undefined) {
            hasDynamicBg = true;
        } else {
            hasDynamicBg = $carousel.data('dynamic-bg') ? true : false;
        }
        if ($carousel.data('parallax') === undefined) {
            enableParallax = true;
        } else {
            enableParallax = $carousel.data('parallax') ? true : false;
        }
        if ($carousel.data('dynamic-bgimage') === undefined) {
            hasDynamicBgImage = true;
        } else {
            hasDynamicBgImage = $carousel.data('dynamic-bgimage') ? true : false;
        }

        $carousel.olRailCarousel({
            railZoom: 0.6,
            dynamicBackground: hasDynamicBg,
            enableParallax: enableParallax,
            dynamicBackgroundImage: hasDynamicBgImage,
        });
    }




    function checkMobile() {
        return $.og.$window.width() < 992;
    }

    function animateScroll() {

        $('.ol-scroll-to').on('click', function (e) {
            e.preventDefault();
            var $target = $($(this).attr('href'));
            if ($target.length) {
                $.og.$body.velocity("scroll", { offset: $target.offset().top, duration: 400, easing: "ease-in-out" });
            }
        });
    }

    function wooCommerceDropdown() {
        $('.woocommerce-ordering .orderby, .variations_form select').olDropdown();
    }

    function wooCommerceUpdateCartIcon() {
        // woocommerce add to cart ajax
        var $counter = $('#shop-cart-icon .counter');
        if (!$counter.length)
            return false;

        $.og.$body.on('added_to_cart', function (e, c) {
            $counter.text(parseInt($counter.text()) + 1);
        });

        $.og.$body.on('wc_fragments_refreshed', function () {

            $.ajax({
                url: eram_vars.adminAjaxUrl,
                type: 'POST',
                cache: false,
                data: {
                    'action': 'eram_get_woo_cart_count',
                },
                success: function (resp) {
                    $counter.text(parseInt(resp));
                }
            });
        });
    }

    var woocommerceDescriptionTab = {
        init: function ($elem) {
            this.$elem = $elem;
            if (!$elem.find('#tab-description').length) {
                return false;
            }
            this.$tabs = $elem.children('.wc-tabs').children('li');
            this.$panels = $elem.children('.woocommerce-Tabs-panel');

            var activeIndex = this.$tabs.filter(function () {
                return $(this).hasClass('active');
            }).index();

            this.switchTabs(activeIndex);
            this.bindEvents();
        },
        switchTabs: function (index) {
            var self = this;

            self.$panels.addClass('em-visually-hidden');
            self.$panels.eq(index).removeClass('em-visually-hidden');

        },
        bindEvents: function () {
            var self = this;

            self.$tabs.on('click', function () {
                var index = $(this).index();
                self.switchTabs(index);
            });
        }
    }


    var productGallery = {
        init: function ($elem) {
            this.$mainGallery = $elem.find('.product-gallery-main');
            this.$thumbGallery = $elem.find('.product-gallery-thumbs');
            this.$productLeft = $elem.find('.er-product-left');
            this.isFixed = false;

            this.runSwiper();

            if ($.og.$window.width() > 1200) {
                this.runSticky();
            }

            this.bindEvents();
        },
        runSticky: function () {
            var self = this;

            self.$productLeft.stick_in_parent({
                offset_top: self.$productLeft.offset().top
            });
            self.isFixed = true;
        },
        runSwiper: function () {
            var self = this;
            var galleryTop = new Swiper(self.$mainGallery, {
                nextButton: '.swiper-button-next',
                prevButton: '.swiper-button-prev',
                spaceBetween: 10,
                keyboardControl: true,
                effect: 'fade',
                fade: {
                    crossFade: false
                }
            });
            var galleryThumbs = new Swiper(self.$thumbGallery, {
                spaceBetween: 10,
                centeredSlides: true,
                slidesPerView: 'auto',
                touchRatio: 0.2,
                slideToClickedSlide: true,
                direction: 'vertical'
            });
            galleryTop.params.control = galleryThumbs;
            galleryThumbs.params.control = galleryTop;
        },
        bindEvents: function () {
            var self = this;

            $.og.$window.on('resize', function () {
                var widthFlag = ($(this).width() > 1200);
                if (widthFlag && !self.isFixed) {
                    self.runSticky();
                } else if (!widthFlag && self.isFixed) {
                    self.$productLeft.trigger("sticky_kit:detach");
                    self.isFixed = false;
                }
            });
        }
    }

    function respondByImageHandler() {
        var $links = $('.respond-by-image');
        var $input = $('textarea#comment');
        if ($links.length < 1 || $input.length != 1)
            return false;

        $links.on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $this = $(this);
            var target = $this.attr('href');
            var imageURL = $this.data('image');
            var imageID = $this.data('id');
            var $input = $('textarea#comment');
            if (!imageURL || $input.length != 1) return false;

            $(target).velocity('scroll', {
                duration: 500,
                offset: -40,
                easing: 'ease-in-out'
            });

            // add description
            $('.extra-notice').remove();
            $input.before('<div class="extra-notice"><img class="prci" src="' + imageURL + '"/><p>' + eram_vars.pg_extra_notice + '</p></div>');

            $('form#commentform').on('submit', function () {
                var val = $input.val();
                console.log(val);
                $input.val('##' + imageID + '## ' + val);
                return true;
            });
        });

    }

    function imageCompare() {
        $(".eram-image-compare").each(function () {
            var $this = $(this);
            $this.imagesLoaded(function () {
                $this.twentytwenty();
            });
        });
    }
    //*********** Init Handler for all Functions *******//
    var initRequired = {
        init: function () {
            $.og = {
                $body: $('body'),
                $header: $('#header'),
                $window: $(window),
                touchDevice: olIsTouchDevice()
            };

            var self = this;

            var $galleryHorizontal = $('.gallery-wrapper.direction-horizontal');
            $.og.horizontalTouch = ($.og.touchDevice && $galleryHorizontal.length);
            $.og.$scrollWrapper = $.og.horizontalTouch ? $galleryHorizontal : $(window);

            if ($.og.touchDevice) {
                $.og.$body.addClass('eram-touch-device');
            }

            $.og.$window.on('eram.lightboxContent', function () {
                self.runOnAjax();
            });

            this.runPriorities();
            this.runMethods();
            this.runInlines();
            $.og.$window.trigger('eram-custom-js-done');
        },
        runMethods: function () {
            createObjInstance('.gallery-wrapper.direction-horizontal', horizontalGallery);
            createObjInstance('.type-grid.direction-vertical,.type-masonry.direction-vertical', verticalGallery);
            createObjInstance('.type-simple.direction-vertical', simpleVerticalGallery);
            createObjInstance('.type-justified .em-gallery', emJustifiedGallery);
            createObjInstance('.ol-animate:not(._wait)', revealAnimate);
            createObjInstance('.tj-ms-slider', msSlider);
            createObjInstance('#side-header, #side-area', sideHeader);
            createObjInstance('.parallax-layer', parallaxer);
            createObjInstance('.em-blog-wrapper.layout-masonry .em-blog-posts, .em-clients-list', simpleMasonry,
                {
                    itemSelector: '.post',
                    setHeight: false
                }
            );
            createObjInstance('.shop-categories-list', simpleMasonry,
                {
                    itemSelector: '.product-category',
                    setHeight: true
                }
            );
            createObjInstance('.gallery-wrapper.mode-proofing', galleryProofing);
            createObjInstance('.em-sticky-wrapper', sticky_relocate);
            createObjInstance('.ol-double-carousel', doubleCarouselHandler);
            createObjInstance('img.ol-retina', retina);
            createObjInstance('.em-fit-image', imageFit);
            createObjInstance('.em-go-fullscreen', goFullScreen);
            createObjInstance('.em-lightBox-gallery', lightGallery);
            createObjInstance('.er-product-scene', productGallery);
            createObjInstance('.em-transparent-header.em-header-has-scroll-bg #classic-header', stickyTransparentHeader);

            handleSocialSharing.init();
            lightBox.init();


        },
        runInlines: function () {
            //Run Inline functions Here
            runInvertScroll();
            runKenburnSlider();
            topMainMenuHandler();
            galleryCarouselHandler();
            videobg();
            runRrailCarousel();
            copyrightNotifier();
            animateScroll();
            wooCommerceDropdown();
            imageCompare();
            wooCommerceUpdateCartIcon();
            respondByImageHandler();

        },
        runPriorities: function () {
            //functions that should be excuted before anything else
            handleFullScreen.init($('.full-screen, .min-height-full').not('.manual_height'));
            createObjInstance('.woocommerce-tabs', woocommerceDescriptionTab);
            createObjInstance('.ol-mask-wrapper', handleMaskReveal);


        },
        runOnAjax: function () {
            handleSocialSharing.init();
        }
    };
    //*********** Init Handler for all Functions *******//

    //Run methods on DOM Ready
    $(document).ready(function () {
        initRequired.init();
    });

    //Run methods on Window load
    $(window).on('load', function () {
        skrollrHandler.init();

        // hack to trigger lazy images if page loads from middle
        $.og.$scrollWrapper.trigger("scroll");


    });

    // customizer cahnge menu
    $(document).on('customize-preview-menu-refreshed', function (e, params) {
        topMainMenuHandler();
    });




    // Add remove class with prefix to jQuery
    jQuery.fn.removeClassPrefix = function (prefix) {
        this.each(function (i, el) {
            var classes = el.className.split(" ").filter(function (c) {
                return c.lastIndexOf(prefix, 0) !== 0;
            });
            el.className = jQuery.trim(classes.join(" "));
        });
        return this;
    };

    //Check for Touch devices
    function olIsTouchDevice() {
        var agent = navigator.userAgent.toLowerCase(),
            isChromeDesktop = (agent.indexOf('chrome') > -1 && ((agent.indexOf('windows') > -1) || (agent.indexOf('macintosh') > -1) || (agent.indexOf('linux') > -1)) && agent.indexOf('mobile') < 0 && agent.indexOf('android') < 0);
        return ('ontouchstart' in window && !isChromeDesktop);
    }

    //Check for Retina devices
    function isRetinaDisplay() {
        if (window.matchMedia) {
            var mq = window.matchMedia("only screen and (min--moz-device-pixel-ratio: 1.3), only screen and (-o-min-device-pixel-ratio: 2.6/2), only screen and (-webkit-min-device-pixel-ratio: 1.3), only screen  and (min-device-pixel-ratio: 1.3), only screen and (min-resolution: 1.3dppx)");
            return (mq && mq.matches || (window.devicePixelRatio > 1));
        }
    }

    //Generates Random Text
    function makeid() {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 5; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

})(jQuery);