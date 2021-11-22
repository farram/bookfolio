<template>
  <button
    v-on:click="deleteThis(comment.id)"
    class="text-danger font-13 d-inline-block mt-2 ml-2"
  >
    <i class="mdi mdi-trash-can-outline"></i> Supprimer
  </button>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";

export default {
  props: {
    comment: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {};
  },
  methods: {
    deleteThis: function (commentId) {
      Swal.fire("Nice !", "Commentaire supprimé", "success");
    },
  },
};
</script>