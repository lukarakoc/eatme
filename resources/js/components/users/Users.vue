<template>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Zaposleni</h3>
                <div class="float-right">
                    <form class="form-inline ml-3">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar"
                                   type="search"
                                   placeholder="Pretraga"
                                   aria-label="Search"
                                   v-model="searchKeyword"
                                   @keyup="searchUsers">
                            <div class="input-group-append">
                                <button class="search-button btn btn-navbar border border-muted" @click.prevent="">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body pt-2">
                <button class="btn btn-sm btn-primary float-right mb-2"
                        @click="openCreateModal">
                    <i class="fas fa-plus-circle"></i> Dodaj zaposlenog
                </button>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center" width="10">ID</th>
                            <th class="text-center">Ime i prezime</th>
                            <th class="text-center">Email</th>
                            <th class="text-center">Uloga</th>
                            <th class="text-center" width="150">Izmijeni</th>
                            <th class="text-center" width="150">Izbriši</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="pageIsLoading">
                            <td class="text-center" colspan="9">
                                <spinner/>
                            </td>
                        </tr>
                        <tr v-for="user in users" :key="user.id">
                            <td class="text-center">{{ user.id }}</td>
                            <td class="text-center">{{ user.name }}</td>
                            <td class="text-center">{{ user.email }}</td>
                            <td class="text-center">{{ user.role.name }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" @click="openEditModal(user)">
                                    <i class="fas fa-edit"></i>
                                    Izmijeni
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deleteUser(user.id)">
                                    <i class="fas fa-trash"></i>
                                    Izbriši
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <pagination v-show="searchMode"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="usersPagination"
                            :limit="1"
                            @pagination-change-page="loadSearchedUsers">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="usersPagination"
                            :limit="1"
                            @pagination-change-page="loadUsers">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
            </div>
        </div>
        <create-and-edit-user :users="users"/>

    </div>
</template>

<script>
import {EventBus, swalError, swalSuccess} from "../../main.js";
import CreateAndEditUser from "./CreateAndEditUser";
import Spinner from "../Spinner";

export default {
    components: {
        CreateAndEditUser,
        Spinner
    },
    data() {
        return {
            pageIsLoading: true,
            searchMode: false,
            searchKeyword: "",
            users: [],
            usersPagination: {},
        };
    },
    methods: {
        loadUsers(page = 1) {
            axios.get(`/admin/users`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.usersPagination = response.data[1];
                        this.users = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
            console.log(this.users[0])
        },
        openCreateModal() {
            EventBus.$emit('open-create-modal');
        },
        openEditModal(user) {
            EventBus.$emit('open-edit-modal', user);
        },
        deleteUser(id) {
            Swal.fire({
                icon: "warning",
                title: "Pažnja",
                text: "Da li ste sigurni da želite da izbrišete zaposlenog?",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                cancelButtonColor: "#c51f1a",
                confirmButtonText: "Da",
                cancelButtonText: "Ne"
            }).then((result) => {
                if (result.value) {
                    axios.delete(`/admin/users/${id}`)
                        .then(response => {
                            if (response.data[0] === "success") {
                                swalSuccess("Uspješno ste izbrisali zaposlenog!");
                                EventBus.$emit('load-users');
                            }
                        }).catch(error => {
                        if (error.response.status === 422) {
                            swalError(error.response.data.error)
                        } else {
                            swalError("Došlo je do greške! Molimo Vas pokušajte ponovo");
                        }
                    });
                }
            })
        },
        loadSearchedUsers(page = 1) {
            axios.get(`/admin/users/${this.searchKeyword}/search?page=${page}`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.usersPagination = response.data[1];
                        this.users = response.data[1].data;
                    }
                });
        },
        searchUsers() {
            if (!(this.searchKeyword === "")) {
                this.searchMode = true;
                axios.get(`/admin/users/${this.searchKeyword}/search`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.usersPagination = response.data[1];
                            this.users = response.data[1].data;
                        }
                    });
            } else {
                this.searchMode = false;
                this.loadUsers();
            }
        },
    },
    mounted() {
        this.loadUsers();
        this.$emit('loadBreadcrumbLink', {url: '/users', pageName: 'Zaposleni'});
        EventBus.$on('load-users', () => this.loadUsers());
    }
}

</script>

<style scoped>
.modal-body .invalid-tab {
    color: #dc3545;
    background-color: #F8D3D7;
}

.image-area img {
    max-width: 100%;
    height: auto;
}

.checkbox:focus {
    box-shadow: none;
}
</style>
