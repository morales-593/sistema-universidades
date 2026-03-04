<?php
// Verificar que las variables existen
if (!isset($informaciones)) $informaciones = [];
if (!isset($universidades)) $universidades = [];
?>

<link rel="stylesheet" href="assets/css/admin/informacion/informacion.css">

<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
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

<div class="admin-info">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-info-circle"></i>
            Información de Universidades
        </h2>
        <p>Gestión de información detallada sobre procesos de admisión</p>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevaInfo()">
            <i class="bi bi-plus-circle"></i>
            Agregar Información
        </button>
        
        <div class="filter-section">
            <select class="filter-select" id="filtroRegion" onchange="filtrarTabla()">
                <option value="">filtrar</option>
                <?php
                $regionesUnicas = [];
                foreach ($informaciones as $i) {
                    if (!in_array($i['region_nombre'], $regionesUnicas)) {
                        $regionesUnicas[] = $i['region_nombre'];
                        echo "<option value=\"" . htmlspecialchars($i['region_nombre']) . "\">" . htmlspecialchars($i['region_nombre']) . "</option>";
                    }
                }
                ?>
            </select>
            
            <input type="text" class="filter-input" id="buscarInfo" placeholder="Buscar universidad..." onkeyup="filtrarTabla()">
        </div>
    </div>

    <!-- Tabla de Informaciones -->
    <div class="table-card-admin">
        <div class="card-header">
            <h6>
                <i class="bi bi-list-task"></i>
                Lista de Información por Universidad
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="modern-table-admin" id="tablaInformacion" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Universidad</th>
                            <th>Región</th>
                            <th>Modalidades</th>
                            <th>Tipo Proceso</th>
                            <th>Tipo Prueba</th>
                            <th>Temas</th>
                            <th>Incidencia</th>
                            <th>Registro</th>
                            <th>Inscripciones</th>
                            <th>Examen</th>
                            <th>Postulación</th>
                            <th>Asig. Cupos</th>
                            <th>Matrícula</th>
                            <th>Última Edición</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($informaciones)): ?>
                        <tr>
                            <td colspan="15" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-info-circle"></i>
                                    <p>No hay información registrada para ninguna universidad.</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($informaciones as $i): 
                                require_once 'models/Carrera.php';
                                $carreraModel = new Carrera();
                                $modalidadesUni = [];
                                $carreras = $carreraModel->getByUniversidad($i['id_universidad']);
                                foreach ($carreras as $c) {
                                    $mods = $carreraModel->getModalidades($c['id']);
                                    foreach ($mods as $m) {
                                        if (!in_array($m['nombre'], $modalidadesUni)) {
                                            $modalidadesUni[] = $m['nombre'];
                                        }
                                    }
                                }
                            ?>
                            <tr data-region="<?php echo htmlspecialchars($i['region_nombre']); ?>" 
                                data-nombre="<?php echo strtolower(htmlspecialchars($i['universidad_nombre'])); ?>">
                                <td class="universidad-cell"><?php echo htmlspecialchars($i['universidad_nombre']); ?></td>
                                <td>
                                    <span class="badge-region">
                                        <i class="bi bi-geo-alt"></i>
                                        <?php echo $i['region_nombre']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php foreach (array_slice($modalidadesUni, 0, 2) as $mod): ?>
                                        <span class="modalidad-mini-badge"><?php echo substr($mod, 0, 8); ?><?php echo strlen($mod) > 8 ? '...' : ''; ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($modalidadesUni) > 2): ?>
                                        <span class="modalidad-mini-badge">+<?php echo count($modalidadesUni) - 2; ?></span>
                                    <?php endif; ?>
                                    <?php if (empty($modalidadesUni)): ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['tipo_proceso'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['tipo_proceso'] ?? '', 0, 20)); ?><?php echo (strlen($i['tipo_proceso'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['tipo_prueba'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['tipo_prueba'] ?? '', 0, 20)); ?><?php echo (strlen($i['tipo_prueba'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['temas'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['temas'] ?? '', 0, 20)); ?><?php echo (strlen($i['temas'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['incidencia'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['incidencia'] ?? '', 0, 20)); ?><?php echo (strlen($i['incidencia'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['registro'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['registro'] ?? '', 0, 20)); ?><?php echo (strlen($i['registro'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['inscripciones'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['inscripciones'] ?? '', 0, 20)); ?><?php echo (strlen($i['inscripciones'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['examen'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['examen'] ?? '', 0, 20)); ?><?php echo (strlen($i['examen'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['postulacion'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['postulacion'] ?? '', 0, 20)); ?><?php echo (strlen($i['postulacion'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['asignacion_cupos'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['asignacion_cupos'] ?? '', 0, 20)); ?><?php echo (strlen($i['asignacion_cupos'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td class="info-cell" title="<?php echo htmlspecialchars($i['matricula'] ?? ''); ?>">
                                    <?php echo htmlspecialchars(substr($i['matricula'] ?? '', 0, 20)); ?><?php echo (strlen($i['matricula'] ?? '') > 20) ? '...' : ''; ?>
                                </td>
                                <td>
                                    <div class="fecha-info">
                                        <?php if ($i['actualizado_por']): ?>
                                            <div class="nombre"><?php echo $i['actualizador_nombre'] ?? 'Desconocido'; ?></div>
                                            <div class="fecha"><?php echo date('d/m/Y H:i', strtotime($i['updated_at'])); ?></div>
                                        <?php else: ?>
                                            <div class="nombre"><?php echo $i['creador_nombre'] ?? 'Sistema'; ?></div>
                                            <div class="fecha"><?php echo date('d/m/Y H:i', strtotime($i['created_at'])); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <button class="btn-action btn-view" onclick="verInfoDetalle(<?php echo $i['id_universidad']; ?>)" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn-action btn-edit" onclick="editarInfo(<?php echo $i['id_universidad']; ?>)" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-action btn-delete" onclick="eliminarInfo(<?php echo $i['id']; ?>, '<?php echo htmlspecialchars($i['universidad_nombre']); ?>')" title="Eliminar">
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

<!-- Modal para Ver Detalle Completo -->
<div class="modal fade" id="modalVerInfo" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>
                    Información Detallada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleInfoContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Información -->
<div class="modal fade" id="modalInfo" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalInfoTitle">
                    <i class="bi bi-plus-circle me-2"></i>
                    Agregar Información
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=informacion-guardar" id="formInfo">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="infoId">
                    
                    <div class="mb-3">
                        <label for="id_universidad" class="form-label-admin">Universidad *</label>
                        <select class="form-select-admin" id="id_universidad" name="id_universidad" required>
                            <option value="">Seleccione una universidad</option>
                            <?php foreach ($universidades as $u): ?>
                            <option value="<?php echo $u['id']; ?>" data-region="<?php echo $u['region_nombre']; ?>">
                                <?php echo htmlspecialchars($u['nombre']); ?> (<?php echo $u['region_nombre']; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_proceso" class="form-label-admin">Tipo de Proceso</label>
                            <textarea class="form-control-admin" id="tipo_proceso" name="tipo_proceso" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_prueba" class="form-label-admin">Tipo de Prueba</label>
                            <textarea class="form-control-admin" id="tipo_prueba" name="tipo_prueba" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="temas" class="form-label-admin">Temas</label>
                            <textarea class="form-control-admin" id="temas" name="temas" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="incidencia" class="form-label-admin">Incidencia</label>
                            <textarea class="form-control-admin" id="incidencia" name="incidencia" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="registro" class="form-label-admin">Registro</label>
                            <textarea class="form-control-admin" id="registro" name="registro" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="inscripciones" class="form-label-admin">Inscripciones</label>
                            <textarea class="form-control-admin" id="inscripciones" name="inscripciones" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="examen" class="form-label-admin">Examen</label>
                            <textarea class="form-control-admin" id="examen" name="examen" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="postulacion" class="form-label-admin">Postulación</label>
                            <textarea class="form-control-admin" id="postulacion" name="postulacion" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="asignacion_cupos" class="form-label-admin">Asignación de Cupos</label>
                            <textarea class="form-control-admin" id="asignacion_cupos" name="asignacion_cupos" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="matricula" class="form-label-admin">Matrícula</label>
                            <textarea class="form-control-admin" id="matricula" name="matricula" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardarInfo">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Información
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nueva información
function abrirModalNuevaInfo() {
    document.getElementById('formInfo').reset();
    document.getElementById('infoId').value = '';
    document.getElementById('modalInfoTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Agregar Información';
    document.getElementById('btnGuardarInfo').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Información';
    
    // Cargar universidades sin información
    fetch('index.php?action=informacion-sin-info')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('id_universidad');
            select.innerHTML = '<option value="">Seleccione una universidad</option>';
            
            data.forEach(u => {
                const option = document.createElement('option');
                option.value = u.id;
                option.textContent = `${u.nombre} (${u.region_nombre})`;
                select.appendChild(option);
            });
        });
    
    new bootstrap.Modal(document.getElementById('modalInfo')).show();
}

// Función para editar información
function editarInfo(id_universidad) {
    fetch(`index.php?action=informacion-ver&id=${id_universidad}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('infoId').value = data.id;
            
            // Cargar todas las universidades para el select
            <?php
            echo "const universidades = " . json_encode($universidades) . ";";
            ?>
            
            const select = document.getElementById('id_universidad');
            select.innerHTML = '<option value="">Seleccione una universidad</option>';
            universidades.forEach(u => {
                const option = document.createElement('option');
                option.value = u.id;
                option.textContent = `${u.nombre} (${u.region_nombre})`;
                if (u.id == data.id_universidad) option.selected = true;
                select.appendChild(option);
            });
            
            document.getElementById('tipo_proceso').value = data.tipo_proceso || '';
            document.getElementById('tipo_prueba').value = data.tipo_prueba || '';
            document.getElementById('temas').value = data.temas || '';
            document.getElementById('incidencia').value = data.incidencia || '';
            document.getElementById('registro').value = data.registro || '';
            document.getElementById('inscripciones').value = data.inscripciones || '';
            document.getElementById('examen').value = data.examen || '';
            document.getElementById('postulacion').value = data.postulacion || '';
            document.getElementById('asignacion_cupos').value = data.asignacion_cupos || '';
            document.getElementById('matricula').value = data.matricula || '';
            
            document.getElementById('modalInfoTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Información';
            document.getElementById('btnGuardarInfo').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Información';
            
            new bootstrap.Modal(document.getElementById('modalInfo')).show();
        });
}

// Función para ver detalle completo
function verInfoDetalle(id_universidad) {
    fetch(`index.php?action=informacion-ver&id=${id_universidad}`)
        .then(response => response.json())
        .then(data => {
            fetch(`index.php?action=carreras-por-universidad&id_universidad=${id_universidad}`)
                .then(response => response.json())
                .then(carreras => {
                    let modalidadesSet = new Set();
                    carreras.forEach(c => {
                        if (c.modalidades) {
                            c.modalidades.forEach(m => modalidadesSet.add(m.nombre));
                        }
                    });
                    const modalidades = Array.from(modalidadesSet);
                    
                    let modalidadesHtml = '';
                    if (modalidades.length > 0) {
                        modalidadesHtml = modalidades.map(m => 
                            `<span class="badge" style="background: linear-gradient(135deg, #667eea15, #764ba215); color: #667eea; padding: 5px 12px; border-radius: 50px; margin: 2px; display: inline-block;">${m}</span>`
                        ).join('');
                    } else {
                        modalidadesHtml = '<span class="text-muted">No especificado</span>';
                    }
                    
                    const html = `
                        <div class="text-center mb-4">
                            <h3 style="color: #2d3748; font-weight: 700;">${data.universidad_nombre}</h3>
                            <span class="badge-region" style="font-size: 0.9rem; padding: 8px 20px;">
                                <i class="bi bi-geo-alt"></i> ${data.region_nombre}
                            </span>
                            <div class="mt-3">
                                <strong style="color: #2d3748;">Modalidades:</strong><br>
                                <div class="mt-2">${modalidadesHtml}</div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-gear"></i> Tipo de Proceso
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.tipo_proceso || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-pencil-square"></i> Tipo de Prueba
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.tipo_prueba || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-book"></i> Temas
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.temas || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-exclamation-triangle"></i> Incidencia
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.incidencia || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-pencil"></i> Registro
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.registro || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-file-text"></i> Inscripciones
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.inscripciones || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-pencil-square"></i> Examen
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.examen || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-send"></i> Postulación
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.postulacion || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-grid"></i> Asignación de Cupos
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.asignacion_cupos || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-detail-card">
                                    <div class="card-header-info">
                                        <i class="bi bi-check-circle"></i> Matrícula
                                    </div>
                                    <div class="card-body-info">
                                        ${nl2br(data.matricula || 'No especificado')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="info-detail-card" style="background: #edf2f7;">
                                    <div class="card-body-info">
                                        <small>
                                            <i class="bi bi-person-circle me-1"></i>
                                            <strong>Creado por:</strong> ${data.creador_nombre || 'Sistema'} el ${new Date(data.created_at).toLocaleString()}<br>
                                            ${data.actualizado_por ? `<i class="bi bi-pencil-square me-1 mt-1"></i><strong>Última edición:</strong> ${data.actualizador_nombre || 'Desconocido'} el ${new Date(data.updated_at).toLocaleString()}` : ''}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('detalleInfoContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerInfo')).show();
                });
        });
}

// Función para eliminar información
function eliminarInfo(id, nombre) {
    Swal.fire({
        title: '¿Eliminar información?',
        html: `¿Estás seguro de eliminar la información de <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: 'white'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=informacion-eliminar&id=${id}`;
        }
    });
}

// Función para filtrar tabla
function filtrarTabla() {
    const region = document.getElementById('filtroRegion').value;
    const busqueda = document.getElementById('buscarInfo').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaInformacion tbody tr');
    let visibleCount = 0;
    
    filas.forEach(fila => {
        // Saltar la fila de "no hay información" si existe
        if (fila.querySelector('td[colspan]')) return;
        
        let mostrar = true;
        
        if (region && fila.dataset.region !== region) {
            mostrar = false;
        }
        
        if (busqueda && !fila.dataset.nombre.includes(busqueda)) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibleCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    const tbody = document.querySelector('#tablaInformacion tbody');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && filas.length > 0 && !filas[0].querySelector('td[colspan]')) {
        if (!mensajeExistente) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            msg.innerHTML = `
                <td colspan="15" class="text-center">
                    <div class="no-results">
                        <i class="bi bi-search me-2"></i>
                        No se encontraron universidades que coincidan con los filtros.
                    </div>
                </td>
            `;
            tbody.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

// Función auxiliar para nl2br
function nl2br(text) {
    if (!text) return '';
    return text.replace(/\n/g, '<br>');
}
</script>