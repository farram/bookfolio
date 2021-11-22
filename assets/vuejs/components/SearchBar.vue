<template>
  <div
    id="kt_header_search"
    class="d-flex align-items-stretch"
    data-kt-search-keypress="true"
    data-kt-search-min-length="2"
    data-kt-search-enter="enter"
    data-kt-search-layout="menu"
    data-kt-menu-trigger="auto"
    data-kt-menu-overflow="false"
    data-kt-menu-permanent="true"
    data-kt-menu-placement="bottom-end"
  >
    <div
      class="d-flex align-items-center"
      data-kt-search-element="toggle"
      id="kt_header_search_toggle"
    >
      <div
        class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px"
      >
        <span class="svg-icon svg-icon-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewbox="0 0 24 24"
            fill="none"
          >
            <rect
              opacity="0.5"
              x="17.0365"
              y="15.1223"
              width="8.15546"
              height="2"
              rx="1"
              transform="rotate(45 17.0365 15.1223)"
              fill="black"
            />
            <path
              d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
              fill="black"
            />
          </svg>
        </span>
      </div>
    </div>
    <div
      data-kt-search-element="content"
      class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px"
    >
      <div data-kt-search-element="wrapper">
        <div class="w-100 position-relative mb-3">
          <span
            class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 translate-middle-y ms-0"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewbox="0 0 24 24"
              fill="none"
            >
              <rect
                opacity="0.5"
                x="17.0365"
                y="15.1223"
                width="8.15546"
                height="2"
                rx="1"
                transform="rotate(45 17.0365 15.1223)"
                fill="black"
              />
              <path
                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                fill="black"
              />
            </svg>
          </span>
          <input
            type="text"
            class="form-control form-control-flush ps-10"
            name="search"
            value=""
            placeholder="Ex : Julie"
            data-kt-search-element="input"
            v-model="query"
          />
          <span class="position-absolute top-50 end-0 translate-middle-y">
            <div
              class="btn btn-icon w-90px btn-sm btn-active-color-primary me-1"
            >
              <button class="btn btn-primary btn-sm" @click="searchData">
                Rechercher
              </button>
            </div>
          </span>
        </div>
        <div class="separator border-gray-200 mb-6"></div>
        <template v-if="loading">
          <div class="row">
            <div class="col-12" v-for="n in 4" :key="n">
              <v-skeleton-loader
                :loading="loading"
                height="60"
                type="list-item-avatar-two-line"
              >
              </v-skeleton-loader>
            </div>
          </div>
        </template>
        <template v-else>
          <div>
            <div class="scroll-y mh-200px mh-lg-350px">
              <a
                v-for="(user, $index) in users"
                :key="$index"
                :href="user.identity.url"
                target="_blank"
                class="d-flex text-dark text-hover-primary align-items-center mb-5"
              >
                <div class="symbol symbol-40px me-4">
                  <img :src="user.identity.avatar" alt="" />
                </div>
                <div class="d-flex flex-column justify-content-start fw-bold">
                  <span class="fs-6 fw-bold">{{ user.identity.fullname }}</span>
                  <span class="fs-7 fw-bold text-muted">{{
                    user.identity.profession
                  }}</span>
                  <span class="fs-7 fw-bold text-muted">{{
                    user.identity.location
                  }}</span>
                </div>
              </a>
            </div>
          </div>
          <div
            data-kt-search-element="empty"
            class="text-center"
            :class="display"
          >
            <div class="pt-10 pb-10">
              <span class="svg-icon svg-icon-4x opacity-50">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewbox="0 0 24 24"
                  fill="none"
                >
                  <path
                    opacity="0.3"
                    d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z"
                    fill="black"
                  />
                  <path
                    d="M20 8L14 2V6C14 7.10457 14.8954 8 16 8H20Z"
                    fill="black"
                  />
                  <rect
                    x="13.6993"
                    y="13.6656"
                    width="4.42828"
                    height="1.73089"
                    rx="0.865447"
                    transform="rotate(45 13.6993 13.6656)"
                    fill="black"
                  />
                  <path
                    d="M15 12C15 14.2 13.2 16 11 16C8.8 16 7 14.2 7 12C7 9.8 8.8 8 11 8C13.2 8 15 9.8 15 12ZM11 9.6C9.68 9.6 8.6 10.68 8.6 12C8.6 13.32 9.68 14.4 11 14.4C12.32 14.4 13.4 13.32 13.4 12C13.4 10.68 12.32 9.6 11 9.6Z"
                    fill="black"
                  />
                </svg>
              </span>
            </div>
            <div class="pb-15 fw-bold">
              <h3 class="text-gray-600 fs-5 mb-2">Aucun résultat</h3>
              <div class="text-muted fs-7">
                Merci d'essayer avec d'autres critères de recherche
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>
      
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
import { Skeleton } from "vue-loading-skeleton";
Routing.setRoutingData(routes);
export default {
  data: function () {
    return {
      users: [],
      query: "",
      display: "d-block",
      loading: false,
    };
  },
  //   watch: {
  //     query: function () {
  //       this.loading = true;
  //     },
  //   },
  methods: {
    async searchData() {
      await this.$http
        .post(
          Routing.generate("search_post_bar", {
            before: () => {
              this.loading = true;
            },
            query: this.query,
          })
        )
        .then(function (response) {
          this.loading = false;

          if (response.data.length > 0) {
            this.users = response.data;
            this.display = "d-none";
          } else {
            this.users = "";
            this.display = "d-block";
          }
        });
    },
  },
};
</script>