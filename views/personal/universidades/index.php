<?php
// Verificar que las variables existen
if (!isset($universidades)) $universidades = [];
if (!isset($regiones)) $regiones = [];
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-buildings"></i> Catálogo de Universidades
        </h2>
        <p class="text-muted">Vista de solo lectura - Personal</p>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filtroRegion" onchange="filtrarUniversidades()">
            <option value="">Todas las regiones</option>
            <?php foreach ($regiones as $r): ?>
            <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="buscarUniversidad" placeholder="Buscar universidad..." onkeyup="filtrarUniversidades()">
    </div>
    <div class="col-md-4 text-end">
        <span class="text-muted">Total: <?php echo count($universidades); ?> universidades</span>
    </div>
</div>

<!-- Grid de Universidades -->
<div class="row" id="universidadesGrid">
    <?php if (empty($universidades)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay universidades registradas en el sistema.
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
            <div class="card h-100 shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <?php if ($u['logo'] && file_exists("assets/uploads/logos/" . $u['logo'])): ?>
                            <img src="assets/uploads/logos/<?php echo $u['logo']; ?>" 
                                 class="rounded-circle me-3" 
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 alt="Logo">
                        <?php else: ?>
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 50px; height: 50px;">
                                <i class="bi bi-building fs-4"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($u['nombre']); ?></h5>
                            <span class="badge bg-info"><?php echo $u['region_nombre']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted mb-3">
                        <?php echo substr(htmlspecialchars($u['descripcion'] ?? ''), 0, 100); ?>
                        <?php if (strlen($u['descripcion'] ?? '') > 100) echo '...'; ?>
                    </p>
                    
                    <div class="small mb-3">
                        <i class="bi bi-geo-alt text-primary"></i> <?php echo htmlspecialchars($u['direccion'] ?? 'No especificada'); ?><br>
                        <i class="bi bi-telephone text-primary"></i> <?php echo htmlspecialchars($u['telefono'] ?? 'No especificado'); ?>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">
                            <i class="bi bi-book"></i> <?php echo $carrerasCount; ?> carreras
                        </span>
                        <button class="btn btn-sm btn-primary" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                            <i class="bi bi-eye"></i> Ver detalles
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Ver Detalle de Universidad (igual que admin) -->
<div class="modal fade" id="modalVerUniversidad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-building"></i> Detalle de Universidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleUniversidadContent">
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
                carrerasHtml = '<div class="list-group">';
                data.carreras.forEach(c => {
                    let modalidadesHtml = '';
                    if (c.modalidades && c.modalidades.length > 0) {
                        modalidadesHtml = '<br><small class="text-muted">Modalidades: ' + 
                            c.modalidades.map(m => m.nombre).join(', ') + '</small>';
                    }
                    
                    carrerasHtml += `
                        <div class="list-group-item">
                            <h6 class="mb-1">${c.nombre}</h6>
                            <p class="mb-1 small">${c.titulo_otorgado || 'Título no especificado'} - ${c.duracion || 'Duración no especificada'}</p>
                            ${modalidadesHtml}
                        </div>
                    `;
                });
                carrerasHtml += '</div>';
            } else {
                carrerasHtml = '<p class="text-muted">No hay carreras registradas en esta universidad</p>';
            }
            
            const logoHtml = data.logo ? 
                `<img src="assets/uploads/logos/${data.logo}" class="img-thumbnail mb-3" style="max-width: 150px;">` :
                '<i class="bi bi-building fs-1 text-primary mb-3"></i>';
            
            const html = `
                <div class="text-center mb-4">
                    ${logoHtml}
                    <h3 class="mt-2">${data.nombre}</h3>
                    <span class="badge bg-info">${data.region_nombre}</span>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-info-circle"></i> Información de Contacto
                            </div>
                            <div class="card-body">
                                <p><i class="bi bi-geo-alt text-primary"></i> <strong>Dirección:</strong> ${data.direccion || 'No especificada'}</p>
                                <p><i class="bi bi-telephone text-primary"></i> <strong>Teléfono:</strong> ${data.telefono || 'No especificado'}</p>
                                <p><i class="bi bi-envelope text-primary"></i> <strong>Email:</strong> ${data.email || 'No especificado'}</p>
                                <p><i class="bi bi-globe text-primary"></i> <strong>Sitio Web:</strong> ${data.link_plataforma ? `<a href="${data.link_plataforma}" target="_blank">${data.link_plataforma}</a>` : 'No disponible'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-bar-chart"></i> Estadísticas
                            </div>
                            <div class="card-body">
                                <p><strong>Total de Carreras:</strong> <span class="badge bg-success fs-6">${data.carreras_count || 0}</span></p>
                                <p><strong>Fecha de Registro:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <i class="bi bi-book"></i> Carreras Ofrecidas
                            </div>
                            <div class="card-body">
                                ${carrerasHtml}
                            </div>
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
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleCount = Array.from(tarjetas).filter(t => t.style.display !== 'none').length;
    const grid = document.getElementById('universidadesGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 alert alert-warning mt-3';
            msg.innerHTML = 'No se encontraron universidades que coincidan con los filtros.';
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}
</script>

<style>
.universidad-card {
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

.universidad-card .card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    border-radius: 1rem;
    overflow: hidden;
}

.universidad-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.universidad-card .card-header {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.rounded-circle {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .universidad-card {
        margin-bottom: 1rem;
    }
}
</style>