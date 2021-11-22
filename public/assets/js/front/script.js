/*var feed = new Instafeed({
    get: 'user',
    clientId: 'b280d6bcafa64299bff3e1cddbba8d3c',
    userId: '3321898779',
    accessToken :'3321898779.1677ed0.214d5a8f25d748ca8ff205d31edd8ce3',
    resolution: 'standard_resolution',
    template: '<a target="_blank" href="{{link}}" class="content35-feed-image content35-image2 w-inline-block" style="background-image:url({{image}})"></a>',
    limit: 6,
    sortBy: 'most-recent',
    target: 'thefeed',
});
feed.run();*/

$( function() {
  $( "input[title]" ).tooltip({
    position: {
      my: "center bottom-20",
      at: "center top",
      collision: "none",
      using: function( position, feedback ) {
        $( this ).css( position );
        $( "<div>" )
        .addClass( "arrow" )
        .addClass( feedback.vertical )
        .addClass( feedback.horizontal )
        .appendTo( this );
      }
    }
  });

  $( ".moreInfos" ).tooltip({
    position: {
      my: "center bottom-20",
      at: "center top",
      collision: "none",
      using: function( position, feedback ) {
        $( this ).css( position );
        $( "<div>" )
        .addClass( "arrow" )
        .addClass( feedback.vertical )
        .addClass( feedback.horizontal )
        .appendTo( this );
      }
    }
  });
} );

floatingLabel.init();

$(document).ready(function() {

  $(".link-like").click(function(e) {
    e.preventDefault();
    var id = $(this).attr('id');
    var url = $(this).attr('data-href');
    var fav = parseInt($('#countfav').val());

    if($(this).hasClass('light')){
      $(this).removeClass('light')
      $(this).addClass('solid');
      $('#countlove').html(fav+1)
      fav = fav+1;
    }else{
      $(this).removeClass('solid')
      $(this).addClass('light')

      $('#countlove').html(fav-1)
      fav = fav-1;
    };
    $('#countfav').val(fav);
    $.ajax({
      type: "POST",
      url: url,
      data: { id: id},
      dataType : "text",
      success: function(html){                   
      }
    });
    return false;
  });

  $('select').niceSelect();
});


function vueInit(elementId, options) {
  var element = document.getElementById(elementId);
  if (element) {
    options.el = element;
    new Vue(options);
  }
}

vueInit("inspirations", {
  data: {
    photos: [],
  },
  created () {
   this.photos = JSON.parse(localStorage.getItem('inspirations') || '[]');
   localStorage.setItem('inspirations', JSON.stringify(this.getDatas()));
 },
 methods: {
  getDatas: function () {
    fetch(photoDataUrl)
    .then(response => response.json())
    .then(json => (this.photos = json))
    .then((data) => {localStorage.setItem('inspirations',JSON.stringify(data))}) 
  },
}   
});


vueInit("fresh", {
  vuetify: new Vuetify(),
  data: {
    page: 1,
    medias: [],
    infiniteId: +new Date(),
    typeList:[],
    newsType: [],
    experience: '',
    experienceList:[],
    disabled: true,
  },
  methods: {
    loadPhoto : function (event) {
      var mediaId = event.currentTarget.id;
      window.history.pushState(mediaId, 'Title', '/photo/' + mediaId+'/');
    },
    infiniteHandler($state) {
      this.$http.get("/front/jsonfresh/", {params: {
        page: this.page,
        type: this.newsType,
        experience: this.experience,
      }})
      .then(({ data }) => {
        if (data.medias.length>0) {
          this.page += 1;
          this.medias.push(...data.medias);
          if (this.page > data.totalPages) {
            $state.complete();
          }
          this.typeList = data.typeList;
          $state.loaded();
        } else {
          $state.complete();
        };
      });
    },
    changeExperience() {
      this.page = 1;
      this.medias = [];
      this.infiniteId += 1;
    },
    getExperienceList() {
     this.$http.get('/dashboard/jsongetexperience/', {params: {type: this.newsType}})
     .then((data) => {
      this.experienceList = data.data;
      this.disabled = false;
    }); 
     this.page = 1;
     this.medias = [];
     this.infiniteId += 1;
   },
 }
});


var buttonAddFav = Vue.component('buttonAddFav', {
  template: `<a type="button" v-on:click="alreadyfav ? removeFav(user) : addFav(user)" class="d-block-sm link-like link-block-2 w-inline-block" :class="alreadyfav ? 'solid' : 'light'"  title="Ajouter dans mes favoris"></a>`,
  props: {
    user: {
      type: Object,
      required: true
    },
  },
  data: function () {
    return {
      alreadyfav: this.user.identity.alreadyfav
    }
  },
  methods: {
    addFav: function (user) {
      this.$http.get('/front/favorisbook/',{params: {userId: user.identity.id}}).then(function(response){
        var fav = parseInt($('#countfav').val());
        this.alreadyfav = true;
        $('#countlove').html(fav+1)   
        fav = fav+1;
        $('#countfav').val(fav);         
      }, function(error){
        console.log(error.statusText);
      });
    },
    removeFav: function(user){
     this.$http.get('/front/favorisbook/',{params: {userId: user.identity.id}}).then(function(response){
       var fav = parseInt($('#countfav').val());
       this.alreadyfav = false; 
       $('#countlove').html(fav-1)
       fav = fav-1;
       $('#countfav').val(fav);
     }, function(error){
      console.log(error.statusText);
    });
   }
 }
})


vueInit("typebook", {
  vuetify: new Vuetify({
    theme: { disable: true },
  }),
  components: {
   "button-add-fav": buttonAddFav,
 },
 data: {
  page: 1,
  users: [],
  infiniteId: +new Date(),
  experience: '',
  experienceList:[],
  sexeList:[],
  newsSexe: '',
  disabled: true,
  originList:[],
  newsOrigin: '',
  hairList:[],
  newsHair:'',
  eyesColorList:[],
  newsEyesColor:'',    

  isLoading: false,
  items: [],
  autocomplete: null,
  search: null,

  defaultOrder: 'last_login',
  sortList: [],
  userlove : true,
},

watch: {
  search(val) {
    if (!val) {
      return;
    }
    this.isLoading = true;
    const service = new google.maps.places.AutocompleteService();
    service.getQueryPredictions({ input: val }, (predictions, status) => {
      if (status != google.maps.places.PlacesServiceStatus.OK) {
        return;
      }
      this.items = predictions.map(prediction => {
        return {
          id: prediction.id,
          name: prediction.description,
        };
      });
      this.isLoading = false;
    });
  },
},
methods: {
  infiniteHandler($state) {
    this.$http.get(URLType, {params: {
      page: this.page,
      experience: this.experience,
      sexe: this.newsSexe,
      origin: this.newsOrigin,
      hair: this.newsHair,
      eyesColor: this.newsEyesColor,
      sortBy: this.defaultOrder,
      autocomplete : this.autocomplete,
    }})
    .then(({ data }) => {
      if (data.users.length>0) {
        this.page += 1;
        this.users.push(...data.users);
        if (this.page > data.totalPages) {
          $state.complete();
        }
        this.experienceList = data.experienceList; 
        this.sexeList = data.sexeList;
        this.typeList = data.typeList;
        this.sortList = data.sortList;            
        this.originList = data.originList;
        this.hairList = data.hairList;
        this.eyesColorList = data.eyesColorList;
        $state.loaded();
      } else {
        $state.complete();
      };
    });
  },
  changeType() {
    this.page = 1;
    this.users = [];
    this.infiniteId += 1;
  },

}


});


