<?php
// Verificar que las variables existen
if (!isset($universidades)) $universidades = [];
if (!isset($regiones)) $regiones = [];
?>

<link rel="stylesheet" href="assets/css/personal/universidad/universidad.css">

<div class="universidades-catalogo">
    <!-- Header -->
    <div class="catalogo-header">
        <h2>
            <i class="bi bi-buildings"></i>
            Catálogo de Universidades
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Vista de solo lectura - Personal
        </p>
    </div>

    <!-- Filtros -->
    <div class="filter-section">
        <div class="row align-items-center">
            <div class="col-md-4 mb-3 mb-md-0">
                <select class="filter-select w-100" id="filtroRegion" onchange="filtrarUniversidades()">
                    <option value="">Todas las regiones</option>
                    <?php foreach ($regiones as $r): ?>
                    <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <input type="text" class="filter-input w-100" id="buscarUniversidad" placeholder="Buscar universidad..." onkeyup="filtrarUniversidades()">
            </div>
            <div class="col-md-4 text-md-end">
                <div class="total-badge">
                    <i class="bi bi-building"></i>
                    Total: <?php echo count($universidades); ?> universidades
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Universidades -->
    <div class="row" id="universidadesGrid">
        <?php if (empty($universidades)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-building"></i>
                <p>No hay universidades registradas en el sistema.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($universidades as $u): 
                $universidadModel = new Universidad();
                $carrerasCount = $universidadModel->getCarrerasCount($u['id']);
            ?>
            <div class="col-md-6 col-lg-4 mb-4 universidad-card" 
                 data-id="<?php echo $u['id']; ?>"
                 data-region="<?php echo $u['id_region']; ?>"
                 data-nombre="<?php echo strtolower(htmlspecialchars($u['nombre'])); ?>">
                <div class="card-universidad">
                    <div class="card-header-universidad">
                        <?php if ($u['logo'] && file_exists("assets/uploads/logos/" . $u['logo'])): ?>
                            <img src="assets/uploads/logos/<?php echo $u['logo']; ?>" 
                                 class="universidad-logo" 
                                 alt="Logo">
                        <?php else: ?>
                            <div class="logo-placeholder">
                                <i class="bi bi-building"></i>
                            </div>
                        <?php endif; ?>
                        <div class="universidad-titulo">
                            <h5><?php echo htmlspecialchars($u['nombre']); ?></h5>
                            <span class="region-badge">
                                <i class="bi bi-geo-alt"></i>
                                <?php echo $u['region_nombre']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body-universidad">
                        <p class="universidad-descripcion">
                            <?php echo substr(htmlspecialchars($u['descripcion'] ?? ''), 0, 100); ?>
                            <?php if (strlen($u['descripcion'] ?? '') > 100) echo '...'; ?>
                        </p>
                        
                        <div class="info-item">
                            <i class="bi bi-geo-alt"></i>
                            <span><?php echo htmlspecialchars($u['direccion'] ?? 'Dirección no especificada'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-telephone"></i>
                            <span><?php echo htmlspecialchars($u['telefono'] ?? 'Teléfono no especificado'); ?></span>
                        </div>
                        
                        <div class="carreras-stats">
                            <span class="carreras-badge">
                                <i class="bi bi-book"></i>
                                <?php echo $carrerasCount; ?> carreras
                            </span>
                            <button class="btn-view-universidad" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                                <i class="bi bi-eye"></i>
                                Ver detalles
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Ver Detalle de Universidad -->
<div class="modal fade" id="modalVerUniversidad" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-universidad">
            <div class="modal-header modal-header-universidad">
                <h5 class="modal-title">
                    <i class="bi bi-building me-2"></i>
                    Detalle de Universidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-universidad" id="detalleUniversidadContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="btnSitioWeb" class="btn btn-primary" target="_blank">
                    <i class="bi bi-box-arrow-up-right"></i> Visitar Sitio Web
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver detalle de universidad
function verUniversidad(id) {
    fetch(`index.php?action=universidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let carrerasHtml = '';
            
            if (data.carreras && data.carreras.length > 0) {
                carrerasHtml = '<div>';
                data.carreras.forEach(c => {
                    let modalidadesHtml = '';
                    if (c.modalidades && c.modalidades.length > 0) {
                        modalidadesHtml = '<div style="margin-top: 8px;">' + 
                            c.modalidades.map(m => 
                                `<span class="modalidad-badge"><i class="bi bi-tag me-1"></i>${m.nombre}</span>`
                            ).join(' ') + 
                            '</div>';
                    }
                    
                    carrerasHtml += `
                        <div class="carrera-item">
                            <h6>${c.nombre}</h6>
                            <p class="mb-1" style="color: #4a5568; font-size: 0.9rem;">
                                <i class="bi bi-award me-1"></i>${c.titulo_otorgado || 'Título no especificado'}
                            </p>
                            <p style="color: #4a5568; font-size: 0.9rem;">
                                <i class="bi bi-clock me-1"></i>${c.duracion || 'Duración no especificada'}
                            </p>
                            ${modalidadesHtml}
                        </div>
                    `;
                });
                carrerasHtml += '</div>';
            } else {
                carrerasHtml = `
                    <div class="text-center py-4">
                        <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">No hay carreras registradas en esta universidad</p>
                    </div>
                `;
            }
            
            const logoHtml = data.logo ? 
                `<img src="assets/uploads/logos/${data.logo}" class="universidad-detail-logo" alt="Logo">` :
                `<div class="logo-placeholder" style="width: 120px; height: 120px; margin: 0 auto 20px; font-size: 3rem;">
                    <i class="bi bi-building"></i>
                </div>`;
            
            const html = `
                <div class="text-center mb-4">
                    ${logoHtml}
                    <h3 style="color: #2d3748; font-weight: 700; margin-bottom: 5px;">${data.nombre}</h3>
                    <span class="region-badge" style="font-size: 1rem; padding: 8px 20px;">
                        <i class="bi bi-geo-alt"></i> ${data.region_nombre}
                    </span>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="info-card">
                            <h6><i class="bi bi-info-circle"></i> Información de Contacto</h6>
                            <p><i class="bi bi-geo-alt"></i> <strong>Dirección:</strong><br>${data.direccion || 'No especificada'}</p>
                            <p><i class="bi bi-telephone"></i> <strong>Teléfono:</strong> ${data.telefono || 'No especificado'}</p>
                            <p><i class="bi bi-envelope"></i> <strong>Email:</strong> ${data.email || 'No especificado'}</p>
                            <p><i class="bi bi-globe"></i> <strong>Sitio Web:</strong> ${data.link_plataforma ? `<a href="${data.link_plataforma}" target="_blank" style="color: #667eea;">${data.link_plataforma}</a>` : 'No disponible'}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="info-card">
                            <h6><i class="bi bi-bar-chart"></i> Estadísticas</h6>
                            <div class="text-center mt-3">
                                <div style="font-size: 3rem; font-weight: 700; color: #667eea;">${data.carreras_count || 0}</div>
                                <p style="color: #4a5568;">Carreras Ofrecidas</p>
                                <hr>
                                <p><i class="bi bi-calendar"></i> <strong>Fecha de Registro:</strong><br>${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="info-card">
                            <h6><i class="bi bi-book"></i> Carreras Ofrecidas</h6>
                            ${carrerasHtml}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detalleUniversidadContent').innerHTML = html;
            document.getElementById('btnSitioWeb').href = data.link_plataforma || '#';
            
            new bootstrap.Modal(document.getElementById('modalVerUniversidad')).show();
        });
}

// Función para filtrar universidades
function filtrarUniversidades() {
    const region = document.getElementById('filtroRegion').value;
    const busqueda = document.getElementById('buscarUniversidad').value.toLowerCase();
    const tarjetas = document.querySelectorAll('.universidad-card');
    let visibleCount = 0;
    
    tarjetas.forEach(tarjeta => {
        let mostrar = true;
        
        // Filtrar por región
        if (region && tarjeta.dataset.region != region) {
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
    const grid = document.getElementById('universidadesGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 no-results';
            msg.innerHTML = `
                <i class="bi bi-search me-2"></i>
                No se encontraron universidades que coincidan con los filtros.
            `;
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

// Inicializar tooltips si los hay
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>