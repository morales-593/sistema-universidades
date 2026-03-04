<?php
// Verificar que las variables existen
if (!isset($regiones)) $regiones = [];
?>

<link rel="stylesheet" href="assets/css/personal/region/regiones.css">

<div class="regiones-catalogo">
    <!-- Header -->
    <div class="catalogo-header">
        <h2>
            <i class="bi bi-geo-alt"></i>
            Catálogo de Regiones
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Vista de solo lectura - Personal
        </p>
    </div>

    <!-- Filtro de búsqueda -->
    <div class="search-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <input type="text" class="search-input w-100" id="buscarRegion" placeholder="Buscar región por nombre..." onkeyup="filtrarRegiones()">
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="total-badge">
                    <i class="bi bi-map"></i>
                    Total: <?php echo count($regiones); ?> regiones
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Regiones -->
    <div class="row" id="regionesGrid">
        <?php if (empty($regiones)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-geo-alt"></i>
                <p>No hay regiones registradas en el sistema.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($regiones as $r): 
                $regionModel = new Region();
                $countUniversidades = $regionModel->getUniversidadesCount($r['id']);
            ?>
            <div class="col-md-6 col-lg-4 mb-4 region-card" data-nombre="<?php echo htmlspecialchars(strtolower($r['nombre'])); ?>">
                <div class="card-region">
                    <div class="card-body">
                        <div class="region-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <h4 class="region-nombre"><?php echo htmlspecialchars($r['nombre']); ?></h4>
                        
                        <div class="region-stats">
                            <span class="stat-badge universidades">
                                <i class="bi bi-buildings"></i>
                                <?php echo $countUniversidades; ?> Universidades
                            </span>
                            <span class="stat-badge fecha">
                                <i class="bi bi-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                            </span>
                        </div>
                        
                        <button class="btn-view-region" onclick="verRegion(<?php echo $r['id']; ?>)">
                            <i class="bi bi-eye"></i>
                            Ver detalles
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Ver Detalle de Región -->
<div class="modal fade" id="modalVerRegion" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt me-2"></i>
                    Detalle de Región
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-custom" id="detalleRegionContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver detalle de región
function verRegion(id) {
    fetch(`index.php?action=region-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let universidadesHtml = '';
            
            if (data.universidades && data.universidades.length > 0) {
                universidadesHtml = '<div class="row">';
                data.universidades.forEach(u => {
                    universidadesHtml += `
                        <div class="col-md-6 mb-3">
                            <div class="universidad-mini-card">
                                <h6 class="mb-2">${u.nombre}</h6>
                                <a href="index.php?action=universidades&region=${u.id}" class="btn-universidad">
                                    <i class="bi bi-eye me-1"></i> Ver carreras
                                </a>
                            </div>
                        </div>
                    `;
                });
                universidadesHtml += '</div>';
            } else {
                universidadesHtml = `
                    <div class="text-center py-4">
                        <i class="bi bi-building fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">No hay universidades en esta región</p>
                    </div>
                `;
            }
            
            const html = `
                <div class="text-center mb-4">
                    <div class="region-detail-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h3 class="mt-2 fw-bold" style="color: #2d3748;">${data.nombre}</h3>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="detail-card">
                            <h5>${data.universidades_count || 0}</h5>
                            <small>Universidades</small>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="detail-card">
                            <h5>${new Date(data.created_at).toLocaleDateString()}</h5>
                            <small>Fecha de creación</small>
                        </div>
                    </div>
                </div>
                
                <h5 class="mb-3" style="color: #2d3748; font-weight: 600;">
                    <i class="bi bi-building me-2"></i>
                    Universidades en esta región:
                </h5>
                ${universidadesHtml}
            `;
            
            document.getElementById('detalleRegionContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerRegion')).show();
        });
}

// Función para filtrar regiones
function filtrarRegiones() {
    const busqueda = document.getElementById('buscarRegion').value.toLowerCase();
    const tarjetas = document.querySelectorAll('.region-card');
    let visibleCount = 0;
    
    tarjetas.forEach(tarjeta => {
        const nombre = tarjeta.dataset.nombre;
        if (nombre.includes(busqueda)) {
            tarjeta.style.display = '';
            visibleCount++;
        } else {
            tarjeta.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const mensaje = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensaje) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 no-results';
            msg.innerHTML = `
                <i class="bi bi-search me-2"></i>
                No se encontraron regiones que coincidan con la búsqueda.
            `;
            document.getElementById('regionesGrid').appendChild(msg);
        }
    } else if (mensaje) {
        mensaje.remove();
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