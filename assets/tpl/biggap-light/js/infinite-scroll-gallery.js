;
$(document).ready(function(){
    
    var page = 2;
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() == $(document).height()) {
            
           
            var totalItems = (page-1) * itemsPerPage;            
            var data = {
                "page": page++
            };            

            if (totalItems > total) {
                $('#no-more').show();
            } else {
                $('#more').show();
                $.ajax({
                    type: "GET",
                    url: Routing.generate('portfolio_medias_from_gallery', { name: name, gallery: gallerySlug }),
                    data: data,
                    success: function (data) {
                        $('#more').hide();
                        var newElements = $(data);
                        var elems = [];
                        newElements.each(function (i) {
                            elems.push(this);
                        });
                        $('.izotope-container').isotope('insert', elems);
                        $('.izotope-container').imagesLoaded().progress( function() {
                            $('.izotope-container').isotope();
                        });
                        $('.izotope-container').find('img').on('load', function () {
                        });
                        
                        $('.gallery-item').magnificPopup({
                            gallery: {
                                enabled: true
                            },
                            mainClass: 'mfp-fade',
                            fixedContentPos: false,
                            type: 'image'
                        });
                        newElements.find('img[data-lazy-src]').foxlazy();
                    }
                });
            }
        }
    });

});