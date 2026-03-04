<?php
// Verificar que las variables existen
if (!isset($modalidades)) $modalidades = [];
?>

<link rel="stylesheet" href="assets/css/personal/modalidad/modalidad.css">

<div class="modalidades-catalogo">
    <!-- Header -->
    <div class="catalogo-header">
        <h2>
            <i class="bi bi-grid-3x3-gap-fill"></i>
            Catálogo de Modalidades
        </h2>
        <p>
            <i class="bi bi-info-circle me-2"></i>
            Vista de solo lectura - Personal
        </p>
    </div>

    <!-- Filtro de búsqueda -->
    <div class="search-section">
        <div class="row align-items-center">
            <div class="col-md-8 mb-3 mb-md-0">
                <input type="text" class="search-input w-100" id="buscarModalidad" placeholder="Buscar modalidad..." onkeyup="filtrarModalidades()">
            </div>
            <div class="col-md-4 text-md-end">
                <div class="total-badge">
                    <i class="bi bi-grid"></i>
                    Total: <?php echo count($modalidades); ?> modalidades
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Modalidades -->
    <div class="row" id="modalidadesGrid">
        <?php if (empty($modalidades)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <p>No hay modalidades registradas en el sistema.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($modalidades as $m): ?>
            <div class="col-md-6 col-lg-4 mb-4 modalidad-card" 
                 data-id="<?php echo $m['id']; ?>"
                 data-nombre="<?php echo strtolower(htmlspecialchars($m['nombre'])); ?>">
                <div class="card-modalidad">
                    <div class="card-header-modalidad">
                        <div class="modalidad-icon">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div class="modalidad-titulo">
                            <h5><?php echo htmlspecialchars($m['nombre']); ?></h5>
                        </div>
                    </div>
                    <div class="card-body-modalidad">
                        <div class="modalidad-descripcion">
                            <?php echo htmlspecialchars($m['descripcion'] ?? 'Sin descripción disponible'); ?>
                        </div>
                        
                        <div class="stats-badge">
                            <i class="bi bi-book"></i>
                            <?php echo $m['carreras_count']; ?> Carreras
                        </div>
                        
                        <button class="btn-view-modalidad" onclick="verModalidad(<?php echo $m['id']; ?>)">
                            <i class="bi bi-eye"></i>
                            Ver carreras disponibles
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para Ver Detalle de Modalidad -->
<div class="modal fade" id="modalVerModalidad" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-custom">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                    Carreras por Modalidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-custom" id="detalleModalidadContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// Función para ver detalle de modalidad
function verModalidad(id) {
    fetch(`index.php?action=modalidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let carrerasHtml = '';
            
            if (data.carreras && data.carreras.length > 0) {
                carrerasHtml = '<div>';
                data.carreras.forEach(c => {
                    carrerasHtml += `
                        <div class="carrera-item-modal">
                            <div class="carrera-header">
                                <h6>${c.nombre}</h6>
                                <span class="duracion-badge">
                                    <i class="bi bi-clock"></i> ${c.duracion || 'N/E'}
                                </span>
                            </div>
                            
                            <div class="universidad-info">
                                <i class="bi bi-building" style="color: #667eea;"></i>
                                <span class="universidad-nombre">${c.universidad_nombre}</span>
                                <span class="region-badge">
                                    <i class="bi bi-geo-alt"></i> ${c.region_nombre}
                                </span>
                            </div>
                            
                            <div class="titulo-info">
                                <i class="bi bi-award me-1" style="color: #667eea;"></i>
                                ${c.titulo_otorgado || 'Título no especificado'}
                            </div>
                        </div>
                    `;
                });
                carrerasHtml += '</div>';
            } else {
                carrerasHtml = `
                    <div class="text-center py-5">
                        <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">No hay carreras que ofrezcan esta modalidad</p>
                    </div>
                `;
            }
            
            const html = `
                <div class="text-center mb-4">
                    <div class="modalidad-detail-icon">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <h3 class="mt-2" style="color: #2d3748; font-weight: 700;">${data.nombre}</h3>
                    <p class="text-muted" style="max-width: 500px; margin: 10px auto;">
                        ${data.descripcion || 'Sin descripción disponible'}
                    </p>
                </div>
                
                <div class="info-alert">
                    <i class="bi bi-info-circle-fill"></i>
                    Esta modalidad es ofrecida por <strong>${data.carreras_count || 0}</strong> ${data.carreras_count === 1 ? 'carrera' : 'carreras'}
                </div>
                
                <h5 class="mb-3" style="color: #2d3748; font-weight: 600;">
                    <i class="bi bi-book me-2"></i>
                    Carreras que ofrecen esta modalidad:
                </h5>
                ${carrerasHtml}
            `;
            
            document.getElementById('detalleModalidadContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerModalidad')).show();
        });
}

// Función para filtrar modalidades
function filtrarModalidades() {
    const busqueda = document.getElementById('buscarModalidad').value.toLowerCase();
    const tarjetas = document.querySelectorAll('.modalidad-card');
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
    const grid = document.getElementById('modalidadesGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 no-results';
            msg.innerHTML = `
                <i class="bi bi-search me-2"></i>
                No se encontraron modalidades que coincidan con la búsqueda.
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