<?php
// Verificar que las variables existen
if (!isset($informaciones)) $informaciones = [];
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-info-circle"></i> Información de Universidades
        </h2>
        <p class="text-muted">Vista de solo lectura - Información sobre procesos de admisión</p>
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
    <div class="col-md-4 text-end">
        <span class="text-muted">Total: <?php echo count($informaciones); ?> universidades</span>
    </div>
</div>

<!-- Tabla de Informaciones -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Información por Universidad</h6>
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
                                <th>Última Actualización</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($informaciones)): ?>
                            <tr>
                                <td colspan="14" class="text-center">No hay información disponible para ninguna universidad.</td>
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
                                        <button class="btn btn-sm btn-info" onclick="verInfoDetalle(<?php echo $i['id_universidad']; ?>)">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
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

<script>
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

@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    
    #tablaInformacion {
        font-size: 0.8rem;
    }
}
</style>