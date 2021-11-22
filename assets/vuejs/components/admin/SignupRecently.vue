<template>
  <v-app>
    <v-text-field
      v-model="search"
      append-icon="mdi-magnify"
      label="Filtrer"
      single-line
      hide-details
      class="mt-0 pt-0 mb-4"
    ></v-text-field>
    <v-data-table
      :headers="headers"
      :items="users"
      :items-per-page="45"
      class="elevation-1"
      :sort-by.sync="sortBy"
      :sort-desc.sync="sortDesc"
      :search="search"
    >
      <template v-slot:item.id="{ item }">
        <small class="text-secondary">{{ item.id }}</small>
      </template>
      <template v-slot:item.fullname="{ item }">
        <a :href="item.link" class="text-secondary" target="_blank">
          <v-avatar size="32" class="mt-2 mb-2 mr-2">
            <img :src="item.avatar" :alt="item.fullname" />
          </v-avatar>
          {{ item.fullname }}
        </a>
      </template>
      <template v-slot:item.status="{ item }">
        <span class="text-secondary">{{ item.status }}</span>
      </template>
      <template v-slot:item.count_photos="{ item }">
        <span
          v-if="item.count_photos > 0"
          class="badge bg-soft-success text-success"
        >
          {{ item.count_photos }}
        </span>
        <span v-else class="badge bg-soft-danger text-danger"> 0 </span>
      </template>
      <template v-slot:item.created_date="{ item }">
        <span class="text-secondary">{{ item.created_date }}</span>
      </template>
    </v-data-table>
  </v-app>
</template>


<script>
const routes = require("../../../../public/js/fos_js_routes.json");
import Routing from "../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

export default {
  data: function () {
    return {
      users: [],
      sortBy: "id",
      sortDesc: true,
      search: "",
      headers: [
        {
          text: "id",
          align: "start",
          sortable: true,
          value: "id",
          width: 60,
        },
        { text: "Informations", value: "fullname" },
        { text: "Type de compte", value: "status" },
        { text: "Photos", value: "count_photos" },
        { text: "Date d'inscription", value: "created_date" },
      ],
    };
  },
  created() {
    this.getDatas();
  },

  methods: {
    getDatas: function () {
      this.$http
        .get(Routing.generate("json_admin_signup_recently"))
        .then(function (response) {
          this.users = response.data.items;
        });
    },
  },
};
</script>