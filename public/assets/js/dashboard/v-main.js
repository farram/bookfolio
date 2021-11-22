
function vueInit(elementId, options) {
	var element = document.getElementById(elementId);
	if (element) {
		options.el = element;
		new Vue(options);
	}
}

var ButtonFollow = Vue.component('ButtonFollow', {
	template: `<button class="btn waves-effect waves-light btn-xs" :class="followed ? 'btn-outline-danger' : 'btn-primary'" 
    v-on:click="followed ? unfollow(user.identity.username,user.identity.fullname) : follow(user.identity.username,user.identity.fullname)">{{followed ? 'Se désabonner' : 'S’abonner'}}</button>`,
	props: {
		user: {
			type: Object,
			required: true
		},
	},
	data: function () {
		return {
			followed: this.user.identity.followed
		}
	},
	methods: {
		follow: function (username, fullname) {
			this.$http.get(Routing.generate('add_follow', {username: username})).then(response => {
				Swal.fire('Nice !', 'Vous commencez à suivre ' + fullname, 'success');
				this.followed = true;
			}, response => { });
		},
		unfollow: function (username, fullname) {
			this.$http.get(Routing.generate('remove_follow', {username: username})).then(response => {
				Swal.fire('Oh :(', 'Vous ne suivez plus ' + fullname, 'success');
				this.followed = false;
			}, response => { });
		}
	}
})


vueInit("vue-annuaire", {
	vuetify: new Vuetify(),
	components: {
		"button-follow": ButtonFollow,
	},
	data: {
		page: 1,
		users: [],
		infiniteId: +new Date(),
		typeList: [],
		newsType: [],
		experience: '',
		experienceList: [],
		sexeList: [],
		newsSexe: '',
		disabled: true,
		originList: [],
		newsOrigin: '',
		hairList: [],
		newsHair: '',
		eyesColorList: [],
		newsEyesColor: '',

		isLoading: false,
		items: [],
		autocomplete: null,
		search: null,

		defaultOrder: 'last_login',
		sortList: [],

		componentForm: {
			street_number: '',
			route: '',
			locality: '',
			administrative_area_level_1: '',
			country: '',
			postal_code: ''
		}
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
			this.$http.get(Routing.generate('json_annuaire', { 
				page: this.page}), {
				params: {
					page: this.page,
					type: this.newsType,
					experience: this.experience,
					sexe: this.newsSexe,
					origin: this.newsOrigin,
					hair: this.newsHair,
					eyesColor: this.newsEyesColor,
					sortBy: this.defaultOrder,
					autocomplete: this.autocomplete,
				}
			})
				.then(({ data }) => {
					if (data.users.length > 0) {
						this.page += 1;
						this.users.push(...data.users);
						if (this.page > data.totalPages) {
							$state.complete();
						}
						this.typeList.push(...data.typeList);
						this.sexeList.push(...data.sexeList);
						this.originList.push(...data.originList);
						this.hairList.push(...data.hairList);
						this.eyesColorList.push(...data.eyesColorList);
						this.sortList.push(...data.sortList);

						$state.loaded();
					} else {
						$state.complete();
					};
				});
		},

		getExperienceList() {
			this.$http.get(Routing.generate('json_experience', { type: this.newsType}), 
			)
				.then((data) => {
					this.experienceList = data.data;
					this.disabled = false;
				});
			this.page = 1;
			this.users = [];
			this.infiniteId += 1;
		},
		changeType() {
			this.page = 1;
			this.users = [];
			this.infiniteId += 1;
		},

	}

});

