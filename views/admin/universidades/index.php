<?php
// Verificar que las variables existen
if (!isset($universidades)) $universidades = [];
if (!isset($regiones)) $regiones = [];
?>

<link rel="stylesheet" href="assets/css/admin/universidades/universidad.css">

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

<div class="admin-universidades">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-buildings"></i>
            Gestión de Universidades
        </h2>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevaUniversidad()">
            <i class="bi bi-plus-circle"></i>
            Nueva Universidad
        </button>
        
        <div class="filter-section">
            <select class="filter-select" id="filtroRegion" onchange="filtrarUniversidades()">
                <option value="">Todas las regiones</option>
                <?php foreach ($regiones as $r): ?>
                <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" class="filter-input" id="buscarUniversidad" placeholder="Buscar universidad..." onkeyup="filtrarUniversidades()">
        </div>
    </div>

    <!-- Grid de Universidades -->
    <div class="universidades-grid" id="universidadesGrid">
        <?php if (empty($universidades)): ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="bi bi-building"></i>
                <p>No hay universidades registradas.</p>
            </div>
        </div>
        <?php else: ?>
            <?php foreach ($universidades as $u): 
                $universidadModel = new Universidad();
                $carrerasCount = $universidadModel->getCarrerasCount($u['id']);
            ?>
            <div class="universidad-card" 
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
                            <span class="region-badge-card">
                                <i class="bi bi-geo-alt"></i>
                                <?php echo $u['region_nombre']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body-universidad">
                        <div class="universidad-descripcion">
                            <?php echo substr(htmlspecialchars($u['descripcion'] ?? ''), 0, 80); ?>
                            <?php if (strlen($u['descripcion'] ?? '') > 80) echo '...'; ?>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-geo-alt"></i>
                            <span><?php echo htmlspecialchars($u['direccion'] ?? 'Dirección no especificada'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-telephone"></i>
                            <span><?php echo htmlspecialchars($u['telefono'] ?? 'Teléfono no especificado'); ?></span>
                        </div>
                        
                        <div class="info-item">
                            <i class="bi bi-envelope"></i>
                            <span><?php echo htmlspecialchars($u['email'] ?? 'Email no especificado'); ?></span>
                        </div>
                        
                        <div class="stats-row">
                            <span class="carreras-badge">
                                <i class="bi bi-book"></i>
                                <?php echo $carrerasCount; ?> carreras
                            </span>
                            <span class="fecha-badge">
                                <i class="bi bi-calendar"></i>
                                <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer-universidad">
                        <div class="action-group">
                            <button class="btn-action-card btn-view-card" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                                <i class="bi bi-eye"></i> Ver
                            </button>
                            <button class="btn-action-card btn-edit-card" onclick="editarUniversidad(<?php echo $u['id']; ?>)">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn-action-card btn-delete-card" onclick="eliminarUniversidad(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>', <?php echo $carrerasCount; ?>)">
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

<!-- Modal para Ver Detalle de Universidad -->
<div class="modal fade" id="modalVerUniversidad" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-building me-2"></i>
                    Detalle de Universidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleUniversidadContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" id="btnSitioWeb" class="btn-save" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-2"></i>
                    Visitar Sitio Web
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Universidad -->
<div class="modal fade" id="modalUniversidad" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalUniversidadTitle">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Universidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=universidad-guardar" id="formUniversidad" enctype="multipart/form-data">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="universidadId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label-admin">Nombre de la Universidad *</label>
                            <input type="text" class="form-control-admin" id="nombre" name="nombre" required maxlength="200" placeholder="Ej: Universidad Central del Ecuador">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="id_region" class="form-label-admin">Región *</label>
                            <select class="form-select-admin" id="id_region" name="id_region" required>
                                <option value="">Seleccione una región</option>
                                <?php foreach ($regiones as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label-admin">Descripción</label>
                            <textarea class="form-control-admin" id="descripcion" name="descripcion" rows="3" placeholder="Breve descripción de la universidad"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="link_plataforma" class="form-label-admin">Sitio Web</label>
                            <input type="url" class="form-control-admin" id="link_plataforma" name="link_plataforma" placeholder="https://www.ejemplo.com">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label-admin">Dirección</label>
                            <input type="text" class="form-control-admin" id="direccion" name="direccion" placeholder="Dirección física">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label-admin">Teléfono</label>
                            <input type="tel" class="form-control-admin" id="telefono" name="telefono" placeholder="+593 2 1234 567">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label-admin">Email</label>
                            <input type="email" class="form-control-admin" id="email" name="email" placeholder="contacto@universidad.edu.ec">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="logo" class="form-label-admin">Logo</label>
                            <input type="file" class="form-control-admin file-input" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="form-hint">
                                <i class="bi bi-info-circle me-1"></i>
                                Formatos permitidos: JPG, PNG, GIF, WEBP. Máximo 2MB
                            </small>
                            <div id="logo-preview" class="preview-container" style="display: none;">
                                <img src="" alt="Vista previa del logo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardarUniversidad">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Universidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nueva universidad
function abrirModalNuevaUniversidad() {
    document.getElementById('formUniversidad').reset();
    document.getElementById('universidadId').value = '';
    document.getElementById('modalUniversidadTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Universidad';
    document.getElementById('btnGuardarUniversidad').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Universidad';
    document.getElementById('logo-preview').style.display = 'none';
    new bootstrap.Modal(document.getElementById('modalUniversidad')).show();
}

// Función para editar universidad
function editarUniversidad(id) {
    fetch(`index.php?action=universidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('universidadId').value = data.id;
            document.getElementById('nombre').value = data.nombre;
            document.getElementById('id_region').value = data.id_region;
            document.getElementById('descripcion').value = data.descripcion || '';
            document.getElementById('link_plataforma').value = data.link_plataforma || '';
            document.getElementById('direccion').value = data.direccion || '';
            document.getElementById('telefono').value = data.telefono || '';
            document.getElementById('email').value = data.email || '';
            
            document.getElementById('modalUniversidadTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Universidad';
            document.getElementById('btnGuardarUniversidad').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Universidad';
            
            // Mostrar preview del logo si existe
            if (data.logo) {
                const preview = document.getElementById('logo-preview');
                preview.style.display = 'block';
                preview.querySelector('img').src = 'assets/uploads/logos/' + data.logo;
            } else {
                document.getElementById('logo-preview').style.display = 'none';
            }
            
            new bootstrap.Modal(document.getElementById('modalUniversidad')).show();
        });
}

// Función para ver detalle de universidad
function verUniversidad(id) {
    fetch(`index.php?action=universidad-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            let carrerasHtml = '';
            
            if (data.carreras && data.carreras.length > 0) {
                carrerasHtml = '<table class="carreras-table">';
                data.carreras.forEach(c => {
                    let modalidadesHtml = '';
                    if (c.modalidades && c.modalidades.length > 0) {
                        modalidadesHtml = c.modalidades.map(m => 
                            `<span class="badge" style="background: linear-gradient(135deg, #667eea15, #764ba215); color: #667eea; padding: 3px 8px; border-radius: 50px; font-size: 0.7rem; margin-left: 3px;">${m.nombre}</span>`
                        ).join('');
                    }
                    
                    carrerasHtml += `
                        <tr>
                            <td style="width: 35%;"><strong>${c.nombre}</strong></td>
                            <td style="width: 35%;">${c.titulo_otorgado || 'No especificado'}</td>
                            <td style="width: 15%;"><i class="bi bi-clock me-1"></i>${c.duracion || 'N/E'}</td>
                            <td style="width: 15%;">${modalidadesHtml}</td>
                        </tr>
                    `;
                });
                carrerasHtml += '</table>';
            } else {
                carrerasHtml = `
                    <div class="text-center py-4">
                        <i class="bi bi-book fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted">No hay carreras registradas en esta universidad</p>
                    </div>
                `;
            }
            
            const logoHtml = data.logo ? 
                `<img src="assets/uploads/logos/${data.logo}" class="detail-logo" alt="Logo">` :
                `<div class="detail-logo" style="background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-building" style="font-size: 3rem; color: white;"></i>
                </div>`;
            
            const html = `
                <div class="text-center mb-4">
                    ${logoHtml}
                    <h3 style="color: #2d3748; font-weight: 700;">${data.nombre}</h3>
                    <span class="region-badge-card" style="font-size: 0.9rem; padding: 8px 20px;">
                        <i class="bi bi-geo-alt"></i> ${data.region_nombre}
                    </span>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-card-detail">
                            <h6><i class="bi bi-info-circle"></i> Información de Contacto</h6>
                            <p><i class="bi bi-geo-alt"></i> <strong>Dirección:</strong><br>${data.direccion || 'No especificada'}</p>
                            <p><i class="bi bi-telephone"></i> <strong>Teléfono:</strong> ${data.telefono || 'No especificado'}</p>
                            <p><i class="bi bi-envelope"></i> <strong>Email:</strong> ${data.email || 'No especificado'}</p>
                            <p><i class="bi bi-globe"></i> <strong>Sitio Web:</strong> ${data.link_plataforma ? `<a href="${data.link_plataforma}" target="_blank" style="color: #667eea;">${data.link_plataforma}</a>` : 'No disponible'}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card-detail">
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
                        <div class="info-card-detail">
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

// Función para eliminar universidad
function eliminarUniversidad(id, nombre, countCarreras) {
    if (countCarreras > 0) {
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            html: `La universidad <strong>${nombre}</strong> tiene ${countCarreras} carreras asociadas.<br>
                   Elimina primero las carreras para poder eliminar esta universidad.`,
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar universidad?',
        html: `¿Estás seguro de eliminar la universidad <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: 'white'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=universidad-eliminar&id=${id}`;
        }
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
            msg.className = 'col-12';
            msg.innerHTML = `
                <div class="no-results">
                    <i class="bi bi-search me-2"></i>
                    No se encontraron universidades que coincidan con los filtros.
                </div>
            `;
            grid.appendChild(msg);
        }
    } else if (mensajeExistente) {
        mensajeExistente.remove();
    }
}

// Vista previa del logo
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo muy grande',
                text: 'El logo no puede superar los 2MB',
                confirmButtonColor: '#667eea',
                background: 'white'
            });
            this.value = '';
            document.getElementById('logo-preview').style.display = 'none';
            return;
        }
        
        // Validar tipo
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Tipo no válido',
                text: 'Solo se permiten imágenes JPG, PNG, GIF y WEBP',
                confirmButtonColor: '#667eea',
                background: 'white'
            });
            this.value = '';
            document.getElementById('logo-preview').style.display = 'none';
            return;
        }
        
        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logo-preview');
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('logo-preview').style.display = 'none';
    }
});

// Validar formulario antes de enviar
document.getElementById('formUniversidad').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const region = document.getElementById('id_region').value;
    
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
    
    if (!region) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Región requerida',
            text: 'Debes seleccionar una región',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
});
</script>