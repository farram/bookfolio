<template>
  <masonry
    :cols="{ default: 4, 1000: 3, 700: 2, 400: 1 }"
    :gutter="{ default: '30px', 700: '15px' }"
  >
    <div
      class="margin-lg-30b port-item-htext"
      v-for="(image, $index) in images"
      :key="$index"
    >
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
    <infinite-loading
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="no-more"></div>
      <div slot="no-results"></div>
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
      images: [],
      lastPage: 0,
      infiniteId: +new Date(),
    };
  },
  methods: {
    setview: function (id) {
      $.ajax({
        url: Routing.generate("setviewmedias", { name: name, image: id }),
        type: "GET",
        data: id,
        success: function (data) {},
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(textStatus, errorThrown);
        },
      });
    },
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("portfolio_images_from_gallery", {
            slug: gallerySlug,
            name: name,
            page: this.page,
          })
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