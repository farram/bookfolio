<template v-if="image.likes.length > 0">
  <v-dialog scrollable max-width="500px" :retain-focus="false" v-model="dialog">
    <template v-slot:activator="{ on }">
      <button href="#" class="text-gray-600 fw-bold me-4" v-on="on">
        <i class="fas fa-heart"></i> {{ image.likes.length }}
      </button>
    </template>
    <v-card>
      <v-card-title>
        <h3 class="m-0">
          {{ image.likes.length }}
          <template v-if="image.likes.length > 1">
            personnes aiment cette photo
          </template>
          <template v-else>personne aime cette photo</template>
        </h3>
        <v-spacer></v-spacer>
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon light @click="dialog = false">
              <i class="fas fa-times"></i>
            </v-btn>
          </template>
        </v-menu>
      </v-card-title>
      <v-divider class="mt-0"></v-divider>
      <v-card-text style="height: 500px">
        <table class="table align-middle gs-0 gy-3">
          <!--begin::Table head-->
          <thead>
            <tr>
              <th class="p-0 w-50px"></th>
              <th class="p-0 min-w-150px"></th>
              <th class="p-0 min-w-140px"></th>
              <th class="p-0 min-w-120px"></th>
            </tr>
          </thead>
          <!--end::Table head-->
          <!--begin::Table body-->
          <tbody>
            <tr v-for="(like, $l) in image.likes" :key="$l">
              <td>
                <div class="symbol symbol-50px">
                  <a :href="like.identity.url" class="symbol" target="_blank">
                    <img
                      :src="like.identity.avatar"
                      :alt="like.identity.fullname"
                    />
                  </a>
                </div>
              </td>
              <td>
                <a
                  :href="like.identity.url"
                  target="_blank"
                  class="text-dark fw-bolder text-hover-primary mb-1 fs-6"
                  >{{ like.identity.fullname }}</a
                >
                <span class="text-muted fw-bold d-block fs-7">{{
                  like.identity.profession
                }}</span>
              </td>

              <td class="text-end">
                <button-follow :user="like"></button-follow>
              </td>
            </tr>
          </tbody>
        </table>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ModalContact from "./ModalContact.vue";
import ButtonFollow from "./ButtonFollow.vue";

export default {
  components: {
    "modal-contact": ModalContact,
    "button-follow": ButtonFollow,
  },
  props: {
    image: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      errors: [],
      dialog: false,
      message: "",
    };
  },
  methods: {
    like: function (image, action) {
      $.ajax({
        type: "GET",
        url: Routing.generate("dashboard_set_like_media", {
          action: action,
          image: image,
        }),
        success: function (event) {
          if (event.target.className == "like-btn") {
            event.target.className = "far fa-heart liked-btn";
          } else {
            event.target.className = "far fa-heart text-danger like-btn";
          }
        },
      });
    },
  },
};
</script>