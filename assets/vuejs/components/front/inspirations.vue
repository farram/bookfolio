<template>
  <div>
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>
    <masonry
      :cols="{ default: 6, 1000: 3, 700: 2, 400: 1 }"
      :gutter="{ default: '10px', 700: '15px' }"
    >
      <div
        v-for="(image, $index) in images"
        :key="$index"
        class="bm30 mb-1 image-wrap w-dyn-item"
      >
        <span class="item-masonry w-inline-block">
          <div class="label" style="z-index: 1">
            <a :href="image.user.book" target="_blank">{{
              image.user.fullname
            }}</a>
          </div>

          <v-img
            v-on:click="setview(image.id)"
            class="grey lighten-2"
            v-img="{ src: image.path, title: image.title }"
            :alt="image.title"
            :src="image.thumb_path"
            :lazy-src="image.thumb_path"
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
        </span>
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
      width: 600,
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
          Routing.generate("image_inspiration", {
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