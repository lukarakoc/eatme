<template>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="float-right">
                    <form class="form-inline ml-3">
                        <div class="input-group input-group-sm">
                            <input class="form-control form-control-navbar"
                                   type="search"
                                   placeholder="Pretraga"
                                   aria-label="Search"
                                   v-model="searchKeyword"
                                   @keyup="searchGroceries">
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
                <button class="btn btn-sm btn-primary float-right mb-2 ml-1"
                        @click="openCreateModal">
                    <i class="fas fa-plus-circle"></i> Dodaj namirnicu
                </button>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center" width="10">ID</th>
                            <th class="text-center" width="150">Slika</th>
                            <th class="text-center">Naziv</th>
                            <th class="text-center" width="100">Cijena</th>
                            <th class="text-center" width="50">Proizvod</th>
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
                        <tr v-for="grocery in groceries" :key="grocery.id">
                            <td class="text-center">{{ grocery.id }}</td>
                            <td class="text-center">
                                <img class="rounded mx-auto"
                                     alt="Image"
                                     style="max-width: 100px;"
                                     :src="grocery.image_path"
                                     v-if="grocery.image_path">
                            </td>
                            <td class="text-center" v-html="grocery.name"></td>
                            <td class="text-center"
                            ><span v-html="grocery.unit_price"></span>&euro;</td>
                            <td class="text-center">{{ grocery.is_product ? 'Da' : 'Ne' }}</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" @click="openEditModal(grocery)">
                                    <i class="fas fa-edit"></i>
                                    Izmijeni
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deleteGrocery(grocery.id)">
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
                            :data="groceriesPagination"
                            :limit="1"
                            @pagination-change-page="loadSearchedGroceries">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="groceriesPagination"
                            :limit="1"
                            @pagination-change-page="loadGroceries">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
            </div>
        </div>
        <create-and-edit-grocery :grocery="groceries"/>
    </div>
</template>

<script>
import Spinner from "../Spinner";
import {EventBus, swalError, swalSuccess} from "../../main";
import CreateAndEditGrocery from "./CreateAndEditGrocery";

export default {
    name: "Groceries",
    components: {
        Spinner,
        CreateAndEditGrocery
    },
    data() {
        return {
            pageIsLoading: true,
            searchMode: false,
            searchKeyword: '',
            groceries: [],
            groceriesPagination: {}
        }
    },
    methods: {
        loadGroceries(page = 1) {
            axios.get('/admin/groceries')
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.groceriesPagination = response.data[1];
                        this.groceries = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        openCreateModal() {
            EventBus.$emit('open-create-modal');
        },
        openEditModal(house) {
            EventBus.$emit('open-edit-modal', house);
        },
        deleteGrocery(id) {
            Swal.fire({
                icon: "warning",
                title: "Pažnja",
                text: "Da li ste sigurni da želite da izbrišete namirnicu?",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                cancelButtonColor: "#c51f1a",
                confirmButtonText: "Da",
                cancelButtonText: "Ne"
            })
                .then((result) => {
                    if (result.value) {
                        axios.delete(`/admin/groceries/${id}`)
                            .then(response => {
                                if (response.data[0] === 'success') {
                                    swalSuccess("Uspješno ste izbrisali namirnicu!");
                                    EventBus.$emit('load-groceries');
                                }
                            })
                            .catch(error => {
                                if (error.response.status === 422) {
                                    swalError(error.response.data.error)
                                } else {
                                    swalError("Došlo je do greške! Molimo Vas pokušajte ponovo");
                                }
                            })
                    }
                })
        },
        loadSearchedGroceries(page = 1) {
            axios.get(`/admin/groceries/${this.searchKeyword}/search?page=${page}`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.groceriesPagination = response.data[1];
                        this.groceries = response.data[1].data;
                    }
                });
        },
        searchGroceries() {
            if (!(this.searchKeyword === '')) {
                this.searchMode = true;
                axios.get(`/admin/groceries/${this.searchKeyword}/search`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.groceriesPagination = response.data[1];
                            this.groceries = response.data[1].data;
                        }
                    });
            } else {
                this.searchMode = false;
                this.loadGroceries();
            }
        }
    },
    mounted() {
        this.loadGroceries();
        this.$emit('loadBreadcrumbLink', {url: '/groceries', pageName: 'Namirnice'});
        EventBus.$on('load-groceries', () => this.loadGroceries());
    }
}

</script>

<style scoped>

</style>
