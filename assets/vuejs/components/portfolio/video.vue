<template>
  <div class="row">
    <go-top
      bg-color="#36b5aa"
      src="/assets/img/icon-backtotop.png"
      :size="50"
      :has-outline="false"
    ></go-top>

    <div
      v-for="(video, $index) in videos"
      :key="$index"
      class="col-lg-6"
      style="margin-bottom: 30px"
    >
      <video-embed :src="video.url"></video-embed>
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
                Aucune vidéo pour l'instant.
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
      videos: [],
      infiniteId: +new Date(),
      page: 1,
    };
  },
  methods: {
    infiniteHandler($state) {
      this.$http
        .get(
          Routing.generate("portfolio_videos", {
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
          if (data.videos.length > 0) {
            this.page += 1;
            this.videos.push(...data.videos);
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