<?php
// Verificar que las variables existen
if (!isset($modalidades)) $modalidades = [];
?>

<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 3000,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $_GET['error']; ?>'
    });
});
</script>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="bi bi-grid-3x3-gap-fill"></i> Gestión de Modalidades
        </h2>
    </div>
</div>

<!-- Botón Nueva Modalidad -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevaModalidad()">
            <i class="bi bi-plus-circle"></i> Nueva Modalidad
        </button>
    </div>
</div>

<!-- Filtro de búsqueda -->
<div class="row mb-4">
    <div class="col-md-6">
        <input type="text" class="form-control" id="buscarModalidad" placeholder="Buscar modalidad..." onkeyup="filtrarModalidades()">
    </div>
</div>

<!-- Grid de Modalidades -->
<div class="row" id="modalidadesGrid">
    <?php if (empty($modalidades)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay modalidades registradas.
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
                            <small class="text-muted">ID: <?php echo $m['id']; ?></small>
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
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($m['created_at'])); ?>
                        </small>
                    </div>
                    
                    <?php if (!empty($m['carreras'])): ?>
                    <div class="small">
                        <strong>Carreras:</strong>
                        <div class="mt-2">
                            <?php 
                            $carrerasLimit = array_slice($m['carreras'], 0, 3);
                            foreach ($carrerasLimit as $c): ?>
                                <span class="badge bg-secondary mb-1"><?php echo htmlspecialchars($c['nombre']); ?></span>
                            <?php endforeach; ?>
                            <?php if (count($m['carreras']) > 3): ?>
                                <span class="badge bg-light text-dark">+<?php echo count($m['carreras']) - 3; ?> más</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <div class="btn-group w-100" role="group">
                        <button class="btn btn-sm btn-info" onclick="verModalidad(<?php echo $m['id']; ?>)">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editarModalidad(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', '<?php echo htmlspecialchars($m['descripcion'] ?? ''); ?>')">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarModalidad(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', <?php echo $m['carreras_count']; ?>)">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
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
                    <i class="bi bi-grid-3x3-gap-fill"></i> Detalle de Modalidad
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

<!-- Modal para Crear/Editar Modalidad -->
<div class="modal fade" id="modalModalidad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalModalidadTitle">
                    <i class="bi bi-plus-circle"></i> Nueva Modalidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=modalidad-guardar" id="formModalidad">
                <div class="modal-body">
                    <input type="hidden" name="id" id="modalidadId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Modalidad *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100"
                               placeholder="Ej: Presencial, Online, Semipresencial">
                        <small class="text-muted">Máximo 100 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" 
                                  placeholder="Describe brevemente esta modalidad"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarModalidad">Guardar Modalidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nueva modalidad
function abrirModalNuevaModalidad() {
    document.getElementById('formModalidad').reset();
    document.getElementById('modalidadId').value = '';
    document.getElementById('modalModalidadTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Nueva Modalidad';
    document.getElementById('btnGuardarModalidad').innerHTML = 'Guardar Modalidad';
    new bootstrap.Modal(document.getElementById('modalModalidad')).show();
}

// Función para editar modalidad
function editarModalidad(id, nombre, descripcion) {
    document.getElementById('modalidadId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('descripcion').value = descripcion;
    document.getElementById('modalModalidadTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Modalidad';
    document.getElementById('btnGuardarModalidad').innerHTML = 'Actualizar Modalidad';
    new bootstrap.Modal(document.getElementById('modalModalidad')).show();
}

// Función para ver detalle de modalidad
function verModalidad(id) {
    fetch(`index.php?action=modalidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let carrerasHtml = '';
            
            if (data.carreras && data.carreras.length > 0) {
                carrerasHtml = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Carrera</th><th>Universidad</th><th>Región</th></tr></thead><tbody>';
                data.carreras.forEach(c => {
                    carrerasHtml += `
                        <tr>
                            <td><strong>${c.nombre}</strong></td>
                            <td>${c.universidad_nombre}</td>
                            <td><span class="badge bg-info">${c.region_nombre}</span></td>
                        </tr>
                    `;
                });
                carrerasHtml += '</tbody></table></div>';
            } else {
                carrerasHtml = '<p class="text-muted">No hay carreras que ofrezcan esta modalidad</p>';
            }
            
            const html = `
                <div class="text-center mb-4">
                    <i class="bi bi-grid-3x3-gap-fill fs-1 text-primary"></i>
                    <h3 class="mt-2">${data.nombre}</h3>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header bg-white">
                                <i class="bi bi-info-circle"></i> Información
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> ${data.id}</p>
                                <p><strong>Descripción:</strong> ${data.descripcion || 'No especificada'}</p>
                                <p><strong>Fecha de Creación:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-header bg-white">
                                <i class="bi bi-bar-chart"></i> Estadísticas
                            </div>
                            <div class="card-body text-center">
                                <h2 class="text-primary">${data.carreras_count || 0}</h2>
                                <p>Carreras que ofrecen esta modalidad</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <i class="bi bi-book"></i> Carreras con esta Modalidad
                            </div>
                            <div class="card-body">
                                ${carrerasHtml}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('detalleModalidadContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerModalidad')).show();
        });
}

// Función para eliminar modalidad
function eliminarModalidad(id, nombre, countCarreras) {
    if (countCarreras > 0) {
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            html: `La modalidad <strong>${nombre}</strong> está siendo utilizada por ${countCarreras} carreras.<br>
                   Elimina primero las carreras o cambia su modalidad para poder eliminar esta modalidad.`,
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar modalidad?',
        html: `¿Estás seguro de eliminar la modalidad <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=modalidad-eliminar&id=${id}`;
        }
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

// Validar formulario antes de enviar
document.getElementById('formModalidad').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    
    if (nombre.length < 3) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre inválido',
            text: 'El nombre debe tener al menos 3 caracteres'
        });
        return;
    }
    
    if (nombre.length > 100) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre muy largo',
            text: 'El nombre no puede exceder los 100 caracteres'
        });
        return;
    }
});
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

.btn-group {
    gap: 5px;
}

.btn-group .btn {
    margin: 0;
    border-radius: 0.5rem !important;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    
    .btn-group .btn {
        width: 100%;
    }
}
</style>