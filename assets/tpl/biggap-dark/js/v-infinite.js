
function vueInit(elementId, options) {
    var element = document.getElementById(elementId);
    if (element) {
        options.el = element;
        new Vue(options);
    }
}

Vue.use(GoTop);
vueInit("infinite_home", {
    vuetify: new Vuetify(),
    data: {
        page: 1,
        images: [],
        infiniteId: +new Date(),
    },
    methods: {
        setview: function (id) {
            $.ajax({
                url: Routing.generate('setviewmedias', { name: name, image: id }),
                type: "GET",
                data: id,
                success: function (response) { },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        },
        infiniteHandler($state) {
            this.$http.get(Routing.generate('portfolio_medias', {
                name: name, page: this.page
            }), {
                params: {
                    page: this.page,
                }
            })
                .then(({ data }) => {
                    if (data.images.length > 0) {
                        this.page += 1;
                        this.images.push(...data.images);
                        if (this.page > data.totalPages) {
                            $state.complete();
                        }
                        $state.loaded();
                    } else {
                        $state.complete();
                    };
                });
        },
    }

});


vueInit("items-gallery", {
    vuetify: new Vuetify(),
    data: {
        page: 1,
        images: [],
        infiniteId: +new Date(),
    },
    methods: {
        setview: function (id) {
            $.ajax({
                url: Routing.generate('setviewmedias', { name: name, image: id }),
                type: "GET",
                data: id,
                success: function (response) { },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }
            });
        },
        infiniteHandler($state) {
            this.$http.get(Routing.generate('portfolio_medias_from_gallery', {
                slug: gallerySlug,
                name: name, page: this.page
            }), {
                params: {
                    page: this.page,
                }
            })
                .then(({ data }) => {
                    if (data.images.length > 0) {
                        this.page += 1;
                        this.images.push(...data.images);
                        if (this.page > data.totalPages) {
                            $state.complete();
                        }
                        $state.loaded();
                    } else {
                        $state.complete();
                    };
                });
        },
    }

});
