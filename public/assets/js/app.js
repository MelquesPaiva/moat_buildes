axios.defaults.headers.common['Authorization'] = "Bearer " + localStorage.getItem("token");

const toastrOptions = {
    closeButton: true,
    progressBar: true,
    timeOut: 7000
}

/**
 * @param {*} value 
 */
function empty(value) {
    return (value === '' || value == null);
}

/**
 * Verificando se elementos com o atributo obrigatório estão preenchidos
 */
 function requiredData(e) {
    let formData = {};

    for (inputForm of e.target.elements) {
        if (['submit', 'button'].indexOf(inputForm.type) === -1) {
            formData[inputForm.name] = inputForm.value;
            if (inputForm.attributes.mandatory != null && (inputForm.value == null || inputForm.value === '' || inputForm.value == 0)) {
                return null;
            }
        }
    }

    return formData;
}

const baseCompoment = {
    async mounted() {
        let self = this;
        self.$root.load();
        await axios.get(`${host}session`).then(function (response) {
            self.$root.userSession = response.data.user;
            console.log(self.$root.userSession);
        });
        self.$root.load(false);
    },
    template: `
        <div>
            <div class="default-load" style="display: none;">
                <div class="default-load-box">
                    <div class="default-load-box-circle"></div>
                    <p class="font-weight-bold color-white">Aguarde, carregando...</p>
                </div>
            </div>

            <main class="app-container">
                <header class="template-header">
                    <h1> {{ $root.titlePage }} </h1>
                    <div class="menu-buttons-area">
                        <button class="btn btn-link text-dark-blue btn-sidebar-menu" title="Menu" data-sidebar="sidebar">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                </header>

                <section class="app-content">
                    <nav class="sidebar">
                        <article class="sidebar-menu">
                            <router-link :to="{name: 'artist'}" class="btn">
                                Artists
                            </router-link>
                            <router-link :to="{name: 'albums'}" class="btn">
                                Albums
                            </router-link>
                            <a href="#" class="btn btn-logout"
                                data-toggle="modal" data-target="#logoutModal">
                                Logout
                            </a>
                        </article>
                    </nav>

                    <section class="app-main">
                        <transition name="slide" mode="out-in">
                            <router-view></router-view>
                        </transition>
                    </section>
                </section>
            </main>

            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="logoutModalLabel">
                                Logout
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Do you really want to get out of the application?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-circle" data-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                            <a href="${host}logout" class="btn btn-danger btn-circle">
                                <i class="far fa-check-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
}

const artistComponent = {
    mounted() {
        let self = this;

        self.$root.titlePage = "Artists";

        console.log('teste');

        axios({
            method: 'GET',
            url: "https://moat.ai/api/task/"
        }).then(function(response) {
            console.log(response);
        }).catch(function(error) {
            console.log(error);
        });

    },
    template: `
        <div>
            <header class="section-header row">
                <section class="col-12">
                    <h1>Artist</h1>
                    <p class="text-light-blue">See the list of artists</p>
                </section>
            </header>

            <hr class="my-4"/>

            <main class="container">
                <section class="row border-bottom-light-gray py-3 text-light-blue" v-for="artist in $root.artistList">
                    <article class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                        ID: {{ artist.id }}
                    </article>
                    <article class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                        Name: {{ artist.name }}
                    </article>
                    <article class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                        <i class="fab fa-twitter"></i>: {{ artist.twitter }}
                    </article>
                </section>
            </main>
        </div>
    `
};

const albumCompoment = {
    async mounted() {
        let self = this;

        self.$root.albumList = [];
        self.$root.titlePage = "Albums";
        self.$root.load();

        const message = $('#albumMessage');

        await axios.get(`${host}session`).then(function (response) {
            self.$root.userSession = response.data.user;
        });

        axios.get(`${host}v1/album`)
            .then(function(response) {
                self.$root.load(false);
                if (response.status == 204) {
                    message.html('Not a single album is registered yet.')
                    message.removeClass('error');
                    message.addClass('info');
                    message.css('display', 'block');

                    return;
                }
                self.$root.albumList = response.data.data;
            })
            .catch(function(err) {
                self.$root.load(false);
                message.removeClass('info');
                message.addClass('error');
                message.html(err.response.data.meta.error.message);
                message.css('display', 'block');
            });
    },
    template: `
        <div>
            <header class="section-header row">
                <section class="col-xl-9 col-lg-9 col-md-12">
                    <h1>Albums</h1>
                    <p class="text-light-blue">See the list of albums and make additions, editions and remove registers</p>
                </section>
                <section class="col-xl-3 col-lg-3 col-md-12">
                    <router-link :to="{name: 'albumsNew'}" class="btn btn-outline-dark-blue btn-sm header-add-button">
                        <i class="fas fa-plus-circle"></i> New Album
                    </router-link>
                </section>
            </header>

            <hr class="my-4"/>

            <div id="albumMessage" class="message" style="display: none;"></div>

            <div class="container">
                <div class="mt-5">
                    <div class="d-style btn btn-brc-tp border-2 bgc-white btn-outline-blue btn-h-outline-blue btn-a-outline-blue w-100 my-2 py-3 shadow-sm">
                        <div class="row align-items-center py-2 border-bottom-gray-500" v-for="album in $root.albumList">
                            <div class="col-12 col-md-7">
                                <h4 class="pt-3 text-170 text-600 text-primary-d1 letter-spacing">
                                    Album: {{ album.album_name }}
                                </h4>

                                <div class="text-secondary-d1 text-120">
                                    <span class="ml-n15 align-text-bottom">
                                        {{ album.artist }}
                                    </span>
                                </div>
                            </div>
                
                            <div class="col-12 col-md-5 text-center">
                                <router-link 
                                    :to="{name: 'albumsEdit', params: { id: album.id }}"
                                    class="f-n-hover btn btn-info btn-raised px-4 py-25 text-600">
                                    <i class="fas fa-edit"></i>
                                </router-link>
                                <a href="#" class="f-n-hover btn btn-danger btn-raised px-4 py-25 text-600"
                                    v-if="$root.userSession.role == 2" @click="prepareRemove(album.id)">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                        <div class="modal fade" id="removeAlbumModal" tabindex="-1" role="dialog" aria-labelledby="removeAlbumModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="removeAlbumModalLabel">
                                            Remove album
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Do you really want to remove this album?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-circle" data-dismiss="modal">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-circle" @click="removeAlbum">
                                            <i class="far fa-check-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
    data: {
        id: null
    },
    methods: {
        prepareRemove: function(id) {
            let self = this;
            self.id = id;
            $('#removeAlbumModal').modal('show');
        },
        removeAlbum: function() {
            let self = this;
            const modal = $('#removeAlbumModal');

            self.$root.load();

            axios.delete(`${host}v1/album/${self.id}`)
                .then(function(response) {
                    self.$root.load(false);
                    if (response.status === 200) {
                        toastr.success(response.data.message, 'Album', toastrOptions);
                        modal.modal('hide');

                        const index = self.$root.albumList.findIndex(function(item) {
                            return item.id == response.data.data.id;
                        })

                        if (index != -1) {
                            self.$root.albumList.splice(index, 1);
                        }

                        return;
                    }

                    toastr.warning('It was not possible to delete the album');
                })
                .catch(function(err) {
                    self.$root.load(false);
                    toastr.error(err.response.data.meta.error.message);
                });
        }
    }
};

