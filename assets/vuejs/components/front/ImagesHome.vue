<template>
  <div class="section">
    <div class="collection-list-wrapper-3 w-dyn-list">
      <div class="product3-grid w-dyn-items">
        <go-top
          bg-color="#36b5aa"
          src="/assets/img/icon-backtotop.png"
          :size="50"
          :has-outline="false"
        ></go-top>
        <div
          id="w-node-1db8f62086bc-d1ff2247"
          v-for="(image, $index) in images"
          :key="$index"
          class="w-dyn-item mb-3"
        >
          <div class="image-wrap">
            <div>
              <div class="product-image product-image-size-l">
                <v-img
                  v-on:click="setview(image.id)"
                  :aspect-ratio="3 / 4"
                  :width="width"
                  class="grey lighten-2"
                  v-img="{ src: image.path, title: image.title }"
                  :alt="image.title"
                  :src="image.thumb_path"
                  :lazy-src="image.thumb_path"
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

              <div class="label inside-label">
                <a :href="image.user.book" target="_blank">{{
                  image.user.fullname
                }}</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
      width: 600,
    };
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
  },
};
</script>