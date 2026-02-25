<?php
// Verificar que las variables existen
if (!isset($regiones)) $regiones = [];
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-geo-alt"></i> Catálogo de Regiones
        </h2>
        <p class="text-muted">Vista de solo lectura - Personal</p>
    </div>
</div>

<!-- Filtro de búsqueda -->
<div class="row mb-4">
    <div class="col-md-6">
        <input type="text" class="form-control" id="buscarRegion" placeholder="Buscar región..." onkeyup="filtrarRegiones()">
    </div>
    <div class="col-md-6 text-end">
        <span class="text-muted">Total: <?php echo count($regiones); ?> regiones</span>
    </div>
</div>

<!-- Tarjetas de Regiones -->
<div class="row" id="regionesGrid">
    <?php if (empty($regiones)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay regiones registradas en el sistema.
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($regiones as $r): 
            $regionModel = new Region();
            $countUniversidades = $regionModel->getUniversidadesCount($r['id']);
        ?>
        <div class="col-md-6 col-lg-4 mb-4 region-card" data-nombre="<?php echo htmlspecialchars($r['nombre']); ?>">
            <div class="card h-100 shadow">
                <div class="card-body text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-3">
                        <i class="bi bi-geo-alt fs-1"></i>
                    </div>
                    <h4 class="card-title"><?php echo htmlspecialchars($r['nombre']); ?></h4>
                    
                    <div class="mt-3">
                        <div class="d-flex justify-content-around">
                            <div>
                                <span class="badge bg-info p-2">
                                    <i class="bi bi-buildings"></i> <?php echo $countUniversidades; ?> Universidades
                                </span>
                            </div>
                            <div>
                                <span class="badge bg-secondary p-2">
                                    <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-primary mt-3" onclick="verRegion(<?php echo $r['id']; ?>)">
                        <i class="bi bi-eye"></i> Ver detalles
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Ver Detalle de Región (igual que admin) -->
<div class="modal fade" id="modalVerRegion" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt"></i> Detalle de Región
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleRegionContent">
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
                        <div class="col-md-6 mb-2">
                            <div class="card">
                                <div class="card-body">
                                    <h6>${u.nombre}</h6>
                                    <a href="index.php?action=universidades&region=${u.id}" class="btn btn-sm btn-outline-primary">
                                        Ver carreras
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                universidadesHtml += '</div>';
            } else {
                universidadesHtml = '<p class="text-muted">No hay universidades en esta región</p>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-geo-alt fs-1 text-primary"></i>
                    <h3 class="mt-2">${data.nombre}</h3>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5>${data.universidades_count || 0}</h5>
                                <small>Universidades</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5>${new Date(data.created_at).toLocaleDateString()}</h5>
                                <small>Fecha de creación</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h5 class="mb-3">Universidades en esta región:</h5>
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
    
    tarjetas.forEach(tarjeta => {
        const nombre = tarjeta.dataset.nombre.toLowerCase();
        if (nombre.includes(busqueda)) {
            tarjeta.style.display = '';
        } else {
            tarjeta.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleCount = Array.from(tarjetas).filter(t => t.style.display !== 'none').length;
    const mensaje = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensaje) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 alert alert-warning mt-3';
            msg.innerHTML = 'No se encontraron regiones que coincidan con la búsqueda.';
            document.getElementById('regionesGrid').appendChild(msg);
        }
    } else if (mensaje) {
        mensaje.remove();
    }
}
</script>

<style>
.region-card {
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.region-card .card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.region-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.rounded-circle {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .region-card {
        margin-bottom: 1rem;
    }
}
</style>