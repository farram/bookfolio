<template>
  <v-app>
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>
    <v-row>
      <div
        v-for="(user, $index) in users"
        :key="$index"
        class="col-sm-6 col-lg-3"
      >
        <div class="text-center card">
          <div class="card-body">
            <div class="pt-2 pb-2">
              <div class="avatar-group">
                <a
                  :href="user.identity.url"
                  target="_blank"
                  class="avatar-group-item d-inline-block"
                >
                  <template v-if="user.identity.certified">
                    <v-badge
                      bordered
                      title="Book certifié"
                      bottom
                      offset-x="20"
                      offset-y="20"
                      color="blue accent-4 small"
                      icon="mdi-check"
                    >
                      <v-avatar size="72">
                        <v-img
                          :src="user.identity.avatar"
                          :alt="user.identity.fullname"
                        ></v-img>
                      </v-avatar>
                    </v-badge>
                  </template>
                  <template v-else>
                    <v-avatar size="72">
                      <v-img
                        :src="user.identity.avatar"
                        :alt="user.identity.fullname"
                      ></v-img>
                    </v-avatar>
                  </template>
                </a>
              </div>
              <h4 class="mt-1">
                <a
                  :href="user.identity.url"
                  target="_blank"
                  class="text-dark"
                  >{{ user.identity.fullname }}</a
                >
              </h4>
              <p class="mb-0 text-muted">{{ user.identity.profession }}</p>
              <p class="text-muted mb-0">{{ user.identity.location }}</p>
              <p class="text-muted mt-0">
                <small v-html="user.identity.countFolders"></small>
                <small class="ml-2" v-html="user.identity.countMedias"></small>
              </p>
              <button-follow :user="user"></button-follow>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <infinite-loading
          spinner="waveDots"
          :identifier="infiniteId"
          @infinite="infiniteHandler"
        >
          <div slot="no-more"></div>
          <div slot="no-results">
            <div class="alert alert-warning text-center">
              Aucun artiste pour l'instant.
            </div>
          </div>
        </infinite-loading>
      </div>
    </v-row>
  </v-app>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

import ButtonFollow from "./ButtonFollow.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
  },
  data: function () {
    return {
      page: 1,
      users: [],
      infiniteId: +new Date(),
      isLoading: false,
    };
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("json_result_search", {
            page: this.page,
            search: search,
          })
        )
        .then(({ data }) => {
          if (data.users.length > 0) {
            this.page += 1;
            this.users.push(...data.users);
            if (this.page > data.totalPages) {
              $state.complete();
            }
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
    changeType() {
      this.page = 1;
      this.users = [];
      this.infiniteId += 1;
    },
  },
  computed: {},
};
</script>