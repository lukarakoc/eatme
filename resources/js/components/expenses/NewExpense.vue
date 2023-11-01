<template>
    <div class="modal fade" id="new-expense-modal" tabindex="-1" role="dialog"
         aria-labelledby="create-and-edit-modal-label" aria-hidden="true" ref="newExpenseModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-and-edit-modal-label">Novi trošak</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="addNewExpense()">
                    <div class="modal-body">
                        <div class="form-group mx-2 mt-2">
                            <label for="productDropdown">Tip troška:</label>
                            <select id="productDropdown" v-model="expensesForm.type" class="form-control">
                                <option value="">Odaberi</option>
                                <option v-for="type in types" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <small class="text-danger ml-1" v-if="this.expensesErrors.typeErrorPresent">
                                {{ this.expensesErrors.type }}
                            </small>
                        </div>
                        <hr>

                        <div class="form-group mx-2 mt-2">
                            <label for="notes">Detalji</label>
                            <ckeditor id="notes"
                                      name="text"
                                      placeholder="Unesite bilješku"
                                      :editor="editor"
                                      :config="editorConfig"
                                      :class="{ 'border border-danger': this.expensesErrors.notesErrorPresent }"
                                      v-model="expensesForm.notes"></ckeditor>
                            <small class="text-danger" v-if="this.expensesErrors.notesErrorPresent">
                                {{ this.expensesErrors.notes }}
                            </small>
                            <hr>
                        </div>
                        <div class="form-group mx-2 mt-2">
                            <label for="notes">Iznos troška u &euro;</label>
                            <input type="text" class="form-control" v-model="expensesForm.amount" placeholder="Iznos">
                            <small class="text-danger" v-if="this.expensesErrors.notesErrorPresent">
                                {{ this.expensesErrors.notes }}
                            </small>
                            <hr>
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                        <button id="updateUser"
                                class="btn btn-primary"
                                type="submit"
                                @click="storeUpdateDisabled = true">
                            Sačuvaj
                            <span class="spinner-border-sm"
                                  role="status"
                                  aria-hidden="true"
                                  :class="{ 'spinner-border': storeUpdateDisabled }"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import {EventBus, swalSuccess} from "../../main";

export default {
    name: "NewExpense",
    data() {
        return {
            storeUpdateDisabled: false,
            editmode: false,
            editor: ClassicEditor,
            editorConfig: {
                toolbar: {
                    items: [
                        'heading',
                        'bold',
                        'italic',
                        'link',
                        'BulletedList',
                        'numberedList',
                        'undo',
                        'redo'
                    ]
                }
            },
            config: {
                wrap: true,
                altInput: true,
                dateFormat: 'd.m.Y.',
                altFormat: 'd.m.Y.'
            },

            types: [],
            expensesForm: {
                notes: '',
                amount: '',
                type: ''
            },
            expensesErrors: {
                notes: '',
                notesErrorPresent: false,
                amount: '',
                amountErrorPresent: false,
                type: '',
                typeErrorPresent: false,
            }
        }

    },
    methods: {
        loadExpenseTypes() {
            axios.get(`/admin/expenses/types`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.types = response.data[1];
                    }
                });
        },
        replaceCommasWithDots(text) {
            return text.replace(/,/g, '.');
        },
        addNewExpense() {
            this.resetNewExpenseErrors();

            const form = new FormData;

            if (typeof this.expensesForm.notes === 'undefined' || this.expensesForm.notes === '<p></p>') {
                form.append('notes', null);
            } else {
                form.append("notes", this.expensesForm.notes);
            }

            form.append("type", this.expensesForm.type);
            form.append("amount", this.replaceCommasWithDots(this.expensesForm.amount));

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/expenses/store`, form, config)
                .then(response => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === 'success') {
                        $('#new-expense-modal').modal('hide');
                        EventBus.$emit('load-expenses');
                        swalSuccess("Trošak je uspješno dodat!");
                    }
                }).catch(error => {
                this.storeUpdateDisabled = false;
                if (error.response.status === 422) {
                    this.checkForValidationErrors(error.response.data.errors);
                }
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            });
        },
        resetNewExpenseForm() {
            this.expensesForm.notes = '';
            this.expensesForm.amount = '';
            this.expensesForm.type = '';
            this.loadExpenseTypes();
        },
        resetNewExpenseErrors() {
            this.expensesErrors.notes = '';
            this.expensesErrors.notesErrorPresent = false;
            this.expensesErrors.amount = '';
            this.expensesErrors.amountErrorPresent = false;
            this.expensesErrors.type = '';
            this.expensesErrors.typeErrorPresent = false;
        },
        clearData() {
            this.resetNewExpenseForm();
            this.resetNewExpenseErrors();
            EventBus.$emit('load-expenses');
        },
        createNewExpense() {
            this.editmode = false;
            this.resetNewExpenseForm();
            this.resetNewExpenseErrors();
            $('#new-expense-modal').modal();
            $('.img-preview').attr('style', 'display: none !important');
        },
        checkForValidationErrors(errors) {

            if (errors.hasOwnProperty('notes')) {
                this.expensesErrors.notes = errors["notes"][0];
                this.expensesErrors.notesErrorPresent = true;
            }

            if (errors.hasOwnProperty('amount')) {
                this.expensesErrors.amount = errors["amount"][0];
                this.expensesErrors.amountErrorPresent = true;
            }

            if (errors.hasOwnProperty('type')) {
                this.expensesErrors.type = errors["type"][0];
                this.expensesErrors.typeErrorPresent = true;
            }
        }
    },
    mounted() {
        EventBus.$on('open-new-expense-modal', () => this.createNewExpense());
        $(this.$refs.newExpenseModalRef ).on("hidden.bs.modal", this.clearData);
    }
}
</script>

<style scoped>
.container {
    position: relative;
}

.image {
    display: block;
    width: 150px;
}

.overlay {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    height: auto;
    width: 170px;
    opacity: 0;
    transition: .5s ease;
    background-color: #008CBA;
}

.container:hover .overlay {
    opacity: 1;
}

.text {
    color: white;
    font-size: 16px;
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    text-align: center;
}
</style>

