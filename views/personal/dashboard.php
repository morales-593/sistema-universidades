<?php
if (!isset($totalRegiones)) $totalRegiones = 0;
if (!isset($totalUniversidades)) $totalUniversidades = 0;
if (!isset($totalCarreras)) $totalCarreras = 0;
if (!isset($totalModalidades)) $totalModalidades = 0;
if (!isset($universidadesDestacadas)) $universidadesDestacadas = [];
if (!isset($carrerasDestacadas)) $carrerasDestacadas = [];
?>

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
        position: 'top-end'
    });
});
</script>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2"></i> Dashboard Personal
        </h2>
        <p class="text-muted mb-4">Bienvenido al sistema de consulta de universidades. Aquí puedes explorar toda la información disponible.</p>
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

<!-- Carrusel de Universidades -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Universidades Destacadas</h6>
            </div>
            <div class="card-body">
                <?php if (empty($universidadesDestacadas)): ?>
                    <p class="text-center">No hay universidades registradas</p>
                <?php else: ?>
                <div id="universidadesCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach (array_chunk($universidadesDestacadas, 3) as $index => $chunk): ?>
                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="row">
                                <?php foreach ($chunk as $u): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-building fs-1 text-primary mb-3"></i>
                                            <h5 class="card-title"><?php echo htmlspecialchars($u['nombre']); ?></h5>
                                            <p class="badge bg-info"><?php echo $u['region_nombre']; ?></p>
                                            <p class="small"><?php echo substr(htmlspecialchars($u['descripcion'] ?? ''), 0, 100); ?>...</p>
                                            <button class="btn btn-sm btn-primary" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                                                <i class="bi bi-eye"></i> Ver detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#universidadesCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#universidadesCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Enlaces rápidos -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Explorar Catálogo</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=regiones" class="btn btn-outline-primary w-100 p-3">
                            <i class="bi bi-geo-alt fs-2 d-block mb-2"></i>
                            Regiones
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=universidades" class="btn btn-outline-success w-100 p-3">
                            <i class="bi bi-buildings fs-2 d-block mb-2"></i>
                            Universidades
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=carreras" class="btn btn-outline-info w-100 p-3">
                            <i class="bi bi-book fs-2 d-block mb-2"></i>
                            Carreras
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="index.php?action=modalidades" class="btn btn-outline-warning w-100 p-3">
                            <i class="bi bi-grid-3x3-gap-fill fs-2 d-block mb-2"></i>
                            Modalidades
                        </a>
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
                        <p><strong>Región:</strong> <span class="badge bg-info">${data.region_nombre}</span></p>
                        <p><strong>Descripción:</strong> ${data.descripcion || 'Sin descripción'}</p>
                        <p><strong>Dirección:</strong> ${data.direccion || 'No especificada'}</p>
                        <p><strong>Teléfono:</strong> ${data.telefono || 'No especificado'}</p>
                        <p><strong>Email:</strong> ${data.email || 'No especificado'}</p>
                        <p><strong>Carreras:</strong> ${data.carreras_count || 0}</p>
                        <p><strong>Sitio Web:</strong> <a href="${data.link_plataforma}" target="_blank">${data.link_plataforma || 'No disponible'}</a></p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                width: '600px'
            });
        });
}
</script>