const formAlbum = `
    <form v-on:submit.prevent="saveAlbum" id="formAlbum">
        <article class="row">

            <article class="input-group col-lg-6 mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                        <i class="fas fa-music text-muted"></i>
                    </span>
                </div>
                <input id="album_name" type="text" name="album_name" placeholder="Album Name"
                    class="form-control bg-white border-left-0 border-md"
                    mandatory="Album Name">
            </article>

            <article class="input-group col-lg-6 mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                        <i class="fas fa-sort-numeric-down-alt text-muted"></i>
                    </span>
                </div>
                <input id="year" type="number" name="year" placeholder="Year"
                    class="form-control bg-white border-left-0 border-md"
                    mandatory="Year">
            </article>

            <article class="input-group col-lg-12 mb-4">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white px-4 border-md border-right-0">
                        <i class="fas fa-user text-muted"></i>
                    </span>
                </div>
                <select id="artist" name="artist" class="form-control bg-white border-left-0 border-md"
                    mandatory="Artist">
                    <option value="">Select a artist</option>
                    <option v-for="artist in $root.artistList"  v-bind:value="artist.name">
                        {{ artist.name }}
                    </option>
                </select>
            </article>

            <!-- Submit Button -->
            <article class="form-group col-lg-12 mx-auto mb-0">
                <button type="submit" class="btn btn-primary btn-block py-2">
                    <span class="font-weight-bold">Save</span>
                </button>
            </article>

        </article>

    </form>
`;

