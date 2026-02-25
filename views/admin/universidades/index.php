<?php
// Verificar que las variables existen
if (!isset($universidades)) $universidades = [];
if (!isset($regiones)) $regiones = [];
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
            <i class="bi bi-buildings"></i> Gestión de Universidades
        </h2>
    </div>
</div>

<!-- Botón Nueva Universidad -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevaUniversidad()">
            <i class="bi bi-plus-circle"></i> Nueva Universidad
        </button>
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
</div>

<!-- Grid de Universidades -->
<div class="row" id="universidadesGrid">
    <?php if (empty($universidades)): ?>
    <div class="col-12">
        <div class="alert alert-info">
            No hay universidades registradas.
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
                    
                    <div class="small mb-2">
                        <i class="bi bi-geo-alt text-primary"></i> <?php echo htmlspecialchars($u['direccion'] ?? 'No especificada'); ?><br>
                        <i class="bi bi-telephone text-primary"></i> <?php echo htmlspecialchars($u['telefono'] ?? 'No especificado'); ?><br>
                        <i class="bi bi-envelope text-primary"></i> <?php echo htmlspecialchars($u['email'] ?? 'No especificado'); ?>
                    </div>
                    
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <span class="badge bg-success">
                            <i class="bi bi-book"></i> <?php echo $carrerasCount; ?> carreras
                        </span>
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i> <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                        </small>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="btn-group w-100" role="group">
                        <button class="btn btn-sm btn-info" onclick="verUniversidad(<?php echo $u['id']; ?>)">
                            <i class="bi bi-eye"></i> Ver
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="editarUniversidad(<?php echo $u['id']; ?>)">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="eliminarUniversidad(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>', <?php echo $carrerasCount; ?>)">
                            <i class="bi bi-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal para Ver Detalle de Universidad -->
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

<!-- Modal para Crear/Editar Universidad -->
<div class="modal fade" id="modalUniversidad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUniversidadTitle">
                    <i class="bi bi-plus-circle"></i> Nueva Universidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=universidad-guardar" id="formUniversidad" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="universidadId">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de la Universidad *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="200">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="id_region" class="form-label">Región *</label>
                            <select class="form-select" id="id_region" name="id_region" required>
                                <option value="">Seleccione una región</option>
                                <?php foreach ($regiones as $r): ?>
                                <option value="<?php echo $r['id']; ?>"><?php echo $r['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="link_plataforma" class="form-label">Sitio Web</label>
                            <input type="url" class="form-control" id="link_plataforma" name="link_plataforma" placeholder="https://">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
                            <small class="text-muted">Formatos permitidos: JPG, PNG, GIF, WEBP. Máximo 2MB</small>
                            <div id="logo-preview" class="mt-2 text-center" style="display: none;">
                                <img src="" alt="Vista previa" style="max-width: 150px; max-height: 150px;" class="img-thumbnail">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarUniversidad">Guardar Universidad</button>
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
    document.getElementById('modalUniversidadTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Nueva Universidad';
    document.getElementById('btnGuardarUniversidad').innerHTML = 'Guardar Universidad';
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
            
            document.getElementById('modalUniversidadTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Universidad';
            document.getElementById('btnGuardarUniversidad').innerHTML = 'Actualizar Universidad';
            
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
                carrerasHtml = '<div class="table-responsive"><table class="table table-sm table-hover"><thead><tr><th>Carrera</th><th>Título</th><th>Duración</th><th>Modalidades</th></tr></thead><tbody>';
                data.carreras.forEach(c => {
                    let modalidadesHtml = '';
                    if (c.modalidades && c.modalidades.length > 0) {
                        modalidadesHtml = c.modalidades.map(m => 
                            `<span class="badge bg-primary me-1">${m.nombre}</span>`
                        ).join('');
                    } else {
                        modalidadesHtml = '<span class="text-muted">No especificado</span>';
                    }
                    
                    carrerasHtml += `
                        <tr>
                            <td>${c.nombre}</td>
                            <td>${c.titulo_otorgado || 'No especificado'}</td>
                            <td>${c.duracion || 'No especificada'}</td>
                            <td>${modalidadesHtml}</td>
                        </tr>
                    `;
                });
                carrerasHtml += '</tbody></table></div>';
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

// Función para eliminar universidad
function eliminarUniversidad(id, nombre, countCarreras) {
    if (countCarreras > 0) {
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            html: `La universidad <strong>${nombre}</strong> tiene ${countCarreras} carreras asociadas.<br>
                   Elimina primero las carreras para poder eliminar esta universidad.`,
            confirmButtonColor: '#4e73df'
        });
        return;
    }
    
    Swal.fire({
        title: '¿Eliminar universidad?',
        html: `¿Estás seguro de eliminar la universidad <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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

// Vista previa del logo
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tamaño (2MB)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Archivo muy grande',
                text: 'El logo no puede superar los 2MB'
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
                text: 'Solo se permiten imágenes JPG, PNG, GIF y WEBP'
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
            text: 'El nombre debe tener al menos 3 caracteres'
        });
        return;
    }
    
    if (!region) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Región requerida',
            text: 'Debes seleccionar una región'
        });
        return;
    }
});
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
}

.universidad-card .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
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