<template>
  <v-app>
    <div class="d-flex flex-wrap flex-stack mb-6">
      <h3 class="fw-bolder my-2">
        {{ searchFilter.length }}
        <template v-if="searchFilter.length > 1"> abonnements </template>
        <template v-else> abonnement </template>
      </h3>
      <div class="d-flex my-2">
        <v-select
          class="form-select form-select-white"
          v-model="newsType"
          :clearable="true"
          @change="changeType"
          hint="Choisissez entre: Ajouts récents, nom ou prénom"
          persistent-hint
          item-value="title"
          item-text="content"
          :items="sortList"
          :menu-props="{ contentClass: 'order-list' }"
          label="Trier par :"
          auto
          attach
        ></v-select>
        <v-text-field
          class="form-select form-select-white"
          hide-details="auto"
          hint="Ex: Julien, Manon, etc."
          persistent-hint
          type="text"
          label="Rechercher par nom"
          v-model="search"
        >
        </v-text-field>
      </div>
    </div>
    <div class="row g-6 g-xl-9">
      <div
        class="col-md-6 col-xxl-4"
        v-for="(user, $index) in searchFilter"
        :key="$index"
      >
        <div class="card">
          <div class="card-body d-flex flex-center flex-column p-9">
            <div class="mb-5">
              <div class="symbol symbol-75px symbol-circle">
                <a
                  :href="user.identity.url"
                  target="_blank"
                  class="symbol symbol-75px"
                >
                  <template v-if="user.identity.certified">
                    <v-badge
                      bordered
                      title="Book certifié"
                      bottom
                      class="symbol symbol-75px"
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
            </div>
            <a
              :href="user.identity.url"
              target="_blank"
              class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0"
              >{{ user.identity.fullname }}</a
            >
            <div class="fw-bold text-center text-gray-400 mb-6">
              {{ user.identity.experience }} <br />
              {{ user.identity.location }}<br />
            </div>
            <div class="d-flex flex-center flex-wrap mb-5">
              <div
                class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
              >
                <div class="fs-6 fw-bolder text-gray-700">
                  {{ user.identity.countFolders }}
                </div>
                <div class="fw-bold text-gray-400">Galeries</div>
              </div>
              <div
                class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
              >
                <div class="fs-6 fw-bolder text-gray-700">
                  {{ user.identity.countImages }}
                </div>
                <div class="fw-bold text-gray-400">Photos</div>
              </div>
              <div
                class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
              >
                <div class="fs-6 fw-bolder text-gray-700">
                  {{ user.identity.countVideos }}
                </div>
                <div class="fw-bold text-gray-400">Vidéos</div>
              </div>
            </div>
            <div class="okok">
              <button-follow :user="user"></button-follow>
              <button-inbox :user="user"></button-inbox>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <infinite-loading
          spinner="waveDots"
          @distance="1"
          :identifier="infiniteId"
          @infinite="infiniteHandler"
        >
          <div slot="no-more"></div>
          <div slot="no-results">
            <div class="card border mt-3 mb-0 text-start">
              <div class="card-body">
                <h4 class="mb-1">Il n'y a encore personne ici.</h4>
                <p class="mb-0">
                  Encore un peu de patience ... Vous allez bientôt attirer
                  l'attention de certains artistes.
                </p>
              </div>
            </div>
          </div>
        </infinite-loading>
      </div>
    </div>
  </v-app>
</template>
      



<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

import ButtonFollow from "./ButtonFollow.vue";
import ButtonInbox from "./ButtonInbox.vue";
import IconNotifications from "./IconNotifications.vue";

export default {
  name: "App",
  components: {
    "button-follow": ButtonFollow,
    "button-inbox": ButtonInbox,
    "icon-notifications": IconNotifications,
  },
  data: function () {
    return {
      page: 1,
      users: [],
      infiniteId: +new Date(),
      typeList: [],
      newsType: [],
      isLoading: false,
      items: [],
      search: "",
      defaultOrder: "createdAt",
      sortList: [],
    };
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("json_following", {
            page: this.page,
          }),
          {
            params: {
              page: this.page,
              type: this.newsType,
              sortBy: this.defaultOrder,
            },
          }
        )
        .then(({ data }) => {
          if (data.users.length > 0) {
            this.page += 1;
            this.users.push(...data.users);
            if (this.page > data.totalPages) {
              $state.complete();
            }
            this.sortList.push(...data.sortList);
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
  computed: {
    searchFilter() {
      return this.users.filter((item) => {
        return (
          item.identity.fullname
            .toLowerCase()
            .indexOf(this.search.toLowerCase()) >= 0
        );
      });
    },
  },
};
</script>
