<template>
  <div>
    <template v-if="releases.length > 0">
      <div
        class="d-flex align-items-start mb-7"
        v-for="release in releases"
        :key="release.id"
      >
        <div class="symbol symbol-45px w-40px me-5">
          <span class="symbol-label bg-lighten">
            <i class="fas fa-newspaper fs-2x"></i>
          </span>
        </div>

        <div class="flex-grow-1">
          <a
            href=""
            target="_blank"
            class="text-dark fw-bolder text-hover-primary fs-6"
            v-html="release.title"
          ></a>
          <span
            class="text-muted d-block fw-bold"
            v-html="release.content"
          ></span>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="text-center alert alert-warning mb-0">
        Aucune actualité pour l'instant.
      </div>
    </template>
  </div>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import FollowBooks from "./FollowBooks.vue";

export default {
  data: function () {
    return {
      releases: [],
      link: "",
    };
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("release_short", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.releases = response.data.releases;
        this.link = response.data.link;
      });
  },
};
</script>