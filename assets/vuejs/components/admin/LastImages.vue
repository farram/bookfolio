<template>
  <v-app>
    <v-row>
      <v-col
        v-for="(image, $index) in images"
        :key="$index"
        class="d-flex child-flex mb-3"
        cols="6"
      >
        <div class="gal-box mb-0">
          <v-img
            aspect-ratio="1.4"
            class="white--text align-end grey lighten-2 img-fluid"
            :lazy-src="image.thumb"
            v-img="{ src: image.path, title: image.title }"
            :alt="image.title"
            :src="image.thumb"
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
          <div class="gall-info text-center pb-2">
            <a :href="image.user.url" target="_blank">
              <span class="text-dark d-block ms-1">{{
                image.user.fullname
              }}</span>
              <span class="text-muted d-block ms-1">{{
                image.user.profession
              }}</span>
              <small class="text-muted d-block ms-1">{{ image.date }}</small>
            </a>
          </div>
        </div>
      </v-col>
    </v-row>
  </v-app>
</template>

<script>
const routes = require("../../../../public/js/fos_js_routes.json");
import Routing from "../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  data: function () {
    return {
      images: [],
    };
  },
  mounted() {
    this.$http
      .get(Routing.generate("json_admin_last_images"))
      .then(function (response) {
        this.images = response.data.items;
      });
  },
};
</script>