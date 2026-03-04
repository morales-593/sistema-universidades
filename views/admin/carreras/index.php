<?php
// Verificar que las variables existen
if (!isset($carreras)) $carreras = [];
if (!isset($universidades)) $universidades = [];
if (!isset($modalidades)) $modalidades = [];
?>
<link rel="stylesheet" href="assets/css/admin/carreras/carreras.css">
<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({rgb(105, 124, 153)
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 3000,
        showConfirmButton: false,
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

<div class="admin-carreras">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-book"></i>
            Gestión de Carreras
        </h2>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevaCarrera()">
            <i class="bi bi-plus-circle"></i>
            Nueva Carrera
        </button>
        
        <div class="filter-section">
            <select class="filter-select" id="filtroRegion" onchange="filtrarCarreras()">
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
            
            <select class="filter-select" id="filtroUniversidad" onchange="filtrarCarreras()">
                <option value="">Todas las universidades</option>
                <?php foreach ($universidades as $u): ?>
                <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" class="filter-input" id="buscarCarrera" placeholder="Buscar carrera..." onkeyup="filtrarCarreras()">
        </div>
    </div>

    <!-- Tabla de Carreras -->
    <div class="table-card-admin">
        <div class="card-header">
            <h6>
                <i class="bi bi-list-task"></i>
                Lista de Carreras
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="modern-table-admin" id="tablaCarreras" width="100%" cellspacing="0">
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
                            <td colspan="8" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-book"></i>
                                    <p>No hay carreras registradas</p>
                                </div>
                            </td>
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
                                <td>
                                    <span class="id-badge">#<?php echo $c['id']; ?></span>
                                </td>
                                <td>
                                    <div class="carrera-nombre"><?php echo htmlspecialchars($c['nombre']); ?></div>
                                </td>
                                <td>
                                    <div class="universidad-nombre"><?php echo htmlspecialchars($c['universidad_nombre']); ?></div>
                                </td>
                                <td>
                                    <span class="badge-region">
                                        <i class="bi bi-geo-alt"></i>
                                        <?php echo $c['region_nombre']; ?>
                                    </span>
                                </td>
                                <td class="titulo-cell" title="<?php echo htmlspecialchars($c['titulo_otorgado'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($c['titulo_otorgado'] ?? 'No especificado'); ?>
                                </td>
                                <td class="duracion-cell">
                                    <i class="bi bi-clock me-1"></i>
                                    <?php echo htmlspecialchars($c['duracion'] ?? 'N/E'); ?>
                                </td>
                                <td>
                                    <?php foreach (array_slice($modalidadesCarrera, 0, 2) as $m): ?>
                                        <span class="modalidad-badge-sm"><?php echo $m['nombre']; ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($modalidadesCarrera) > 2): ?>
                                        <span class="modalidad-badge-sm">+<?php echo count($modalidadesCarrera) - 2; ?></span>
                                    <?php endif; ?>
                                    <?php if (empty($modalidadesCarrera)): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button class="btn-action btn-view" onclick="verCarrera(<?php echo $c['id']; ?>)" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editarCarrera(<?php echo $c['id']; ?>)" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-action btn-delete" onclick="eliminarCarrera(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['nombre']); ?>')" title="Eliminar">
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

<!-- Modal para Ver Detalle de Carrera -->
<div class="modal fade" id="modalVerCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-book me-2"></i>
                    Detalle de Carrera
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleCarreraContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Carrera -->
<div class="modal fade" id="modalCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalCarreraTitle">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Carrera
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=carrera-guardar" id="formCarrera">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="carreraId">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nombre" class="form-label-admin">Nombre de la Carrera *</label>
                            <input type="text" class="form-control-admin" id="nombre" name="nombre" required maxlength="200" placeholder="Ej: Ingeniería en Sistemas">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="duracion" class="form-label-admin">Duración</label>
                            <input type="text" class="form-control-admin" id="duracion" name="duracion" placeholder="Ej: 9 semestres">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="id_universidad" class="form-label-admin">Universidad *</label>
                            <select class="form-select-admin" id="id_universidad" name="id_universidad" required onchange="cargarModalidadesDisponibles()">
                                <option value="">Seleccione una universidad</option>
                                <?php foreach ($universidades as $u): ?>
                                <option value="<?php echo $u['id']; ?>" data-region="<?php echo $u['region_nombre']; ?>">
                                    <?php echo htmlspecialchars($u['nombre']); ?> (<?php echo $u['region_nombre']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="titulo_otorgado" class="form-label-admin">Título que otorga</label>
                            <input type="text" class="form-control-admin" id="titulo_otorgado" name="titulo_otorgado" placeholder="Ej: Ingeniero/a en Sistemas">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label-admin">Descripción</label>
                            <textarea class="form-control-admin" id="descripcion" name="descripcion" rows="2" placeholder="Breve descripción de la carrera"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="perfil_egreso" class="form-label-admin">Perfil de Egreso</label>
                            <textarea class="form-control-admin" id="perfil_egreso" name="perfil_egreso" rows="3" placeholder="Habilidades y competencias del egresado"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="campo_laboral" class="form-label-admin">Campo Laboral</label>
                            <textarea class="form-control-admin" id="campo_laboral" name="campo_laboral" rows="3" placeholder="Áreas de trabajo donde puede desempeñarse"></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label-admin">Modalidades</label>
                            <div class="modalidades-container" id="modalidadesContainer">
                                <?php foreach ($modalidades as $m): ?>
                                <div class="modalidad-check">
                                    <input type="checkbox" 
                                           name="modalidades[]" 
                                           value="<?php echo $m['id']; ?>" 
                                           id="modalidad_<?php echo $m['id']; ?>">
                                    <label for="modalidad_<?php echo $m['id']; ?>">
                                        <?php echo htmlspecialchars($m['nombre']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="form-hint">
                                <i class="bi bi-info-circle me-1"></i>
                                Seleccione una o varias modalidades
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardarCarrera">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Carrera
                    </button>
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
    document.getElementById('modalCarreraTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Carrera';
    document.getElementById('btnGuardarCarrera').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Carrera';
    
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
            
            document.getElementById('modalCarreraTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Carrera';
            document.getElementById('btnGuardarCarrera').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Carrera';
            
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
                    `<span class="badge" style="background: linear-gradient(135deg, #667eea15, #764ba215); color: #667eea; padding: 5px 12px; border-radius: 50px; margin: 2px; display: inline-block;">${m.nombre}</span>`
                ).join('');
            } else {
                modalidadesHtml = '<span class="text-muted">No especificado</span>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <div class="detail-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <h3 style="color: #2d3748; font-weight: 700;">${data.nombre}</h3>
                    <div class="mt-2">
                        <span class="badge-region" style="font-size: 0.9rem; padding: 8px 15px;">
                            <i class="bi bi-building"></i> ${data.universidad_nombre}
                        </span>
                        <span class="badge-region ms-2" style="font-size: 0.9rem; padding: 8px 15px;">
                            <i class="bi bi-geo-alt"></i> ${data.region_nombre}
                        </span>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-info-circle"></i> Información General</h6>
                            <p><i class="bi bi-award"></i> <strong>Título:</strong> ${data.titulo_otorgado || 'No especificado'}</p>
                            <p><i class="bi bi-clock"></i> <strong>Duración:</strong> ${data.duracion || 'No especificada'}</p>
                            <p><i class="bi bi-grid"></i> <strong>Modalidades:</strong><br> ${modalidadesHtml}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-calendar"></i> Fechas</h6>
                            <p><i class="bi bi-calendar-plus"></i> <strong>Creación:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            <p><i class="bi bi-calendar-check"></i> <strong>Última Actualización:</strong> ${data.updated_at ? new Date(data.updated_at).toLocaleDateString() : 'No hay actualizaciones'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-card-text"></i> Descripción</h6>
                            <p>${data.descripcion || 'No hay descripción disponible.'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-person-badge"></i> Perfil de Egreso</h6>
                            <p>${data.perfil_egreso || 'No especificado.'}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-briefcase"></i> Campo Laboral</h6>
                            <p>${data.campo_laboral || 'No especificado.'}</p>
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
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: 'white'
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
    let visibleCount = 0;
    
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
            if (mostrar) visibleCount++;
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const tbody = document.querySelector('#tablaCarreras tbody');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && filas.length > 0 && filas[0].cells.length > 1) {
        if (!mensajeExistente) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            msg.innerHTML = `
                <td colspan="8" class="text-center">
                    <div class="no-results">
                        <i class="bi bi-search me-2"></i>
                        No se encontraron carreras que coincidan con los filtros.
                    </div>
                </td>
            `;
            tbody.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
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
            text: 'El nombre debe tener al menos 3 caracteres',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
    
    if (!universidad) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Universidad requerida',
            text: 'Debes seleccionar una universidad',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
});
</script>