<template>
  <v-app id="w-node-10c73ece941f-559b0ad0" class="form-block w-form">
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>

    <div class="w-layout-grid grid-2">
      <div id="w-node-2e7630a24e2f-559b0ad0" class="search">
        <v-container class="p-0 m-0">
          <v-row>
            <v-col>
              <v-select
                :items="experienceList"
                :clearable="true"
                @change="getExperienceList"
                hint="Choisissez une expérience."
                persistent-hint
                item-value="id"
                item-text="title"
                v-model="experience"
                label="Expérience"
                outlined
                attach
              ></v-select>
            </v-col>
            <v-col>
              <v-autocomplete
                v-model="autocomplete"
                :hint="'Indiquez un lieu. Ex : Bordeaux, France'"
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
                outlined
                attach
              >
                <template slot="no-data">
                  Commencer à saisir un lieu, puis sélectionner.
                </template>
              </v-autocomplete>
            </v-col>
            <v-col>
              <v-select
                v-model="newsSexe"
                :clearable="true"
                @change="changeType"
                item-value="id"
                item-text="title"
                hint="Homme, femme ou sans importance."
                persistent-hint
                :items="sexeList"
                label="Sexe"
                outlined
                attach
              ></v-select>
            </v-col>
          </v-row>
        </v-container>
      </div>

      <template v-if="advancedSearch">
        <div id="w-node-a3c2e5a831dd-559b0ad0" class="mb-2 mt-2">
          <template>
            <v-expansion-panels>
              <v-expansion-panel>
                <v-expansion-panel-header>
                  <template v-slot:default="{}"> Recherche avancée </template>
                </v-expansion-panel-header>
                <v-expansion-panel-content>
                  <v-container class="p-0 m-0">
                    <v-row>
                      <v-col class="pr-0">
                        <v-select
                          v-model="newsOrigin"
                          hint="Ex: Européen(e), asiatique, etc."
                          persistent-hint
                          :clearable="true"
                          @change="changeType"
                          item-value="id"
                          item-text="title"
                          :items="originList"
                          label="Origine ethnique"
                          outlined
                          attach
                        ></v-select>
                      </v-col>
                      <v-col>
                        <v-select
                          v-model="newsHair"
                          hint="Ex: Blonds, bruns, etc."
                          persistent-hint
                          :clearable="true"
                          @change="changeType"
                          item-value="id"
                          item-text="title"
                          :items="hairList"
                          label="Couleur des cheveux"
                          outlined
                          attach
                        ></v-select>
                      </v-col>
                      <v-col class="pl-0">
                        <v-select
                          v-model="newsEyesColor"
                          hint="Ex: Noirs, marrons, etc."
                          persistent-hint
                          :clearable="true"
                          @change="changeType"
                          item-value="id"
                          item-text="title"
                          :items="eyesColorList"
                          label="Couleur des yeux"
                          outlined
                          attach
                        ></v-select>
                      </v-col>
                    </v-row>
                  </v-container>
                  <h5>Mensurations :</h5>

                  <v-container class="p-0 m-0">
                    <v-row>
                      <v-col>
                        <b-form-checkbox
                          v-model="sizeGroup"
                          name="check-button"
                          switch
                          @change="changeType"
                        >
                          Taille
                        </b-form-checkbox>
                        <v-expansion-panels :disabled="!sizeGroup" flat>
                          <v-expansion-panel>
                            <v-expansion-panel-header
                              >Définissez une taille
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-range-slider
                                :disabled="!sizeGroup"
                                v-model="size"
                                @change="changeType"
                                :max="maxSize"
                                :min="minSize"
                                hide-details
                                :thumb-size="24"
                                thumb-label
                                class="align-center range-lider mb-3"
                              >
                                <template v-slot:prepend>
                                  <span>
                                    <span class="text-muted">Entre</span>
                                  </span>
                                  <v-text-field
                                    :value="size[0]"
                                    class="mt-0 pt-0 text-center"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(size, 0, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">et</span>
                                  <v-text-field
                                    :value="size[1]"
                                    class="mt-0 pt-0"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(size, 1, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">cm</span>
                                </template>
                              </v-range-slider>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <b-form-checkbox
                          v-model="weightGroup"
                          name="check-button"
                          switch
                          @change="changeType"
                        >
                          Poids
                        </b-form-checkbox>
                        <v-expansion-panels :disabled="!weightGroup" flat>
                          <v-expansion-panel>
                            <v-expansion-panel-header
                              >Définissez un poids
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-range-slider
                                :disabled="!weightGroup"
                                v-model="weight"
                                @change="changeType"
                                :max="maxWeight"
                                :min="minWeight"
                                hide-details
                                class="align-center range-lider mb-3"
                              >
                                <template v-slot:prepend>
                                  <span>
                                    <span class="text-muted">Entre</span>
                                  </span>
                                  <v-text-field
                                    :value="weight[0]"
                                    class="mt-0 pt-0 text-center"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(weight, 0, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">et</span>
                                  <v-text-field
                                    :value="weight[1]"
                                    class="mt-0 pt-0"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(weight, 1, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">kg</span>
                                </template>
                              </v-range-slider>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <b-form-checkbox
                          v-model="hipGroup"
                          name="check-button"
                          switch
                          @change="changeType"
                        >
                          Tour de hanche
                        </b-form-checkbox>
                        <v-expansion-panels :disabled="!hipGroup" flat>
                          <v-expansion-panel>
                            <v-expansion-panel-header
                              >Définissez un tour de hanche
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-range-slider
                                v-model="hip"
                                :disabled="!hipGroup"
                                @change="changeType"
                                :max="maxHip"
                                :min="minHip"
                                hide-details
                                class="align-center range-lider mb-3"
                              >
                                <template v-slot:prepend>
                                  <span>
                                    <span class="text-muted">Entre</span>
                                  </span>
                                  <v-text-field
                                    :value="hip[0]"
                                    class="mt-0 pt-0 text-center"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(hip, 0, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">et</span>
                                  <v-text-field
                                    :value="hip[1]"
                                    class="mt-0 pt-0"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(hip, 1, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">cm</span>
                                </template>
                              </v-range-slider>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <b-form-checkbox
                          v-model="confectionGroup"
                          name="check-button"
                          switch
                          @change="changeType"
                        >
                          Confection
                        </b-form-checkbox>
                        <v-expansion-panels :disabled="!confectionGroup" flat>
                          <v-expansion-panel>
                            <v-expansion-panel-header
                              >Définissez une confection
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-range-slider
                                v-model="confection"
                                :disabled="!confectionGroup"
                                @change="changeType"
                                :max="maxConfection"
                                :min="minConfection"
                                hide-details
                                class="align-center range-lider mb-3"
                              >
                                <template v-slot:prepend>
                                  <span>
                                    <span class="text-muted">Entre</span>
                                  </span>
                                  <v-text-field
                                    :value="confection[0]"
                                    class="mt-0 pt-0 text-center"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(confection, 0, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">et</span>
                                  <v-text-field
                                    :value="confection[1]"
                                    class="mt-0 pt-0"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(confection, 1, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">cm</span>
                                </template>
                              </v-range-slider>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <b-form-checkbox
                          v-model="pointureGroup"
                          name="check-button"
                          switch
                          @change="changeType"
                        >
                          Pointure
                        </b-form-checkbox>
                        <v-expansion-panels :disabled="!pointureGroup" flat>
                          <v-expansion-panel>
                            <v-expansion-panel-header
                              >Définissez une pointure
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-range-slider
                                v-model="pointure"
                                :disabled="!pointureGroup"
                                @change="changeType"
                                :max="maxPointure"
                                :min="minPointure"
                                hide-details
                                class="align-center range-lider mb-3"
                              >
                                <template v-slot:prepend>
                                  <span>
                                    <span class="text-muted">Entre</span>
                                  </span>
                                  <v-text-field
                                    :value="pointure[0]"
                                    class="mt-0 pt-0 text-center"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(pointure, 0, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">et</span>
                                  <v-text-field
                                    :value="pointure[1]"
                                    class="mt-0 pt-0"
                                    hide-details
                                    single-line
                                    type="number"
                                    style="width: 60px"
                                    @change="$set(pointure, 1, $event)"
                                  ></v-text-field>
                                  <span class="text-muted">cm</span>
                                </template>
                              </v-range-slider>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>
                      </v-col>
                    </v-row>
                  </v-container>
                </v-expansion-panel-content>
              </v-expansion-panel>
            </v-expansion-panels>
          </template>
        </div>
      </template>

      <div id="w-node-a7f330fd6da8-559b0ad0">
        <v-col class="d-flex mb-3" offset-lg="8" cols="12" lg="4">
          <v-select
            label="Trier par :"
            :items="sortList"
            item-value="title"
            item-text="content"
            v-model="defaultOrder"
            @change="changeType"
            attach
          ></v-select>
        </v-col>
      </div>
      <div class="w-dyn-list">
        <div class="product5-feed-grid w-dyn-items">
          <div
            id="w-node-7281d8089065-612a1c64"
            v-for="(user, $index) in users"
            :key="$index"
            class="w-dyn-item"
          >
            <div class="div-block-4 user-card">
              <a
                :href="user.identity.url"
                target="_blank"
                class="link-block w-inline-block"
              >
                <div
                  class="author-picture image-user rounded"
                  :style="{ backgroundImage: `url('${user.identity.avatar}')` }"
                ></div>
              </a>

              <a
                :href="user.identity.url"
                target="_blank"
                class="link-block-2 w-inline-block text-dark"
              >
                <div
                  class="size4-text size4-bottom-clear"
                  :title="user.identity.fullname"
                >
                  {{ user.identity.fullname }}
                </div>
                <div
                  v-if="user.identity.certified"
                  title="Book certifié"
                  class="size4-text size4-bottom-clear verified"
                >
                  
                </div>
              </a>

              <a
                :href="user.identity.url"
                class="text-dark link-block-2 w-inline-block"
              >
                <div
                  class="size6-text size6-top-clear"
                  :title="user.identity.experience"
                >
                  {{ user.identity.experience }}
                </div>
              </a>
              <p
                class="paragraph-70 paragraph70-bottom-clear"
                :title="user.identity.location"
              >
                {{ user.identity.location }}
              </p>
              <p class="paragraph-70">
                <span v-html="user.identity.countFolders"></span>
                <span class="ml-2" v-html="user.identity.countMedias"></span>
              </p>
              <a
                data-w-id="c249d0e8-c721-094b-4a01-beffae3e1483"
                :href="user.identity.url"
                class="button-text button-left w-inline-block"
              >
                <div class="button-label text-dark">Explorer le book</div>
                <img
                  src="/assets/img/arrow-dark2x.svg"
                  alt="Explorer le book"
                  class="button-arrow"
              /></a>
            </div>
          </div>
        </div>
        <infinite-loading
          spinner="waveDots"
          :identifier="infiniteId"
          @infinite="infiniteHandler"
        >
          <div slot="no-more"></div>
          <div slot="no-results">
            <div class="empty-state w-dyn-empty">
              Aucun artiste pour l'instant.
            </div>
          </div>
        </infinite-loading>
      </div>
    </div>
  </v-app>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
const routes = require("../../../../public/js/fos_js_routes.json");
import Routing from "../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  components: {},
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
      advancedSearch: false,

      isLoading: false,
      items: [],
      autocomplete: null,
      search: null,

      defaultOrder: "updatedAt",
      sortList: [],

      sizeGroup: false,
      minSize: 120,
      maxSize: 220,
      size: [120, 220],

      weightGroup: false,
      minWeight: 40,
      maxWeight: 140,
      weight: [40, 140],

      hipGroup: false,
      minHip: 60,
      maxHip: 120,
      hip: [60, 120],

      confectionGroup: false,
      minConfection: 40,
      maxConfection: 140,
      confection: [40, 140],

      pointureGroup: false,
      minPointure: 32,
      maxPointure: 49,
      pointure: [32, 49],

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

  mounted() {
    this.$http
      .get(
        Routing.generate("front_images_home", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.images = response.data;
      });
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("json_type_artist", {
            page: this.page,
            slug: professionSlug,
          }),
          {
            params: {
              page: this.page,
              experience: this.experience,
              sexe: this.newsSexe,
              origin: this.newsOrigin,
              hair: this.newsHair,
              eyesColor: this.newsEyesColor,
              sortBy: this.defaultOrder,
              autocomplete: this.autocomplete,
              size: this.sizeGroup ? this.size : "",
              weight: this.weightGroup ? this.weight : "",
              hip: this.hipGroup ? this.hip : "",
              confection: this.confectionGroup ? this.confection : "",
              pointure: this.pointureGroup ? this.pointure : "",
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
            this.experienceList = data.experienceList;
            this.sexeList = data.sexeList;
            this.typeList = data.typeList;
            this.sortList = data.sortList;
            this.originList = data.originList;
            this.hairList = data.hairList;
            this.eyesColorList = data.eyesColorList;
            this.advancedSearch = data.advancedSearch;
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
    getExperienceList() {
      this.$http
        .get(Routing.generate("json_experience", { type: professionSlug }))
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