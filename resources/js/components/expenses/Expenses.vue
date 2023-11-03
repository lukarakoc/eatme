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
                    <!-- By product -->
                    <div class="d-flex align-items-center ml-2">
                        <select id="product" v-model="byType" class="form-control select2">
                            <option selected value="">Tip troška</option>
                            <option v-for="type in types" :key="type.id" :value="type">
                                {{ type.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <button class="btn btn-sm btn-primary ml-2" @click="applyFilters"><i class="fas fa-filter"></i> Filter</button>

                    <button class="btn btn-sm btn-danger ml-1" @click="removeFilters"><i class="fas fa-times"></i> Ukloni</button>
                </div>
            </div>


            <div class="card-body pt-2">
                <button class="btn btn-sm btn-success float-right mb-2 ml-1"
                        @click="openNewExpenseModal">
                    <i class="fas fa-calculator"></i> Dodaj trošak
                </button>
                <div class="table-responsive">
                    <table id="datatable" class="table table-bordered table-striped text-center">
                        <thead>
                        <tr>
                            <th class="text-center" width="10">ID</th>
                            <th class="text-center">Tip troška</th>
                            <th class="text-center">Detalji</th>
                            <th class="text-center">Datum</th>
                            <th class="text-center">Ukupno</th>
<!--                            <th class="text-center" width="150">Pregled</th>-->
                            <th class="text-center" width="150">Izbriši</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="pageIsLoading">
                            <td class="text-center" colspan="9">
                                <spinner/>
                            </td>
                        </tr>
                        <tr v-for="e in expenses" :key="e.id">
                            <td class="text-center">{{ e.id }}</td>
                            <td class="text-center">{{ e.expense_type.name }}</td>
                            <td class="text-center" v-html="e.notes"></td>
                            <td class="text-center" v-html="e.created_at"></td>
                            <td class="text-center"><span v-html="e.amount"></span>&euro;</td>
<!--                            <td class="text-center">-->
<!--                                <button class="btn btn-sm btn-info" @click="openPreviewModal(e)">-->
<!--                                    <i class="fas fa-eye"></i>-->
<!--                                    Pregled-->
<!--                                </button>-->
<!--                            </td>-->
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" @click="deleteExpense(e.id)">
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
                            :data="expensesPagination"
                            :limit="1"
                            @pagination-change-page="loadSearchedExpenses">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && !filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="expensesPagination"
                            :limit="1"
                            @pagination-change-page="loadExpenses">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
                <pagination v-show="!searchMode && filtered"
                            class="mt-2"
                            align="right"
                            show-disabled
                            :data="expensesPagination"
                            :limit="1"
                            @pagination-change-page="loadFiltered">
                    <span slot="prev-nav">Prethodna</span>
                    <span slot="next-nav">Sledeća</span>
                </pagination>
            </div>
        </div>
        <new-expense/>
        <preview-expenses :expense="expenses"/>
    </div>
</template>
<script>
import Spinner from "../Spinner";
import {EventBus, swalError, swalSuccess} from "../../main";
import PreviewExpenses from "./PreviewExpenses";
import NewExpense from "./NewExpense";

export default {
    name: "Expenses",
    components: {
        NewExpense,
        PreviewExpenses,
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
            byType: '',
            types: [],
            expenses: [],
            expensesPagination: {}
        }
    },
    methods: {
        loadExpenses(page = 1) {
            axios.get(`/admin/expenses?page=${page}`)
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.expensesPagination = response.data[1];
                        this.expenses = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        loadTypes() {
            axios.get(`/admin/expenses/types`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.types = response.data[1];
                    }
                });
        },
        openNewExpenseModal() {
            EventBus.$emit('open-new-expense-modal');
        },
        openPreviewModal(expense) {
            EventBus.$emit('open-preview-modal', expense);
        },
        deleteExpense(id) {
            Swal.fire({
                icon: "warning",
                title: "Pažnja",
                text: "Da li ste sigurni da želite da izbrišete trošak?",
                showCancelButton: true,
                confirmButtonColor: "#38c172",
                cancelButtonColor: "#c51f1a",
                confirmButtonText: "Da",
                cancelButtonText: "Ne"
            })
                .then((result) => {
                    if (result.value) {
                        axios.delete(`/admin/expenses/${id}`)
                            .then(response => {
                                if (response.data[0] === 'success') {
                                    swalSuccess("Uspješno ste izbrisali trošak!");
                                    EventBus.$emit('load-expenses');
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
        loadSearchedExpenses(page = 1) {
            if (this.searchKeyword !== null && this.searchKeyword !== '') {
                axios.get(`/admin/expenses/${this.searchKeyword}/search?page=${page}`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.expensesPagination = response.data[1];
                            this.expenses = response.data[1].data;
                        }
                    });
            } else {
                axios.get(this.buildQueryParams(page))
                    .then(response => {
                        if (response.data[0] === 'success') {
                            this.expensesPagination = response.data[1];
                            this.expenses = response.data[1].data;
                            this.pageIsLoading = false;
                        }
                    });
            }
        },
        searchExpenses(page = 1) {
            if (!(this.searchKeyword === '')) {
                this.searchMode = true;
                axios.get(`/admin/expenses/${this.searchKeyword}/search?page=${page}`)
                    .then(response => {
                        if (response.data[0] === "success") {
                            this.expensesPagination = response.data[1];
                            this.expenses = response.data[1].data;
                        }
                    });
            } else {
                this.searchMode = false;
                this.loadExpenses();
            }
        },
        applyFilters() {
            this.searchMode = false;
            this.filtered = true;
            axios.get(this.buildQueryParams(1))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.expensesPagination = response.data[1];
                        this.expenses = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        loadFiltered(page = 1) {
            axios.get(this.buildQueryParams(page))
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.expensesPagination = response.data[1];
                        this.expenses = response.data[1].data;
                        this.pageIsLoading = false;
                    }
                });
        },
        buildQueryParams(page) {
            console.log(page);
            const queryParams = [];

            if (this.startDate) {
                queryParams.push(`dateFrom=${this.startDate}`);
            }

            if (this.endDate) {
                queryParams.push(`dateTo=${this.endDate}`);
            }

            if (this.byType) {
                queryParams.push(`type=${this.byType.id}`);
            }

            queryParams.push(`page=${page}`);

            return '/admin/expenses' + (queryParams.length ? '?' + queryParams.join('&') : '');
        },
        removeFilters() {
            this.searchMode = false;
            this.filtered = false;
            this.startDate = '';
            this.endDate = '';
            this.searchKeyword = '';
            this.byType = '';
            this.loadExpenses();
        }
    },
    mounted() {
        this.loadExpenses();
        this.loadTypes();
        this.$emit('loadBreadcrumbLink', {url: '/expenses', pageName: 'Troškovi'});
        EventBus.$on('load-expenses', () => this.loadExpenses());
    }
}

</script>

