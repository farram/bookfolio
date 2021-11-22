<template>
  <div>
    <vue-instant-loading-spinner ref="Spinner"></vue-instant-loading-spinner>
    <button
      class="btn btn-sm btn-hover-rise"
      :class="followed ? 'btn-light' : 'btn-light-primary'"
      v-bind="{ isLoading, status }"
      :disabled="isLoading"
      @click="
        followed
          ? unfollow(user.identity.username, user.identity.fullname)
          : follow(user.identity.username, user.identity.fullname)
      "
    >
      <template v-if="followed">
        <span class="svg-icon svg-icon-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
          >
            <path
              opacity="0.3"
              d="M10 18C9.7 18 9.5 17.9 9.3 17.7L2.3 10.7C1.9 10.3 1.9 9.7 2.3 9.3C2.7 8.9 3.29999 8.9 3.69999 9.3L10.7 16.3C11.1 16.7 11.1 17.3 10.7 17.7C10.5 17.9 10.3 18 10 18Z"
              fill="black"
            />
            <path
              d="M10 18C9.7 18 9.5 17.9 9.3 17.7C8.9 17.3 8.9 16.7 9.3 16.3L20.3 5.3C20.7 4.9 21.3 4.9 21.7 5.3C22.1 5.7 22.1 6.30002 21.7 6.70002L10.7 17.7C10.5 17.9 10.3 18 10 18Z"
              fill="black"
            />
          </svg>
        </span>
      </template>
      <template v-else>
        <span class="svg-icon svg-icon-3">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
          >
            <rect
              opacity="0.5"
              x="11.364"
              y="20.364"
              width="16"
              height="2"
              rx="1"
              transform="rotate(-90 11.364 20.364)"
              fill="black"
            />
            <rect
              x="4.36396"
              y="11.364"
              width="16"
              height="2"
              rx="1"
              fill="black"
            />
          </svg>
        </span>
      </template>

      {{ followed ? "Abonné" : "S’abonner" }}
    </button>
  </div>
  <!-- <vue-button-spinner
      type="button"
      :class="followed ? 'btn-light-primary' : 'btn-light-primary'"
      class="btn btn-sm btn-hover-rise"
      @click.native="
        followed
          ? unfollow(user.identity.username, user.identity.fullname)
          : follow(user.identity.username, user.identity.fullname)
      "
      v-bind="{ isLoading, status }"
      :disabled="isLoading"
    >
      {{ followed ? "Se désabonner" : "S’abonner" }}
    </vue-button-spinner> -->
</template>  
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import VueButtonSpinner from "vue-button-spinner";
import VueInstantLoadingSpinner from "vue-instant-loading-spinner";

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      followed: this.user.identity.followed,
      isLoading: false,
      status: "",
    };
  },
  components: {
    VueButtonSpinner,
    VueInstantLoadingSpinner,
  },
  methods: {
    // test: function () {
    //   this.$refs.Spinner.show();
    //   setTimeout(
    //     function () {
    //       this.$refs.Spinner.hide();
    //     }.bind(this),
    //     2000
    //   );
    // },
    follow: function (username, fullname) {
      this.$refs.Spinner.show();
      // this.isLoading = true;
      // this.statusMessage = "";
      this.$http
        .get(Routing.generate("add_follow", { username: username }))
        .then(
          (response) => {
            Swal.fire(
              "Nice !",
              "Vous commencez à suivre " + fullname,
              "success"
            );
            this.followed = true;
            // this.isLoading = false;
            // this.status = Math.random() >= 0.5;
            // this.statusMessage = this.status;
            this.$refs.Spinner.hide();
            setTimeout(() => {
              this.status = "";
            }, 2000);
          },
          (response) => {}
        );
    },
    unfollow: function (username, fullname) {
      this.$refs.Spinner.show();
      // this.isLoading = true;
      // this.statusMessage = "";
      this.$http
        .get(Routing.generate("remove_follow", { username: username }))
        .then(
          (response) => {
            Swal.fire("Oh :(", "Vous ne suivez plus " + fullname, "success");
            this.followed = false;
            // this.isLoading = false;
            // this.status = Math.random() >= 0.5;
            // this.statusMessage = this.status;
            this.$refs.Spinner.hide();
            setTimeout(() => {
              this.status = "";
            }, 2000);
          },
          (response) => {}
        );
    },
  },
};
</script>
<style scoped>
.vue-btn[data-v-bb7979c4] {
  height: auto !important;
  font-size: small !important;
  padding: 0.45rem 0.9rem !important;
  background-color: inherit !important;
}
</style>