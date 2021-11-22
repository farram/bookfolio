<template v-if="user">
  <v-row>
    <v-col cols="12" xl="3">
      <a :href="user.identity.url" target="_blank">
        <img
          :src="user.identity.avatar"
          class="rounded-circle img-fluid"
          :alt="user.identity.fullname"
        />
      </a>
    </v-col>
    <v-col class="text-left" cols="12" xl="8">
      <h4 class="mb-0 mt-0">
        <a :href="user.identity.url" target="_blank" class="text-reset">
          {{ user.identity.fullname }}
        </a>
      </h4>
      <p class="text-muted mb-0">{{ user.identity.experience }}</p>
      <p class="text-muted mb-0">
        <small>{{ user.identity.location }}</small>
      </p>
      <p class="text-muted mt-0">
        <small v-html="user.countFolders"></small>
        <small class="ml-2" v-html="user.countMedias"></small>
      </p>
      <button-follow :user="user"></button-follow>
    </v-col>

    <div class="col-lg-12">
      <div class="border-top border-light mt-3 pt-2 text-left">
        <h4 class="mb-3 text-left">Dernières publications</h4>
      </div>
      <div v-if="user.images.length > 0" class="media">
        <v-row>
          <v-col
            v-for="image in user.images"
            :key="image"
            class="d-flex child-flex"
            cols="4"
          >
            <v-img
              v-on:click="setview(image.id)"
              v-img="{ src: image.path, title: image.title }"
              :src="image.thumb"
              :lazy-src="image.thumb"
              :alt="image.title"
              aspect-ratio="1"
              class="grey lighten-2 rounded mb-3"
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
          </v-col>
          <v-col
            v-if="user.countMedias.length > 6"
            class="text-right"
            cols="12"
            xl="12"
          >
            <div class="mt-0">
              <a
                :href="user.identity.url"
                target="_blank"
                role="button"
                aria-disabled="true"
                class="text-white btn btn-primary-color btn-sm waves-effect waves-light"
                >Plus de publications <i class="fe-external-link"></i
              ></a>
            </div>
          </v-col>
        </v-row>
      </div>
      <div v-else>
        <div class="alert alert-warning">
          Aucune publication sur son book pour l'instant.
        </div>
      </div>
    </div>
  </v-row>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ButtonFollow from "./ButtonFollow.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
  },
  data: function () {
    return {
      user: [],
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
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("dashboard_inbox_card_user", {
          uuid: uuid,
        })
      )
      .then(function (response) {
        this.user = response.data;
      });
  },
};
</script>