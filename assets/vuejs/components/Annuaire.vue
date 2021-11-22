<template>
  <v-app>
    <h3 class="fw-bolder me-5 my-1 mb-5">
      {{ "Qui recherchez-vous ?" | trans }}
    </h3>
    <div class="card">
      <div class="card-body fs-6 py-5 px-10 py-lg-5 px-lg-5 text-gray-700">
        <v-row align="center">
          <v-col class="d-flex" cols="12" lg="3">
            <v-select
              class="form-select form-select-white"
              v-model="newsType"
              :clearable="true"
              @change="changeFilterExperience"
              :hint="'Ex : Photographe, modèle, styliste, etc.' | trans"
              persistent-hint
              item-value="id"
              item-text="title"
              :items="typeList"
              label="Type de book"
              auto
              attach
            ></v-select>
          </v-col>
          <v-col class="d-flex" cols="12" lg="3">
            <v-select
              class="form-select form-select-white form-select-sm"
              :items="experienceList"
              :clearable="true"
              @change="changeType"
              :hint="'En fonction du type de book choisi.' | trans"
              persistent-hint
              :disabled="disabled"
              item-value="id"
              item-text="title"
              v-model="experience"
              label="Expérience"
              attach
            ></v-select>
          </v-col>
          <v-col class="d-flex" cols="12" lg="3">
            <v-autocomplete
              class="form-select form-select-white form-select-sm"
              v-model="autocomplete"
              :hint="'Indiquez un lieu. Ex : Bordeaux, France' | trans"
              persistent-hint
              :items="items"
              :loading="isLoading"
              :search-input.sync="search"
              clearable
              hide-selected
              item-text="name"
              item-value="symbol"
              label="Où ?"
              @change="changeType"
              attach
            >
              <template slot="no-data">
                {{ "Commencer à saisir un lieu, puis sélectionner." | trans }}
              </template>
            </v-autocomplete>
          </v-col>
          <v-col class="d-flex" cols="12" lg="2">
            <v-select
              class="form-select form-select-white form-select-sm"
              v-model="newsSexe"
              :clearable="true"
              @change="changeType"
              item-value="id"
              :hint="'Homme, femme ou sans importance.' | trans"
              persistent-hint
              item-text="title"
              :items="sexeList"
              label="Sexe"
              attach
            ></v-select>
          </v-col>
          <v-col class="d-flex text-end" cols="12" lg="1">
            <a
              data-bs-toggle="collapse"
              href="#advancedSearch"
              role="button"
              aria-expanded="false"
              aria-controls="advancedSearch"
              class="btn btn-icon btn-primary"
              ><i class="las la-plus fs-2"></i
            ></a>
          </v-col>
        </v-row>
        <div class="collapse" id="advancedSearch">
          <div class="row">
            <div class="col-md-4">
              <v-select
                class="form-select form-select-white"
                v-model="newsOrigin"
                :clearable="true"
                @change="changeType"
                item-value="id"
                item-text="title"
                :items="originList"
                label="Origine ethnique"
                attach
              ></v-select>
            </div>
            <div class="col-md-4">
              <v-select
                class="form-select form-select-white"
                v-model="newsHair"
                :clearable="true"
                @change="changeType"
                item-value="id"
                item-text="title"
                :items="hairList"
                label="Couleur des cheveux"
                attach
              ></v-select>
            </div>
            <div class="col-md-4">
              <v-select
                class="form-select form-select-white"
                v-model="newsEyesColor"
                :clearable="true"
                @change="changeType"
                item-value="id"
                item-text="title"
                :items="eyesColorList"
                label="Couleur des yeux"
                attach
              ></v-select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content mt-10">
      <div id="kt_project_users_card_pane" class="tab-pane fade show active">
        <div class="row g-6 g-xl-9">
          <div
            class="col-md-6 col-xxl-4"
            v-for="(user, $index) in users"
            :key="$index"
          >
            <div class="card">
              <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                <div class="symbol symbol-75px mb-5">
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
                <a
                  :href="user.identity.url"
                  target="_blank"
                  class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0"
                  >{{ user.identity.fullname }}</a
                >
                <div class="fw-bold text-center text-gray-400 mb-6">
                  {{ user.identity.experience }} <br />
                  {{ user.identity.location }}<br />
                  <small
                    >{{ user.followers.length }}
                    <template v-if="user.followers.length > 1"
                      >abonnés</template
                    >
                    <template v-else>abonné</template>
                  </small>
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
                <button-follow :user="user"></button-follow>
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
        </div>
      </div>
    </div>
  </v-app>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

import ButtonFollow from "./ButtonFollow.vue";
import "@mdi/font/css/materialdesignicons.css";

export default {
  components: {
    "button-follow": ButtonFollow,
  },
  data: function () {
    return {
      page: 1,
      users: [],
      infiniteId: +new Date(),
      typeList: [],
      newsType: [],
      experience: "",
      experienceList: [],
      sexeList: [],
      newsSexe: "",
      disabled: true,
      originList: [],
      newsOrigin: "",
      hairList: [],
      newsHair: "",
      eyesColorList: [],
      newsEyesColor: "",

      isLoading: false,
      items: [],
      autocomplete: null,
      search: null,

      defaultOrder: "updatedAt",
      sortList: [],

      componentForm: {
        street_number: "",
        route: "",
        locality: "",
        administrative_area_level_1: "",
        country: "",
        postal_code: "",
      },
    };
  },
  watch: {
    search(val) {
      if (!val) {
        return;
      }
      this.isLoading = true;
      const service = new google.maps.places.AutocompleteService();
      service.getQueryPredictions({ input: val }, (predictions, status) => {
        if (status != google.maps.places.PlacesServiceStatus.OK) {
          return;
        }
        this.items = predictions.map((prediction) => {
          return {
            id: prediction.id,
            name: prediction.description,
          };
        });

        this.isLoading = false;
      });
    },
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("json_annuaire", {
            page: this.page,
          }),
          {
            params: {
              page: this.page,
              type: this.newsType,
              experience: this.experience,
              sexe: this.newsSexe,
              origin: this.newsOrigin,
              hair: this.newsHair,
              eyesColor: this.newsEyesColor,
              sortBy: this.defaultOrder,
              autocomplete: this.autocomplete,
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
            this.typeList.push(...data.typeList);
            this.sexeList.push(...data.sexeList);
            this.originList.push(...data.originList);
            this.hairList.push(...data.hairList);
            this.eyesColorList.push(...data.eyesColorList);
            this.sortList.push(...data.sortList);

            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },

    changeFilterExperience() {
      this.$http
        .get(Routing.generate("json_experience", { type: this.newsType }))
        .then((data) => {
          this.experienceList = data.data;
          this.disabled = false;
        });
      this.page = 1;
      this.users = [];
      this.infiniteId += 1;
    },
    changeType() {
      this.page = 1;
      this.users = [];
      this.infiniteId += 1;
    },
  },
};
</script>

<style scoped>
.form-select {
  background-image: initial;
}
</style>

