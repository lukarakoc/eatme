<template>
    <div class="modal fade" id="purchase-preview-modal" tabindex="-1" role="dialog"
         aria-labelledby="purchase-preview-label" aria-hidden="true" ref="previewPurchaseModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="create-and-edit-modal-label">Pregled kupovine</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mx-2 mt-2" style="white-space: nowrap" v-for="(g, i) in groceries">
                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Left side with name and image -->
                            <div class="d-flex align-items-center">
                                <img :src="getImageSource(g.grocery.image_path)" alt="Grocery Image" style="width: 75px; height: auto;" class="mr-2">
                                <h5 class="mb-0">{{ g.grocery.name }}</h5>
                            </div>

                            <!-- Right side with quantity, unit price, and total -->
                            <div>
                                <span>{{ g.quantity }} x {{ g.unit_price }} &euro; | <b>{{ g.total }} &euro;</b></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" v-show="this.purchaseNotes !== ''">
                        <span><b>Bilješka:</b></span>
                        <div v-html="this.purchaseNotes"></div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5 class="mb-0">Ukupno: <b>{{ purchaseTotal }} &euro;</b></h5>
                        <h5 class="mb-0">Datum kupovine: <b>{{ purchaseDate }}</b></h5>
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
    name: "PreviewPurchase",
    data() {
        return {
            groceries: [],
            purchaseDate: '',
            purchaseTotal: '',
            purchaseNotes: ''
        }
    },
    methods: {
        previewPurchase(purchase) {
            this.groceries = purchase.purchase_details;
            this.purchaseDate = purchase.created_at;
            this.purchaseTotal = purchase.total;
            this.purchaseNotes = purchase.notes;

            $('#purchase-preview-modal').modal("show");
        },
        getImageSource(imagePath) {
            // Check if imagePath is empty or null, and return the appropriate image source
            return imagePath ? imagePath : '/placeholder.jpeg';
        },
    },
    mounted() {
        EventBus.$on('open-preview-modal', purchase => this.previewPurchase(purchase));
    }
}
</script>

<style scoped>

</style>
