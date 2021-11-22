<template>
  <v-app>
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>

    <v-row align="center">
      <v-col class="d-flex" cols="12" lg="5">
        <v-select
          class="form-select form-select-white p-0 mb-10"
          v-model="newsType"
          :items="filterList"
          :clearable="true"
          @change="changeType"
          :hint="'Ex : Tous les designs, Starter, Pro, Awesome.' | trans"
          persistent-hint
          item-value="id"
          item-text="title"
          label="Tous les designs"
          auto
          attach
        ></v-select>
      </v-col>
    </v-row>

    <template v-if="designs">
      <div class="list mt-4">
        <v-row>
          <div
            v-for="(design, $index) in designs"
            :item="design"
            :key="$index"
            class="col-lg-6"
          >
            <div class="card border mb-5">
              <img class="card-img-top img-fluid" :src="design.image" alt="#" />
              <div class="card-body">
                <h4 class="card-title">{{ design.title }}</h4>

                <template v-if="design.plan">
                  <div>
                    <p
                      v-for="(plan, $i) in design.plan"
                      :key="$i"
                      class="badge badge-pill p-1 px-2 mr-1"
                      :class="plan.badgeColor"
                    >
                      {{ plan.name }}
                    </p>
                  </div>
                </template>

                <template v-if="design.id == design.user.design.id">
                  <a
                    type="button"
                    class="btn btn-sm btn-primary disabled btn-light"
                  >
                    <i class="fe-check"></i>
                    Mon design</a
                  >
                </template>
                <template v-else>
                  <template v-if="design.forPro">
                    <template v-if="design.user.hasActiveSubscription">
                      <a
                        :href="design.link.choose"
                        class="btn btn-sm btn-primary btn-hover-rise"
                        >{{ "Choisir ce design" | trans }}</a
                      >
                    </template>
                    <template v-else>
                      <a
                        :href="design.link.change_plan"
                        class="btn btn-sm btn-primary btn-hover-rise"
                        >{{ "Changer mon offre" | trans }}</a
                      >
                    </template>
                  </template>
                  <template v-else>
                    <a
                      :href="design.link.choose"
                      class="btn btn-sm btn-primary btn-hover-rise"
                      >{{ "Choisir ce design" | trans }}</a
                    >
                  </template>
                </template>

                <a
                  :href="design.link.preview"
                  target="_blank"
                  class="ml-2 btn btn-sm btn-light btn-hover-rise"
                  >Prévisualiser
                  <i
                    class="text-muted font-14 ml-1 fas fa-external-link-alt"
                  ></i>
                </a>
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
                  Aucun design pour l'instant.
                </div>
              </div>
            </infinite-loading>
          </div>
        </v-row>
      </div>
    </template>
  </v-app>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  data: function () {
    return {
      page: 1,
      designs: [],
      infiniteId: +new Date(),
      disabled: true,
      isLoading: false,
      defaultOrder: "updatedAt",
      sortList: [],
      newsType: [],
      filterList: [],
    };
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("json_designs", {
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
          if (data.items.length > 0) {
            this.page += 1;
            this.designs.push(...data.items);
            if (this.page > data.totalPages) {
              $state.complete();
            }
            this.sortList.push(...data.sortList);
            this.filterList.push(...data.filterList);
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
    changeType() {
      this.page = 1;
      this.designs = [];
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