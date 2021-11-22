<template>
  <v-dialog scrollable max-width="500px" :retain-focus="false" v-model="dialog">
    <template v-slot:activator="{ on }">
      <button
        v-on="on"
        href="#"
        class="btn btn-sm btn-light btn-color-muted btn-active-light-success px-4 py-2"
      >
        <span class="svg-icon svg-icon-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24px"
            height="24px"
            viewBox="0 0 24 24"
            version="1.1"
          >
            <path
              d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z"
              fill="#000000"
            ></path>
            <path
              d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z"
              fill="#000000"
              opacity="0.3"
            ></path>
          </svg> </span
        >Commenter
      </button>
    </template>
    <v-card>
      <v-card-title>
        <h3 class="m-0" v-if="countTotalComment > 1">
          {{ countTotalComment }} commentaires
        </h3>
        <h3 class="m-0" v-else>{{ countTotalComment }} commentaire</h3>
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
      <v-card-text style="max-height: 500px">
        <div class="d-flex mt-5">
          <div class="flex-shrink-0">
            <v-img
              v-on:click="setview(image.id)"
              aspect-ratio="1.4"
              class="rounded grey lighten-2 img-fluid mr-2"
              :lazy-src="image.thumb"
              v-img="{ src: image.path, title: image.title }"
              :alt="image.title"
              :src="image.thumb"
              width="85"
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
          <div class="flex-grow-1 ms-3">
            <div class="form-block w-form mb-3">
              <validation-observer ref="observer" v-slot="{ invalid, valid }">
                <form @submit.prevent="checkForm">
                  <validation-provider
                    v-slot="{ errors }"
                    name="comment"
                    rules="required"
                  >
                    <v-textarea
                      :success="valid"
                      hide-details
                      class="mb-0"
                      id="comment"
                      :auto-grow="true"
                      v-model="comment"
                      name="comment"
                      row-height="10"
                      outlined
                      required
                      label="Que pensez-vous de cette photo ?"
                      :error-messages="errors"
                      aria-describedby="name-errors"
                    ></v-textarea>
                    <b-form-invalid-feedback id="name-errors">{{
                      errors[0]
                    }}</b-form-invalid-feedback>
                  </validation-provider>
                  <v-btn
                    :disabled="invalid"
                    right
                    type="submit"
                    class="float-right mt-2 btn waves-effect waves-light btn-primary"
                    name="comment-button"
                    >Commenter</v-btn
                  >
                  <div class="clearfix"></div>
                </form>
              </validation-observer>
            </div>
          </div>
        </div>

        <div></div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";

import { required, digits, email, max, regex } from "vee-validate/dist/rules";
import {
  extend,
  ValidationObserver,
  ValidationProvider,
  setInteractionMode,
} from "vee-validate";

extend("required", {
  ...required,
  message: "{_field_} can not be empty",
});

export default {
  components: {
    ValidationProvider,
    ValidationObserver,
  },
  props: {
    image: {
      type: Object,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      page: 1,
      errors: [],
      success: [],
      dialog: false,
      comment: "",
      savingSuccessful: false,
      comments: [],
      infiniteId: +new Date(),
      comment_c: null,
      countTotalComment: 0,
      clicked: [],
    };
  },
  computed: {},
  methods: {
    checkForm: function (e) {
      this.$refs.observer.validate();
      this.clicked.push(this.comment);
      if (!this.comment) {
        return;
      }
      e.preventDefault();
      if (!this.comment) {
        this.errors.push("Vous avez oublié de saisir votre message.");
      } else {
        var imageId = this.image.id;
        this.savingSuccessful = true;
        $.ajax({
          type: "POST",
          url: Routing.generate("dashboard_post_comment_media"),
          data: "image=" + imageId + "&comment=" + this.comment,
          success: function () {
            this.dialog = false;
            Swal.fire({
              title: "Tip top !",
              text: "Votre commentaire a bien été pris en compte.",
              icon: "success",
              confirmButtonText: "Fermer",
            });
          },
        });
        this.comment = null;
      }
    },
    checkFormComment: function (e) {
      e.preventDefault();
      if (!this.comment_c) {
        this.errors.push("Vous avez oublié de saisir votre message.");
      } else {
        this.$refs.infiniteLoading.stateChanger.reset();
        var imageId = this.image.id;
        this.savingSuccessful = true;
        $.ajax({
          type: "POST",
          url: Routing.generate("dashboard_post_comment_media"),
          data:
            "image=" +
            imageId +
            "&comment=" +
            this.comment_c +
            "&comment_parent=" +
            e.currentTarget.id,
          success: function () {
            Swal.fire({
              title: "Tip top !",
              text: "Votre réponse à bien été prise en compte",
              icon: "success",
              confirmButtonText: "Fermer",
            });
          },
        });
        this.comment_c = null;
      }
    },

    deleteEvent: function (event) {
      let resetComments = this.$refs.infiniteLoading.stateChanger.reset();
      var commentId = event.currentTarget.id;
      Swal.fire({
        title: "Êtes-vous sûr ?",
        text: "Souhaitez-vous vraiment supprimer ce commentaire ?",
        footer:
          "<small class='text-center'>Les réponses liées à ce commentaire, seront également supprimées.</small>",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2169F5",
        cancelButtonColor: "#ccc",
        confirmButtonText: "Oui, je confirme",
        cancelButtonText: "Annuler",
      }).then((result) => {
        if (result.value) {
          $.ajax({
            type: "POST",
            url: Routing.generate("dashboard_delete_comment_media"),
            data: "commentId=" + commentId + "&imageId=" + this.image.id,
            success: function () {
              resetComments;
              Swal.fire(
                "Ok, c'est supprimé !",
                "Votre commentaire a bien été supprimé.",
                "success"
              );
            },
          });
        }
      });
    },
    setview: function (id) {
      $.ajax({
        url: Routing.generate("portfolio_setviewmedias", {
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