<template>
  <button class="card-drop">
    <i
      class="mdi"
      :class="
        notify ? 'text-blue mdi-bell-check' : 'text-muted mdi-bell-outline'
      "
      v-on:click="
        notify
          ? removenotify(user.identity.username, user.identity.fullname)
          : addnotify(user.identity.username, user.identity.fullname)
      "
    ></i>
  </button>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {
      notify: this.user.identity.notify,
    };
  },
  methods: {
    addnotify: function (username, fullname) {
      this.$http
        .get(Routing.generate("relation_add_notify", { username: username }))
        .then(
          (response) => {
            Swal.fire(
              "Nice !",
              "Vous avez activer les notifications de " + fullname,
              "success"
            );
            this.notify = true;
          },
          (response) => {}
        );
    },
    removenotify: function (username, fullname) {
      this.$http
        .get(Routing.generate("relation_remove_notify", { username: username }))
        .then(
          (response) => {
            Swal.fire(
              "Oh :(",
              "Vous avez désactiver les notifications de " + fullname,
              "success"
            );
            this.notify = false;
          },
          (response) => {}
        );
    },
  },
};
</script>