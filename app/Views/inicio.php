<main>
    <div class="container-fluid">
        <div class="row pt-1">
            <?php if ($idRol == 1) { ?>
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card text-white bg-primary o-hidden h-100">
                        <div class="card-body">
                            <div class="card-body-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <?php echo $total; ?> Total de productos
                        </div>

                        <a class="card-footer text-white" href="<?php echo base_url(); ?>/productos">
                            Ver detalles
                            <span class="float-right">
                                <i class="fas fa-angle-right"></i>
                            </span></a>
                    </div>
                </div>
            <?php } ?>

            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-success o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <?php echo number_format($totalVentas['total'], 2, '.', ','); ?> Ventas del día
                    </div>

                    <a class="card-footer text-white" href="<?php echo base_url(); ?>/ventas">
                        Ver detalles
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>

            <div class="col-xl-3 col-sm-6 mb-3">
                <div class="card text-white bg-danger o-hidden h-100">
                    <div class="card-body">
                        <div class="card-body-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div>
                            <?php echo $minimos; ?> Productos con stock mínimo
                        </div>
                    </div>

                    <a class="card-footer text-white" href="<?php echo base_url(); ?>/productos/mostrarMinimos">
                        Ver detalles
                        <span class="float-right">
                            <i class="fas fa-angle-right"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Ventas de la semana
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" width="100%" height="75"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Productos más vendidos del mes
                    </div>
                    <div class="card-body">
                        <canvas id="myPieChart" width="400" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="<?php echo base_url(); ?>/js/Chart.min.js"></script>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'],
            datasets: [{
                labels: ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'],
                data: [<?php echo $diasVentas; ?>],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            plugins: {
                legend: {
                    display: false,
                }
            }
        }
    });

    // Pie Chart Example
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['<?php echo $nombreProductos; ?>'],
            datasets: [{
                data: [<?php echo $cantidadProductos; ?>],
                backgroundColor: ['#007bff', '#dc3545', '#ffc107', '#28a745', '#697bff'],
            }],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        }
    });
</script>