function vueInit(elementId, options) {
    var element = document.getElementById(elementId);
    if (element) {
        options.el = element;
        new Vue(options);
    }
}

vueInit("admin_medias", {
    vuetify: new Vuetify(),
    data: {
        page: 1,
        medias: [],
        infiniteId: +new Date(),
    },
    methods: {
        setview : function (id) {
            var dataString = "mediaId="+id;
            $.ajax({
                url: "/front/setviewmedias/",
                type: "GET",
                data: dataString ,
                success: function (response) {},
                error: function(jqXHR, textStatus, errorThrown) {
                 console.log(textStatus, errorThrown);
             }
         });
        },
        infiniteHandler($state) {
          this.$http.get('/administrator/jsonmedianews/',{params: {page: this.page}})
          .then(({ data }) => {
            console.log(data);

            if (data.list.length) {
              this.page += 1;
              this.medias.push(...data.list);
              if (this.page > data.totalPages) {
                $state.complete();
            }
            $state.loaded();
        } else {
          $state.complete();
      };
  });
      },
      makeInspiration: function(mediaId){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-light mr-2'
        },
        buttonsStyling: false
    })
        swalWithBootstrapButtons.fire({
            title: 'Ajouter en inspiration',
            text: "Souhaitez-vous vraiment la rajouter dans les inspirations ?",
            icon: 'warning',
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonColor: '#36b5aa',
            cancelButtonColor: '#ccc',
            cancelButtonText: 'Annuler',
            confirmButtonText: 'Oui',
        }).then((result) => {
            if (result.value) {
                this.$http.get('/administrator/selected/', { params: {mediaId: mediaId} }).then(response => {
                    swalWithBootstrapButtons.fire(
                        'Mise en inspiration !',
                        'La photo a bien été ajoutée en inspiration',
                        'success'
                        );
                }, response => {
                });
            }
        });
    },
},
})

vueInit("admin_users", {
    vuetify: new Vuetify(),

    data: {
        loading: true,
        users: [],
        sortBy: 'id',
        sortDesc: true,
        search: '',
        headers: [
        {
            text: 'id',
            align: 'start',
            sortable: true,
            value: 'id',
            width : 60
        },
        { text: 'Informations', value: 'fullname' },
        { text: 'Pseudo', value: 'username' },
        { text: 'Email', value: 'email' },
        { text: 'Type de compte', value: 'status' },
        { text: 'Photos', value: 'count_photos' },
        { text: 'Inscription', value: 'created_date' },
        { text: 'Source', value: 'source' },
        { text: 'Lieu', value: 'location' },

        ],
    },
    created() {
        this.getDatas();
    },
    methods: {
        getDatas: function () {
            this.$http.get('/administrator/jsonusersactifs/')
            .then(response => response.json())
            .then(json => (this.users = json))
            .then(response => {
                this.loading = false;
            });
        },
    },
})

vueInit("adminIndex", {
    vuetify: new Vuetify(),
    data: {
        dashboard: [],
        sortBy: 'id',
        sortDesc: true,
        search: '',
        headers: [
        {
            text: 'id',
            align: 'start',
            sortable: true,
            value: 'id',
            width : 60
        },
        { text: 'Informations', value: 'fullname' },
        { text: 'Type de compte', value: 'status' },
        { text: 'Photos', value: 'count_photos' },
        { text: 'Date d\'inscription', value: 'created_date' },
        ],
    },
    created() {
        this.getDatas();
    },
    methods: {
        setview : function (id) {
            var dataString = "mediaId="+id;
            $.ajax({
                url: "/front/setviewmedias/",
                type: "GET",
                data: dataString ,
                success: function (response) {},
                error: function(jqXHR, textStatus, errorThrown) {
                   console.log(textStatus, errorThrown);
               }
           });
        },
        getDatas: function () {
            fetch(url)
            .then(response => response.json())
            .then(json => (this.dashboard = json))
        },
    },
})

