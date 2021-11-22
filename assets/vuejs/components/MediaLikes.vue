<template>
  <v-app>
    <template v-if="loading">
      <v-avatar
        v-for="n in 1"
        :key="n"
        class="position-static d-inline-block mr-1"
      >
        <v-skeleton-loader :loading="loading" type="avatar">
        </v-skeleton-loader>
      </v-avatar>
    </template>
    <template v-else>
      <div
        class="d-flex align-items-center mb-5"
        v-for="(like, $index) in datas.likes"
        :key="$index"
      >
        <div class="me-5 position-relative">
          <div class="symbol symbol-35px symbol-circle">
            <a :href="like.identity.url" target="_blank" class="symbol">
              <template v-if="like.identity.certified">
                <v-badge
                  bordered
                  title="Book certifié"
                  bottom
                  class="symbol"
                  offset-x="20"
                  offset-y="20"
                  color="blue accent-4 small"
                  icon="mdi-check"
                >
                  <img :src="like.identity.avatar" class="" alt="" />
                </v-badge>
              </template>
              <template v-else>
                <img :src="like.identity.avatar" class="" alt="" />
              </template>
            </a>
          </div>
        </div>
        <div class="fw-bold">
          <a href="#" class="fs-5 fw-bolder text-gray-900 text-hover-primary">{{
            like.identity.fullname
          }}</a>
          <div class="text-gray-400">{{ like.identity.profession }}</div>
        </div>
        <button-follow class="ms-auto" :user="like"></button-follow>
      </div>
    </template>
  </v-app>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import VCardUser from "./VCardUser.vue";
import ButtonFollow from "./ButtonFollow.vue";
import ButtonNoSuggest from "./ButtonNoSuggest.vue";

export default {
  components: {
    "v-card-user": VCardUser,
    "button-follow": ButtonFollow,
  },
  data: function () {
    return {
      datas: [],
      menu: [],
      linkSuggest: false,
      loading: true,
    };
  },
  mounted() {
    this.$http
      .get(Routing.generate("get_likes_media", { imageId: imageId }))
      .then(function (response) {
        this.datas = response.data;
        this.loading = false;
      });
  },
};
</script>