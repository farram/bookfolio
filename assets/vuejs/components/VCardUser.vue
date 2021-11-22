<template>
  <v-card>
    <v-list>
      <v-list-item>
        <a :href="user.user.url" target="_blank">
          <v-list-item-avatar class="mr-2">
            <v-img :src="user.user.avatar" :alt="user.user.fullname"></v-img>
          </v-list-item-avatar>
        </a>
        <v-list-item-content class="text-left">
          <v-list-item-title>
            <a :href="user.user.url" target="_blank">
              <h5 class="d-inline-block m-0">
                {{ user.user.fullname }}
              </h5>
            </a>
          </v-list-item-title>
          <v-list-item-subtitle class="text-muted">
            {{ user.user.profession }}<br />
            {{ user.user.location }}
          </v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <template v-if="user.user.uuid != user.user.uuid">
            <div class="dropdown float-right" v-if="linkSuggest">
              <a
                href="#"
                class="dropdown-toggle arrow-none card-drop"
                data-toggle="dropdown"
                aria-expanded="false"
              >
                <i class="mdi mdi-dots-horizontal"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right">
                <link-no-suggest
                  :user="user"
                  :index="index"
                  :users="users"
                ></link-no-suggest>
              </div>
            </div>
            <button-follow :user="user"></button-follow>
          </template>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-card>
</template>  
<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import Swal from "sweetalert2";
import ButtonFollow from "./ButtonFollow.vue";
import LinkNoSuggest from "./LinkNoSuggest.vue";

export default {
  components: {
    "button-follow": ButtonFollow,
    "link-no-suggest": LinkNoSuggest,
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    linkSuggest: {
      type: Boolean,
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
};
</script>