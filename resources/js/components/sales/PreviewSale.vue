<template>
    <div class="modal fade" id="sale-preview-modal" tabindex="-1" role="dialog"
         aria-labelledby="sale-preview-label" aria-hidden="true" ref="previewSaleModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-and-edit-modal-label">Pregled unosa prodaje</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mx-2 mt-2" style="white-space: nowrap" v-for="(p, i) in products">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Left side with name and image -->
                            <div class="d-flex align-items-center">
                                <img :src="getImageSource(p.grocery.image_path)" alt="Grocery Image" style="width: 75px; height: auto;" class="mr-2">
                                <h5 class="mb-0">{{ p.grocery.name }}</h5>
                            </div>

                            <!-- Right side with quantity, unit price, and total -->
                            <div>
                                <span>{{ p.quantity }} x {{ p.unit_price }} &euro; | <b>{{ p.total }} &euro;</b></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" v-show="this.saleNotes !== ''">
                        <span><b>Bilješka:</b></span>
                        <div v-html="this.saleNotes"></div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Ukupno: <b>{{ saleTotal }} &euro;</b></h5>
                        <h5 class="mb-0">Datum prodaje: <b>{{ saleDate }}</b></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zatvori</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import {EventBus} from "../../main";

export default {
    name: "PreviewSale",
    data() {
        return {
            products: [],
            saleDate: '',
            saleTotal: '',
            saleNotes: ''
        }
    },
    methods: {
        previewSale(sale) {
            this.products = sale.sale_details;
            this.saleDate = sale.created_at;
            this.saleTotal = sale.total;
            this.saleNotes = sale.notes;

            $('#sale-preview-modal').modal("show");
        },
        getImageSource(imagePath) {
            // Check if imagePath is empty or null, and return the appropriate image source
            return imagePath ? imagePath : '/placeholder.jpeg';
        },
    },
    mounted() {
        EventBus.$on('open-preview-modal', sale => this.previewSale(sale));
    }
}
</script>

<style scoped>

</style>
