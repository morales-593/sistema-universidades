<?php
require_once '../../config/session.php';
Session::checkAccess('admin');

$title = 'Universidades - Admin';
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
            <i class="bi bi-buildings"></i> Gestión de Universidades
        </h2>
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
    <div class="col-md-4 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#universidadModal">
            <i class="bi bi-plus-circle"></i> Nueva Universidad
        </button>
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
                <button class="btn btn-sm btn-warning" onclick="editarUniversidad(<?php echo $u['id']; ?>)">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn btn-sm btn-danger" onclick="eliminarUniversidad(<?php echo $u['id']; ?>, '<?php echo addslashes($u['nombre']); ?>')">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal de Detalle -->
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

<!-- Modal Crear/Editar Universidad -->
<div class="modal fade" id="universidadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="universidadModalTitle">Nueva Universidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="universidadForm">
                    <input type="hidden" id="universidadId">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre de la Universidad *</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Región *</label>
                            <select class="form-select" id="id_region" required>
                                <option value="">Seleccione una región</option>
                                <?php foreach ($regiones as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" rows="3"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sitio Web</label>
                            <input type="url" class="form-control" id="link_plataforma" placeholder="https://">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="email">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" accept="image/*">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarUniversidad()">Guardar</button>
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

function editarUniversidad(id) {
    fetch(`../../api/universidades.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('universidadId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('id_region').value = data.id_region;
            document.getElementById('descripcion').value = data.descripcion;
            document.getElementById('link_plataforma').value = data.link_plataforma;
            document.getElementById('direccion').value = data.direccion;
            document.getElementById('telefono').value = data.telefono;
            document.getElementById('email').value = data.email;
            
            document.getElementById('universidadModalTitle').textContent = 'Editar Universidad';
            new bootstrap.Modal(document.getElementById('universidadModal')).show();
        });
}

function guardarUniversidad() {
    const id = document.getElementById('universidadId').value;
    const data = {
        nombre: document.getElementById('nombre').value,
        id_region: document.getElementById('id_region').value,
        descripcion: document.getElementById('descripcion').value,
        link_plataforma: document.getElementById('link_plataforma').value,
        direccion: document.getElementById('direccion').value,
        telefono: document.getElementById('telefono').value,
        email: document.getElementById('email').value
    };

    const url = id ? `../../api/universidades.php?id=${id}` : '../../api/universidades.php';
    const method = id ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function eliminarUniversidad(id, nombre) {
    if (confirm(`¿Está seguro que desea eliminar la universidad "${nombre}"?`)) {
        fetch(`../../api/universidades.php?id=${id}`, {method: 'DELETE'})
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
    }
}
</script>

<?php
require_once '../layouts/footer.php';
?>