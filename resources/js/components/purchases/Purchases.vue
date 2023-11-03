<template>
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <!-- Datepickers and Apply Button -->
                <div class="d-flex align-items-center">
                    <!-- Start Date -->
                    <div class="d-flex align-items-center">
                        <label for="startDate" class="mr-2 mb-0">Od:</label>
                        <input type="date" placeholder="Datum od:" class="form-control" id="startDate" v-model="startDate">
                    </div>

                    <!-- End Date -->
                    <div class="d-flex align-items-center ml-2">
                        <label for="endDate" class="mr-2 mb-0">Do:</label>
                        <input type="date" placeholder="Datum do:" class="form-control" id="endDate" v-model="endDate">
                    </div>

                    <!-- Apply Button -->
                    <button class="btn btn-sm btn-primary ml-2" @click="applyFilters"><i class="fas fa-filter"></i> Filter</button>

                    <button class="btn btn-sm btn-danger ml-1" @click="removeFilters"><i class="fas fa-times"></i> Ukloni</button>
                </div>

                <!-- Search Bar and Button (Floated to the Right) -->
                <div class="form-inline ml-auto">
                    <button class="btn btn-sm btn-outline-success" disabled><i class="fas fa-file-export"></i> Export</button>
                </div>
            </div>


            <div class="card-body pt-2">
                <button class="btn btn-sm btn-success float-right mb-2 ml-1"
                        @click="openNewPurchaseModal">
                    <i class="fas fa-shopping-basket"></i> Nova kupovina
                </button>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center" width="10">ID</th>
                            <th class="text-center">Datum kupovine</th>
                            <th class="text-center">Račun</th>
                            <th class="text-center" width="150">Pregled</th>
                            <th class="text-center" width="150">Izbriši</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="pageIsLoading">
                            <td class="text-center" colspan="9">
                                <spinner/>
                            </td>
                        </tr>
                        <tr v-for="purchase in purchases" :key="purchase.id">
                            <td class="text-center">{{ purchase.id }}</td>
                            <td class="text-center" v-html="purchase.created_at"></td>
                            <td class="text-center"><span v-html="purchase.total"></span>&euro;</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" @click="openPreviewModal(purchase)">
                                    <i class="fas fa-eye"></i>
                                    Pregled
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deletePurchase(purchase.id)">
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
                            :data="purchasesPagination"
                            :limit="1"
                            @pagination-change-page="loadSearchedPurchases">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && !filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="purchasesPagination"
                            :limit="1"
                            @pagination-change-page="loadPurchases">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="purchasesPagination"
                            :limit="1"
                            @pagination-change-page="loadFiltered">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
            </div>
        </div>
        <new-purchase/>
        <preview-purchase :purchase="purchases"/>
    </div>
</template>

<script>
import Spinner from "../Spinner";
import {EventBus, swalError, swalSuccess} from "../../main";
import NewPurchase from './NewPurchase';
import PreviewPurchase from "./PreviewPurchase";

export default {
    name: "Purchases",
    components: {
        NewPurchase,
        PreviewPurchase,
        Spinner
    },
    data() {
        return {
            startDate: '',
            endDate: '',
            pageIsLoading: true,
            searchMode: false,
            filtered: false,
            searchKeyword: '',
            purchases: [],
            purchasesPagination: {}
        }
    },
    methods: {
        loadPurchases(page = 1) {
            axios.get(`/admin/purchases?page=${page}`)
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.purchasesPagination = response.data[1];
                        this.purchases = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        openNewPurchaseModal() {
            EventBus.$emit('open-new-purchase-modal');
        },
        openPreviewModal(purchase) {
            EventBus.$emit('open-preview-modal', purchase);
        },
        deletePurchase(id) {
            Swal.fire({
                icon: "warning",
                title: "Pažnja",
                text: "Da li ste sigurni da želite da izbrišete kupovinu?",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                cancelButtonColor: "#c51f1a",
                confirmButtonText: "Da",
                cancelButtonText: "Ne"
            })
                .then((result) => {
                    if (result.value) {
                        axios.delete(`/admin/purchases/${id}`)
                            .then(response => {
                                if (response.data[0] === 'success') {
                                    swalSuccess("Uspješno ste izbrisali kupovinu!");
                                    EventBus.$emit('load-purchases');
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
        loadSearchedPurchases(page = 1) {
            axios.get(`/admin/purchases/${this.searchKeyword}/search?page=${page}`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.purchasesPagination = response.data[1];
                        this.purchases = response.data[1].data;
                    }
                });
        },
        searchPurchases() {
            if (!(this.searchKeyword === '')) {
                this.searchMode = true;
                axios.get(`/admin/purchases/${this.searchKeyword}/search`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.purchasesPagination = response.data[1];
                            this.purchases = response.data[1].data;
                        }
                    });
            } else {
                this.searchMode = false;
                this.loadPurchases();
            }
        },
        applyFilters() {
            this.searchMode = false;
            this.filtered = true;
            axios.get(this.buildQueryParams(1))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.purchasesPagination = response.data[1];
                        this.purchases = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        loadFiltered(page = 1) {
            axios.get(this.buildQueryParams(page))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.purchasesPagination = response.data[1];
                        this.purchases = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        buildQueryParams(page) {
            const queryParams = [];

            if (this.startDate) {
                queryParams.push(`dateFrom=${this.startDate}`);
            }

            if (this.endDate) {
                queryParams.push(`dateTo=${this.endDate}`);
            }

            queryParams.push(`page=${page}`);

            return '/admin/purchases' + (queryParams.length ? '?' + queryParams.join('&') : '');
        },
        removeFilters() {
            this.searchMode = false;
            this.filtered = false;
            this.startDate = '';
            this.endDate = '';
            this.searchKeyword = '';
            this.loadPurchases();
        }
    },
    mounted() {
        this.loadPurchases();
        this.$emit('loadBreadcrumbLink', {url: '/purchases', pageName: 'Kupovine'});
        EventBus.$on('load-purchases', () => this.loadPurchases());
    }
}

</script>

<style scoped>

</style>
