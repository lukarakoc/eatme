<template>
    <div class="modal fade" id="create-and-edit-modal" tabindex="-1" role="dialog"
         aria-labelledby="create-and-edit-modal-label" aria-hidden="true" ref="createAndEditModalRef">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-show="editmode" class="modal-title" id="create-and-edit-modal-label">Izmijeni korisnika</h5>
                    <h5 v-show="!editmode" class="modal-title" id="create-and-edit-modal-label">Dodaj korisnika</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form @submit.prevent="editmode ? updateUser() : storeUser()">
                    <div class="modal-body">
                        <div class="form-group mx-2 mt-2">
                            <label for="names">Ime i prezime *</label>
                            <input id="names"
                                   class="form-control"
                                   type="text"
                                   name="name"
                                   placeholder="Unesite ime i prezime"
                                   :class="{ 'border border-danger': usersErrors.nameErrorPresent }"
                                   v-model="usersForm.name">
                            <small class="text-danger" v-if="usersErrors.nameErrorPresent">
                                {{ usersErrors.name }}
                            </small>
                        </div>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label for="email">Email *</label>
                            <input type="text"
                                   class="form-control"
                                   id="email"
                                   placeholder="Unesite email"
                                   :class="{ 'border border-danger': usersErrors.emailErrorPresent}"
                                   v-model="usersForm.email">
                            <small class="text-danger" v-if="usersErrors.emailErrorPresent">
                                {{ usersErrors.email }}
                            </small>
                        </div>
                        <hr>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label for="password">Lozinka *</label>
                            <input type="password"
                                   class="form-control"
                                   id="password"
                                   :placeholder="!editmode ? 'Unesite lozinku' : 'Unesite novu lozinku'"
                                   :class="{ 'border border-danger': usersErrors.passwordErrorPresent}"
                                   v-model="usersForm.password">
                            <small class="text-danger" v-if="usersErrors.passwordErrorPresent">
                                {{ usersErrors.password }}
                            </small>
                        </div>
                        <hr>
                        <div class="form-group mx-2 mt-2">
                            <label for="role">Uloga *</label>
                            <select name="role"
                                    id="role"
                                    class="form-control"
                                    :class="{ 'border border-danger': usersErrors.roleErrorPresent }"
                                    v-model="usersForm.role">
                                <option value="" disabled selected>Izaberi ulogu</option>
                                <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
                            </select>
                            <small class="text-danger" v-if="usersErrors.roleErrorPresent">
                                {{ usersErrors.role }}
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
import {EventBus, swalError, swalSuccess} from "../../main.js";

export default {
    data() {
        return {
            storeUpdateDisabled: false,
            editmode: true,
            roles: [],
            usersForm: {
                id: '',
                name: '',
                email: '',
                password: '',
                role: '',
            },
            usersErrors: {
                name: '',
                nameErrorPresent: false,
                email: '',
                emailErrorPresent: false,
                password: '',
                passwordErrorPresent: false,
                role: '',
                roleErrorPresent: false,
            },
        }
    },
    methods: {
        createUser() {
            this.editmode = false;
            this.resetUsersForm();
            this.resetUsersErrors();
            $('#create-and-edit-modal').modal('show');
        },
        getAllRoles() {
            axios.get(`/admin/roles`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.roles = response.data[1];
                    }
                });
        },
        editUser(user) {
            this.editmode = true;
            this.resetUsersForm();
            this.resetUsersErrors();
            this.fillUserForm(user);
            $('#create-and-edit-modal').modal("show");
        },
        fillUserForm(user) {
            this.usersForm.id = user.id;
            this.usersForm.name = user.name;
            this.usersForm.email = user.email;
            this.usersForm.role = user.role_id;
        },
        resetUsersForm() {
            this.usersForm.id = '';
            this.usersForm.name = '';
            this.usersForm.email = '';
            this.usersForm.role = '';
            $('#role option').prop('selected', function () {
                return this.defaultSelected;
            });
            this.usersForm.password = '';
        },
        resetUsersErrors() {
            this.usersErrors.name = "";
            this.usersErrors.nameErrorPresent = false;
            this.usersErrors.email = '';
            this.usersErrors.emailErrorPresent = false;
            this.usersErrors.role = '';
            this.usersErrors.roleErrorPresent = false;
            this.usersErrors.password = '';
            this.usersErrors.passwordErrorPresent = false;
        },
        storeUser() {
            this.resetUsersErrors();
            const data = new FormData;

            data.append('id', this.usersForm.id);
            data.append('name', this.usersForm.name);
            data.append('email', this.usersForm.email);
            data.append('password', this.usersForm.password);
            data.append('role_id', this.usersForm.role);

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/users/store`, data, config)
                .then((response) => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === "success") {
                        $("#create-and-edit-modal").modal("hide");
                        EventBus.$emit('load-users');
                        swalSuccess("Korisnik je uspješno kreiran!");
                    }
                }).catch(error => {
                this.storeUpdateDisabled = false;
                if (error.response.status === 422) {
                    this.checkForValidationErrors(error.response.data.errors);
                }
                if (error.response.status === 423 || error.response.status === 403) {
                    swalError(error.response.data)
                }
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            });
        },
        updateUser() {
            this.resetUsersErrors();
            var data = new FormData;

            data.append('id', this.usersForm.id);
            data.append('name', this.usersForm.name);
            data.append('email', this.usersForm.email);
            data.append('password', this.usersForm.password);
            data.append('role_id', this.usersForm.role);

            const config = {headers: {'content-type': 'multipart/form-data'}};
            axios.post(`/admin/users/update`, data, config)
                .then((response) => {
                    this.storeUpdateDisabled = false;
                    if (response.data[0] === "success") {
                        $("#create-and-edit-modal").modal("hide");
                        EventBus.$emit('load-users');
                        swalSuccess("Korisnik je uspješno izmijenjen!");
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
        checkForValidationErrors(errors) {
            if (errors.hasOwnProperty("name")) {
                this.usersErrors.name = errors["name"][0];
                this.usersErrors.nameErrorPresent = true;
            }
            if (errors.hasOwnProperty('email')) {
                this.usersErrors.email = errors['email'][0];
                this.usersErrors.emailErrorPresent = true;
            }
            if (errors.hasOwnProperty('password')) {
                this.usersErrors.password = errors['password'][0];
                this.usersErrors.passwordErrorPresent = true;
            }
            if (errors.hasOwnProperty('role_id')) {
                this.usersErrors.role = errors['role_id'][0];
                this.usersErrors.roleErrorPresent = true;
            }
        }
    },
    mounted() {
        this.getAllRoles();
        EventBus.$on('open-create-modal', () => this.createUser());
        EventBus.$on('open-edit-modal', user => this.editUser(user));
        $(this.$refs.createAndEditModalRef).on("hidden.bs.modal", this.clearData);
    }
}
</script>

<style scoped>

</style>
