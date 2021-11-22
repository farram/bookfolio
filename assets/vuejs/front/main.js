import Vue from 'vue';

import Vuetify from 'vuetify';
import VueMasonry from 'vue-masonry-css';
import InfiniteLoading from 'vue-infinite-loading';
import VueResource from 'vue-resource';
import VueImg from 'v-img';
import BootstrapVue from 'bootstrap-vue';

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
Vue.use(BootstrapVue)

import frontImagesHome from '../components/front/imagesHome.vue';
import inspirations from '../components/front/inspirations.vue';
import annuaireFilter from '../components/front/annuaireFilter.vue';

vueInit("front_images_home", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    render: h => h(frontImagesHome)
})

vueInit("inspirations", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    render: h => h(inspirations)
})

vueInit("annuaireFilter", {
    vuetify: new Vuetify(),
    GoTop,
    VueMasonry,
    VueResource,
    VueImg,
    BootstrapVue,
    render: h => h(annuaireFilter)
})