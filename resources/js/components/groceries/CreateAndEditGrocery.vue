<template>
    <div class="modal fade" id="create-and-edit-modal" tabindex="-1" role="dialog"
         aria-labelledby="create-and-edit-modal-label" aria-hidden="true" ref="createAndEditModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-show="editmode" class="modal-title" id="create-and-edit-modal-label">Izmijeni namirnicu/proizvod</h5>
                    <h5 v-show="!editmode" class="modal-title" id="create-and-edit-modal-label">Dodaj namirnicu/proizvod</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updateGrocery() : storeGrocery()">
                    <div class="modal-body">
                        <div class="form-group mx-2 mt-2">
                            <label for="name">Naziv namirnice/proizvoda *</label>
                            <input id="name"
                                   class="form-control"
                                   type="text"
                                   name="name"
                                   placeholder="Unesite naziv namirnice"
                                   :class="{ 'border border-danger': this.groceryErrors.nameErrorPresent }"
                                   v-model="groceryForm.name">
                            <small class="text-danger" v-if="this.groceryErrors.nameErrorPresent">
                                {{ groceryErrors.name }}
                            </small>
                        </div>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label for="unit_price">Cijena namirnice/proizvoda po KG/Kom.*</label>
                            <input id="unit_price"
                                   class="form-control"
                                   type="text"
                                   name="unit_price"
                                   placeholder="Unesite cijenu namirnice"
                                   :class="{ 'border border-danger': this.groceryErrors.unitPriceErrorPresent }"
                                   v-model="groceryForm.unitPrice">
                            <small class="text-danger" v-if="this.groceryErrors.unitPriceErrorPresent">
                                {{ this.groceryErrors.unitPrice }}
                            </small>
                        </div>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label class="checkbox">
                                <span for="isProduct">Da li je izlazni proizvod? *</span>
                                <input
                                    type="checkbox"
                                    id="isProduct"
                                    name="isProduct"
                                    v-model="groceryForm.isProduct"
                                />
                            </label>
                        </div>
                        <hr>
                        <div class="form-group mx-2 my-2">
                            <label>Slika </label>
                            <div>
                                <img class="rounded"
                                     width="100"
                                     alt="Slika"
                                     v-if="checkIfNotEmpty(loadImage) || checkIfNotEmpty(groceryForm.image)"
                                     :src="loadImage !== '' ? loadImage : groceryForm.image">
                            </div>
                            <div class="custom-file mt-2">
                                <input type="file"
                                       class="custom-file-input"
                                       id="image"
                                       name="header_image"
                                       @change="loadImageFile"/>
                                <label class="custom-file-label"
                                       for="image"
                                       :class="{ 'border border-danger': groceryErrors.imageErrorPresent }">Odaberi
                                    sliku</label>
                            </div>
                            <small class="text-danger" v-if="groceryErrors.imageErrorPresent">
                                {{ groceryErrors.image }}
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Odustani</button>
                        <button id="updateUser"
                                class="btn btn-primary"
                                type="submit"
                                v-show="editmode"
                                @click="storeUpdateDisabled = true">
                            Sačuvaj
                            <span class="spinner-border-sm"
                                  role="status"
                                  aria-hidden="true"
                                  :class="{ 'spinner-border': storeUpdateDisabled }"></span>
                        </button>
                        <button id="storeUser"
                                class="btn btn-primary"
                                type="submit"
                                v-show="!editmode"
                                @click="storeUpdateDisabled = true">
                            Dodaj
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

