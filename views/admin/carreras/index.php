<?php
// Verificar que las variables existen
if (!isset($carreras)) $carreras = [];
if (!isset($universidades)) $universidades = [];
if (!isset($modalidades)) $modalidades = [];
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
            <i class="bi bi-book"></i> Gestión de Carreras
        </h2>
    </div>
</div>

<!-- Botón Nueva Carrera -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevaCarrera()">
            <i class="bi bi-plus-circle"></i> Nueva Carrera
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filtroRegion" onchange="filtrarCarreras()">
            <option value="">Todas las regiones</option>
            <?php
            $regionesUnicas = [];
            foreach ($universidades as $u) {
                if (!in_array($u['region_nombre'], $regionesUnicas)) {
                    $regionesUnicas[] = $u['region_nombre'];
                    echo "<option value=\"" . htmlspecialchars($u['region_nombre']) . "\">" . htmlspecialchars($u['region_nombre']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="filtroUniversidad" onchange="filtrarCarreras()">
            <option value="">Todas las universidades</option>
            <?php foreach ($universidades as $u): ?>
            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="buscarCarrera" placeholder="Buscar carrera..." onkeyup="filtrarCarreras()">
    </div>
</div>

<!-- Tabla de Carreras -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Carreras</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaCarreras" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Carrera</th>
                                <th>Universidad</th>
                                <th>Región</th>
                                <th>Título</th>
                                <th>Duración</th>
                                <th>Modalidades</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($carreras)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No hay carreras registradas</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($carreras as $c): 
                                    $carreraModel = new Carrera();
                                    $modalidadesCarrera = $carreraModel->getModalidades($c['id']);
                                ?>
                                <tr data-id="<?php echo $c['id']; ?>"
                                    data-region="<?php echo htmlspecialchars($c['region_nombre']); ?>"
                                    data-universidad="<?php echo $c['id_universidad']; ?>"
                                    data-nombre="<?php echo strtolower(htmlspecialchars($c['nombre'])); ?>">
                                    <td><?php echo $c['id']; ?></td>
                                    <td><strong><?php echo htmlspecialchars($c['nombre']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($c['universidad_nombre']); ?></td>
                                    <td><span class="badge bg-info"><?php echo $c['region_nombre']; ?></span></td>
                                    <td><?php echo htmlspecialchars($c['titulo_otorgado'] ?? 'No especificado'); ?></td>
                                    <td><?php echo htmlspecialchars($c['duracion'] ?? 'No especificada'); ?></td>
                                    <td>
                                        <?php foreach ($modalidadesCarrera as $m): ?>
                                            <span class="badge bg-primary mb-1"><?php echo $m['nombre']; ?></span>
                                        <?php endforeach; ?>
                                        <?php if (empty($modalidadesCarrera)): ?>
                                            <span class="text-muted">Sin modalidades</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="verCarrera(<?php echo $c['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarCarrera(<?php echo $c['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarCarrera(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['nombre']); ?>')">
                                                <i class="bi bi-trash"></i>
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

<!-- Modal para Ver Detalle de Carrera -->
<div class="modal fade" id="modalVerCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-book"></i> Detalle de Carrera
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleCarreraContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Carrera -->
<div class="modal fade" id="modalCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalCarreraTitle">
                    <i class="bi bi-plus-circle"></i> Nueva Carrera
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=carrera-guardar" id="formCarrera">
                <div class="modal-body">
                    <input type="hidden" name="id" id="carreraId">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nombre" class="form-label">Nombre de la Carrera *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="duracion" class="form-label">Duración</label>
                            <input type="text" class="form-control" id="duracion" name="duracion" placeholder="Ej: 9 semestres">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="id_universidad" class="form-label">Universidad *</label>
                            <select class="form-select" id="id_universidad" name="id_universidad" required onchange="cargarModalidadesDisponibles()">
                                <option value="">Seleccione una universidad</option>
                                <?php foreach ($universidades as $u): ?>
                                <option value="<?php echo $u['id']; ?>" data-region="<?php echo $u['region_nombre']; ?>">
                                    <?php echo htmlspecialchars($u['nombre']); ?> (<?php echo $u['region_nombre']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="titulo_otorgado" class="form-label">Título que otorga</label>
                            <input type="text" class="form-control" id="titulo_otorgado" name="titulo_otorgado" placeholder="Ej: Ingeniero/a en Sistemas">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="perfil_egreso" class="form-label">Perfil de Egreso</label>
                            <textarea class="form-control" id="perfil_egreso" name="perfil_egreso" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="campo_laboral" class="form-label">Campo Laboral</label>
                            <textarea class="form-control" id="campo_laboral" name="campo_laboral" rows="3"></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Modalidades</label>
                            <div class="border p-3 rounded" id="modalidadesContainer">
                                <?php foreach ($modalidades as $m): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" 
                                           name="modalidades[]" 
                                           value="<?php echo $m['id']; ?>" 
                                           id="modalidad_<?php echo $m['id']; ?>">
                                    <label class="form-check-label" for="modalidad_<?php echo $m['id']; ?>">
                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="text-muted">Seleccione una o varias modalidades</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarCarrera">Guardar Carrera</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nueva carrera
function abrirModalNuevaCarrera() {
    document.getElementById('formCarrera').reset();
    document.getElementById('carreraId').value = '';
    document.getElementById('modalCarreraTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Nueva Carrera';
    document.getElementById('btnGuardarCarrera').innerHTML = 'Guardar Carrera';
    
    // Desmarcar todos los checkboxes
    document.querySelectorAll('input[name="modalidades[]"]').forEach(cb => cb.checked = false);
    
    new bootstrap.Modal(document.getElementById('modalCarrera')).show();
}

// Función para editar carrera
function editarCarrera(id) {
    fetch(`index.php?action=carrera-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('carreraId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('id_universidad').value = data.id_universidad;
            document.getElementById('titulo_otorgado').value = data.titulo_otorgado || '';
            document.getElementById('duracion').value = data.duracion || '';
            document.getElementById('descripcion').value = data.descripcion || '';
            document.getElementById('perfil_egreso').value = data.perfil_egreso || '';
            document.getElementById('campo_laboral').value = data.campo_laboral || '';
            
            // Marcar modalidades
            document.querySelectorAll('input[name="modalidades[]"]').forEach(cb => cb.checked = false);
            if (data.modalidades) {
                data.modalidades.forEach(m => {
                    const checkbox = document.getElementById(`modalidad_${m.id}`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            document.getElementById('modalCarreraTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Carrera';
            document.getElementById('btnGuardarCarrera').innerHTML = 'Actualizar Carrera';
            
            new bootstrap.Modal(document.getElementById('modalCarrera')).show();
        });
}

// Función para ver detalle de carrera
function verCarrera(id) {
    fetch(`index.php?action=carrera-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let modalidadesHtml = '';
            if (data.modalidades && data.modalidades.length > 0) {
                modalidadesHtml = data.modalidades.map(m => 
                    `<span class="badge bg-primary me-1">${m.nombre}</span>`
                ).join('');
            } else {
                modalidadesHtml = '<span class="text-muted">No especificado</span>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-book fs-1 text-primary"></i>
                    <h3 class="mt-2">${data.nombre}</h3>
                    <span class="badge bg-info">${data.universidad_nombre}</span>
                    <span class="badge bg-secondary">${data.region_nombre}</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-info-circle"></i> Información General
                            </div>
                            <div class="card-body">
                                <p><strong>Título que otorga:</strong> ${data.titulo_otorgado || 'No especificado'}</p>
                                <p><strong>Duración:</strong> ${data.duracion || 'No especificada'}</p>
                                <p><strong>Modalidades:</strong><br> ${modalidadesHtml}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-calendar"></i> Fechas
                            </div>
                            <div class="card-body">
                                <p><strong>Fecha de Creación:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                                <p><strong>Última Actualización:</strong> ${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'No hay actualizaciones'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-card-text"></i> Descripción
                            </div>
                            <div class="card-body">
                                <p>${data.descripcion || 'No hay descripción disponible.'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-person-badge"></i> Perfil de Egreso
                            </div>
                            <div class="card-body">
                                <p>${data.perfil_egreso || 'No especificado.'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-briefcase"></i> Campo Laboral
                            </div>
                            <div class="card-body">
                                <p>${data.campo_laboral || 'No especificado.'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detalleCarreraContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerCarrera')).show();
        });
}

// Función para eliminar carrera
function eliminarCarrera(id, nombre) {
    Swal.fire({
        title: '¿Eliminar carrera?',
        html: `¿Estás seguro de eliminar la carrera <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=carrera-eliminar&id=${id}`;
        }
    });
}

// Función para filtrar carreras
function filtrarCarreras() {
    const region = document.getElementById('filtroRegion').value;
    const universidad = document.getElementById('filtroUniversidad').value;
    const busqueda = document.getElementById('buscarCarrera').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tablaCarreras tbody tr');
    
    filas.forEach(fila => {
        if (fila.cells.length > 1) {
            let mostrar = true;
            
            // Filtrar por región
            if (region && fila.dataset.region !== region) {
                mostrar = false;
            }
            
            // Filtrar por universidad
            if (universidad && fila.dataset.universidad !== universidad) {
                mostrar = false;
            }
            
            // Filtrar por búsqueda
            if (busqueda) {
                const texto = fila.textContent.toLowerCase();
                if (!texto.includes(busqueda)) {
                    mostrar = false;
                }
            }
            
            fila.style.display = mostrar ? '' : 'none';
        }
    });
}

// Función para cargar modalidades disponibles (placeholder)
function cargarModalidadesDisponibles() {
    // Ya tenemos todas las modalidades cargadas
}

// Validar formulario antes de enviar
document.getElementById('formCarrera').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const universidad = document.getElementById('id_universidad').value;
    
    if (nombre.length < 3) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre inválido',
            text: 'El nombre debe tener al menos 3 caracteres'
        });
        return;
    }
    
    if (!universidad) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Universidad requerida',
            text: 'Debes seleccionar una universidad'
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

#tablaCarreras td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
}

#modalidadesContainer {
    max-height: 150px;
    overflow-y: auto;
    background-color: #f8f9fc;
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