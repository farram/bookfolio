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
      <transition-group
        class="row g-6 g-xl-9"
        type="transition"
        name="flip-list"
      >
        <div
          class="col-md-6 col-xl-4"
          v-for="(item, n) in items"
          :key="item.gallery.id"
          :data-id="item.gallery.id"
        >
          <div class="card border border-2 border-gray-300 border-hover">
            <div class="card-header border-0 pt-9">
              <div class="card-toolbar">
                <template v-if="item.gallery.isProtect === true">
                  <span
                    class="badge badge-light-danger fw-bolder me-auto px-4 py-3"
                  >
                    <i class="fe-lock"></i>
                    {{ item.gallery.isProtectText }}
                  </span>
                </template>
                <template v-else>
                  <span
                    class="badge badge-light-success fw-bolder me-auto px-4 py-3"
                  >
                    {{ item.gallery.isProtectText }}
                  </span>
                </template>
              </div>
            </div>
            <div class="card-body pt-0 pl-9 pr-9 pb-9">
              <div class="fs-3 fw-bolder text-dark">
                <a :href="item.gallery.link" class="text-dark">
                  {{ item.gallery.title }}
                </a>
              </div>
              <p class="text-gray-400 fw-bold fs-5 mt-1 mb-7">
                {{ item.gallery.createdAt }}
              </p>
              <div class="d-flex flex-wrap mb-5 text-center">
                <div
                  class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3"
                >
                  <div class="fs-6 text-gray-800 fw-bolder">
                    {{ item.gallery.countImages }}
                  </div>
                  <div class="fw-bold text-gray-400">Photos</div>
                </div>
                <div
                  class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3"
                >
                  <div class="fs-6 text-gray-800 fw-bolder">
                    {{ item.gallery.countViews }}
                  </div>
                  <div class="fw-bold text-gray-400">vues</div>
                </div>
              </div>

              <div class="d-flex align-items-center">
                <a
                  :href="item.gallery.link"
                  v-for="(image, $key) in item.gallery.images"
                  :key="$key"
                  class="symbol symbol-35px me-2"
                >
                  <img :src="image.thumb" alt="" />
                </a>
              </div>

              <template v-if="item.gallery.totalImage == 0">
                <button
                  class="btn btn-danger btn-xs"
                  @click="removeAt(item.gallery.id, n)"
                >
                  <i class="mdi mdi-close"></i> Supprimer la galerie
                </button>
              </template>
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
const routes = require("../../../public/js/fos_js_routes.json");
import Routing from "../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js";
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
          Routing.generate("json_galleries", {
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