vueInit("vue-videos", {
    vuetify: new Vuetify(),
    data: {
        dialog: false,
        videos: [],
        sortBy: 'id',
        sortDesc: true,
        search: '',
        expanded: [],
        singleExpand: false,
        snack: false,
        snackColor: '',
        snackText: '',
        max1chars: v => v.length <= 1 || 'Input too long!',
        pagination: {},
        editedIndex: -1,
        editedItem: {
            status: 0,
        },
        defaultItem: {
            status: 0,
        },
        headers: [
        {
            text: 'id',
            align: 'start',
            sortable: true,
            value: 'id',
            width : 60
        },
        { text: 'Publiée par', value: 'user',width : 300},
        { text: 'URL', value: 'url' },
        { text: 'Titre', value: 'title' },
        { text: 'statut', value: 'status', width : 100 },
        { text: '', value: 'data-table-expand' },
        { text: 'Actions', value: 'actions', sortable: false },
        
        ],
        loading: true,
    },
    watch: {
      dialog (val) {
        val || this.close()
    },
},
created() {
    this.getDatas();
    this.getLocalStorage();
}, 
methods: {
    editItem (item) {
        this.editedIndex = this.videos.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
    },
    videoItem(item){
        this.$http.get('/administrator/curlvideo/', { params: {videoId: item.id} }).then(response => {
            this.getDatas();
        }, response => {
        });
    },
    deleteItem (item) {
        const index = this.videos.indexOf(item)
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-light mr-2'
        },
        buttonsStyling: false
    })

        swalWithBootstrapButtons.fire({
            title: 'Supprimer la vidéo',
            text: "Souhaitez-vous vraiment supprimer cette vidéo ?",
            icon: 'warning',
            reverseButtons: true,
            showCancelButton: true,
            confirmButtonColor: '#36b5aa',
            cancelButtonColor: '#ccc',
            cancelButtonText: 'Annuler',
            confirmButtonText: 'Supprimer',
            footer: '<small>La vidéo sera immédiatement supprimée. Cette action est irréversible.</small>'
        }).then((result) => {
            if (result.value) {
                this.$http.get('/administrator/deletevideo/', { params: {videoId: item.id} }).then(response => {
                    swalWithBootstrapButtons.fire(
                        'Vidéo supprimée !',
                        'La vidéo a bien été supprimée',
                        'success'
                        );
                    this.videos.splice(index, 1)
                }, response => {
                });
            }
        });
    },

    close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
      })
    },
    save () {
        if (this.editedIndex > -1) {
          Object.assign(this.videos[this.editedIndex], this.editedItem)
          this.$http.get('/administrator/editvideo/', { params: {videoId: this.editedItem.id,status: this.editedItem.status} }).then(response => {
             this.snack = true
             this.snackColor = 'success'
             this.snackText = 'C\'est sauvégarder !'
         }, response => {

         });

      } else {
          this.videos.push(this.editedItem)
      }
      this.close()
  },
  getColor (status) {
    if (status > 0) return 'green'
        else return 'red'                    
    },
saveEdit () {
    this.snack = true
    this.snackColor = 'success'
    this.snackText = 'Data saved'
},
cancelEdit () {
    this.snack = true
    this.snackColor = 'error'
    this.snackText = 'Canceled'
},
openEdit () {
    this.snack = true
    this.snackColor = 'info'
    this.snackText = 'Dialog opened'
},
closeEdit () {
    console.log('Dialog closed')
},
getLocalStorage: function (){
    this.videos = JSON.parse(localStorage.getItem('videosListAdmin') || '[]');
},
getDatas: function () {
    this.$http.get('/administrator/vuevideos/')
    .then(response => response.json())
    .then(json => (this.videos = json))
    .then((data) => {localStorage.setItem('videosListAdmin',JSON.stringify(data))}) 
    .then(response => {
        this.loading = false;
    });
},
deletethisvideo: function(videoId){
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: 'btn btn-danger',
        cancelButton: 'btn btn-light mr-2'
    },
    buttonsStyling: false
})
    swalWithBootstrapButtons.fire({
        title: 'Supprimer la vidéo',
        text: "Souhaitez-vous vraiment supprimer cette vidéo ?",
        icon: 'warning',
        reverseButtons: true,
        showCancelButton: true,
        confirmButtonColor: '#36b5aa',
        cancelButtonColor: '#ccc',
        cancelButtonText: 'Annuler',
        confirmButtonText: 'Supprimer',
        footer: '<small>La vidéo sera immédiatement supprimée. Cette action est irréversible.</small>'
    }).then((result) => {
        if (result.value) {
            this.$http.get('/administrator/deletevideo/', { params: {videoId: videoId} }).then(response => {
                swalWithBootstrapButtons.fire(
                    'Vidéo supprimée !',
                    'La vidéo a bien été supprimée',
                    'success'
                    );
                this.getDatas();
                this.getLocalStorage();
            }, response => {
            });
        }
    });
}
}
})

Vue.use(GoTop);
vueInit("vuefollow", {
    vuetify: new Vuetify(),
    data: {
        follows: [],
        famous: [],
        followings: [],        
        page: 1,
        infiniteId: +new Date(),
    },
    methods: {    
        infiniteHandler($state) {
          this.$http.get('/administrator/jsonfollow/',{params: {page: this.page}})
          .then(({ data }) => {
            if (data.users.length) {
              this.page += 1;
              this.follows.push(...data.users);
              this.famous.push(...data.famous);
              this.followings.push(...data.followings);
              if (this.page > data.totalPages) {
                $state.complete();
            }
            $state.loaded();
        } else {
          $state.complete();
      };
  });
      },
  },
});

