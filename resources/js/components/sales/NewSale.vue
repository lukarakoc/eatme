<template>
    <div class="modal fade" id="new-sale-modal" tabindex="-1" role="dialog"
         aria-labelledby="create-and-edit-modal-label" aria-hidden="true" ref="newSaleModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-and-edit-modal-label">Novi unos prodaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="addNewSale()">
                    <div class="modal-body">
                        <div class="form-group mx-2 mt-2">
                            <label for="productDropdown">Proizvodi:</label>
                            <select id="productDropdown" v-model="selectedProduct" class="form-control">
                                <option value="">Odaberi proizvod</option>
                                <option v-for="product in saleForm.products" :key="product.id" :value="product">
                                    {{ product.name }}
                                </option>
                            </select>
                        </div>
                        <div v-if="selectedProduct" class="form-group">
                            <label>{{ selectedProduct.name }}</label>
                            <div style="display: flex; align-items: center;">
                                <input type="text" class="form-control" v-model="selectedProductAmount"
                                       placeholder="Količina KG/Kom." style="width: 150px;">
                                <button type="button" class="btn btn-sm btn-success ml-2"
                                        :disabled="!selectedProduct || !selectedProductAmount" @click="addProduct"><i
                                    class="fas fa-plus"></i> Dodaj
                                </button>
                            </div>
                        </div>
                        <hr>
                        <div class="product-list">
                            <div v-for="product in selectedProducts" :key="product.name" class="product-item">
                                <div class="mt-1">
                                    <b>{{ product.name }}</b> - Količina: {{ product.quantity }} KG/Kom.
                                    <button type="button" class="btn btn-sm btn-danger"
                                            @click="removeProduct(product.id)"><i class="fas fa-trash"></i> Ukloni
                                    </button>
                                </div>
                            </div>
                            <small class="text-danger ml-1" v-if="this.saleErrors.productErrorPresent">
                                {{ this.saleErrors.products }}
                            </small>
                        </div>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label for="clientDropdown">Klijent:</label>
                            <select id="clientDropdown" v-model="selectedClient" class="form-control">
                                <option value="">Odaberi klijenta</option>
                                <option v-for="client in this.clients" :key="client.id" :value="client">
                                    {{ client.name }}
                                </option>
                            </select>
                        </div>
                        <hr>

                        <div class="form-group mx-2 mt-2">
                            <label for="notes">Bilješka</label>
                            <ckeditor id="notes"
                                      name="text"
                                      placeholder="Unesite bilješku"
                                      :editor="editor"
                                      :config="editorConfig"
                                      :class="{ 'border border-danger': this.saleErrors.notesErrorPresent }"
                                      v-model="saleForm.notes"></ckeditor>
                            <small class="text-danger" v-if="this.saleErrors.notesErrorPresent">
                                {{ this.saleErrors.notes }}
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
                            Sačuvaj unos
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
    name: "NewSale",
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

            selectedProduct: '',
            selectedClient: '',
            selectedProductAmount: '',
            selectedProducts: [],
            products: [],
            clients: [],
            saleForm: {
                notes: '',
                products: [],
            },
            saleErrors: {
                notes: '',
                notesErrorPresent: false,
                products: '',
                productErrorPresent: false,
            }
        }

    },
    methods: {
        loadProducts() {
            axios.get(`/admin/groceries/products`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.products = response.data[1];
                        this.createProductObjectsInForm();
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
        createProductObjectsInForm() {
            this.saleForm.products = this.products.map(product => ({...product, quantity: null}));
        },
        addNewSale() {
            this.resetNewSaleErrors();

            const form = new FormData;
            for (let i = 0; i < this.selectedProducts.length; i++) {
                const obj = this.selectedProducts[i];
                const payloadObj = {id: obj.id, quantity: this.replaceCommasWithDots(obj.quantity), unit_price: obj.unitPrice}
                form.append(`groceries[${i}]`, JSON.stringify(payloadObj));
            }

            if (this.selectedClient !== null && this.selectedClient !== '') {
                form.append('client', this.selectedClient.id);
            }

            if (typeof this.saleForm.notes === 'undefined' || this.saleForm.notes === '<p></p>') {
                form.append('notes', null);
            } else {
                form.append("notes", this.saleForm.notes);
            }

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/sales/store`, form, config)
                .then(response => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === 'success') {
                        $('#new-sale-modal').modal('hide');
                        EventBus.$emit('load-sales');
                        swalSuccess("Unos prodaje je uspješno kreiran!");
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
        resetNewSaleForm() {
            this.saleForm.notes = '';
            this.saleForm.products = [];
            this.selectedProduct = ''
            this.selectedClient = ''
            this.selectedProductAmount = ''
            this.selectedProducts = [];
            this.clients = [];
            this.loadProducts();
            this.loadClients();
        },
        resetNewSaleErrors() {
            this.saleErrors.notes = '';
            this.saleErrors.notesErrorPresent = false;
            this.saleErrors.products = [];
            this.saleErrors.productErrorPresent = false;
        },
        clearData() {
            this.resetNewSaleForm();
            this.resetNewSaleErrors();
            EventBus.$emit('load-sales');
        },
        createNewSale() {
            this.editmode = false;
            this.resetNewSaleForm();
            this.resetNewSaleErrors();
            $('#new-sale-modal').modal();
            $('.img-preview').attr('style', 'display: none !important');
        },
        checkForValidationErrors(errors) {

            if (errors.hasOwnProperty('notes')) {
                this.saleErrors.notes = errors["notes"][0];
                this.saleErrors.notesErrorPresent = true;
            }

            if (errors.hasOwnProperty('groceries')) {
                this.saleErrors.products = errors["groceries"][0];
                this.saleErrors.productErrorPresent = true;
            }
        },
        removeProduct(selectedProduct) {
            const index = this.selectedProducts.findIndex(product => product.id === selectedProduct);
            if (index !== -1) {
                this.selectedProducts.splice(index, 1);
            }
        },
        addProduct() {
            if (this.selectedProduct && this.selectedProductAmount) {
                if (this.selectedProducts.some(g => g.id === this.selectedProduct.id)) {
                    return;
                }
                this.selectedProducts.push({
                    id: this.selectedProduct.id,
                    name: this.selectedProduct.name,
                    quantity: this.selectedProductAmount,
                    unitPrice: this.selectedProduct.unit_price
                });
                this.selectedProduct = '';
                this.selectedProductAmount = '';
            }
        },
    },
    mounted() {
        EventBus.$on('open-new-sale-modal', () => this.createNewSale());
        $(this.$refs.newSaleModalRef).on("hidden.bs.modal", this.clearData);
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

