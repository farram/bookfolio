<template>
  <div class="w-dyn-list">
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>

    <div class="project-grid w-dyn-items">
      <div
        v-for="(image, $index) in images"
        :key="$index"
        data-custom=""
        title=""
        data-w-id="78ff313e-0279-806c-d00c-825c46dc0562"
        class="project-grid-item items w-dyn-item"
      >
        <div
          data-w-id="9d536c8f-2f81-90e3-7188-fb1a3e79ac93"
          class="project-grid-link w-inline-block w-lightbox-"
        >
          <div class="project-grid-image-container">
            <img
              v-on:click="setview(image.id)"
              v-img="{ src: image.path, title: image.title }"
              :lazy-src="image.thumb_path"
              :src="image.thumb_path"
              :alt="image.title"
              class="project-image-element"
            />
          </div>
          <div class="project-grid-title-container">
            <h3 class="project-grid-title text-h1">{{ image.title }}</h3>
          </div>
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