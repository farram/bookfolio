<template>
  <v-app>
    <template v-if="loading">
      <div
        class="inbox-item"
        style="overflow: inherit !important"
        v-for="n in 6"
        :key="n"
      >
        <v-skeleton-loader
          :loading="loading"
          height="94"
          type="list-item-avatar-three-line"
        >
        </v-skeleton-loader>
      </div>
    </template>
    <template v-else>
      <div class="d-flex mb-7" v-for="(user, index) in users" :key="index">
        <div class="symbol symbol-60px symbol-2by3 flex-shrink-0 me-4">
          <a :href="user.url" target="_blank" class="symbol">
            <template v-if="user.identity.certified">
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
                <img :src="user.identity.avatar" class="" alt="" />
              </v-badge>
            </template>
            <template v-else>
              <img :src="user.identity.avatar" class="" alt="" />
            </template>
          </a>
        </div>
        <div
          class="d-flex align-items-center flex-wrap flex-grow-1 mt-n2 mt-lg-n1"
        >
          <div class="d-flex flex-column flex-grow-1 my-lg-0 my-2 pe-3">
            <a
              :href="user.identity.url"
              target="_blank"
              class="fs-5 text-gray-800 text-hover-primary fw-bolder"
              >{{ user.identity.fullname }}</a
            >
            <span class="text-gray-400 fw-bold fs-7">{{
              user.identity.profession
            }}</span>
            <span class="text-muted">
              {{ user.identity.locationTruncate }}
            </span>
          </div>
          <div class="text-end py-lg-0 py-2">
            <button-follow :user="user"></button-follow>
          </div>
        </div>
      </div>
    </template>
  </v-app>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ButtonFollow from "./ButtonFollow.vue";
import ButtonNoSuggest from "./ButtonNoSuggest.vue";
import VCardUser from "./VCardUser.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
    "button-no-suggest": ButtonNoSuggest,
    "v-card-user": VCardUser,
  },
  data: function () {
    return {
      users: [],
      menu: [],
      loading: true,
    };
  },
  mounted() {
    this.$http
      .get(Routing.generate("short_suggest_book_to_follow"))
      .then(function (response) {
        this.users = response.data;
        this.loading = false;
      });
  },
};
</script>