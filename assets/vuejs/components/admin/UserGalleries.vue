<template>
  <v-app>
    <draggable
      ref="draggable"
      v-model="items"
      ghost-class="ghost"
      :options="options"
      v-bind="dragOptions"
      @start="onDraggableStart"
      @update="onDraggableUpdate"
    >
      <transition-group type="transition" name="flip-list">
        <div
          v-for="(item, n) in items"
          :key="item.gallery.id"
          :data-id="item.gallery.id"
        >
          <div class="card border project-box">
            <div class="card-body">
              <div class="media">
                <div class="media-body">
                  <h4 class="mt-0 mb-0">
                    {{ item.gallery.title }}
                    <template v-if="item.gallery.countImages > 0">
                      ({{ item.gallery.countImages }})
                    </template>
                  </h4>

                  <p class="text-muted mb-1">
                    <i class="mdi mdi-time"></i>
                    <small> {{ item.gallery.createdAt }} </small>
                  </p>

                  <template v-if="item.gallery.isProtect === true">
                    <div class="badge bg-soft-danger text-danger">
                      <i class="fe-lock"></i>
                      {{ item.gallery.isProtectText }}
                    </div>
                  </template>
                  <template v-else>
                    <div class="badge bg-soft-success text-success">
                      {{ item.gallery.isProtectText }}
                    </div>
                  </template>

                  <template v-if="item.gallery.description">
                    <p class="text-muted mb-0 mt-3 sp-line-2">
                      {{ item.gallery.description }}
                    </p>
                  </template>
                  <template v-if="item.gallery.images.length != 0">
                    <div class="row mt-2">
                      <div
                        v-for="(image, $key) in item.gallery.images"
                        :key="$key"
                        class="col-sm-6 col-xl-4"
                      >
                        <div class="gal-box">
                          <v-img
                            v-on:click="setview(image.id)"
                            class="grey lighten-2"
                            :lazy-src="image.thumb"
                            v-img="{
                              src: image.path,
                              title: image.title,
                            }"
                            :alt="image.title"
                            :src="image.thumb"
                          >
                            <template v-slot:placeholder>
                              <v-row
                                class="fill-height ma-0"
                                align="center"
                                justify="center"
                              >
                                <v-progress-circular
                                  indeterminate
                                  color="grey lighten-5"
                                ></v-progress-circular>
                              </v-row>
                            </template>
                          </v-img>

                          <div class="gall-info text-center">
                            <small class="text-muted ms-1">{{
                              image.createdAt
                            }}</small>
                            <div class="stats mt-2">
                              <ul class="list-unstyled">
                                <li class="d-inline-block">
                                  <span class="text-muted">
                                    <i class="fe-eye"></i>
                                    {{ image.stats.view }}
                                  </span>
                                </li>
                                <li class="d-inline-block ml-2 mr-2">
                                  <span class="text-muted">
                                    <i class="fe-heart"></i>
                                    {{ image.stats.view }}
                                  </span>
                                </li>
                                <li class="d-inline-block">
                                  <span class="text-muted">
                                    <i class="fe-message-square"></i>
                                    {{ image.stats.comment }}
                                  </span>
                                </li>
                              </ul>
                            </div>
                            <div class="actions mb-2">
                              <a
                                :href="image.link"
                                class="mt-2 btn btn-light waves-effect text-dark"
                                >Accéder</a
                              >
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                  <template v-else> </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </transition-group>
    </draggable>
    <infinite-loading
      spinner="waveDots"
      :identifier="infiniteId"
      @infinite="infiniteHandler"
    >
      <div slot="no-more"></div>
      <div slot="no-results">
        <div class="alert alert-warning text-center">
          C'est tout pour le moment :)
        </div>
      </div>
    </infinite-loading>
  </v-app>
</template>

<script>
const routes = require("../../../../public/js/fos_js_routes.json");
import Routing from "../../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
Routing.setRoutingData(routes);

import draggable from "vuedraggable";
import Swal from "sweetalert2";

export default {
  display: "Transition",
  components: {
    draggable,
  },
  data: function () {
    return {
      options: {
        disabled: false,
      },
      page: 1,
      items: [],
      infiniteId: +new Date(),
    };
  },
  methods: {
    onDraggableStart() {
      //this.currentOrder = this.$refs.draggable._sortable.toArray();
    },
    onDraggableUpdate(oldIndex, newIndex) {
      this.options.disabled = true;

      var order = this.$refs.draggable._sortable.toArray({
        attribute: "data-id",
      });

      this.$http
        .post(Routing.generate("order_galleries", { list: order }))
        .then(function (response) {})
        .catch((error) => {})
        .finally(() => (this.options.disabled = false));
    },
    removeAt: function (folderId, index) {
      const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: "btn btn-light mr-2",
        },
        buttonsStyling: false,
      });
      swalWithBootstrapButtons
        .fire({
          title: "Supprimer la galerie",
          text:
            "Bien qu'elle soit vide, souhaitez-vous vraiment supprimer cette galerie ?",
          icon: "warning",
          reverseButtons: true,
          showCancelButton: true,
          confirmButtonColor: "#36b5aa",
          cancelButtonColor: "#ccc",
          cancelButtonText: "Annuler",
          confirmButtonText: "Supprimer",
          footer:
            "<small>Elle sera immédiatement supprimée. Cette action est irréversible.</small>",
        })
        .then((result) => {
          if (result.value) {
            this.$http
              .get(Routing.generate("delete_gallery", { id: folderId }))
              .then(
                (response) => {
                  swalWithBootstrapButtons.fire(
                    "Ok. C'est fait !",
                    "La galerie a bien été supprimée.",
                    "success"
                  );
                  this.items.splice(index, 1);
                },
                (response) => {}
              );
          }
        });
    },
    infiniteHandler: function ($state) {
      this.$http
        .get(
          Routing.generate("json_admin_profile_galleries", {
            uuid: uuid,
            page: this.page,
          }),
          {
            params: {
              page: this.page,
            },
          }
        )
        .then((response) => {
          if (response.data.items.length > 0) {
            this.page += 1;
            this.items.push(...response.data.items);
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
  },
  computed: {
    dragOptions() {
      return {
        animation: 200,
        group: "description",
        disabled: false,
        ghostClass: "ghost",
      };
    },
  },
};
</script>