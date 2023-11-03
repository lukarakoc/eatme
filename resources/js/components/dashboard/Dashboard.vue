<template>
    <div class="col-12">
        <div class="container-fluid">
            <div class="row">
                <div class="alert alert-warning alert-dismissible" style="width: 100%; margin: 15px;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-exclamation-triangle"></i> Aplikacija u Beta verziji!</h5>
                    Aplikacija je u Beta verziji i neke funkcionalnosti su još uvijek u izradi!
                </div>
            </div>
            <div class="row">

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ this.salesThisMonth }}&euro;</h3>
                            <p>Prodato ovog mjeseca</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-arrow-graph-up-right"></i>
                        </div>
                        <router-link class="small-box-footer" :to="{ path: '/sales' }">Idi na sekciju <i
                            class="fas fa-arrow-circle-right"></i></router-link>
                    </div>
                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ this.salesThisYear }}&euro;</h3>
                            <p>Prodato ove godine</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="#" class="small-box-footer">Idi na sekciju <i
                            class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ this.purchasesThisMonth }}&euro;</h3>
                            <p>Kupovine ovog mjeseca</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer">Idi na sekciju <i
                            class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">

                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ this.purchasesThisYear }}&euro;</h3>
                            <p>Kupovine ove godine</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-arrow-graph-down-right"></i>
                        </div>
                        <a href="#" class="small-box-footer">Idi na sekciju <i
                            class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Prodaja po mjesecima</h3>
                        </div>
                        <div class="card-body">
                            <div class="chart">
                                <canvas id="areaChart"
                                        style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

export default {
    name: 'Dashboard',
    data() {
        return {
            salesThisMonth: '',
            salesThisYear: '',
            purchasesThisMonth: '',
            purchasesThisYear: '',
            salesByMonths: [],
        }
    },
    mounted() {
        this.loadDashboardData();
        this.$emit('loadBreadcrumbLink', {url: '/', pageName: 'Početna'});
    },
    methods: {
        loadDashboardData() {
            axios.get('/admin/dashboard')
                .then(response => {
                    if (response.data[0] === 'success') {
                        this.salesThisMonth = response.data[1].salesThisMonth;
                        this.salesThisYear = response.data[1].salesThisYear;
                        this.purchasesThisYear = response.data[1].purchasesThisYear;
                        this.purchasesThisMonth = response.data[1].purchasesThisMonth;
                        this.salesByMonths = response.data[1].salesByMonths;
                        this.pageIsLoading = false;
                        this.loadChart(this.salesByMonths);

                    }
                });
        },
        loadChart(salesByMonths) {

            let values = salesByMonths.map(obj => obj.TotalSum);
            console.log(values);
            var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

            var areaChartData = {
                labels: ['Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun', 'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar'],
                datasets: [
                    {
                        label: 'Prodaja u eurima',
                        backgroundColor: 'rgba(60,141,188,0.9)',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: values
                        // data: [20000, 24000, 18000, 15000, 27000, 23000, 21000]
                    }
                ]
            }

            var areaChartOptions = {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: false
                },
                scales: {
                    xAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display: false,
                        }
                    }]
                }
            }

            // This will get the first returned node in the jQuery collection.
            new Chart(areaChartCanvas, {
                type: 'line',
                data: areaChartData,
                options: areaChartOptions
            })
        }
    }
}
</script>
