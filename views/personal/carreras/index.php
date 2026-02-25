<?php
// Verificar que las variables existen
if (!isset($carreras)) $carreras = [];
if (!isset($universidades)) $universidades = [];
if (!isset($modalidades)) $modalidades = [];
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-book"></i> Catálogo de Carreras
        </h2>
        <p class="text-muted">Vista de solo lectura - Personal</p>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filtroRegion" onchange="filtrarCarreras()">
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
        <select class="form-select" id="filtroUniversidad" onchange="filtrarCarreras()">
            <option value="">Todas las universidades</option>
            <?php foreach ($universidades as $u): ?>
            <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="buscarCarrera" placeholder="Buscar carrera..." onkeyup="filtrarCarreras()">
    </div>
</div>

<!-- Grid de Carreras -->
<div class="row" id="carrerasGrid">
    <?php if (empty($carreras)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay carreras registradas en el sistema.
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
            <div class="card h-100 shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="bi bi-book fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($c['nombre']); ?></h5>
                            <small class="text-muted"><?php echo htmlspecialchars($c['universidad_nombre']); ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <span class="badge bg-info"><?php echo $c['region_nombre']; ?></span>
                        <?php if ($c['duracion']): ?>
                            <span class="badge bg-secondary"><i class="bi bi-clock"></i> <?php echo $c['duracion']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="card-text small text-muted mb-3">
                        <?php echo substr(htmlspecialchars($c['descripcion'] ?? ''), 0, 100); ?>
                        <?php if (strlen($c['descripcion'] ?? '') > 100) echo '...'; ?>
                    </p>
                    
                    <div class="mb-3">
                        <strong>Título:</strong> <?php echo htmlspecialchars($c['titulo_otorgado'] ?? 'No especificado'); ?>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Modalidades:</strong><br>
                        <?php foreach ($modalidadesCarrera as $m): ?>
                            <span class="badge bg-primary mt-1"><?php echo $m['nombre']; ?></span>
                        <?php endforeach; ?>
                        <?php if (empty($modalidadesCarrera)): ?>
                            <span class="text-muted">No especificado</span>
                        <?php endif; ?>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="verCarrera(<?php echo $c['id']; ?>)">
                        <i class="bi bi-eye"></i> Ver detalles completos
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Ver Detalle de Carrera (igual que admin) -->
<div class="modal fade" id="modalVerCarrera" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-book"></i> Detalle de Carrera
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleCarreraContent">
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
                    `<span class="badge bg-primary me-1">${m.nombre}</span>`
                ).join('');
            } else {
                modalidadesHtml = '<span class="text-muted">No especificado</span>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-book fs-1 text-primary"></i>
                    <h3 class="mt-2">${data.nombre}</h3>
                    <span class="badge bg-info">${data.universidad_nombre}</span>
                    <span class="badge bg-secondary">${data.region_nombre}</span>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-info-circle"></i> Información General
                            </div>
                            <div class="card-body">
                                <p><strong>Título que otorga:</strong> ${data.titulo_otorgado || 'No especificado'}</p>
                                <p><strong>Duración:</strong> ${data.duracion || 'No especificada'}</p>
                                <p><strong>Modalidades:</strong><br> ${modalidadesHtml}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-calendar"></i> Fechas
                            </div>
                            <div class="card-body">
                                <p><strong>Fecha de Creación:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-card-text"></i> Descripción
                            </div>
                            <div class="card-body">
                                <p>${data.descripcion || 'No hay descripción disponible.'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-person-badge"></i> Perfil de Egreso
                            </div>
                            <div class="card-body">
                                <p>${data.perfil_egreso || 'No especificado.'}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light mb-3">
                            <div class="card-header bg-white">
                                <i class="bi bi-briefcase"></i> Campo Laboral
                            </div>
                            <div class="card-body">
                                <p>${data.campo_laboral || 'No especificado.'}</p>
                            </div>
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
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleCount = Array.from(tarjetas).filter(t => t.style.display !== 'none').length;
    const grid = document.getElementById('carrerasGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 alert alert-warning mt-3';
            msg.innerHTML = 'No se encontraron carreras que coincidan con los filtros.';
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}
</script>

<style>
.carrera-card {
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

.carrera-card .card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    border-radius: 1rem;
    overflow: hidden;
}

.carrera-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.carrera-card .card-header {
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
    .carrera-card {
        margin-bottom: 1rem;
    }
}
</style>