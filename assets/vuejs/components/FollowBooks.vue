<template>
  <v-row>
    <div class="col-12">
      <div
        class="notice d-flex bg-light-warning rounded border-warning border border-dashed d-flex flex-center flex-column py-10 px-10 px-lg-20 mb-10"
      >
        <span class="svg-icon svg-icon-5tx svg-icon-warning mb-5">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
          >
            <rect
              opacity="0.3"
              x="2"
              y="2"
              width="20"
              height="20"
              rx="10"
              fill="black"
            ></rect>
            <rect
              x="11"
              y="14"
              width="7"
              height="2"
              rx="1"
              transform="rotate(-90 11 14)"
              fill="black"
            ></rect>
            <rect
              x="11"
              y="17"
              width="2"
              height="2"
              rx="1"
              transform="rotate(-90 11 17)"
              fill="black"
            ></rect>
          </svg>
        </span>
        <div class="text-center text-dark">
          <h1 class="fw-bolder mb-4">Aucune publication</h1>

          <div class="mb-9">
            N'hésitez pas à suivre le travail des autres artistes 😃<br />
            Les publications des artistes auxquels vous êtes abonnés
            s'afficheront ici.
          </div>
        </div>
      </div>

      <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center my-1">
          <div>
            <h3 class="fw-bolder me-5 my-1 text-left">
              Suggestions des books à suivre
            </h3>
            <span class="text-muted mt-1 fw-bold fs-7"
              >Voici quelques suggestions des books susceptibles de vous
              intéressés.</span
            >
          </div>
        </div>
      </div>
    </div>
    <div v-for="(user, index) in users" :key="index" class="col-lg-6 col-xl-6">
      <div class="card mb-7">
        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
          <div class="symbol symbol-75px mb-5">
            <a
              :href="user.identity.url"
              target="_blank"
              class="symbol symbol-75px"
            >
              <template v-if="user.identity.certified">
                <v-badge
                  bordered
                  title="Book certifié"
                  bottom
                  class="symbol symbol-75px"
                  offset-x="20"
                  offset-y="20"
                  color="blue accent-4 small"
                  icon="mdi-check"
                >
                  <img :src="user.identity.avatar" class="" alt="" />
                </v-badge>
              </template>
              <template v-else>
                <img :src="user.identity.avatar" class="" alt="" />
              </template>
            </a>
          </div>
          <a
            :href="user.identity.url"
            target="_blank"
            class="fs-4 text-gray-800 text-hover-primary fw-bolder mb-0"
            >{{ user.identity.fullname }}</a
          >
          <div class="fw-bold text-center text-gray-400 mb-6">
            {{ user.identity.profession }} <br />
            {{ user.identity.location }}<br />
            <small
              >{{ user.followers.length }}
              <template v-if="user.followers.length > 1">abonnés</template>
              <template v-else>abonné</template>
            </small>
          </div>
          <div class="d-flex flex-center flex-wrap mb-5">
            <div
              class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
            >
              <div class="fs-6 fw-bolder text-gray-700">
                {{ user.identity.countFolders }}
              </div>
              <div class="fw-bold text-gray-400">Galeries</div>
            </div>
            <div
              class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
            >
              <div class="fs-6 fw-bolder text-gray-700">
                {{ user.identity.countImages }}
              </div>
              <div class="fw-bold text-gray-400">Photos</div>
            </div>
            <div
              class="border border-gray-300 border-dashed text-center rounded min-w-80px py-3 px-4 mx-2 mb-3"
            >
              <div class="fs-6 fw-bolder text-gray-700">
                {{ user.identity.countVideos }}
              </div>
              <div class="fw-bold text-gray-400">Vidéos</div>
            </div>
          </div>
          <div class="d-flex flex-center flex-wrap">
            <button-follow :user="user"></button-follow>
            <button-no-suggest
              :user="user"
              :index="index"
              :users="users"
            ></button-no-suggest>
          </div>
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
import ButtonNoSuggest from "./ButtonNoSuggest.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
    "button-no-suggest": ButtonNoSuggest,
  },
  data: function () {
    return {
      users: [],
      page: 1,
    };
  },
  mounted() {
    this.$http
      .get(
        Routing.generate("short_suggest_book_to_follow", {
          page: this.page,
        })
      )
      .then(function (response) {
        this.users = response.data;
      });
  },
};
</script>