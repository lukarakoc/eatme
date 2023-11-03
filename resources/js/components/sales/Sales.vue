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
                    <!-- By client -->
                    <div class="d-flex align-items-center ml-2">
                        <select id="client" v-model="byClient" class="form-control select2">
                            <option selected value="">Filtriraj po klijentu</option>
                            <option v-for="client in clients" :key="client.id" :value="client">
                                {{ client.name }}
                            </option>
                        </select>
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
                        @click="openNewSaleModal">
                    <i class="fas fa-euro-sign"></i> Novi unos prodaje
                </button>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center" width="10">ID</th>
                            <th class="text-center">Klijent</th>
                            <th class="text-center">Datum unosa</th>
                            <th class="text-center">Ukupno prodato</th>
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
                        <tr v-for="sale in sales" :key="sale.id">
                            <td class="text-center">{{ sale.id }}</td>
                            <td class="text-center">{{ sale.client.name }}</td>
                            <td class="text-center" v-html="sale.created_at"></td>
                            <td class="text-center"><span v-html="sale.total"></span>&euro;</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-info" @click="openPreviewModal(sale)">
                                    <i class="fas fa-eye"></i>
                                    Pregled
                                </button>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deleteSale(sale.id)">
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
                            :data="salesPagination"
                            :limit="1"
                            @pagination-change-page="loadSearchedSales">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && !filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="salesPagination"
                            :limit="1"
                            @pagination-change-page="loadSales">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="salesPagination"
                            :limit="1"
                            @pagination-change-page="loadFiltered">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
            </div>
        </div>
        <new-sale/>
        <preview-sale :sale="sales"/>
    </div>
</template>
<script>
import Spinner from "../Spinner";
import {EventBus, swalError, swalSuccess} from "../../main";
import NewSale from './NewSale';
import PreviewSale from "./PreviewSale";

export default {
    name: "Sales",
    components: {
        NewSale,
        PreviewSale,
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
            byClient: '',
            clients: [],
            sales: [],
            salesPagination: {}
        }
    },
    methods: {
        loadSales(page = 1) {
            axios.get(`/admin/sales?page=${page}`)
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.salesPagination = response.data[1];
                        this.sales = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        loadClients() {
            axios.get(`/admin/clients`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.clients = response.data[1];
                    }
                });
        },
        openNewSaleModal() {
            EventBus.$emit('open-new-sale-modal');
        },
        openPreviewModal(sale) {
            EventBus.$emit('open-preview-modal', sale);
        },
        deleteSale(id) {
            Swal.fire({
                icon: "warning",
                title: "Pažnja",
                text: "Da li ste sigurni da želite da izbrišete unos prodaje?",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                cancelButtonColor: "#c51f1a",
                confirmButtonText: "Da",
                cancelButtonText: "Ne"
            })
                .then((result) => {
                    if (result.value) {
                        axios.delete(`/admin/sales/${id}`)
                            .then(response => {
                                if (response.data[0] === 'success') {
                                    swalSuccess("Uspješno ste izbrisali unos prodaje!");
                                    EventBus.$emit('load-sales');
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
        loadSearchedSales(page = 1) {
            axios.get(`/admin/sales/${this.searchKeyword}/search?page=${page}`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.salesPagination = response.data[1];
                        this.sales = response.data[1].data;
                    }
                });
        },
        searchSales() {
            if (!(this.searchKeyword === '')) {
                this.searchMode = true;
                axios.get(`/admin/sales/${this.searchKeyword}/search`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.salesPagination = response.data[1];
                            this.sales = response.data[1].data;
                        }
                    });
            } else {
                this.searchMode = false;
                this.loadSales();
            }
        },
        applyFilters() {
            this.searchMode = false;
            this.filtered = true;
            axios.get(this.buildQueryParams(1))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.salesPagination = response.data[1];
                        this.sales = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        loadFiltered(page = 1) {
            axios.get(this.buildQueryParams(page))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.salesPagination = response.data[1];
                        this.sales = response.data[1].data;
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

            if (this.byClient) {
                queryParams.push(`client=${this.byClient.id}`);
            }

            queryParams.push(`page=${page}`);

            return '/admin/sales' + (queryParams.length ? '?' + queryParams.join('&') : '');
        },
        removeFilters() {
            this.searchMode = false;
            this.filtered = false;
            this.startDate = '';
            this.endDate = '';
            this.searchKeyword = '';
            this.byClient = '';
            this.loadSales();
        }
    },
    mounted() {
        this.loadSales();
        this.loadClients();
        this.$emit('loadBreadcrumbLink', {url: '/sales', pageName: 'Prodaja'});
        EventBus.$on('load-sales', () => this.loadSales());
    }
}

</script>

