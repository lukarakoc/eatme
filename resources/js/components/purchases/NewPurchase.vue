<template>
    <div class="modal fade" id="new-purchase-modal" tabindex="-1" role="dialog"
         aria-labelledby="create-and-edit-modal-label" aria-hidden="true" ref="newPurchaseModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-and-edit-modal-label">Nova kupovina</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="addNewPurchase()">
                    <div class="modal-body">
                        <div class="form-group mx-2 mt-2">
                            <label for="productDropdown">Namirnice:</label>
                            <select id="productDropdown" v-model="selectedGrocery" class="form-control">
                                <option value="">Odaberi namirnicu</option>
                                <option v-for="grocery in purchaseForm.groceries" :key="grocery.id" :value="grocery">
                                    {{ grocery.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="selectedGrocery" class="form-group">
                            <label>{{ selectedGrocery.name }}</label>
                            <div style="display: flex; align-items: center;">
                                <input type="text" class="form-control" v-model="selectedGroceryAmount"
                                       placeholder="Količina KG/Kom." style="width: 150px;">
                                <button type="button" class="btn btn-sm btn-success ml-2"
                                        :disabled="!selectedGrocery || !selectedGroceryAmount" @click="addProduct"><i
                                    class="fas fa-plus"></i> Dodaj
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="product-list">
                            <div v-for="grocery in selectedGroceries" :key="grocery.name" class="product-item">
                                <div class="mt-1">
                                    <b>{{ grocery.name }}</b> - Količina: {{ grocery.quantity }} KG/Kom.
                                    <button type="button" class="btn btn-sm btn-danger"
                                            @click="removeGrocery(grocery.id)"><i class="fas fa-trash"></i> Ukloni
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger ml-1" v-if="this.purchaseErrors.groceriesErrorPresent">
                                {{ this.purchaseErrors.groceries }}
                            </small>
                        </div>
                        <hr>

                        <div class="form-group mx-2 mt-2">
                            <label for="notes">Bilješka</label>
                            <ckeditor id="notes"
                                      name="text"
                                      placeholder="Unesite bilješku"
                                      :editor="editor"
                                      :config="editorConfig"
                                      :class="{ 'border border-danger': this.purchaseErrors.notesErrorPresent }"
                                      v-model="purchaseForm.notes"></ckeditor>
                            <small class="text-danger" v-if="this.purchaseErrors.notesErrorPresent">
                                {{ this.purchaseErrors.notes }}
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
                            Sačuvaj kupovinu
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
    name: "NewPurchase",
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

            selectedGrocery: '',
            selectedGroceryAmount: '',
            selectedGroceries: [],
            groceries: [],
            purchaseForm: {
                notes: '',
                groceries: [],
            },
            purchaseErrors: {
                notes: '',
                notesErrorPresent: false,
                groceries: '',
                groceriesErrorPresent: false,
            }
        }

    },
    methods: {
        loadGroceries() {
            axios.get(`/admin/groceries/only`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.groceries = response.data[1];
                        this.createGroceryObjectsInForm();
                    }
                });
        },
        createGroceryObjectsInForm() {
            this.purchaseForm.groceries = this.groceries.map(grocery => ({...grocery, quantity: null}));
        },
        addNewPurchase() {
            this.resetNewPurchaseErrors();

            const form = new FormData;
            for (let i = 0; i < this.selectedGroceries.length; i++) {
                const obj = this.selectedGroceries[i];
                const payloadObj = {id: obj.id, quantity: this.replaceCommasWithDots(obj.quantity), unit_price: obj.unitPrice}
                form.append(`groceries[${i}]`, JSON.stringify(payloadObj));
            }

            if (typeof this.purchaseForm.notes === 'undefined' || this.purchaseForm.notes === '<p></p>') {
                form.append('notes', null);
            } else {
                form.append("notes", this.purchaseForm.notes);
            }

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/purchases/store`, form, config)
                .then(response => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === 'success') {
                        $('#new-purchase-modal').modal('hide');
                        EventBus.$emit('load-purchases');
                        swalSuccess("Kupovina je uspješno kreirana!");
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
        replaceCommasWithDots(text) {
            return text.replace(/,/g, '.');
        },
        resetNewPurchaseForm() {
            this.purchaseForm.notes = '';
            this.purchaseForm.groceries = [];
            this.selectedGrocery = ''
            this.selectedGroceryAmount = ''
            this.selectedGroceries = [];
            this.loadGroceries();
        },
        resetNewPurchaseErrors() {
            this.purchaseErrors.notes = '';
            this.purchaseErrors.notesErrorPresent = false;
            this.purchaseErrors.groceries = [];
            this.purchaseErrors.groceriesErrorPresent = false;
        },
        clearData() {
            this.resetNewPurchaseForm();
            this.resetNewPurchaseErrors();
            EventBus.$emit('load-purchases');
        },
        createNewPurchase() {
            this.editmode = false;
            this.resetNewPurchaseForm();
            this.resetNewPurchaseErrors();
            $('#new-purchase-modal').modal();
            $('.img-preview').attr('style', 'display: none !important');
        },
        checkForValidationErrors(errors) {

            if (errors.hasOwnProperty('notes')) {
                this.purchaseErrors.notes = errors["notes"][0];
                this.purchaseErrors.notesErrorPresent = true;
            }

            if (errors.hasOwnProperty('groceries')) {
                this.purchaseErrors.groceries = errors["groceries"][0];
                this.purchaseErrors.groceriesErrorPresent = true;
            }
        },
        removeGrocery(selectedGrocery) {
            const index = this.selectedGroceries.findIndex(grocery => grocery.id === selectedGrocery);
            if (index !== -1) {
                this.selectedGroceries.splice(index, 1);
            }
        },
        addProduct() {
            if (this.selectedGrocery && this.selectedGroceryAmount) {
                if (this.selectedGroceries.some(g => g.id === this.selectedGrocery.id)) {
                    return;
                }
                this.selectedGroceries.push({
                    id: this.selectedGrocery.id,
                    name: this.selectedGrocery.name,
                    quantity: this.selectedGroceryAmount,
                    unitPrice: this.selectedGrocery.unit_price
                });
                this.selectedGrocery = '';
                this.selectedGroceryAmount = '';
            }
        },
    },
    mounted() {
        EventBus.$on('open-new-purchase-modal', () => this.createNewPurchase());
        $(this.$refs.newPurchaseModalRef).on("hidden.bs.modal", this.clearData);
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

