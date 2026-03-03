<?php
// Verificar que las variables existen
if (!isset($informaciones)) $informaciones = [];
if (!isset($universidades)) $universidades = [];
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
            <i class="bi bi-info-circle"></i> Información de Universidades
        </h2>
        <p class="text-muted">Gestión de información detallada sobre procesos de admisión</p>
    </div>
</div>

<!-- Botón Nueva Información -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevaInfo()">
            <i class="bi bi-plus-circle"></i> Agregar Información
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filtroRegion" onchange="filtrarTabla()">
            <option value="">Todas las regiones</option>
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
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="buscarInfo" placeholder="Buscar universidad..." onkeyup="filtrarTabla()">
    </div>
</div>

<!-- Tabla de Informaciones -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Información por Universidad</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaInformacion" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>Universidad</th>
                                <th>Región</th>
                                <th>Tipo de Proceso</th>
                                <th>Tipo de Prueba</th>
                                <th>Temas</th>
                                <th>Incidencia</th>
                                <th>Registro</th>
                                <th>Inscripciones</th>
                                <th>Examen</th>
                                <th>Postulación</th>
                                <th>Asignación Cupos</th>
                                <th>Matrícula</th>
                                <th>Última Edición</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($informaciones)): ?>
                            <tr>
                                <td colspan="14" class="text-center">No hay información registrada para ninguna universidad.</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($informaciones as $i): ?>
                                <tr data-region="<?php echo htmlspecialchars($i['region_nombre']); ?>" 
                                    data-nombre="<?php echo strtolower(htmlspecialchars($i['universidad_nombre'])); ?>">
                                    <td><strong><?php echo htmlspecialchars($i['universidad_nombre']); ?></strong></td>
                                    <td><span class="badge bg-info"><?php echo $i['region_nombre']; ?></span></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['tipo_proceso'] ?? '', 0, 50))); ?><?php echo (strlen($i['tipo_proceso'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['tipo_prueba'] ?? '', 0, 50))); ?><?php echo (strlen($i['tipo_prueba'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['temas'] ?? '', 0, 50))); ?><?php echo (strlen($i['temas'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['incidencia'] ?? '', 0, 50))); ?><?php echo (strlen($i['incidencia'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['registro'] ?? '', 0, 50))); ?><?php echo (strlen($i['registro'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['inscripciones'] ?? '', 0, 50))); ?><?php echo (strlen($i['inscripciones'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['examen'] ?? '', 0, 50))); ?><?php echo (strlen($i['examen'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['postulacion'] ?? '', 0, 50))); ?><?php echo (strlen($i['postulacion'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['asignacion_cupos'] ?? '', 0, 50))); ?><?php echo (strlen($i['asignacion_cupos'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars(substr($i['matricula'] ?? '', 0, 50))); ?><?php echo (strlen($i['matricula'] ?? '') > 50) ? '...' : ''; ?></td>
                                    <td>
                                        <small>
                                            <?php if ($i['actualizado_por']): ?>
                                                <?php echo $i['actualizador_nombre'] ?? 'Desconocido'; ?><br>
                                                <span class="text-muted"><?php echo date('d/m/Y H:i', strtotime($i['updated_at'])); ?></span>
                                            <?php else: ?>
                                                <?php echo $i['creador_nombre'] ?? 'Sistema'; ?><br>
                                                <span class="text-muted"><?php echo date('d/m/Y H:i', strtotime($i['created_at'])); ?></span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-info" onclick="verInfoDetalle(<?php echo $i['id_universidad']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="editarInfo(<?php echo $i['id_universidad']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarInfo(<?php echo $i['id']; ?>, '<?php echo htmlspecialchars($i['universidad_nombre']); ?>')">
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

<!-- Modal para Ver Detalle Completo -->
<div class="modal fade" id="modalVerInfo" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle"></i> Información Detallada
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleInfoContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Información -->
<div class="modal fade" id="modalInfo" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalInfoTitle">
                    <i class="bi bi-plus-circle"></i> Agregar Información
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=informacion-guardar" id="formInfo">
                <div class="modal-body">
                    <input type="hidden" name="id" id="infoId">
                    
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="id_universidad" class="form-label">Universidad *</label>
                            <select class="form-select" id="id_universidad" name="id_universidad" required>
                                <option value="">Seleccione una universidad</option>
                                <?php foreach ($universidades as $u): ?>
                                <option value="<?php echo $u['id']; ?>" data-region="<?php echo $u['region_nombre']; ?>">
                                    <?php echo htmlspecialchars($u['nombre']); ?> (<?php echo $u['region_nombre']; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_proceso" class="form-label">Tipo de Proceso</label>
                            <textarea class="form-control" id="tipo_proceso" name="tipo_proceso" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tipo_prueba" class="form-label">Tipo de Prueba</label>
                            <textarea class="form-control" id="tipo_prueba" name="tipo_prueba" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="temas" class="form-label">Temas</label>
                            <textarea class="form-control" id="temas" name="temas" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="incidencia" class="form-label">Incidencia</label>
                            <textarea class="form-control" id="incidencia" name="incidencia" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="registro" class="form-label">Registro</label>
                            <textarea class="form-control" id="registro" name="registro" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="inscripciones" class="form-label">Inscripciones</label>
                            <textarea class="form-control" id="inscripciones" name="inscripciones" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="examen" class="form-label">Examen</label>
                            <textarea class="form-control" id="examen" name="examen" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="postulacion" class="form-label">Postulación</label>
                            <textarea class="form-control" id="postulacion" name="postulacion" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="asignacion_cupos" class="form-label">Asignación de Cupos</label>
                            <textarea class="form-control" id="asignacion_cupos" name="asignacion_cupos" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="matricula" class="form-label">Matrícula</label>
                            <textarea class="form-control" id="matricula" name="matricula" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarInfo">Guardar Información</button>
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
    document.getElementById('modalInfoTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Agregar Información';
    document.getElementById('btnGuardarInfo').innerHTML = 'Guardar Información';
    
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
            
            document.getElementById('modalInfoTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Información';
            document.getElementById('btnGuardarInfo').innerHTML = 'Actualizar Información';
            
            new bootstrap.Modal(document.getElementById('modalInfo')).show();
        });
}

// Función para ver detalle completo
function verInfoDetalle(id_universidad) {
    fetch(`index.php?action=informacion-ver&id=${id_universidad}`)
        .then(response => response.json())
        .then(data => {
            const html = `
                <div class="text-center mb-4">
                    <h3>${data.universidad_nombre}</h3>
                    <span class="badge bg-info">${data.region_nombre}</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Tipo de Proceso</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.tipo_proceso || 'No especificado')}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Tipo de Prueba</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.tipo_prueba || 'No especificado')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Temas</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.temas || 'No especificado')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Incidencia</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.incidencia || 'No especificado')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Registro</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.registro || 'No especificado')}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Inscripciones</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.inscripciones || 'No especificado')}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Examen</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.examen || 'No especificado')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Postulación</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.postulacion || 'No especificado')}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Asignación de Cupos</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.asignacion_cupos || 'No especificado')}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong>Matrícula</strong>
                            </div>
                            <div class="card-body">
                                ${nl2br(data.matricula || 'No especificado')}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <small>
                                    <strong>Creado por:</strong> ${data.creador_nombre || 'Sistema'} el ${new Date(data.created_at).toLocaleString()}<br>
                                    ${data.actualizado_por ? `<strong>Última edición:</strong> ${data.actualizador_nombre || 'Desconocido'} el ${new Date(data.updated_at).toLocaleString()}` : ''}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detalleInfoContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerInfo')).show();
        });
}

// Función para eliminar información
function eliminarInfo(id, nombre) {
    Swal.fire({
        title: '¿Eliminar información?',
        html: `¿Estás seguro de eliminar la información de <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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
    
    filas.forEach(fila => {
        let mostrar = true;
        
        if (region && fila.dataset.region !== region) {
            mostrar = false;
        }
        
        if (busqueda && !fila.dataset.nombre.includes(busqueda)) {
            mostrar = false;
        }
        
        fila.style.display = mostrar ? '' : 'none';
    });
}

// Función auxiliar para nl2br
function nl2br(text) {
    if (!text) return '';
    return text.replace(/\n/g, '<br>');
}
</script>

<style>
#tablaInformacion th {
    background-color: #f8f9fc;
    font-weight: 600;
    font-size: 0.85rem;
    white-space: nowrap;
}

#tablaInformacion td {
    font-size: 0.85rem;
    vertical-align: middle;
}

.btn-group {
    display: flex;
    gap: 3px;
}

.btn-group .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    
    #tablaInformacion {
        font-size: 0.8rem;
    }
    
    .btn-group {
        flex-direction: column;
    }
}
</style>