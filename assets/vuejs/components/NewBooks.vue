<template>
  <div>
    <div class="d-flex flex-wrap flex-stack mb-6">
      <h3 class="fw-bolder my-2">{{ "Nouveaux books" | trans }}</h3>
      <div class="d-flex my-2">
        <select
          name="status"
          data-control="select2"
          data-hide-search="true"
          class="form-select form-select-white form-select-sm w-125px select2-hidden-accessible"
          data-select2-id="select2-data-10-n8xw"
          tabindex="-1"
          aria-hidden="true"
        >
          <option
            value="Active"
            selected="selected"
            data-select2-id="select2-data-12-p3jf"
          >
            Tous
          </option>
          <option value="Approved">Photographes</option>
          <option value="Declined">Modèles</option>
        </select>
      </div>
    </div>
    <v-row class="gy-5 g-xl-8">
      <template v-if="loading">
        <div class="col-md-6 col-lg-3" v-for="n in 4" :key="n">
          <div class="card mb-xl-8">
            <div class="card-body">
              <v-skeleton-loader
                :loading="loading"
                height="94"
                type="list-item-avatar-three-line"
              >
              </v-skeleton-loader>
            </div>
          </div>
        </div>
      </template>
      <template v-else>
        <div class="col-xl-3" v-for="(user, $index) in users" :key="$index">
          <div class="card mb-xl-8">
            <div class="card-body">
              <div class="d-flex align-items-center mb-0">
                <div class="symbol symbol-50px me-5">
                  <a :href="user.url" target="_blank" class="symbol">
                    <template v-if="user.certified">
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
                        <img :src="user.avatar" class="" alt="" />
                      </v-badge>
                    </template>
                    <template v-else>
                      <img :src="user.avatar" class="" alt="" />
                    </template>
                  </a>
                </div>
                <div class="flex-grow-1">
                  <a
                    :href="user.url"
                    target="_blank"
                    class="text-gray-800 fw-bolder text-hover-primary fs-6"
                  >
                    {{ user.fullname }}
                  </a>
                  <span class="text-muted d-block fw-bold">{{
                    user.profession
                  }}</span>
                  <span class="text-muted mb-1 fw-bold d-block">{{
                    user.locationTruncate
                  }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </v-row>
  </div>
</template>

<style>
html,
body,
.v-application,
.v-application--wrap {
  min-height: -webkit-fill-available;
}
</style>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ButtonFollow from "./ButtonFollow.vue";
import VCardUser from "./VCardUser.vue";
import { Skeleton } from "vue-loading-skeleton";

export default {
  components: {
    "button-follow": ButtonFollow,
    "v-card-user": VCardUser,
    Skeleton,
  },
  data: function () {
    return {
      users: null,
      loading: true,
      page: 1,
    };
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("dashboard_new_books", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.users = response.data.data;
        this.loading = false;
      });
  },
};
</script>