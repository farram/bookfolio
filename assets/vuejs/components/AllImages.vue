<template>
  <v-app>
    <draggable
      ref="draggable"
      v-model="images"
      class="row"
      ghost-class="ghost"
      :options="options"
      @start="onDraggableStart"
      @update="onDraggableUpdate"
    >
      <v-col
        v-for="(image, $index) in images"
        :key="$index"
        class="mb-5"
        cols="3"
      >
        <v-card
          elevation="0"
          class="card-body d-flex justify-content-center text-center flex-column p-8 mx-auto"
          max-width="400"
        >
          <v-img
            aspect-ratio="1.4"
            class="white--text align-end grey lighten-2 img-fluid card-rounded"
            :lazy-src="image.thumb"
            v-img="{ src: image.path, title: image.title }"
            :alt="image.title"
            :src="image.thumb"
          >
            <template v-slot:placeholder>
              <v-row class="fill-height ma-0" align="center" justify="center">
                <v-progress-circular
                  indeterminate
                  color="grey lighten-5"
                ></v-progress-circular>
              </v-row>
            </template>
          </v-img>
          <v-card-title>
            <p class="text-dark">{{ image.title }}</p>
          </v-card-title>
          <v-card-subtitle class="pb-0 text-muted">
            {{ image.gallery }}
          </v-card-subtitle>
          <v-card-text class="text--primary text-muted">
            <div class="text-light-white">Publiée {{ image.date }}</div>
            <div class="text-light-white">Vue : {{ image.views }}</div>
          </v-card-text>
          <v-card-actions
            class="text-center d-flex justify-content-center flex-stack"
          >
            <a
              :href="image.urlEdit"
              class="btn btn-sm btn-light-primary btn-hover-rise me-5"
              >Modifier</a
            >
            <button
              @click="removeAt(image.id, $index)"
              class="btn btn-sm btn-light-danger btn-hover-rise"
            >
              Supprimer
            </button>
          </v-card-actions>
        </v-card>
      </v-col>
    </draggable>

    <infinite-loading
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="no-more"></div>
      <div slot="no-results">
        <v-container>
          <v-row>
            <v-col>
              <div class="alert alert-warning">
                Aucune publication pour l'instant.
              </div>
            </v-col>
          </v-row>
        </v-container>
      </div>
    </infinite-loading>
  </v-app>
</template>


<script>
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);
import draggable from "vuedraggable";

export default {
  components: {
    draggable,
  },
  data: function () {
    return {
      page: 1,
      images: [],
      infiniteId: +new Date(),
      options: {
        disabled: false,
      },
    };
  },
  methods: {
    fetchMedias: function () {
      let url = Routing.generate("image_all_json", {
        page: this.page,
      });
      return this.$http.get(url, {
        params: {
          page: this.page,
          type: this.newsType,
          displayBy: this.defaultOrder,
        },
      });
    },
    infiniteHandler: function ($state) {
      this.fetchMedias()
        .then((response) => {
          if (response.data.items.length > 0) {
            this.page += 1;
            this.images.push(...response.data.items);
            if (this.page > response.data.totalPages) {
              $state.complete();
            }
            $state.loaded();
          } else {
            $state.complete();
          }
        })
        .catch((e) => console.log(e));
    },
    onDraggableStart() {
      this.currentOrder = this.$refs.draggable._sortable.toArray();
    },
    onDraggableUpdate(event) {
      this.options.disabled = true;
      let order = this.images;
      this.$http
        .post(Routing.generate("images_order"), { list: order })
        .then(async (data) => {})
        .catch((error) => {
          let moved = this.images.splice(event.moved.newIndex, 1);
          this.images.splice(event.moved.oldIndex, 0, moved[0]);
        })
        .finally(() => (this.options.disabled = false));
    },

    removeAt: function (imageId, index) {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: "btn btn-light mr-2",
        },
        buttonsStyling: false,
      });
      swalWithBootstrapButtons
        .fire({
          title: "Supprimer la photo",
          text: "Êtes-vous sûr de vouloir supprimer cette photo ?",
          icon: "warning",
          reverseButtons: true,
          showCancelButton: true,
          confirmButtonColor: "#36b5aa",
          cancelButtonColor: "#ccc",
          cancelButtonText: "Annuler",
          confirmButtonText: "Supprimer",
          footer:
            "<small>Tous les commentaires, likes et vues seront immédiatement supprimés. Cette action est irréversible.</small>",
        })
        .then((result) => {
          if (result.value) {
            this.$http
              .get(Routing.generate("delete_image", { id: imageId }))
              .then(
                (response) => {
                  swalWithBootstrapButtons.fire(
                    "Ok. C'est fait !",
                    "La photo a bien été supprimée.",
                    "success"
                  );
                  this.images.splice(index, 1);
                },
                (response) => {}
              );
          }
        });
    },
  },
};
</script>