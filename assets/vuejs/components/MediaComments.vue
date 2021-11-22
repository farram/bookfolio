<template>
  <div>
    <div
      class="card mb-5 border mb-xl-8"
      v-for="(comment, $c) in comments"
      :key="$c"
    >
      <div class="card-body pb-0">
        <div class="d-flex align-items-center mb-5">
          <div class="d-flex align-items-center flex-grow-1">
            <div class="symbol symbol-45px me-5">
              <a :href="comment.user.url" target="_blank" class="symbol">
                <template v-if="comment.user.certified">
                  <v-badge
                    bordered
                    title="Book certifié"
                    bottom
                    class="symbol"
                    offset-x="20"
                    offset-y="20"
                    color="blue accent-4 small"
                    icon="mdi-check"
                  >
                    <img :src="comment.user.avatar" class="" alt="" />
                  </v-badge>
                </template>
                <template v-else>
                  <img :src="comment.user.avatar" class="" alt="" />
                </template>
              </a>
            </div>
            <div class="d-flex flex-column">
              <a
                :href="comment.user.url"
                target="_blank"
                class="text-gray-900 text-hover-primary fs-6 fw-bolder"
                >{{ comment.user.fullname }}</a
              >
              <span class="text-gray-400 fw-bold"
                >{{ comment.user.profession }} - {{ comment.date }}</span
              >
            </div>
          </div>
          <div class="my-0">
            <a
              v-on:click="deleteEvent"
              :id="comment.id"
              href="javascript:void(0);"
              class="ms-auto text-danger text-hover-danger fw-bold fs-7"
              >Supprimer</a
            >

            <div
              class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-bold w-200px"
              data-kt-menu="true"
            >
              <div class="menu-item px-3">
                <a
                  v-on:click="deleteEvent"
                  :id="comment.id"
                  href="javascript:void(0);"
                  class="menu-link px-3"
                  >Supprimer</a
                >
              </div>
            </div>
          </div>
        </div>
        <div class="mb-5">
          <p class="text-gray-800 fw-normal mb-5">
            {{ comment.content }}
          </p>
        </div>

        <div class="mb-7 ps-10" v-if="comment.answers">
          <div
            class="d-flex mb-5"
            v-for="(answer, $a) in comment.answers"
            :key="$a"
          >
            <div class="symbol symbol-45px me-5">
              <a :href="answer.user.url" target="_blank" class="symbol">
                <template v-if="answer.user.certified">
                  <v-badge
                    bordered
                    title="Book certifié"
                    bottom
                    class="symbol"
                    offset-x="20"
                    offset-y="20"
                    color="blue accent-4 small"
                    icon="mdi-check"
                  >
                    <img
                      :src="answer.user.avatar"
                      class=""
                      :alt="answer.user.fullname"
                    />
                  </v-badge>
                </template>
                <template v-else>
                  <img
                    :src="answer.user.avatar"
                    class=""
                    :alt="answer.user.fullname"
                  />
                </template>
              </a>
            </div>
            <div class="d-flex flex-column flex-row-fluid">
              <div class="d-flex align-items-center flex-wrap mb-1">
                <a
                  :href="answer.user.url"
                  target="_blank"
                  class="text-gray-800 text-hover-primary fw-bolder me-2"
                  >{{ answer.user.fullname }}</a
                >
                <span class="text-gray-400 fw-bold fs-7">{{
                  answer.date
                }}</span>
                <a
                  v-on:click="deleteEvent"
                  :id="answer.id"
                  href="javascript:void(0);"
                  class="ms-auto text-danger text-hover-danger fw-bold fs-7"
                  >Supprimer</a
                >
              </div>
              <span class="text-gray-800 fs-7 fw-normal pt-1">{{
                answer.content
              }}</span>
            </div>
          </div>
        </div>

        <validation-observer :ref="'answer'" v-slot="{ invalid }">
          <form
            @submit.prevent="checkFormComment"
            :id="comment.id"
            class="position-relative mb-6"
            ref="form"
            lazy-validation
          >
            <div class="mb-1">
              <validation-provider
                v-slot="{ errors }"
                name="comment_c"
                rules="required"
              >
                <v-textarea
                  hide-details
                  data-kt-autosize="true"
                  required
                  class="form-control border-0 p-0 pe-10 resize-none min-h-25px"
                  :id="'answer-' + comment.id"
                  :auto-grow="true"
                  v-model="comment_c"
                  name="comment"
                  row-height="8"
                  :error-messages="errors"
                  :label="'Votre réponse à ' + comment.user.fullname"
                ></v-textarea>
              </validation-provider>
              <v-btn
                :disabled="invalid"
                type="submit"
                class="mt-2 btn btn-xs waves-effect waves-light btn-primary"
                name="answer-comment-button"
                >Répondre</v-btn
              >
            </div>
          </form>
        </validation-observer>
      </div>
    </div>

    <infinite-loading
      @distance="1"
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
      ref="infiniteLoading"
    >
      <div slot="no-more"></div>
      <div slot="no-results">
        <b-alert show variant="light"
          >Aucun commentaire pour l'instant.</b-alert
        >
      </div>
    </infinite-loading>
  </div>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import VCardUser from "./VCardUser.vue";

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
    fetchCommentsImage: function () {
      let url = Routing.generate("dashboard_get_comment_media", {
        page: this.page,
        image: imageId,
      });
      return this.$http.get(url);
    },
    infiniteHandler: function ($state) {
      this.fetchCommentsImage()
        .then((response) => {
          if (response.data.comments.length > 0) {
            this.page += 1;
            this.countTotalComment = response.data.countTotalComment;
            this.comments.push(...response.data.comments);
            if (this.page > response.data.totalPages) {
              $state.complete();
            }
            $state.loaded();
          } else {
            $state.complete();
          }
        })
        .catch((e) => console.log(e));
    },
    checkFormComment: function (e) {
      e.preventDefault();
      if (!this.comment_c) {
        this.errors.push("Vous avez oublié de saisir votre message.");
      } else {
        this.$refs.infiniteLoading.stateChanger.reset();

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
            data: "commentId=" + commentId + "&imageId=" + imageId,
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
  },
};
</script>