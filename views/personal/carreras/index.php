<?php
// Verificar que las variables existen
if (!isset($carreras)) $carreras = [];
if (!isset($universidades)) $universidades = [];
if (!isset($modalidades)) $modalidades = [];
?>

<link rel="stylesheet" href="assets/css/personal/carreras/carrera.css">

<div class="carreras-catalogo">
    <!-- Header -->
    <div class="catalogo-header">
        <h2>
            <i class="bi bi-book"></i>
            Catálogo de Carreras
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Vista de solo lectura - Personal
        </p>
    </div>

    <!-- Filtros -->
    <div class="filter-section">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="filter-select w-100" id="filtroRegion" onchange="filtrarCarreras()">
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
                <select class="filter-select w-100" id="filtroUniversidad" onchange="filtrarCarreras()">
                    <option value="">Todas las universidades</option>
                    <?php foreach ($universidades as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="filter-input w-100" id="buscarCarrera" placeholder="Buscar carrera..." onkeyup="filtrarCarreras()">
            </div>
        </div>
    </div>

    <!-- Grid de Carreras -->
    <div class="row" id="carrerasGrid">
        <?php if (empty($carreras)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-book"></i>
                <p>No hay carreras registradas en el sistema.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($carreras as $c): 
                $carreraModel = new Carrera();
                $modalidadesCarrera = $carreraModel->getModalidades($c['id']);
            ?>
            <div class="col-md-6 col-lg-4 mb-4 carrera-card" 
                 data-id="<?php echo $c['id']; ?>"
                 data-region="<?php echo htmlspecialchars($c['region_nombre']); ?>"
                 data-universidad="<?php echo $c['id_universidad']; ?>"
                 data-nombre="<?php echo strtolower(htmlspecialchars($c['nombre'])); ?>">
                <div class="card-carrera">
                    <div class="card-header-carrera">
                        <div class="carrera-icon">
                            <i class="bi bi-book"></i>
                        </div>
                        <div class="carrera-titulo">
                            <h5><?php echo htmlspecialchars($c['nombre']); ?></h5>
                            <small>
                                <i class="bi bi-building"></i>
                                <?php echo htmlspecialchars($c['universidad_nombre']); ?>
                            </small>
                        </div>
                    </div>
                    <div class="card-body-carrera">
                        <div class="badge-container">
                            <span class="badge-region">
                                <i class="bi bi-geo-alt"></i>
                                <?php echo $c['region_nombre']; ?>
                            </span>
                            <?php if ($c['duracion']): ?>
                                <span class="badge-duracion">
                                    <i class="bi bi-clock"></i>
                                    <?php echo $c['duracion']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="carrera-descripcion">
                            <?php echo substr(htmlspecialchars($c['descripcion'] ?? ''), 0, 80); ?>
                            <?php if (strlen($c['descripcion'] ?? '') > 80) echo '...'; ?>
                        </div>
                        
                        <div class="titulo-info">
                            <strong><i class="bi bi-award"></i> Título:</strong>
                            <?php echo htmlspecialchars($c['titulo_otorgado'] ?? 'No especificado'); ?>
                        </div>
                        
                        <div class="modalidades-container">
                            <strong><i class="bi bi-grid"></i> Modalidades:</strong>
                            <div>
                                <?php foreach ($modalidadesCarrera as $m): ?>
                                    <span class="modalidad-badge">
                                        <i class="bi bi-tag"></i>
                                        <?php echo $m['nombre']; ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (empty($modalidadesCarrera)): ?>
                                    <span class="text-muted">No especificado</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <button class="btn-view-carrera" onclick="verCarrera(<?php echo $c['id']; ?>)">
                            <i class="bi bi-eye"></i>
                            Ver detalles completos
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Ver Detalle de Carrera -->
<div class="modal fade" id="modalVerCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-carrera">
            <div class="modal-header modal-header-carrera">
                <h5 class="modal-title">
                    <i class="bi bi-book me-2"></i>
                    Detalle de Carrera
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-carrera" id="detalleCarreraContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver detalle de carrera
function verCarrera(id) {
    fetch(`index.php?action=carrera-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let modalidadesHtml = '';
            if (data.modalidades && data.modalidades.length > 0) {
                modalidadesHtml = data.modalidades.map(m => 
                    `<span class="modalidad-badge me-1">${m.nombre}</span>`
                ).join('');
            } else {
                modalidadesHtml = '<span class="text-muted">No especificado</span>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <div class="carrera-detail-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <h3 style="color: #2d3748; font-weight: 700; margin-bottom: 10px;">${data.nombre}</h3>
                    <div>
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
                        <div class="detail-card-carrera">
                            <h6><i class="bi bi-info-circle"></i> Información General</h6>
                            <p><i class="bi bi-award"></i> <strong>Título:</strong> ${data.titulo_otorgado || 'No especificado'}</p>
                            <p><i class="bi bi-clock"></i> <strong>Duración:</strong> ${data.duracion || 'No especificada'}</p>
                            <p><i class="bi bi-grid"></i> <strong>Modalidades:</strong><br> ${modalidadesHtml}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card-carrera">
                            <h6><i class="bi bi-calendar"></i> Información de Registro</h6>
                            <p><i class="bi bi-calendar-check"></i> <strong>Fecha de Creación:</strong><br> ${new Date(data.created_at).toLocaleDateString()}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="detail-card-carrera">
                            <h6><i class="bi bi-card-text"></i> Descripción</h6>
                            <p>${data.descripcion || 'No hay descripción disponible.'}</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-card-carrera">
                            <h6><i class="bi bi-person-badge"></i> Perfil de Egreso</h6>
                            <p>${data.perfil_egreso || 'No especificado.'}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="detail-card-carrera">
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

// Función para filtrar carreras
function filtrarCarreras() {
    const region = document.getElementById('filtroRegion').value;
    const universidad = document.getElementById('filtroUniversidad').value;
    const busqueda = document.getElementById('buscarCarrera').value.toLowerCase();
    
    const tarjetas = document.querySelectorAll('.carrera-card');
    let visibleCount = 0;
    
    tarjetas.forEach(tarjeta => {
        let mostrar = true;
        
        // Filtrar por región
        if (region && tarjeta.dataset.region !== region) {
            mostrar = false;
        }
        
        // Filtrar por universidad
        if (universidad && tarjeta.dataset.universidad !== universidad) {
            mostrar = false;
        }
        
        // Filtrar por búsqueda
        if (busqueda && !tarjeta.dataset.nombre.includes(busqueda)) {
            mostrar = false;
        }
        
        tarjeta.style.display = mostrar ? '' : 'none';
        if (mostrar) visibleCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    const grid = document.getElementById('carrerasGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 no-results';
            msg.innerHTML = `
                <i class="bi bi-search me-2"></i>
                No se encontraron carreras que coincidan con los filtros.
            `;
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}
</script>