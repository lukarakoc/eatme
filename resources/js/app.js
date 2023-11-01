/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import App from './App.vue';
import VueRouter from 'vue-router';
import axios from 'axios';
import VueAxios from 'vue-axios';
import {BootstrapVue, IconsPlugin} from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

import Swal from 'sweetalert2';
// Date picker
import VueFlatPickr from 'vue-flatpickr-component';
import 'flatpickr/dist/flatpickr.css';
// CK Editor
import CKEditor from '@ckeditor/ckeditor5-vue';
import Select2 from 'v-select2-component';
import Users from "./components/users/Users";
import VueLazyload from 'vue-lazyload'
import Admin from "./components/Admin";
import Groceries from "./components/groceries/Groceries";
import Purchases from "./components/purchases/Purchases";
import Sales from "./components/sales/Sales";
import Expenses from "./components/expenses/Expenses";
import Dashboard from "./components/dashboard/Dashboard";

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.use(VueRouter, VueAxios, axios);

const routes = [
    {path: '/', name: 'Dashboard', component: Dashboard},
    {path: '/users', name: 'Users', component: Users},
    {path: '/groceries', name: 'Groceries', component: Groceries},
    {path: '/purchases', name: 'Purchases', component: Purchases},
    {path: '/sales', name: 'Sales', component: Sales},
    {path: '/expenses', name: 'Expenses', component: Expenses},
];

Vue.use(VueLazyload);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
window.Swal = Swal;

Vue.use(VueFlatPickr);

// Moment.js
Vue.use(require('vue-moment'));

Vue.use(CKEditor);

Vue.component('select2', Select2);
Vue.use(Select2);

// laravel Vue pagination
Vue.component('pagination', require('laravel-vue-pagination'));

const router = new VueRouter({
    routes,
});

const app = new Vue({
    el: '#app',
    router,
    render(h) { return h(App) }
});
