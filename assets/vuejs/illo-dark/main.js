import Vue from 'vue';

import Vuetify from 'vuetify';
import VueMasonry from 'vue-masonry-css';
import InfiniteLoading from 'vue-infinite-loading';
import VueResource from 'vue-resource';
import VueImg from 'v-img';


import './../../vuejs/vue-go-top.min';

function vueInit(elementId, options) {
    var element = document.getElementById(elementId);
    if (element) {
        options.el = element;
        new Vue(options).$mount('#'.elementId);
    }
}

Vue.use(Vuetify);
Vue.use(GoTop);
Vue.use(VueMasonry);
Vue.use(InfiniteLoading);
Vue.use(VueResource);
Vue.use(VueImg);

import ImagesGallery from '../components/illo-dark/ImagesGallery.vue';

vueInit("items_gallery", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    render: h => h(ImagesGallery)
})

import Embed from 'v-video-embed';
Vue.use(Embed);
import portfolioVideo from '../components/portfolio/video.vue';
vueInit("portfolioVideo", {
    vuetify: new Vuetify(),
    GoTop,
    Embed,
    render: h => h(portfolioVideo)
})