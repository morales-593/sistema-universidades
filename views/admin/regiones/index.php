<?php
// Verificar que las variables existen
if (!isset($regiones)) $regiones = [];
?>

<!-- Mensajes con SweetAlert -->
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

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-geo-alt"></i> Gestión de Regiones
        </h2>
    </div>
</div>

<!-- Botón Nueva Región -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevaRegion()">
            <i class="bi bi-plus-circle"></i> Nueva Región
        </button>
    </div>
</div>

<!-- Filtro de búsqueda -->
<div class="row mb-4">
    <div class="col-md-6">
        <input type="text" class="form-control" id="buscarRegion" placeholder="Buscar región..." onkeyup="filtrarRegiones()">
    </div>
</div>

<!-- Tabla de Regiones -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Regiones</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaRegiones" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Universidades</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($regiones)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No hay regiones registradas</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($regiones as $r): 
                                    $regionModel = new Region();
                                    $countUniversidades = $regionModel->getUniversidadesCount($r['id']);
                                ?>
                                <tr data-id="<?php echo $r['id']; ?>" data-nombre="<?php echo htmlspecialchars($r['nombre']); ?>">
                                    <td><?php echo $r['id']; ?></td>
                                    <td><?php echo htmlspecialchars($r['nombre']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $countUniversidades; ?> universidades</span>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="verRegion(<?php echo $r['id']; ?>)">
                                                <i class="bi bi-eye"></i> Ver
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarRegion(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>')">
                                                <i class="bi bi-pencil"></i> Editar
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarRegion(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>', <?php echo $countUniversidades; ?>)">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </div>
                                    </td>
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

<!-- Modal para Ver Detalle de Región -->
<div class="modal fade" id="modalVerRegion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt"></i> Detalle de Región
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleRegionContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Región -->
<div class="modal fade" id="modalRegion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalRegionTitle">
                    <i class="bi bi-plus-circle"></i> Nueva Región
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=region-guardar" id="formRegion">
                <div class="modal-body">
                    <input type="hidden" name="id" id="regionId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Región *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" 
                               placeholder="Ej: Sierra, Costa, Amazonía, Insular">
                        <small class="text-muted">Máximo 100 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarRegion">Guardar Región</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nueva región
function abrirModalNuevaRegion() {
    document.getElementById('formRegion').reset();
    document.getElementById('regionId').value = '';
    document.getElementById('modalRegionTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Nueva Región';
    document.getElementById('btnGuardarRegion').innerHTML = 'Guardar Región';
    new bootstrap.Modal(document.getElementById('modalRegion')).show();
}

// Función para editar región
function editarRegion(id, nombre) {
    document.getElementById('regionId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('modalRegionTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Región';
    document.getElementById('btnGuardarRegion').innerHTML = 'Actualizar Región';
    new bootstrap.Modal(document.getElementById('modalRegion')).show();
}

// Función para ver detalle de región
function verRegion(id) {
    fetch(`index.php?action=region-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let universidadesHtml = '';
            
            if (data.universidades && data.universidades.length > 0) {
                universidadesHtml = '<ul class="list-group">';
                data.universidades.forEach(u => {
                    universidadesHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${u.nombre}
                            <span class="badge bg-primary rounded-pill">${u.carreras_count || 0} carreras</span>
                        </li>
                    `;
                });
                universidadesHtml += '</ul>';
            } else {
                universidadesHtml = '<p class="text-muted">No hay universidades en esta región</p>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-geo-alt fs-1 text-primary"></i>
                </div>
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%">ID:</th>
                        <td>${data.id}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td><strong>${data.nombre}</strong></td>
                    </tr>
                    <tr>
                        <th>Universidades:</th>
                        <td><span class="badge bg-info">${data.universidades_count || 0} universidades</span></td>
                    </tr>
                    <tr>
                        <th>Fecha Creación:</th>
                        <td>${new Date(data.created_at).toLocaleDateString()}</td>
                    </tr>
                </table>
                
                <h6 class="mt-4 mb-3">Universidades en esta región:</h6>
                ${universidadesHtml}
            `;
            
            document.getElementById('detalleRegionContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerRegion')).show();
        });
}

// Función para eliminar región
function eliminarRegion(id, nombre, countUniversidades) {
    if (countUniversidades > 0) {
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            html: `La región <strong>${nombre}</strong> tiene ${countUniversidades} universidades asociadas.<br>
                   Elimina primero las universidades para poder eliminar esta región.`,
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar región?',
        html: `¿Estás seguro de eliminar la región <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=region-eliminar&id=${id}`;
        }
    });
}

// Función para filtrar regiones
function filtrarRegiones() {
    const busqueda = document.getElementById('buscarRegion').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaRegiones tbody tr');
    
    filas.forEach(fila => {
        if (fila.cells.length > 1) {
            const nombre = fila.cells[1].textContent.toLowerCase();
            if (nombre.includes(busqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        }
    });
}

// Validar formulario antes de enviar
document.getElementById('formRegion').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    
    if (nombre.length < 3) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre inválido',
            text: 'El nombre debe tener al menos 3 caracteres'
        });
        return;
    }
    
    if (nombre.length > 100) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre muy largo',
            text: 'El nombre no puede exceder los 100 caracteres'
        });
        return;
    }
});
</script>

<style>
.btn-group {
    display: flex;
    gap: 5px;
}

.btn-group .btn {
    margin: 0;
    border-radius: 0.5rem !important;
}

#tablaRegiones td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        width: 100%;
    }
}
</style>