<template>
  <button
    class="btn waves-effect waves-light ml-1 btn btn-light"
    v-on:click="neverFollow(user, index)"
  >
    Ne plus me suggérer
  </button>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  props: {
    user: {
      type: Object,
      required: true,
    },
    index: {
      type: Number,
      required: true,
    },
    users: {
      type: Array,
      required: true,
    },
  },
  data: function () {
    return {
      //followed: this.user.identity.followed,
    };
  },
  methods: {
    neverFollow: function (user, index) {
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
            user.identity.fullname,
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
                Routing.generate("annuaire_notsuggested", {
                  uuid: user.identity.uuid,
                }),
                {
                  params: {
                    uuid: user.identity.uuid,
                  },
                }
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