import {checkIfEveryNotEmpty, checkIfNotEmpty, EventBus, getFileNameFromPath, swalSuccess} from "../../main";
import ImageUploader from "vue-image-upload-resize";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    name: "CreateAndEditGrocery",
    components: {
        ImageUploader
    },
    data() {
        return {
            hasImage: true,
            image: null,
            storeUpdateDisabled: false,
            editmode: true,
            loadImage: null,
            editor: ClassicEditor,
            languages: [],
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
            groceryForm: {
                id: '',
                name: '',
                stock: '',
                unitPrice: '',
                image: '',
                isProduct: false,
                loadImage: ''
            },
            groceryErrors: {
                name: '',
                nameErrorPresent: false,
                stock: '',
                stockErrorPresent: false,
                unitPrice: '',
                unitPriceErrorPresent: false,
                image: '',
                imageErrorPresent: false
            }
        }
    },
    methods: {
        createGrocery() {
            this.editmode = false;
            this.resetGroceryForm();
            this.resetGroceryFormErrors();
            $('#create-and-edit-modal').modal();
            $('.img-preview').attr('style', 'display: none !important');
        },
        editGrocery(grocery) {
            this.editmode = true;
            this.resetGroceryForm();
            this.resetGroceryFormErrors();
            this.fillGroceryForm(grocery);
            $('#create-and-edit-modal').modal("show");
            $('.img-preview').attr('style', 'display: none !important');
        },
        booleanValue(input) {
            if (input === true) {
                return 'true';
            }
            if (input === false) {
                return 'false';
            }
        },
        storeGrocery() {
            this.resetGroceryFormErrors();
            const form = new FormData;
            form.append('name', this.groceryForm.name);
            form.append('unit_price', this.groceryForm.unitPrice);
            form.append('is_product', this.booleanValue(this.groceryForm.isProduct));
            if (this.groceryForm.image != null) {
                form.append('image', this.groceryForm.image)
            }

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/groceries/store`, form, config)
                .then(response => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === 'success') {
                        $('#create-and-edit-modal').modal('hide');
                        EventBus.$emit('load-groceries');
                        swalSuccess("Namirnica je uspješno dodata");
                    }
                }).catch(error => {
                this.storeUpdateDisabled = false;
                if (error.response.status === 422 || error.response.status === 403) {
                    this.checkForValidationErrors(error.response.data.errors);
                }
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            });
        },
        updateGrocery() {
            this.resetGroceryFormErrors();
            const form = new FormData;
            form.append('id', this.groceryForm.id);
            form.append('name', this.groceryForm.name);
            form.append('is_product', this.groceryForm.isProduct);
            form.append('unit_price', this.groceryForm.unitPrice);
            if (this.groceryForm.image != null) {
                form.append('image', this.groceryForm.image);
            }

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/groceries/update`, form, config)
                .then(response => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === 'success') {
                        $('#create-and-edit-modal').modal('hide');
                        EventBus.$emit('load-groceries');
                        swalSuccess("Namirnica je uspješno izmijenjena");
                    }
                }).catch(error => {
                this.storeUpdateDisabled = false;
                if (error.response.status === 422 || error.response.status === 403) {
                    this.checkForValidationErrors(error.response.data.errors);
                }
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            });
        },
        resetGroceryForm() {
            this.groceryForm.id = '';
            this.groceryForm.name = '';
            this.groceryForm.stock = '';
            this.groceryForm.unitPrice = [];
            $('#image').val('');
            this.groceryForm.loadImage = '';
            this.groceryForm.image = '';
            this.groceryForm.isProduct = false;
        },
        resetGroceryFormErrors() {
            this.groceryErrors.name = [];
            this.groceryErrors.nameErrorPresent = false;
            this.groceryErrors.stock = [];
            this.groceryErrors.stockErrorPresent = false;
            this.groceryErrors.unitPrice = '';
            this.groceryErrors.unitPriceErrorPresent = false;
            this.groceryErrors.image = '';
            this.groceryErrors.imageErrorPresent = false;
        },
        fillGroceryForm(grocery) {
            this.groceryForm.id = grocery.id
            this.groceryForm.name = grocery.name;
            this.groceryForm.stock = grocery.stock;
            this.groceryForm.unitPrice = grocery.unit_price;
            this.loadImage = grocery.image_path;
            this.groceryForm.isProduct = grocery.is_product;
        },
        checkForValidationErrors(errors) {

            if (errors.hasOwnProperty("name")) {
                this.groceryErrors.name = errors["name"][0];
                this.groceryErrors.nameErrorPresent = true;
            }
            if (errors.hasOwnProperty('stock')) {
                this.groceryErrors.stock = errors['stock'][0];
                this.groceryErrors.stockErrorPresent = true;
            }
            if (errors.hasOwnProperty('unit_price')) {
                this.groceryErrors.unitPrice = errors['unit_price'][0];
                this.groceryErrors.unitPriceErrorPresent = true;
            }
            if (errors.hasOwnProperty('image')) {
                this.groceryErrors.image = errors['image'][0];
                this.groceryErrors.imageErrorPresent = true;
            }
        },
        checkImageFileSize(imagFile, limitInMb) {
            return imagFile.size > (limitInMb * 1024 * 1024)
        },
        loadImageFile(e) {
            let imageInput = e.target;
            if (imageInput.files && imageInput.files[0]) {
                this.groceryForm.image = imageInput.files[0];
                const reader = new FileReader();
                reader.onload = evt => this.loadImage = evt.target.result;
                reader.readAsDataURL(imageInput.files[0]);
            }
            imageInput.value = "";
        },
        clearData() {
            this.resetGroceryFormErrors();
            // EventBus.$emit('load-groceries');
        },
        checkIfNotEmpty(value) {
            return checkIfNotEmpty(value);
        },
        checkIfEveryNotEmpty(values) {
            return checkIfEveryNotEmpty(values);
        },
        getFileNameFromPath(path) {
            return getFileNameFromPath(path);
        }
    },
    mounted() {
        EventBus.$on('open-create-modal', () => this.createGrocery());
        EventBus.$on('open-edit-modal', grocery => this.editGrocery(grocery));
        $(this.$refs.createAndEditModalRef).on("hidden.bs.modal", this.clearData);
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
