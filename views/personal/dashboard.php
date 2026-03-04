<?php
if (!isset($totalRegiones)) $totalRegiones = 0;
if (!isset($totalUniversidades)) $totalUniversidades = 0;
if (!isset($totalCarreras)) $totalCarreras = 0;
if (!isset($totalModalidades)) $totalModalidades = 0;
if (!isset($universidadesDestacadas)) $universidadesDestacadas = [];
if (!isset($carrerasDestacadas)) $carrerasDestacadas = [];
?>

<link rel="stylesheet" href="assets/css/personal/dashboard-personal.css">

<!-- Mensaje de bienvenida -->
<?php if (isset($_GET['bienvenido'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'info',
        title: '¡Bienvenido, <?php echo $_SESSION['user_nombre']; ?>!',
        text: 'Has iniciado sesión como Personal',
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

<div class="personal-dashboard">
    <!-- Welcome Header -->
    <div class="welcome-header">
        <h2>
            <i class="bi bi-speedometer2"></i>
            Dashboard Personal
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Bienvenido al sistema de consulta de universidades. Aquí puedes explorar toda la información disponible.
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-personal">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-personal primary me-3">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <div class="stat-label-personal">Regiones</div>
                            <div class="stat-value-personal"><?php echo $totalRegiones; ?></div>
                        </div>
                    </div>
                    <div class="stat-desc">
                        <i class="bi bi-map me-1"></i> Total regiones del país
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-personal">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-personal success me-3">
                            <i class="bi bi-buildings"></i>
                        </div>
                        <div>
                            <div class="stat-label-personal">Universidades</div>
                            <div class="stat-value-personal"><?php echo $totalUniversidades; ?></div>
                        </div>
                    </div>
                    <div class="stat-desc">
                        <i class="bi bi-building me-1"></i> Instituciones educativas
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-personal">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-personal info me-3">
                            <i class="bi bi-book"></i>
                        </div>
                        <div>
                            <div class="stat-label-personal">Carreras</div>
                            <div class="stat-value-personal"><?php echo $totalCarreras; ?></div>
                        </div>
                    </div>
                    <div class="stat-desc">
                        <i class="bi bi-mortarboard me-1"></i> Programas académicos
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card-personal">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-personal warning me-3">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div>
                            <div class="stat-label-personal">Modalidades</div>
                            <div class="stat-value-personal"><?php echo $totalModalidades; ?></div>
                        </div>
                    </div>
                    <div class="stat-desc">
                        <i class="bi bi-layout-three-columns me-1"></i> Tipos de estudio
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Carrusel de Universidades -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="carousel-card">
                <div class="card-header">
                    <h6>
                        <i class="bi bi-star-fill"></i>
                        Universidades Destacadas
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($universidadesDestacadas)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-building fs-1 text-muted d-block mb-3"></i>
                            <p class="text-muted">No hay universidades registradas</p>
                        </div>
                    <?php else: ?>
                    <div id="universidadesCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach (array_chunk($universidadesDestacadas, 3) as $index => $chunk): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <div class="row">
                                    <?php foreach ($chunk as $u): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="uni-card">
                                            <div class="card-body">
                                                <div class="uni-icon">
                                                    <i class="bi bi-building"></i>
                                                </div>
                                                <h5 class="uni-title"><?php echo htmlspecialchars($u['nombre']); ?></h5>
                                                <span class="uni-badge">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    <?php echo $u['region_nombre']; ?>
                                                </span>
                                                <p class="uni-desc"><?php echo substr(htmlspecialchars($u['descripcion'] ?? ''), 0, 80); ?>...</p>
                                                <button class="btn-view" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                                                    <i class="bi bi-eye me-2"></i>Ver detalles
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (count($universidadesDestacadas) > 3): ?>
                        <button class="carousel-control-prev" type="button" data-bs-target="#universidadesCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#universidadesCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces rápidos -->
    <div class="row">
        <div class="col-12">
            <div class="quick-links-card">
                <div class="card-header">
                    <h6>
                        <i class="bi bi-compass"></i>
                        Explorar Catálogo
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <a href="index.php?action=regiones" class="quick-link-item primary">
                                <i class="bi bi-geo-alt"></i>
                                <span>Regiones</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?action=universidades" class="quick-link-item success">
                                <i class="bi bi-buildings"></i>
                                <span>Universidades</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?action=carreras" class="quick-link-item info">
                                <i class="bi bi-book"></i>
                                <span>Carreras</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="index.php?action=modalidades" class="quick-link-item warning">
                                <i class="bi bi-grid-3x3-gap-fill"></i>
                                <span>Modalidades</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function verUniversidad(id) {
    fetch(`index.php?action=api-universidades&id=${id}`)
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                title: data.nombre,
                html: `
                    <div class="text-start">
                        <div class="text-center mb-3">
                            <i class="bi bi-building fs-1 text-primary"></i>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-2">
                                <strong><i class="bi bi-geo-alt me-1 text-primary"></i> Región:</strong>
                                <span class="badge" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">${data.region_nombre}</span>
                            </div>
                            <div class="col-6 mb-2">
                                <strong><i class="bi bi-mortarboard me-1 text-success"></i> Carreras:</strong>
                                <span class="badge bg-success">${data.carreras_count || 0}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3 p-3 rounded" style="background: #f8f9fc;">
                            <strong><i class="bi bi-info-circle me-1 text-info"></i> Descripción:</strong>
                            <p class="mb-0 mt-1">${data.descripcion || 'Sin descripción'}</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 mb-2">
                                <i class="bi bi-geo-fill me-1 text-warning"></i>
                                <strong>Dirección:</strong> ${data.direccion || 'No especificada'}
                            </div>
                            <div class="col-6 mb-2">
                                <i class="bi bi-telephone-fill me-1 text-primary"></i>
                                <strong>Teléfono:</strong> ${data.telefono || 'N/A'}
                            </div>
                            <div class="col-6 mb-2">
                                <i class="bi bi-envelope-fill me-1 text-danger"></i>
                                <strong>Email:</strong> ${data.email || 'N/A'}
                            </div>
                        </div>
                        
                        ${data.link_plataforma ? `
                        <div class="mt-3 text-center">
                            <a href="${data.link_plataforma}" target="_blank" class="btn" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
                                <i class="bi bi-globe"></i> Visitar Sitio Web
                            </a>
                        </div>
                        ` : ''}
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                width: '600px',
                background: 'white',
                confirmButtonColor: '#667eea'
            });
        });
}
</script>