<?php
// Verificar que las variables existen
if (!isset($totalRegiones)) $totalRegiones = 0;
if (!isset($totalUniversidades)) $totalUniversidades = 0;
if (!isset($totalCarreras)) $totalCarreras = 0;
if (!isset($totalModalidades)) $totalModalidades = 0;
if (!isset($totalUsuarios)) $totalUsuarios = 0;
if (!isset($universidadesPorRegion)) $universidadesPorRegion = [];
if (!isset($carrerasRecientes)) $carrerasRecientes = [];
?>

<!-- Mensaje de bienvenida con SweetAlert -->
<?php if (isset($_GET['bienvenido'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Bienvenido, <?php echo $_SESSION['user_nombre']; ?>!',
        text: 'Has iniciado sesión como Administrador',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
</script>
<?php endif; ?>

<!-- Mensajes de éxito/error -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 3000,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_GET['error']; ?>'
    });
});
</script>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2"></i> Dashboard Administrador
        </h2>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Regiones</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalRegiones; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-geo-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Universidades</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalUniversidades; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-buildings fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Carreras</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalCarreras; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Modalidades</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalModalidades; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-grid-3x3-gap-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico y Acciones Rápidas -->
<div class="row">
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Universidades por Región</h6>
            </div>
            <div class="card-body">
                <canvas id="universidadesPorRegionChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Resumen del Sistema</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted">Total Usuarios</small>
                            <h3><?php echo $totalUsuarios; ?></h3>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="bg-light p-3 rounded">
                            <small class="text-muted">Promedio Carreras/Universidad</small>
                            <h3><?php echo $totalUniversidades > 0 ? round($totalCarreras / $totalUniversidades, 1) : 0; ?></h3>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Accesos Rápidos</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="window.location.href='index.php?action=regiones'">
                            <i class="bi bi-geo-alt"></i> Gestionar Regiones
                        </button>
                        <button class="btn btn-success" onclick="window.location.href='index.php?action=universidades'">
                            <i class="bi bi-buildings"></i> Gestionar Universidades
                        </button>
                        <button class="btn btn-info text-white" onclick="window.location.href='index.php?action=carreras'">
                            <i class="bi bi-book"></i> Gestionar Carreras
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimas Carreras -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Últimas Carreras Agregadas</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Carrera</th>
                                <th>Universidad</th>
                                <th>Región</th>
                                <th>Duración</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($carrerasRecientes)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay carreras registradas</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($carrerasRecientes as $c): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($c['universidad_nombre']); ?></td>
                                    <td><span class="badge bg-info"><?php echo $c['region_nombre']; ?></span></td>
                                    <td><?php echo $c['duracion']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($c['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Universidades por Región
const ctx = document.getElementById('universidadesPorRegionChart');
if (ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($universidadesPorRegion)); ?>,
            datasets: [{
                label: 'Número de Universidades',
                data: <?php echo json_encode(array_values($universidadesPorRegion)); ?>,
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)'
                ],
                borderColor: [
                    'rgba(78, 115, 223, 1)',
                    'rgba(28, 200, 138, 1)',
                    'rgba(54, 185, 204, 1)',
                    'rgba(246, 194, 62, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
}
</script>