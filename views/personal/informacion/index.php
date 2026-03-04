<?php
// Verificar que las variables existen
if (!isset($informaciones)) $informaciones = [];
?>

<link rel="stylesheet" href="assets/css/personal/informacion/informacion.css">

<div class="info-catalogo">
    <!-- Header -->
    <div class="catalogo-header">
        <h2>
            <i class="bi bi-info-circle"></i>
            Información de Universidades
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Vista de solo lectura - Información sobre procesos de admisión
        </p>
    </div>

    <!-- Filtros -->
    <div class="filter-section">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <select class="filter-select w-100" id="filtroRegion" onchange="filtrarTabla()">
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
            <div class="col-md-4 mb-3 mb-md-0">
                <input type="text" class="filter-input w-100" id="buscarInfo" placeholder="Buscar universidad..." onkeyup="filtrarTabla()">
            </div>
            <div class="col-md-4 text-md-end">
                <div class="total-badge">
                    <i class="bi bi-building"></i>
                    Total: <?php echo count($informaciones); ?> universidades
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Informaciones -->
    <div class="row">
        <div class="col-12">
            <div class="table-card">
                <div class="card-header">
                    <h6>
                        <i class="bi bi-table"></i>
                        Información por Universidad
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="modern-table" id="tablaInformacion" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Universidad</th>
                                    <th>Región</th>
                                    <th>Modalidades</th>
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
                                    <td colspan="15" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="bi bi-info-circle"></i>
                                            <p>No hay información disponible para ninguna universidad.</p>
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
                                            <?php foreach ($modalidadesUni as $mod): ?>
                                                <span class="modalidad-mini-badge">
                                                    <i class="bi bi-tag"></i>
                                                    <?php echo substr($mod, 0, 10); ?><?php echo strlen($mod) > 10 ? '...' : ''; ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (empty($modalidadesUni)): ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['tipo_proceso'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['tipo_proceso'] ?? '', 0, 30)); ?><?php echo (strlen($i['tipo_proceso'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['tipo_prueba'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['tipo_prueba'] ?? '', 0, 30)); ?><?php echo (strlen($i['tipo_prueba'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['temas'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['temas'] ?? '', 0, 30)); ?><?php echo (strlen($i['temas'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['incidencia'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['incidencia'] ?? '', 0, 30)); ?><?php echo (strlen($i['incidencia'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['registro'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['registro'] ?? '', 0, 30)); ?><?php echo (strlen($i['registro'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['inscripciones'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['inscripciones'] ?? '', 0, 30)); ?><?php echo (strlen($i['inscripciones'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['examen'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['examen'] ?? '', 0, 30)); ?><?php echo (strlen($i['examen'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['postulacion'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['postulacion'] ?? '', 0, 30)); ?><?php echo (strlen($i['postulacion'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['asignacion_cupos'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['asignacion_cupos'] ?? '', 0, 30)); ?><?php echo (strlen($i['asignacion_cupos'] ?? '') > 30) ? '...' : ''; ?>
                                        </td>
                                        <td class="info-cell" title="<?php echo htmlspecialchars($i['matricula'] ?? ''); ?>">
                                            <?php echo htmlspecialchars(substr($i['matricula'] ?? '', 0, 30)); ?><?php echo (strlen($i['matricula'] ?? '') > 30) ? '...' : ''; ?>
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
                                            <button class="btn-view-info" onclick="verInfoDetalle(<?php echo $i['id_universidad']; ?>)">
                                                <i class="bi bi-eye"></i>
                                                Ver
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
</div>

<!-- Modal para Ver Detalle Completo -->
<div class="modal fade" id="modalVerInfo" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content modal-content-info">
            <div class="modal-header modal-header-info">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>
                    Información Detallada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-info" id="detalleInfoContent">
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
                            `<span class="modalidad-badge-lg"><i class="bi bi-tag me-1"></i>${m}</span>`
                        ).join('');
                    } else {
                        modalidadesHtml = '<span class="text-muted">No especificado</span>';
                    }
                    
                    const html = `
                        <div class="text-center mb-4">
                            <h3 style="color: #2d3748; font-weight: 700;">${data.universidad_nombre}</h3>
                            <span class="badge-region" style="font-size: 1rem; padding: 8px 20px;">
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
                                <div class="footer-info">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <strong>Creado por:</strong> ${data.creador_nombre || 'Sistema'} el ${new Date(data.created_at).toLocaleString()}<br>
                                    ${data.actualizado_por ? `<i class="bi bi-pencil-square me-1 mt-1"></i><strong>Última edición:</strong> ${data.actualizador_nombre || 'Desconocido'} el ${new Date(data.updated_at).toLocaleString()}` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('detalleInfoContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalVerInfo')).show();
                });
        });
}

// Función para filtrar tabla
function filtrarTabla() {
    const region = document.getElementById('filtroRegion').value;
    const busqueda = document.getElementById('buscarInfo').value.toLowerCase();
    const filas = document.querySelectorAll('#tablaInformacion tbody tr');
    let visibleCount = 0;
    
    filas.forEach(fila => {
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
    
    // Mostrar mensaje si no hay resultados (excluyendo la fila de "no hay información")
    const tbody = document.querySelector('#tablaInformacion tbody');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && filas.length > 0 && !filas[0].querySelector('td[colspan]')) {
        if (!mensajeExistente) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            msg.innerHTML = `
                <td colspan="15" class="no-results py-4">
                    <i class="bi bi-search me-2"></i>
                    No se encontraron universidades que coincidan con los filtros.
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