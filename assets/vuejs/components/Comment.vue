<template>
  <div class="media mt-3 w-100">
    <a href="#" target="_blank">
      <img
        class="mr-2 rounded-circle"
        :src="comment.user.avatar"
        :alt="comment.user.fullname"
        height="32"
      />
    </a>
    <div class="media-body">
      <h5 class="mt-0 mb-1">
        <a href="#" target="_blank" class="text-reset">
          {{ comment.user.fullname }}
        </a>
        <small class="text-muted float-right">
          {{ comment.date }}
        </small>
      </h5>
      {{ comment.content }}
      <br />
      <button-reply :comment="comment"></button-reply>
      <!--<button-trash :comment="comment"></button-trash>-->

      <template v-if="comment.child.length > 0">
        <div
          v-for="(childcomment, $cc) in comment.child"
          :key="$cc"
          class="w-100"
        >
          <childcomment :childcomment="childcomment"></childcomment>
        </div>
      </template>
    </div>
  </div>
</template>
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ButtonReply from "./ButtonReply.vue";
import ButtonTrash from "./ButtonTrash.vue";
import ChildComment from "./ChildComment.vue";

export default {
  components: {
    childcomment: ChildComment,
    "button-reply": ButtonReply,
    "button-trash": ButtonTrash,
  },
  props: {
    comment: {
      type: Object,
      required: true,
    },
  },
  data: function () {
    return {};
  },
  methods: {},
};
</script>