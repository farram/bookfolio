<template>
  <v-dialog
    persistent
    max-width="600px"
    :retain-focus="false"
    v-model="dialog"
    scrollable
  >
    <template v-slot:activator="{ on }">
      <button v-on="on" small class="btn btn-sm btn-light">Contacter</button>
    </template>

    <v-card>
      <v-card-title>
        <v-container>
          <v-row>
            <v-col cols="2">
              <v-avatar size="62">
                <img
                  :src="user.identity.avatar"
                  :alt="user.identity.fullname"
                />
              </v-avatar>
            </v-col>
            <v-col cols="10">
              <h3 class="mb-0">{{ user.identity.fullname }}</h3>
              <small style="margin-top: -5px" class="d-block text-muted"
                >{{ user.identity.experience }} -
                {{ user.identity.location }}</small
              >
            </v-col>
          </v-row>
        </v-container>
      </v-card-title>
      <v-card-text>
        <form id="app" @submit="checkForm" method="post">
          <v-container>
            <v-row>
              <v-col cols="12">
                <template v-if="errors.length">
                  <div
                    class="alert alert-danger"
                    v-for="(error, $index) in errors"
                    :key="$index"
                  >
                    {{ error }}
                  </div>
                </template>

                <v-textarea
                  v-model="message"
                  auto-grow
                  filled
                  color="deep-purple"
                  rows="5"
                  :label="
                    'Saisissez votre message pour ' + user.identity.fullname
                  "
                  hint="Hint text"
                ></v-textarea>
              </v-col>
            </v-row>
          </v-container>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              class="btn btn-light text-capitalize waves-effect waves-light"
              text
              @click="dialog = false"
            >
              Annuler
            </v-btn>
            <v-btn
              type="submit"
              class="btn btn-primary text-capitalize waves-effect waves-light"
              text
            >
              Envoyer
            </v-btn>
          </v-card-actions>
        </form>
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

export default {
  components: {
    "modal-contact": ModalContact,
  },
  props: {
    user: {
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
    checkForm: function (e) {
      e.preventDefault();
      this.errors = [];

      if (this.message === "") {
        this.errors.push("Merci de saisir un message.");
      } else {
        fetch(
          Routing.generate("relation_send_message_contact", {
            message: this.message,
            uuid: this.user.identity.uuid,
          })
        ).then(async (res) => {
          if (res.status === 200) {
            Swal.fire(
              "Nice !",
              "Votre message a bien été envoyé à " +
                this.user.identity.fullname,
              "success"
            );
            this.dialog = false;
          } else if (res.status === 400) {
            let errorResponse = await res.json();
            this.errors.push(errorResponse.error);
          }
        });
      }
    },
  },
};
</script>
<style scoped>
.btn {
  height: auto !important;
  font-size: small !important;
  padding: 0.45rem 0.9rem !important;
  /* background-color: #f1faff !important; */
}
</style>