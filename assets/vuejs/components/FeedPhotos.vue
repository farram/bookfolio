<template>
  <v-app>
    <v-row>
      <v-col cols="12">
        <div class="d-flex flex-wrap flex-stack pb-5">
          <div class="d-flex flex-wrap align-items-center my-1">
            <h3 class="fw-bolder me-5 my-1">Votre fil d'actualité</h3>
          </div>
          <div class="d-flex flex-wrap my-1">
            <div class="d-flex my-0">
              <select
                v-model="selected"
                class="form-select form-select-white form-select-sm text-muted"
                data-control="select2_"
                data-hide-search="true"
                @change="changeType"
              >
                <option
                  v-bind:value="list.value"
                  v-for="list in displayList"
                  :key="list.id"
                >
                  {{ list.text }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div
            class="col-sm-6 col-xl-6"
            v-for="(image, $index) in images"
            :key="$index"
          >
            <div class="card mb-5 mb-xl-8">
              <div class="card-body pb-0">
                <div class="d-flex align-items-center mb-5">
                  <div class="d-flex align-items-center flex-grow-1">
                    <a
                      :href="image.user.url"
                      target="_blank"
                      class="symbol symbol-45px me-5"
                    >
                      <img :src="image.user.avatar" alt="" />
                    </a>
                    <div class="d-flex flex-column">
                      <a
                        :href="image.user.url"
                        target="_blank"
                        class="text-gray-800 text-hover-primary fs-6 fw-bolder"
                      >
                        {{ image.user.fullname }}</a
                      >
                      <span class="text-gray-400 fw-bold">
                        {{ image.user.profession }} - {{ image.date }}</span
                      >
                    </div>
                  </div>

                  <div class="my-0">
                    <button
                      type="button"
                      class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary"
                      data-kt-menu-trigger="click"
                      data-kt-menu-placement="bottom-start"
                      data-kt-menu-flip="top-start"
                    >
                      >
                      <span class="svg-icon svg-icon-2">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          xmlns:xlink="http://www.w3.org/1999/xlink"
                          width="24px"
                          height="24px"
                          viewBox="0 0 24 24"
                          version="1.1"
                        >
                          <g
                            stroke="none"
                            stroke-width="1"
                            fill="none"
                            fill-rule="evenodd"
                          >
                            <rect
                              x="5"
                              y="5"
                              width="5"
                              height="5"
                              rx="1"
                              fill="#000000"
                            />
                            <rect
                              x="14"
                              y="5"
                              width="5"
                              height="5"
                              rx="1"
                              fill="#000000"
                              opacity="0.3"
                            />
                            <rect
                              x="5"
                              y="14"
                              width="5"
                              height="5"
                              rx="1"
                              fill="#000000"
                              opacity="0.3"
                            />
                            <rect
                              x="14"
                              y="14"
                              width="5"
                              height="5"
                              rx="1"
                              fill="#000000"
                              opacity="0.3"
                            />
                          </g>
                        </svg>
                      </span>
                    </button>
                    <div
                      class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4"
                      data-kt-menu="true"
                    >
                      <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">New Ticket</a>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="mb-5">
                  <v-img
                    v-on:click="setview(image.id)"
                    aspect-ratio="1.4"
                    class="grey lighten-2 bgi-no-repeat bgi-size-cover rounded min-h-250px mb-5"
                    :lazy-src="image.thumb"
                    v-img="{ src: image.path, title: image.title }"
                    :alt="image.title"
                    :src="image.thumb"
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

                  <div class="d-flex align-items-end mb-5">
                    <button-show-likes :image="image"></button-show-likes>
                    <button-show-comments
                      :image="image"
                      :user="user"
                    ></button-show-comments>
                  </div>

                  <div class="separator mb-4"></div>

                  <div class="d-flex align-items-center mb-5">
                    <a
                      href="#"
                      class="btn btn-sm btn-light btn-color-muted btn-active-light-danger px-4 py-2 me-4"
                    >
                      <span class="svg-icon svg-icon-2">
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          xmlns:xlink="http://www.w3.org/1999/xlink"
                          width="24px"
                          height="24px"
                          viewBox="0 0 24 24"
                          version="1.1"
                        >
                          <g
                            stroke="none"
                            stroke-width="1"
                            fill="none"
                            fill-rule="evenodd"
                          >
                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                            <path
                              d="M16.5,4.5 C14.8905,4.5 13.00825,6.32463215 12,7.5 C10.99175,6.32463215 9.1095,4.5 7.5,4.5 C4.651,4.5 3,6.72217984 3,9.55040872 C3,12.6834696 6,16 12,19.5 C18,16 21,12.75 21,9.75 C21,6.92177112 19.349,4.5 16.5,4.5 Z"
                              fill="#000000"
                              fill-rule="nonzero"
                            ></path>
                          </g>
                        </svg>
                      </span>
                      J'aime</a
                    >

                    <button-comment
                      v-if="image.allowComments"
                      :image="image"
                      :user="user"
                    ></button-comment>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <infinite-loading
          spinner="waveDots"
          :identifier="feedId"
          @infinite="infiniteHandler"
        >
          <div slot="no-more"></div>
          <div slot="no-results">
            <follow-books :user="user"></follow-books>
          </div>
        </infinite-loading>
      </v-col>
    </v-row>
  </v-app>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

import Comment from "./Comment.vue";
import FollowBooks from "./FollowBooks.vue";
import buttonShowLikes from "./ButtonShowLikes.vue";
import buttonShowComments from "./ButtonShowComments.vue";
import buttonComment from "./ButtonComment.vue";
import ButtonCommentIcon from "./ButtonCommentIcon.vue";
import VCardUser from "./VCardUser.vue";
import VueStar from "vue-star";

export default {
  components: {
    comment: Comment,
    "follow-books": FollowBooks,
    "button-show-likes": buttonShowLikes,
    "button-show-comments": buttonShowComments,
    "button-comment": buttonComment,
    "button-comment-icon": ButtonCommentIcon,
    "v-card-user": VCardUser,
    VueStar,
  },
  data: function () {
    return {
      selected: "following",
      displayList: [
        { value: "following", text: "Les publications de mes abonnés" },
        { value: "all", text: "Les publications récentes" },
        // { value: "popular", text: "Les publications populaires" },
      ],
      page: 1,
      images: [],
      likesCount: 0,
      liked: false,
      feedId: +new Date(),
      errors: [],
      comment: "",
      dialog: false,
      user: [],
      newsType: [],
      menu: [],
      linkSuggest: false,
    };
  },

  methods: {
    checkForm: function (index) {
      if (this.comment[index]) {
        return true;
      }
      this.errors = [];
      if (!this.comment[index]) {
        this.errors.push("Merci de saisir un commentaire");
      } else {
        fetch(
          Routing.generate("dashboard_comment_media", {
            media: "",
            comment: this.comment[index],
          })
        ).then(async (res) => {
          if (res.status === 204) {
            alert("OK");
          } else if (res.status === 400) {
            let errorResponse = await res.json();
            this.errors.push(errorResponse.error);
          }
        });
      }
      //e.preventDefault();
    },
    fetchMedias: function () {
      let url = Routing.generate("dashboard_feed_medias", {
        page: this.page,
      });
      return this.$http.get(url, {
        params: {
          page: this.page,
          type: this.newsType,
          displayBy: this.selected,
        },
      });
    },

    toggleLike: function (image, event, action) {
      $.ajax({
        type: "GET",
        url: Routing.generate("dashboard_set_like_media", {
          action: action,
          image: image,
        }),
        success: function () {
          if (action == "unlike") {
            event.target.classList.toggle("text-danger");
            event.target.classList.toggle("text-muted");
          } else {
            event.target.classList.toggle("text-muted");
            event.target.classList.toggle("text-danger");
          }
        },
      });
    },

    like: function (image, action) {
      $.ajax({
        type: "GET",
        url: Routing.generate("dashboard_set_like_media", {
          action: action,
          image: image,
        }),
        success: function () {
          console.log($(this));
          if (action == "unlike") {
            $(this).removeClass("text-danger").addClass("text-dark");
          } else {
            $(this).removeClass("text-dark").addClass("text-danger");
          }
        },
      });
    },

    setview: function (id) {
      this.fetchMedias();
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
    infiniteHandler: function ($state) {
      this.fetchMedias()
        .then((response) => {
          if (response.data.items.length > 0) {
            this.page += 1;
            this.user = response.data.user;
            this.images.push(...response.data.items);
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
    changeType() {
      this.page = 1;
      this.images = [];
      this.feedId += 1;
    },
  },
};
</script>