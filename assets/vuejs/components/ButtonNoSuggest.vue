<template>
  <button
    class="btn btn-sm btn-light btn-hover-rise ms-5"
    v-on:click="neverSuggest(user, index)"
  >
    Ne plus me suggérer
  </button>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";

export default {
  props: {
    index: {
      type: Number,
      required: true,
    },
    user: {
      type: Object,
      required: true,
    },
    users: {
      type: Array,
      required: true,
    },
  },
  methods: {
    neverSuggest: function (user, index) {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: "btn btn-light mr-2",
        },
        buttonsStyling: false,
      });
      swalWithBootstrapButtons
        .fire({
          title: "En êtes-vous sûr ?",
          text:
            "Nous ne vous suggérerons plus le book de " +
            user.identity.fullname +
            ".",
          icon: "warning",
          reverseButtons: true,
          showCancelButton: true,
          confirmButtonColor: "#36b5aa",
          cancelButtonColor: "#ccc",
          cancelButtonText: "Annuler",
          confirmButtonText: "Oui, ne plus suggérer",
          footer: "<small>Cette action est irréversible.</small>",
        })
        .then((result) => {
          if (result.value) {
            this.$http
              .get(
                Routing.generate("never_suggest_book", {
                  uuid: user.identity.uuid,
                })
              )
              .then(
                (response) => {
                  swalWithBootstrapButtons.fire(
                    "C’est noté !",
                    "Nous ne vous suggérerons plus le book de " +
                      user.identity.fullname,
                    "success"
                  );
                  this.users.splice(index, 1);
                },
                (response) => {}
              );
          }
        });
    },
  },
};
</script>