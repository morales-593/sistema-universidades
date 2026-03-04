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

<link rel="stylesheet" href="assets/css/admin/dashboard-admin.css">

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
        position: 'top-end',
        background: 'linear-gradient(135deg, #667eea, #764ba2)',
        color: 'white'
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
        showConfirmButton: false,
        background: 'linear-gradient(135deg, #28a745, #20c997)',
        color: 'white'
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
        text: '<?php echo $_GET['error']; ?>',
        background: 'linear-gradient(135deg, #dc3545, #c82333)',
        color: 'white'
    });
});
</script>
<?php endif; ?>

<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <h2>
            <i class="bi bi-speedometer2"></i>
            Dashboard Administrador
        </h2>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon primary me-3">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Regiones</div>
                            <h4 class="stat-value"><?php echo $totalRegiones; ?></h4>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-arrow-up"></i> Total regiones del país
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon success me-3">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Universidades</div>
                            <h4 class="stat-value"><?php echo $totalUniversidades; ?></h4>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-building"></i> Instituciones educativas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon info me-3">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Carreras</div>
                            <h4 class="stat-value"><?php echo $totalCarreras; ?></h4>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-mortarboard"></i> Programas académicos
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon warning me-3">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="stat-label">Modalidades</div>
                            <h4 class="stat-value"><?php echo $totalModalidades; ?></h4>
                        </div>
                    </div>
                    <div class="stat-trend">
                        <i class="bi bi-layout-three-columns"></i> Tipos de estudio
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico y Acciones Rápidas -->
    <div class="row">
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="chart-card">
                <div class="card-header">
                    <h6><i class="bi bi-pie-chart me-2"></i>Universidades por Región</h6>
                </div>
                <div class="card-body">
                    <canvas id="universidadesPorRegionChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="summary-card">
                <div class="card-header">
                    <h6><i class="bi bi-graph-up me-2"></i>Resumen del Sistema</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="summary-item">
                                <small>Total Usuarios</small>
                                <h3><?php echo $totalUsuarios; ?></h3>
                                <i class="bi bi-people-fill text-primary" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="summary-item">
                                <small>Promedio Carreras/Universidad</small>
                                <h3><?php echo $totalUniversidades > 0 ? round($totalCarreras / $totalUniversidades, 1) : 0; ?></h3>
                                <i class="bi bi-bar-chart-fill text-success" style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="quick-actions">
                        <h6><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Accesos Rápidos</h6>
                        <div class="d-grid gap-2">
                            <button class="action-btn" onclick="window.location.href='index.php?action=regiones'">
                                <i class="bi bi-geo-alt"></i> Gestionar Regiones
                            </button>
                            <button class="action-btn" onclick="window.location.href='index.php?action=universidades'">
                                <i class="bi bi-buildings"></i> Gestionar Universidades
                            </button>
                            <button class="action-btn" onclick="window.location.href='index.php?action=carreras'">
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
            <div class="table-card">
                <div class="card-header">
                    <h6><i class="bi bi-clock-history me-2"></i>Últimas Carreras Agregadas</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-table">
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
                                    <td colspan="5" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 d-block text-muted mb-2"></i>
                                        No hay carreras registradas
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($carrerasRecientes as $c): ?>
                                    <tr>
                                        <td class="fw-500"><?php echo htmlspecialchars($c['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($c['universidad_nombre']); ?></td>
                                        <td><span class="badge-custom"><?php echo $c['region_nombre']; ?></span></td>
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
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(23, 162, 184, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ],
                borderColor: [
                    'rgba(102, 126, 234, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                borderWidth: 1,
                borderRadius: 10,
                barPercentage: 0.6
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>