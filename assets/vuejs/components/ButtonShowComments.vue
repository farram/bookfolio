<template v-if="image.countComments.length > 0">
  <v-dialog scrollable max-width="500px" :retain-focus="false" v-model="dialog">
    <template v-slot:activator="{ on }">
      <button v-on="on" href="#" class="text-gray-600 fw-bold mr-2">
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
          </svg>
        </span>
        {{ image.countComments.length }}
        <template v-if="image.countComments.length > 1">
          commentaires
        </template>
        <template v-else>commentaire</template>
      </button>
    </template>
    <v-card>
      <v-card-title>
        <h3 class="m-0">
          {{ image.countComments.length }}
          <template v-if="image.countComments.length > 1">
            commentaires
          </template>
          <template v-else>commentaire</template>
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
        <template v-if="image.countComments.length > 0">
          <div
            class="mb-7 mt-5"
            v-for="(comment, $l) in image.comments"
            :key="$l"
          >
            <div class="d-flex mb-5">
              <div class="symbol symbol-45px me-5">
                <img :src="comment.user.avatar" :alt="comment.user.fullname" />
              </div>
              <div class="d-flex flex-column flex-row-fluid">
                <div class="d-flex align-items-center flex-wrap mb-1">
                  <a
                    href="#"
                    class="text-gray-800 text-hover-primary fw-bolder me-2"
                    >{{ comment.user.fullname }}</a
                  >
                  <span class="text-gray-400 fw-bold fs-7">{{
                    comment.date
                  }}</span>

                  <template v-if="comment.user.uuid == user.uuid">
                    <a
                      role="button"
                      v-on:click="deleteComment"
                      :id="comment.id"
                      class="ms-auto text-hover-primary fw-bold fs-7 text-danger"
                      >Supprimer</a
                    >
                  </template>

                  <template v-else>
                    <a
                      v-b-toggle="'comment-' + comment.id"
                      role="button"
                      aria-expanded="false"
                      class="ms-auto text-gray-400 text-hover-primary fw-bold fs-7"
                      ><i class="mdi mdi-reply"></i> Répondre</a
                    >
                  </template>
                </div>
                <div class="text-gray-800 fs-7 fw-normal pt-1">
                  {{ comment.content }}
                  <b-collapse
                    class="form-block w-form mt-5"
                    :id="'comment-' + comment.id"
                  >
                    <validation-observer :ref="'answer'">
                      <form
                        @submit.prevent="checkFormComment"
                        :id="comment.id"
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
                              required
                              class="form-control border-0 p-0 pe-10 resize-none min-h-25px"
                              :id="'answer-' + comment.id"
                              :auto-grow="true"
                              v-model="comment_c"
                              name="comment"
                              row-height="8"
                              :error-messages="errors"
                              outlined
                              :label="
                                'Votre réponse à ' + comment.user.fullname
                              "
                            ></v-textarea>
                          </validation-provider>

                          <v-btn
                            v-b-toggle="'comment-' + comment.id"
                            role="button"
                            aria-expanded="false"
                            class="mt-2 btn btn-xs waves-effect waves-light btn-primary"
                            >Répondre</v-btn
                          >
                        </div>
                      </form>
                    </validation-observer>
                  </b-collapse>
                </div>

                <div
                  class="mb-0 mt-3"
                  v-for="(answer, $a) in comment.child"
                  :key="$a"
                >
                  <div class="d-flex mb-5">
                    <a
                      :href="answer.user.url"
                      target="_blank"
                      class="symbol symbol-45px me-5"
                    >
                      <img
                        :src="answer.user.avatar"
                        :alt="answer.user.fullname"
                      />
                    </a>
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

                        <template v-if="answer.user.uuid == user.uuid">
                          <a
                            v-on:click="deleteComment"
                            :id="answer.id"
                            class="ms-auto text-hover-primary fw-bold fs-7 text-danger"
                            >Supprimer</a
                          >
                        </template>
                        <template v-else>
                          <a
                            v-b-toggle="'answer-' + answer.id"
                            role="button"
                            aria-expanded="false"
                            class="ms-auto text-gray-400 text-hover-primary fw-bold fs-7"
                            >Répondre</a
                          >
                        </template>
                      </div>
                      <span class="text-gray-800 fs-7 fw-normal pt-1"
                        >{{ answer.content }}
                        <b-collapse
                          class="form-block w-form mt-5"
                          :id="'answer-' + answer.id"
                        >
                          <validation-observer
                            :ref="'answer'"
                            v-slot="{ invalid }"
                          >
                            <form
                              @submit.prevent="checkFormComment"
                              :id="answer.id"
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
                                    required
                                    class="form-control border-0 p-0 pe-10 resize-none min-h-25px"
                                    :id="'answer-' + answer.id"
                                    :auto-grow="true"
                                    v-model="comment_c"
                                    name="answer"
                                    row-height="8"
                                    :error-messages="errors"
                                    outlined
                                    :label="
                                      'Votre réponse à ' + answer.user.fullname
                                    "
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
                        </b-collapse>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <div
            class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-10"
          >
            <span
              class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-5 mb-sm-0"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
              >
                <path
                  opacity="0.25"
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M5.69477 2.48932C4.00472 2.74648 2.66565 3.98488 2.37546 5.66957C2.17321 6.84372 2 8.33525 2 10C2 11.6647 2.17321 13.1563 2.37546 14.3304C2.62456 15.7766 3.64656 16.8939 5 17.344V20.7476C5 21.5219 5.84211 22.0024 6.50873 21.6085L12.6241 17.9949C14.8384 17.9586 16.8238 17.7361 18.3052 17.5107C19.9953 17.2535 21.3344 16.0151 21.6245 14.3304C21.8268 13.1563 22 11.6647 22 10C22 8.33525 21.8268 6.84372 21.6245 5.66957C21.3344 3.98488 19.9953 2.74648 18.3052 2.48932C16.6859 2.24293 14.4644 2 12 2C9.53559 2 7.31411 2.24293 5.69477 2.48932Z"
                  fill="#191213"
                ></path>
                <path
                  fill-rule="evenodd"
                  clip-rule="evenodd"
                  d="M7 7C6.44772 7 6 7.44772 6 8C6 8.55228 6.44772 9 7 9H17C17.5523 9 18 8.55228 18 8C18 7.44772 17.5523 7 17 7H7ZM7 11C6.44772 11 6 11.4477 6 12C6 12.5523 6.44772 13 7 13H11C11.5523 13 12 12.5523 12 12C12 11.4477 11.5523 11 11 11H7Z"
                  fill="#121319"
                ></path>
              </svg>
            </span>
            <div class="d-flex flex-column pe-0 pe-sm-10">
              <h4 class="fw-bold">Aucun commentaire</h4>
              <span>Cette photo n'a pas encore été commenter.</span>
            </div>
          </div>
        </template>
        <infinite-loading
          @distance="1"
          spinner="waveDots"
          :identifier="infiniteId"
          @infinite="infiniteHandler"
          ref="infiniteLoading"
        >
          <div slot="no-more"></div>
          <div slot="no-results"></div>
        </infinite-loading>
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
    fetchCommentsImage: function () {
      let url = Routing.generate("dashboard_get_comment_media", {
        page: this.page,
        image: this.image.id,
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
        this.$refs.infiniteLoading.stateChanger.reset();
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

    deleteComment: function (event) {
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