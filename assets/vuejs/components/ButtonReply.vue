<template>
  <div>
    <button
      data-toggle="collapse"
      v-bind:data-target="'#openReply' + comment.id"
      class="text-muted font-13 d-inline-block mt-2"
    >
      <i class="mdi mdi-reply"></i> Répondre
    </button>
    <div class="collapse" v-bind:id="'openReply' + comment.id">
      <div class="media mt-2">
        <div class="media-body">
          <input
            type="text"
            class="form-control form-control-light border-0 form-control-sm"
            placeholder="Votre réponse..."
          />
          <input
            type="submit"
            value="Répondre"
            class="mt-2 btn waves-effect waves-light btn-xs btn-primary"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

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
    openReply: function (commentId) {
      let element = $(this);
      $("#slidepanel" + commentId).slideToggle(300);
      $(this).toggleClass("active");
    },
  },
};
</script>