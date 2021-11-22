<template>
  <v-app>
    <div
      class="timeline-item"
      v-for="(notification, $index) in notifications"
      :key="$index"
    >
      <div class="timeline-line w-40px"></div>
      <div class="timeline-icon symbol symbol-circle symbol-40px me-4">
        <div class="symbol-label bg-light">
          <span class="svg-icon svg-icon-1 svg-icon-gray-500">
            <template v-if="notification.type == 'like'">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewbox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M18.3721 4.65439C17.6415 4.23815 16.8052 4 15.9142 4C14.3444 4 12.9339 4.73924 12.003 5.89633C11.0657 4.73913 9.66 4 8.08626 4C7.19611 4 6.35789 4.23746 5.62804 4.65439C4.06148 5.54462 3 7.26056 3 9.24232C3 9.81001 3.08941 10.3491 3.25153 10.8593C4.12155 14.9013 9.69287 20 12.0034 20C14.2502 20 19.875 14.9013 20.7488 10.8593C20.9109 10.3491 21 9.81001 21 9.24232C21.0007 7.26056 19.9383 5.54462 18.3721 4.65439Z"
                  fill="black"
                />
              </svg>
            </template>
            <template v-if="notification.type == 'comment'">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewbox="0 0 24 24"
                fill="none"
              >
                <path
                  opacity="0.3"
                  d="M2 4V16C2 16.6 2.4 17 3 17H13L16.6 20.6C17.1 21.1 18 20.8 18 20V17H21C21.6 17 22 16.6 22 16V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4Z"
                  fill="black"
                />
                <path
                  d="M18 9H6C5.4 9 5 8.6 5 8C5 7.4 5.4 7 6 7H18C18.6 7 19 7.4 19 8C19 8.6 18.6 9 18 9ZM16 12C16 11.4 15.6 11 15 11H6C5.4 11 5 11.4 5 12C5 12.6 5.4 13 6 13H15C15.6 13 16 12.6 16 12Z"
                  fill="black"
                />
              </svg>
            </template>
            <template v-if="notification.type == 'follow'">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewbox="0 0 24 24"
                fill="none"
              >
                <path
                  d="M16.0173 9H15.3945C14.2833 9 13.263 9.61425 12.7431 10.5963L12.154 11.7091C12.0645 11.8781 12.1072 12.0868 12.2559 12.2071L12.6402 12.5183C13.2631 13.0225 13.7556 13.6691 14.0764 14.4035L14.2321 14.7601C14.2957 14.9058 14.4396 15 14.5987 15H18.6747C19.7297 15 20.4057 13.8774 19.912 12.945L18.6686 10.5963C18.1487 9.61425 17.1285 9 16.0173 9Z"
                  fill="black"
                />
                <rect
                  opacity="0.3"
                  x="14"
                  y="4"
                  width="4"
                  height="4"
                  rx="2"
                  fill="black"
                />
                <path
                  d="M4.65486 14.8559C5.40389 13.1224 7.11161 12 9 12C10.8884 12 12.5961 13.1224 13.3451 14.8559L14.793 18.2067C15.3636 19.5271 14.3955 21 12.9571 21H5.04292C3.60453 21 2.63644 19.5271 3.20698 18.2067L4.65486 14.8559Z"
                  fill="black"
                />
                <rect
                  opacity="0.3"
                  x="6"
                  y="5"
                  width="6"
                  height="6"
                  rx="3"
                  fill="black"
                />
              </svg>
            </template>
          </span>
        </div>
      </div>
      <div class="timeline-content mb-10 mt-n1">
        <div class="pe-3 mb-5">
          <div class="fs-5 fw-bold mb-2">
            <a
              :href="notification.user.identity.url"
              target="_blank"
              class="text-dark"
            >
              {{ notification.user.identity.fullname }}</a
            >
            {{ notification.text }}
          </div>
          <div class="d-flex align-items-center mt-1 fs-6">
            <div class="text-muted me-2 fs-7">
              {{ notification.date }}
            </div>
            <a
              class="symbol symbol-circle symbol-25px"
              :href="notification.user.identity.url"
              target="_blank"
            >
              <img
                :src="notification.user.identity.avatar"
                :alt="notification.user.identity.fullname"
              />
            </a>
          </div>
        </div>
        <template v-if="notification.type != 'follow'">
          <div class="overflow-auto pb-5">
            <div
              class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-7"
            >
              <div class="overlay me-10">
                <div class="overlay-wrapper">
                  <a :href="notification.media.url">
                    <v-img
                      class="rounded mt-2"
                      max-width="200"
                      :lazy-src="notification.media.thumb"
                      :alt="notification.media.title"
                      :src="notification.media.thumb"
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
                  </a>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
    <infinite-loading
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="no-more"></div>
      <div slot="no-results">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <h4 class="text-center">
                  Retrouvez ici toutes vos prochaines notifications
                </h4>
              </div>
            </div>
          </div>
        </div>
      </div>
    </infinite-loading>
  </v-app>
</template>

<script>
import InfiniteLoading from "vue-infinite-loading";
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import ButtonFollow from "./ButtonFollow.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
  },
  data: function () {
    return {
      page: 1,
      notifications: [],
      infiniteId: +new Date(),
      checkboxComments: true,
      checkboxLikes: true,
      checkboxFollows: true,
    };
  },
  methods: {
    changeFilter() {
      this.page = 1;
      this.notifications = [];
      this.infiniteId += 1;
    },
    fetchMedias: function () {
      let url = Routing.generate("dashboard_api_notifications", {
        page: this.page,
        showComments: "" + this.checkboxComments + "",
        showLikes: "" + this.checkboxLikes + "",
        showFollows: "" + this.checkboxFollows + "",
      });
      return this.$http.get(url);
    },
    infiniteHandler: function ($state) {
      this.fetchMedias()
        .then((response) => {
          if (response.data.notifications.length > 0) {
            this.page += 1;
            this.notifications.push(...response.data.notifications);
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
  },
};
</script>