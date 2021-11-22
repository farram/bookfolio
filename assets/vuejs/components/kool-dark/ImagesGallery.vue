<template>
  <div id="items-gallery">
    <v-app>
      <go-top
        bg-color="#36b5aa"
        src="/assets/img/icon-backtotop.png"
        :size="50"
        :has-outline="false"
      ></go-top>
      <template v-if="images">
        <masonry
          :cols="{ default: 2, 1000: 2, 700: 1, 400: 1 }"
          :gutter="{ default: '25px', 700: '15px' }"
        >
          <div v-for="(image, $index) in images" :key="$index" class="">
            <div class="overlay">
              <v-img
                v-on:click="setview(image.id)"
                class="grey lighten-2 mb-5"
                :lazy-src="image.thumb_path"
                v-img="{ src: image.thumb_path, title: image.title }"
                :alt="image.title"
                :src="image.thumb_path"
              >
                <template v-slot:placeholder>
                  <v-row
                    class="fill-height ma-0"
                    align="center"
                    justify="center"
                  >
                    <v-progress-circular
                      indeterminate
                      color="grey lighten-5"
                    ></v-progress-circular>
                  </v-row>
                </template>
              </v-img>
            </div>
          </div>
        </masonry>
        <infinite-loading
          spinner="waveDots"
          :identifier="infiniteId"
          @infinite="infiniteHandler"
        >
          <div slot="no-more"></div>
          <div slot="no-results">
            <v-container>
              <v-row>
                <v-col>
                  <div class="alert alert-warning">
                    Aucune publication pour l'instant.
                  </div>
                </v-col>
              </v-row>
            </v-container>
          </div>
        </infinite-loading>
      </template>
    </v-app>
  </div>
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
      images: [],
      infiniteId: +new Date(),
      page: 1,
    };
  },
  methods: {
    setview: function (id) {
      $.ajax({
        url: Routing.generate("portfolio_setviewmedias", {
          name: name,
          image: id,
        }),
        type: "GET",
        data: id,
        success: function (response) {},
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
        },
      });
    },
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("portfolio_medias_from_gallery", {
            slug: gallerySlug,
            name: name,
            page: this.page,
          }),
          {
            params: {
              page: this.page,
            },
          }
        )
        .then(({ data }) => {
          if (data.images.length > 0) {
            this.page += 1;
            this.images.push(...data.images);
            if (this.page > data.totalPages) {
              $state.complete();
            }
            $state.loaded();
          } else {
            $state.complete();
          }
        });
    },
  },
};
</script>