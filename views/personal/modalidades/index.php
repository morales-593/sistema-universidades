<?php
// Verificar que las variables existen
if (!isset($modalidades)) $modalidades = [];
?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-grid-3x3-gap-fill"></i> Catálogo de Modalidades
        </h2>
        <p class="text-muted">Vista de solo lectura - Personal</p>
    </div>
</div>

<!-- Filtro de búsqueda -->
<div class="row mb-4">
    <div class="col-md-6">
        <input type="text" class="form-control" id="buscarModalidad" placeholder="Buscar modalidad..." onkeyup="filtrarModalidades()">
    </div>
    <div class="col-md-6 text-end">
        <span class="text-muted">Total: <?php echo count($modalidades); ?> modalidades</span>
    </div>
</div>

<!-- Grid de Modalidades -->
<div class="row" id="modalidadesGrid">
    <?php if (empty($modalidades)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay modalidades registradas en el sistema.
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($modalidades as $m): ?>
        <div class="col-md-6 col-lg-4 mb-4 modalidad-card" 
             data-id="<?php echo $m['id']; ?>"
             data-nombre="<?php echo strtolower(htmlspecialchars($m['nombre'])); ?>">
            <div class="card h-100 shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($m['nombre']); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text small text-muted mb-3">
                        <?php echo htmlspecialchars($m['descripcion'] ?? 'Sin descripción'); ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-info">
                            <i class="bi bi-book"></i> <?php echo $m['carreras_count']; ?> carreras
                        </span>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="verModalidad(<?php echo $m['id']; ?>)">
                        <i class="bi bi-eye"></i> Ver carreras
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Ver Detalle de Modalidad -->
<div class="modal fade" id="modalVerModalidad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Carreras por Modalidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleModalidadContent">
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
                carrerasHtml = '<div class="list-group">';
                data.carreras.forEach(c => {
                    carrerasHtml += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${c.nombre}</h6>
                                <small class="text-muted">${c.duracion || 'Duración no especificada'}</small>
                            </div>
                            <p class="mb-1">${c.universidad_nombre} <span class="badge bg-info">${c.region_nombre}</span></p>
                            <small class="text-muted">${c.titulo_otorgado || 'Título no especificado'}</small>
                        </div>
                    `;
                });
                carrerasHtml += '</div>';
            } else {
                carrerasHtml = '<p class="text-muted">No hay carreras que ofrezcan esta modalidad</p>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-grid-3x3-gap-fill fs-1 text-primary"></i>
                    <h3 class="mt-2">${data.nombre}</h3>
                    <p class="text-muted">${data.descripcion || 'Sin descripción'}</p>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Esta modalidad es ofrecida por <strong>${data.carreras_count || 0}</strong> carreras
                </div>
                
                <h5 class="mb-3">Carreras que ofrecen esta modalidad:</h5>
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
    
    tarjetas.forEach(tarjeta => {
        const nombre = tarjeta.dataset.nombre;
        if (nombre.includes(busqueda)) {
            tarjeta.style.display = '';
        } else {
            tarjeta.style.display = 'none';
        }
    });
    
    // Mostrar mensaje si no hay resultados
    const visibleCount = Array.from(tarjetas).filter(t => t.style.display !== 'none').length;
    const grid = document.getElementById('modalidadesGrid');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0) {
        if (!mensajeExistente) {
            const msg = document.createElement('div');
            msg.id = 'noResultsMessage';
            msg.className = 'col-12 alert alert-warning mt-3';
            msg.innerHTML = 'No se encontraron modalidades que coincidan con la búsqueda.';
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}
</script>

<style>
.modalidad-card {
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

.modalidad-card .card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
    border-radius: 1rem;
    overflow: hidden;
}

.modalidad-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}

.modalidad-card .card-header {
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
    .modalidad-card {
        margin-bottom: 1rem;
    }
}
</style>