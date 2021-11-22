
import Vue from 'vue'; // Here it work
import Vuetify from 'vuetify'
import VueMasonry from 'vue-masonry-css';
import VueImg from 'v-img';
import VueResource from 'vue-resource'
import InfiniteLoading from 'vue-infinite-loading';
import './../vuejs/v-img.min';
import './../vuejs/vue-go-top.min';


Vue.use(VueResource);
Vue.use(VueMasonry);
Vue.use(VueImg);
Vue.use(GoTop);
Vue.use(InfiniteLoading);
Vue.use(Vuetify);
import MediasHome from './components/MediasHome.vue';
import MediasGallery from './components/MediasGallery.vue';

function vueInit(elementId, options) {
    var element = document.getElementById(elementId);
    if (element) {
        options.el = element;
        new Vue(options).$mount('#'.elementId);
    }
}

vueInit("app-media-home", {
    Vuetify,
    VueMasonry,
    VueImg,
    VueResource,
    GoTop,
    render: h => h(MediasHome)
})

vueInit("app-media-gallery", {
    Vuetify,
    VueMasonry,
    VueImg,
    VueResource,
    GoTop,
    render: h => h(MediasGallery)
})