import Vue from 'vue';
import Vuetify from 'vuetify'
import VueMasonry from 'vue-masonry-css';
import VueImg from 'v-img';
import VueResource from 'vue-resource'
import InfiniteLoading from 'vue-infinite-loading';
import './../vuejs/v-img.min';
import './../vuejs/vue-go-top.min';
import 'es6-promise/auto';
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";

Vue.use(BootstrapVue);
Vue.use(VueResource);
Vue.use(VueMasonry);
Vue.use(VueImg);
Vue.use(GoTop);
Vue.use(InfiniteLoading);
Vue.use(Vuetify);

function vueInit(elementId, options) {
    var element = document.getElementById(elementId);
    if (element) {
        options.el = element;
        new Vue(options).$mount('#'.elementId);
    }
}

import LastImages from './components/admin/LastImages.vue';
vueInit("admin-app-last-images", {
    vuetify: new Vuetify(),
    VueMasonry,
    VueImg,
    VueResource,
    render: h => h(LastImages)
})

import SignupRecently from './components/admin/SignupRecently.vue';
vueInit("admin-app-signup-recently", {
    vuetify: new Vuetify(),
    VueMasonry,
    VueImg,
    VueResource,
    render: h => h(SignupRecently)
})

import UserGalleries from './components/admin/UserGalleries.vue';
vueInit("admin-app-galleries", {
    vuetify: new Vuetify(),
    VueMasonry,
    VueImg,
    VueResource,
    render: h => h(UserGalleries)
})
