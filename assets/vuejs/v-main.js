
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
import transFilter from 'vue-trans';

// Add to vue
Vue.use(transFilter);

import * as VeeValidate from 'vee-validate';
Vue.use(VeeValidate);

Vue.use(BootstrapVue);
Vue.use(VueResource);
Vue.use(VueMasonry);
Vue.use(VueImg);
Vue.use(GoTop);
Vue.use(InfiniteLoading);
Vue.use(Vuetify);
import Annuaire from './components/Annuaire.vue';
import Galleries from './components/Galleries.vue';
import NewBooks from './components/NewBooks.vue';
import FeedPhotos from './components/FeedPhotos.vue';
import FollowBooks from './components/FollowBooks.vue';
import Annonces from './components/Annonces.vue';
import Notifications from './components/Notifications.vue';
import UserInboxCard from './components/UserInboxCard.vue';
import Followers from './components/Followers.vue';
import Following from './components/Following.vue';
import SuggestBooks from './components/SuggestBooks.vue';
import LastPostAnnonces from './components/LastPostAnnonces.vue';
import AnnuaireSuggested from './components/AnnuaireSuggested.vue';
import MediaLikes from './components/MediaLikes.vue';
import MediaComments from './components/MediaComments.vue';
import ResultSearch from './components/ResultSearch.vue';
import News from './components/News.vue';
import Designs from './components/Designs.vue';
import AllImages from './components/AllImages.vue';
import SearchBar from './components/SearchBar.vue';

function vueInit(elementId, options) {
	var element = document.getElementById(elementId);
	if (element) {
		options.el = element;
		new Vue(options).$mount('#'.elementId);
	}
}

vueInit("app-annuaire", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	transFilter,
	render: h => h(Annuaire)
})

vueInit("app-galleries", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(Galleries)
})

vueInit("app-new-books", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(NewBooks)
})

vueInit("app-follow-books", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(FollowBooks)
})

vueInit("app-suggest-books", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(SuggestBooks)
})

vueInit("app-feed-photos", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	VeeValidate,
	render: h => h(FeedPhotos)
})

vueInit("app-annonces", {
	vuetify: new Vuetify(),
	VueImg,
	render: h => h(Annonces)
})

vueInit("app-notifications", {
	vuetify: new Vuetify(),
	VueImg,
	render: h => h(Notifications)
})

vueInit("app-user-inbox-card", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(UserInboxCard)
})

vueInit("app-followers", {
	vuetify: new Vuetify({
		theme: { options: { customProperties: true, variations: false }, },
	}),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(Followers)
})

vueInit("app-following", {
	vuetify: new Vuetify({
		theme: { options: { customProperties: true, variations: false }, },
	}),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(Following)
})

vueInit("app-last-post-annonces", {
	vuetify: new Vuetify(),
	VueImg,
	render: h => h(LastPostAnnonces)
})

vueInit("app-annuaire-suggested", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	render: h => h(AnnuaireSuggested)
})

vueInit("app-media-likes", {
	vuetify: new Vuetify(),
	VueImg,
	VueResource,
	GoTop,
	render: h => h(MediaLikes)
})

vueInit("app-media-comments", {
	vuetify: new Vuetify(),
	VueImg,
	VueResource,
	GoTop,
	render: h => h(MediaComments)
})

vueInit("app-result-search", {
	vuetify: new Vuetify(),
	render: h => h(ResultSearch)
})

vueInit("app-news", {
	vuetify: new Vuetify(),
	render: h => h(News)
})

vueInit("app-designs", {
	vuetify: new Vuetify(),
	render: h => h(Designs)
})

vueInit("app-all-images", {
	vuetify: new Vuetify(),
	VueMasonry,
	VueImg,
	VueResource,
	GoTop,
	transFilter,
	render: h => h(AllImages)

})

vueInit("app-search-bar", {
	vuetify: new Vuetify(),
	render: h => h(SearchBar)
})



