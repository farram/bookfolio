<template>
  <div>
    <template v-if="annonces.length > 0">
      <div class="widget-rounded-circle card-box border">
        <v-list class="p-0">
          <v-list-item
            v-for="annonce in annonces"
            :key="annonce.id"
            class="p-0"
          >
            <a :href="annonce.user.url" target="_blank">
              <v-list-item-avatar>
                <v-img
                  :alt="annonce.user.fullname"
                  :src="annonce.user.avatar"
                ></v-img>
              </v-list-item-avatar>
            </a>

            <v-list-item-content class="ml-2">
              <a :href="annonce.url" target="_blank" class="text-dark">
                <v-list-item-title v-text="annonce.title"></v-list-item-title>
              </a>
              <v-list-item-subtitle class="mt-1 text-muted"
                >De {{ annonce.user.fullname }} -
                {{ annonce.date }}</v-list-item-subtitle
              >
            </v-list-item-content>

            <v-list-item-icon>
              <a :href="annonce.url" target="_blank">
                <v-icon> mdi-eye </v-icon>
              </a>
            </v-list-item-icon>
          </v-list-item>
        </v-list>
      </div>
    </template>
    <template v-else>
      <div class="text-center alert alert-warning">
        Aucune annonce pour l'instant.
      </div>
    </template>

    <template v-if="displaySuggestFollowers">
      <div class="row">
        <div class="col-12">
          <h4 class="float-left mb-3">Suggestion des books à suivre</h4>
          <div class="float-right">
            <a href="#" class="btn btn-link text-blue" target="_blank">
              Voir plus
              <i class="fe-arrow-right"></i>
            </a>
          </div>
          <div class="clearfix"></div>
          <follow-books></follow-books>
        </div>
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
  components: {
    "follow-books": FollowBooks,
  },
  data: function () {
    return {
      annonces: [],
      displaySuggestFollowers: true,
    };
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("dashboard_short_annonces", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.annonces = response.data.annonces;
        if (response.data.totalItemsSuggestFollowers <= 0) {
          this.displaySuggestFollowers = false;
        }
      });
  },
};
</script>