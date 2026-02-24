<?php
require_once '../../config/session.php';
Session::checkAccess();

$title = 'Universidades - Personal';
require_once '../layouts/header.php';
require_once '../layouts/navbar.php';

require_once '../../models/Universidad.php';
require_once '../../models/Region.php';

$universidad = new Universidad();
$region = new Region();
$universidades = $universidad->getAllWithRegion();
$regiones = $region->getAll();
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-buildings"></i> Catálogo de Universidades
        </h2>
        <p class="text-muted">Vista de solo lectura - Personal</p>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filterRegion">
            <option value="">Todas las regiones</option>
            <?php foreach ($regiones as $r): ?>
            <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Buscar universidad..." id="searchUniversity">
    </div>
</div>

<!-- Universities Grid -->
<div class="row" id="universitiesGrid">
    <?php foreach ($universidades as $u): 
        $carrerasCount = $universidad->getCarrerasCount($u['id']);
    ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle p-3 me-3">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0"><?php echo htmlspecialchars($u['nombre']); ?></h5>
                        <span class="badge bg-info"><?php echo $u['region_nombre']; ?></span>
                    </div>
                </div>
                <p class="card-text small"><?php echo substr(htmlspecialchars($u['descripcion']), 0, 100); ?>...</p>
                <div class="small mb-2">
                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($u['direccion']); ?><br>
                    <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($u['telefono']); ?><br>
                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($u['email']); ?>
                </div>
                <div class="mt-3">
                    <strong>Carreras:</strong> <?php echo $carrerasCount; ?>
                </div>
            </div>
            <div class="card-footer bg-white">
                <button class="btn btn-sm btn-info" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                    <i class="bi bi-eye"></i> Ver detalles
                </button>
                <!-- Sin botones de edición/eliminación para personal -->
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal de Detalle (igual que en admin) -->
<div class="modal fade" id="universidadDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-building"></i> Detalle de Universidad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="detailWebsiteBtn" class="btn btn-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Visitar Sitio Web
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function verUniversidad(id) {
    fetch(`../../api/universidades.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <div class="bg-light p-4 rounded-circle d-inline-block">
                            <i class="bi bi-building fs-1 text-primary"></i>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h3>${data.nombre}</h3>
                        <p class="badge bg-info mb-3">${data.region_nombre}</p>
                        <p class="text-muted">${data.descripcion || 'Sin descripción'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">Información de Contacto</div>
                            <div class="card-body">
                                <p><i class="bi bi-geo-alt text-primary"></i> <strong>Dirección:</strong> ${data.direccion || 'No especificada'}</p>
                                <p><i class="bi bi-telephone text-primary"></i> <strong>Teléfono:</strong> ${data.telefono || 'No especificado'}</p>
                                <p><i class="bi bi-envelope text-primary"></i> <strong>Email:</strong> ${data.email || 'No especificado'}</p>
                                <p><i class="bi bi-globe text-primary"></i> <strong>Sitio Web:</strong> <a href="${data.link_plataforma}" target="_blank">${data.link_plataforma || 'No disponible'}</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">Estadísticas</div>
                            <div class="card-body">
                                <p><strong>Total de Carreras:</strong> <span class="badge bg-success fs-6">${data.carreras_count || 0}</span></p>
                                <p><strong>Fecha de Registro:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detailContent').innerHTML = html;
            document.getElementById('detailWebsiteBtn').href = data.link_plataforma || '#';
            
            new bootstrap.Modal(document.getElementById('universidadDetailModal')).show();
        });
}
</script>

<?php
require_once '../layouts/footer.php';
?>