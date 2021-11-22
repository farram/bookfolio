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
Vue.use(Vuetify, {
    iconfont: 'md',
})
Vue.use(GoTop);
Vue.use(VueMasonry);
Vue.use(InfiniteLoading);
Vue.use(VueResource);
Vue.use(VueImg);

import portfolioImagesHome from '../components/biggap-light/ImagesHome.vue';
import portfolioImagesGallery from '../components/biggap-light/ImagesGallery.vue';

vueInit("portfolioImagesHome", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    render: h => h(portfolioImagesHome)
})

vueInit("portfolioImagesGallery", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    render: h => h(portfolioImagesGallery)
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