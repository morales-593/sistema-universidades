<?php
// Verificar que las variables existen
if (!isset($modalidades)) $modalidades = [];
?>

<link rel="stylesheet" href="assets/css/admin/modalidades/modalidad.css">

<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 3000,
        showConfirmButton: false,
        background: 'linear-gradient(135deg, #667eea, #764ba2)',
        color: 'white'
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
        text: '<?php echo $_GET['error']; ?>',
        background: 'linear-gradient(135deg, #dc3545, #c82333)',
        color: 'white'
    });
});
</script>
<?php endif; ?>

<div class="admin-modalidades">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-grid-3x3-gap-fill"></i>
            Gestión de Modalidades
        </h2>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevaModalidad()">
            <i class="bi bi-plus-circle"></i>
            Nueva Modalidad
        </button>
        
        <div class="search-wrapper">
            <input type="text" class="search-input-admin" id="buscarModalidad" placeholder="Buscar modalidad..." onkeyup="filtrarModalidades()">
        </div>
    </div>

    <!-- Grid de Modalidades -->
    <div class="modalidades-grid" id="modalidadesGrid">
        <?php if (empty($modalidades)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-grid-3x3-gap-fill"></i>
                <p>No hay modalidades registradas.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($modalidades as $m): ?>
            <div class="modalidad-card" 
                 data-id="<?php echo $m['id']; ?>"
                 data-nombre="<?php echo strtolower(htmlspecialchars($m['nombre'])); ?>">
                <div class="card-modalidad">
                    <div class="card-header-modalidad">
                        <div class="modalidad-icon">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </div>
                        <div class="modalidad-titulo">
                            <h5><?php echo htmlspecialchars($m['nombre']); ?></h5>
                            <small>
                                <i class="bi bi-hash"></i> ID: <?php echo $m['id']; ?>
                            </small>
                        </div>
                    </div>
                    
                    <div class="card-body-modalidad">
                        <div class="modalidad-descripcion">
                            <?php echo htmlspecialchars($m['descripcion'] ?? 'Sin descripción'); ?>
                        </div>
                        
                        <div class="stats-row">
                            <span class="carreras-badge">
                                <i class="bi bi-book"></i>
                                <?php echo $m['carreras_count']; ?> carreras
                            </span>
                            <span class="fecha-badge">
                                <i class="bi bi-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($m['created_at'])); ?>
                            </span>
                        </div>
                        
                        <?php if (!empty($m['carreras'])): ?>
                        <div class="carreras-preview">
                            <strong><i class="bi bi-book"></i> Carreras:</strong>
                            <div>
                                <?php 
                                $carrerasLimit = array_slice($m['carreras'], 0, 3);
                                foreach ($carrerasLimit as $c): ?>
                                    <span class="carrera-mini-badge">
                                        <?php echo htmlspecialchars($c['nombre']); ?>
                                    </span>
                                <?php endforeach; ?>
                                <?php if (count($m['carreras']) > 3): ?>
                                    <span class="more-badge">
                                        +<?php echo count($m['carreras']) - 3; ?> más
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer-modalidad">
                        <div class="action-group">
                            <button class="btn-action-card btn-view-card" onclick="verModalidad(<?php echo $m['id']; ?>)">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn-action-card btn-edit-card" onclick="editarModalidad(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', '<?php echo htmlspecialchars($m['descripcion'] ?? ''); ?>')">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn-action-card btn-delete-card" onclick="eliminarModalidad(<?php echo $m['id']; ?>, '<?php echo htmlspecialchars($m['nombre']); ?>', <?php echo $m['carreras_count']; ?>)">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
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
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                    Detalle de Modalidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleModalidadContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Modalidad -->
<div class="modal fade" id="modalModalidad" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalModalidadTitle">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Modalidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=modalidad-guardar" id="formModalidad">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="modalidadId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label-admin">Nombre de la Modalidad *</label>
                        <input type="text" class="form-control-admin" id="nombre" name="nombre" required maxlength="100"
                               placeholder="Ej: Presencial, Online, Semipresencial">
                        <small class="form-hint">
                            <i class="bi bi-info-circle me-1"></i>
                            Máximo 100 caracteres
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label-admin">Descripción</label>
                        <textarea class="form-control-admin" id="descripcion" name="descripcion" rows="4" 
                                  placeholder="Describe brevemente esta modalidad"></textarea>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardarModalidad">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Modalidad
                    </button>
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
    document.getElementById('modalModalidadTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Modalidad';
    document.getElementById('btnGuardarModalidad').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Modalidad';
    new bootstrap.Modal(document.getElementById('modalModalidad')).show();
}

// Función para editar modalidad
function editarModalidad(id, nombre, descripcion) {
    document.getElementById('modalidadId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('descripcion').value = descripcion;
    document.getElementById('modalModalidadTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Modalidad';
    document.getElementById('btnGuardarModalidad').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Modalidad';
    new bootstrap.Modal(document.getElementById('modalModalidad')).show();
}

// Función para ver detalle de modalidad
function verModalidad(id) {
    fetch(`index.php?action=modalidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let carrerasHtml = '';
            
            if (data.carreras && data.carreras.length > 0) {
                carrerasHtml = '<table class="carreras-table">';
                data.carreras.forEach(c => {
                    carrerasHtml += `
                        <tr>
                            <td style="width: 40%;"><strong>${c.nombre}</strong></td>
                            <td style="width: 40%;">${c.universidad_nombre}</td>
                            <td style="width: 20%;"><span class="badge-region" style="background: linear-gradient(135deg, #667eea15, #764ba215); color: #667eea; padding: 5px 12px; border-radius: 50px; font-size: 0.8rem;">${c.region_nombre}</span></td>
                        </tr>
                    `;
                });
                carrerasHtml += '</table>';
            } else {
                carrerasHtml = `
                    <div class="text-center py-4">
                        <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">No hay carreras que ofrezcan esta modalidad</p>
                    </div>
                `;
            }
            
            const html = `
                <div class="text-center mb-4">
                    <div class="detail-icon">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                    </div>
                    <h3 style="color: #2d3748; font-weight: 700;">${data.nombre}</h3>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">
                                <i class="bi bi-info-circle me-2"></i>Información
                            </h6>
                            <p><i class="bi bi-hash me-2"></i> <strong>ID:</strong> ${data.id}</p>
                            <p><i class="bi bi-calendar me-2"></i> <strong>Fecha Creación:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            <p><i class="bi bi-card-text me-2"></i> <strong>Descripción:</strong><br>${data.descripcion || 'No especificada'}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card-detail text-center">
                            <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">
                                <i class="bi bi-bar-chart me-2"></i>Estadísticas
                            </h6>
                            <div class="stat-number">${data.carreras_count || 0}</div>
                            <p style="color: #4a5568;">Carreras que ofrecen esta modalidad</p>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="info-card-detail">
                            <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 15px;">
                                <i class="bi bi-book me-2"></i>Carreras con esta Modalidad
                            </h6>
                            ${carrerasHtml}
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
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar modalidad?',
        html: `¿Estás seguro de eliminar la modalidad <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: 'white'
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
            msg.className = 'col-12';
            msg.innerHTML = `
                <div class="no-results">
                    <i class="bi bi-search me-2"></i>
                    No se encontraron modalidades que coincidan con la búsqueda.
                </div>
            `;
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
            text: 'El nombre debe tener al menos 3 caracteres',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
    
    if (nombre.length > 100) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Nombre muy largo',
            text: 'El nombre no puede exceder los 100 caracteres',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
});
</script>