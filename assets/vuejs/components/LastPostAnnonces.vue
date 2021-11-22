<template>
  <div>
    <template v-if="loading">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <v-list three-line>
                <div v-for="n in 5" :key="n">
                  <v-skeleton-loader
                    :loading="loading"
                    height="94"
                    type="list-item-avatar-three-line"
                  >
                  </v-skeleton-loader>
                </div>
              </v-list>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-else>
      <template v-if="annonces.length > 0">
        <div
          class="d-flex align-items-center mb-7"
          v-for="(item, index) in annonces"
          :key="index.id"
        >
          <div class="symbol symbol-50px me-5">
            <a :href="item.user.url" target="_blank" class="symbol">
              <template v-if="item.user.certified">
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
                  <img :src="item.user.avatar" class="" alt="" />
                </v-badge>
              </template>
              <template v-else>
                <img :src="item.user.avatar" class="" alt="" />
              </template>
            </a>
          </div>
          <div class="flex-grow-1">
            <a
              :href="item.link"
              target="_blank"
              class="text-gray-800 fw-bolder text-hover-primary fs-6"
              v-html="item.title"
            ></a>
            <span
              class="text-muted d-block fw-bold"
              v-html="item.author"
            ></span>
            <small class="text-muted" v-html="item.date"></small>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="text-center alert alert-warning mb-0">
          Aucune annonce pour l'instant.
        </div>
      </template>
    </template>
  </div>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  data: function () {
    return {
      annonces: [],
      link: "",
      page: 1,
      loading: true,
    };
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("annonces_last_post", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.annonces = response.data.annonces;
        this.link = response.data.link;
        this.loading = false;
      });
  },
};
</script>