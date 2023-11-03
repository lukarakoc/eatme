<template>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="z-index: 2;">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">

                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link btn" @click.prevent="logout">
                        <i class="fas fa-power-off"></i> Odjavi se
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
<!--            <router-link class="brand-link" :to="{ path: '/' }" style="text-align: center;">-->
<!--                <span class="brand-text font-weight-light">-->
<!--                    <img :src="resolveLogoPath" alt="">-->
<!--                </span>-->
<!--            </router-link>-->

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center text-center">
                    <div class="image" style="margin: 0 auto;">
                        <span style="color: #fff;">{{ user.name }}</span>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block" style="white-space: normal;"></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false" v-show="userRole === 'Admin'">
                        <!-- Add icons to the links using the .nav-icon class
                             with font-awesome or any other icon font library -->

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/' }">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Početna</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/groceries' }">
                                <i class="fas fa-hamburger nav-icon"></i>
                                <p>Namirnice</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/purchases' }">
                                <i class="fas fa-shopping-basket nav-icon"></i>
                                <p>Kupovine</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/sales' }">
                                <i class="fas fa-euro-sign nav-icon"></i>
                                <p>Prodaja</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/expenses' }">
                                <i class="fas fa-calculator nav-icon"></i>
                                <p>Troškovi</p>
                            </router-link>
                        </li>

                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/users' }">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Zaposleni</p>
                            </router-link>
                        </li>
                    </ul>

                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false" v-show="userRole === 'admin'">
                        <!-- Add icons to the links using the .nav-icon class
                             with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ path: '/users' }">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Zaposleni</p>
                            </router-link>
                        </li>
                    </ul>
                </nav><!-- /.sidebar-menu -->
            </div><!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">
                                {{ pageName }}
                            </h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item">
                                </li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div><!-- /.content-header -->

            <!-- Main content -->
            <section id="content" class="content">
                <div class="container-fluid">
                    <div class="row">
                        <router-view @loadBreadcrumbLink="showBreadcrumbLink" :authUser="user"></router-view>
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->
        <footer class="main-footer">
            Copyright &copy; {{ new Date().getFullYear() }} <strong></strong>
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside><!-- /.control-sidebar -->
    </div>
</template>

<script>
import {EventBus} from './libraries/event-bus.js';

export default {
    data() {
        return {
            user: "",
            year: "",
            breadcrumbUrl: "",
            pageName: "",
            userRole: ''
        };
    },
    methods: {
        logout() {
            Swal.fire({
                icon: 'warning',
                title: 'Odjava!',
                text: "Da li ste sigurni da želite da se odjavite?",
                showCancelButton: true,
                cancelButtonColor: '#5a6268',
                confirmButtonColor: '#0069d9',
                cancelButtonText: 'Odustani',
                confirmButtonText: 'Odjavi me'
            }).then((result) => {
                if (result.value) {
                    axios.post(`/logout`).then(response => location.href = "/login");
                }
            })
        },
        loadAdminInfo() {
            axios.get(`/admin/admin-info`)
                .then(response => {
                    if (response.data[0] === "success") {
                        this.userRole = response.data[1].role.name;
                        this.user = response.data[1];
                        EventBus.$emit("authUser", this.user);
                    }
                })
                .catch(() => {
                    Swal.fire("Neuspješno!", "Došlo je do greške", "warning");
                });
        },
        showBreadcrumbLink(data) {
            this.breadCrumbUrl = data.url;
            this.pageName = data.pageName;
        },
        resolveLogoPath() {
            return '/LOGO-EAT-ME.jpeg';
        }
    },
    watch: {
        user(newValue, oldValue) {
            this.user = newValue;
        }
    },
    mounted() {
        this.loadAdminInfo();
    }
}
</script>

<style lang="scss">
.nav-item .router-link-exact-active.router-link-active {
    background-color: rgba(255, 255, 255, 0.1);
    color: #f5fafe;
}
</style>