// @todo Finalizar new component
const newAlbumComponent = {
    mounted() {
        let self = this;
        self.$root.titlePage = "New Album";
    },
    template: `
        <div>
            <header class="section-header row">
                <section class="col-12">
                    <h1>Albums</h1>
                    <p class="text-light-blue">Add a new album</p>
                </section>
            </header>

            <hr class="my-4"/>

            ${formAlbum}
        </div>
    `,
    methods: {
        saveAlbum: function(e) {
            let self = this;
            self.$root.submitForm(e, 'v1/album', function(response) {
                toastr.success(response.data.message, 'Album', toastrOptions);
                self.$router.push({'name': 'albums'});
            }, 'POST');
        }
    }
};

// @todo Finalizar edit component
const editAlbumComponent = {
    async mounted() {
        let self = this;
        const id = self.$route.params.id;

        if (self.$root.empty(id)) {
            self.$router.push({'name': 'albums'});
            return;
        }

        self.$root.titlePage = "Edit Album";
        self.$root.load();

        await axios.get(`${host}v1/album/${id}`)
            .then(function(response) {
                if (response.status == 204) {
                    self.$router.push({'name': 'albums'});
                    return;
                }

                const result = response.data.data;
                const form = document.querySelector('#formAlbum');
                for (input of form.elements) {
                    if (['submit', 'button'].indexOf(input.type) === -1) {
                        input.value = result[input.name];
                    }
                }
            })
            .catch(function(err) {
                toastr.error(err.response.data.meta.error.message, 'Album', toastrOptions);
                self.$router.push({'name': 'albums'});
            })

        // @todo corrigir cors error

        self.$root.load(false);
    },
    template: `
        <div>
            <header class="section-header row">
                <section class="col-12">
                    <h1>Albums</h1>
                    <p class="text-light-blue">Edit this album</p>
                </section>
            </header>

            <hr class="my-4"/>
        
            ${formAlbum}
        </div>
    `,
    methods: {
        saveAlbum: function(e) {
            let self = this;

            self.$root.submitForm(e, `v1/album/${self.$route.params.id}`, function(response) {
                toastr.success(response.data.message, 'Album', toastrOptions);
                self.$router.push({'name': 'albums'});
            }, 'PUT');
        }
    }
};

const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: baseCompoment,
            name: 'home',
            children: [
                {
                    path: 'artist',
                    component: artistComponent,
                    name: 'artist'
                },
                {                    
                    path: 'albums',
                    component: albumCompoment,
                    name: 'albums',
                },
                {
                    path: 'albums/new',
                    component: newAlbumComponent,
                    name: 'albumsNew',
                },
                {
                    path: 'albums/edit/:id',
                    component: editAlbumComponent,
                    name: 'albumsEdit',
                }
            ]
        }
    ],
    linkActiveClass: "active",
});

const app = new Vue({
    el: '#app',
    data: {
        albumList: [],
        artistList: [
            {
                "id": 1,
                "twitter": "@justinbieber",
                "name": "Justin Bieber"
            },
            {
                "id": 2,
                "twitter": "@katyperry",
                "name": "Katy Perry"
            }
        ],
        active: true,
        titlePage: ''
    },
    router,
    methods: {
        submitForm: function(e, endpoint, callback, method) {
            let self = this;
            let sendData = requiredData(e);
            if (empty(sendData)) {
                toastr.warning('Enter all mandatory data', 'Sign In', toastrOptions);
                return;
            }

            self.load();

            axios({
                method: method,
                url: `${host}${endpoint}`,
                data: sendData
            }).then(function(response) {
                callback(response);
                self.load(false);
            }).catch(function(err) {
                toastr.error(err.response.data.meta.error.message);
                self.load(false);
            });
        },
        load: function(loading = true) {
            const defaultLoad = $('.default-load');
            if (loading) {
                defaultLoad.css('display', 'flex');
                return;
            }

            defaultLoad.css('display', 'none');
        },
        empty: function(value) {
            return (value === '' || value == null);
        }
    }
});
