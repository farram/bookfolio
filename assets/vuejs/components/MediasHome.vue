<template>
  <masonry
    :cols="{ default: 4, 1000: 3, 700: 2, 400: 1 }"
    :gutter="{ default: '30px', 700: '15px' }"
    ><go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>
    <div v-for="(image, $index) in medias" :key="$index" class="margin-lg-30b">
      <div class="port-item-htext">
        <v-img
          v-on:click="setview(image.id)"
          class="grey lighten-2"
          :lazy-src="image.thumb_path"
          v-img="{ src: image.path, title: image.title }"
          :alt="image.title"
          :src="image.thumb_path"
        >
          <template v-slot:placeholder>
            <v-row class="fill-height ma-0" align="center" justify="center">
              <v-progress-circular
                indeterminate
                color="grey lighten-5"
              ></v-progress-circular>
            </v-row>
          </template>
        </v-img>
      </div>
    </div>
    <infinite-loading
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="no-more"></div>
      <div slot="no-results">
        <div class="alert alert-warning text-center">
          C'est tout pour le moment :)
        </div>
      </div>
    </infinite-loading>
  </masonry>
</template>


<script>
import InfiniteLoading from "vue-infinite-loading";
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  data: function () {
    return {
      page: 1,
      medias: [],
      infiniteId: +new Date(),
    };
  },
  methods: {
    setview: function (id) {
      $.ajax({
        url: Routing.generate("setviewmedias", { name: name, image: id }),
        type: "GET",
        data: id,
        success: function (response) {},
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
        },
      });
    },
    infiniteHandler: function ($state) {
      {
        this.$http
          .get(
            Routing.generate("portfolio_medias", {
              name: name,
              page: this.page,
            }),
            {
              params: {
                page: this.page,
              },
            }
          )
          .then((response) => {
            if (response.data.images.length > 0) {
              this.page += 1;
              this.medias.push(...response.data.images);
              if (this.page > response.data.totalPages) {
                $state.complete();
              }
              $state.loaded();
            } else {
              $state.complete();
            }
          })
          .catch((e) => console.log(e));
      }
    },
  },
};
</script>