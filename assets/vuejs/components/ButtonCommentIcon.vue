<template>
  <v-dialog scrollable max-width="500px" :retain-focus="false" v-model="dialog">
    <template v-slot:activator="{ on }">
      <v-btn v-on="on" x-small class="bg-white">
        <v-icon small class="text-muted">mdi-comment-multiple</v-icon>
        <span class="font-14 text-muted d-inline-block ml-1">Commenter</span>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        <h3 class="m-0">Commenter</h3>
        <v-spacer></v-spacer>
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn v-bind="attrs" v-on="on" icon light @click="dialog = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </template>
        </v-menu>
      </v-card-title>
      <v-divider class="mt-0"></v-divider>
      <v-card-text style="max-height: 500px">
        <div>
          <b-media class="mt-2">
            <template #aside>
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
            </template>

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
          </b-media>
        </div>
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
        this.dialog = false;
      }
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