<?php
// Verificar que las variables existen
if (!isset($regiones))
    $regiones = [];
?>

<link rel="stylesheet" href="assets/css/admin/regiones/region2.css">
<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
        document.addEventListener('DOMContentLoaded', function () {
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

<div class="admin-regiones">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-geo-alt"></i>
            Gestión de Regiones
        </h2>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevaRegion()">
            <i class="bi bi-plus-circle"></i>
            Nueva Región
        </button>

        <div class="search-wrapper">
            <input type="text" class="search-input-admin" id="buscarRegion" placeholder="Buscar región por nombre..."
                onkeyup="filtrarRegiones()">
        </div>
    </div>

    <!-- Tabla de Regiones -->
    <div class="table-card-admin">
        <div class="card-header">
            <h6>
                <i class="bi bi-list-task"></i>
                Lista de Regiones
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="modern-table-admin" id="tablaRegiones" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Universidades</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($regiones)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="empty-state">
                                        <i class="bi bi-geo-alt"></i>
                                        <p>No hay regiones registradas</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($regiones as $r):
                                $regionModel = new Region();
                                $countUniversidades = $regionModel->getUniversidadesCount($r['id']);
                                ?>
                                <tr data-id="<?php echo $r['id']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($r['nombre']); ?>">
                                    <td>
                                        <span class="id-badge">#<?php echo $r['id']; ?></span>
                                    </td>
                                    <td class="region-nombre"><?php echo htmlspecialchars($r['nombre']); ?></td>
                                    <td>
                                        <span class="universidades-count">
                                            <i class="bi bi-building"></i>
                                            <?php echo $countUniversidades; ?> universidades
                                        </span>
                                    </td>
                                    <td class="fecha-cell">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button class="btn-action btn-view" onclick="verRegion(<?php echo $r['id']; ?>)"
                                                title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn-action btn-edit"
                                                onclick="editarRegion(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>')"
                                                title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn-action btn-delete"
                                                onclick="eliminarRegion(<?php echo $r['id']; ?>, '<?php echo htmlspecialchars($r['nombre']); ?>', <?php echo $countUniversidades; ?>)"
                                                title="Eliminar">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Ver Detalle de Región -->
<div class="modal fade" id="modalVerRegion" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt me-2"></i>
                    Detalle de Región
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleRegionContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Región -->
<div class="modal fade" id="modalRegion" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalRegionTitle">
                    <i class="bi bi-plus-circle me-2"></i>
                    Nueva Región
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=region-guardar" id="formRegion">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="regionId">

                    <div class="mb-3">
                        <label for="nombre" class="form-label-admin">Nombre de la Región *</label>
                        <input type="text" class="form-control-admin" id="nombre" name="nombre" required maxlength="100"
                            placeholder="Ej: Sierra, Costa, Amazonía, Insular">
                        <small class="form-hint">
                            <i class="bi bi-info-circle me-1"></i>
                            Máximo 100 caracteres
                        </small>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardarRegion">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Región
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Función para abrir modal de nueva región
    function abrirModalNuevaRegion() {
        document.getElementById('formRegion').reset();
        document.getElementById('regionId').value = '';
        document.getElementById('modalRegionTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nueva Región';
        document.getElementById('btnGuardarRegion').innerHTML = '<i class="bi bi-check-circle me-2"></i>Guardar Región';
        new bootstrap.Modal(document.getElementById('modalRegion')).show();
    }

    // Función para editar región
    function editarRegion(id, nombre) {
        document.getElementById('regionId').value = id;
        document.getElementById('nombre').value = nombre;
        document.getElementById('modalRegionTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Región';
        document.getElementById('btnGuardarRegion').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Región';
        new bootstrap.Modal(document.getElementById('modalRegion')).show();
    }

    // Función para ver detalle de región
    function verRegion(id) {
        fetch(`index.php?action=region-ver&id=${id}`)
            .then(response => response.json())
            .then(data => {
                let universidadesHtml = '';

                if (data.universidades && data.universidades.length > 0) {
                    universidadesHtml = '<div style="max-height: 300px; overflow-y: auto;">';
                    data.universidades.forEach(u => {
                        universidadesHtml += `
                        <div class="university-item">
                            <span><i class="bi bi-building me-2" style="color: #667eea;"></i>${u.nombre}</span>
                            <span class="carreras-count">
                                <i class="bi bi-book me-1"></i>${u.carreras_count || 0} carreras
                            </span>
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
                    <div class="detail-icon">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h4 style="color: #2d3748; font-weight: 700;">${data.nombre}</h4>
                </div>
                
                <table class="info-table">
                    <tr>
                        <td><i class="bi bi-hash me-2"></i>ID:</td>
                        <td><strong>#${data.id}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-building me-2"></i>Universidades:</td>
                        <td>
                            <span class="universidades-count">
                                <i class="bi bi-building"></i> ${data.universidades_count || 0} universidades
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-calendar me-2"></i>Fecha Creación:</td>
                        <td>${new Date(data.created_at).toLocaleDateString()}</td>
                    </tr>
                </table>
                
                <h6 class="mb-3" style="color: #2d3748; font-weight: 600;">
                    <i class="bi bi-building me-2"></i>
                    Universidades en esta región:
                </h6>
                ${universidadesHtml}
            `;

                document.getElementById('detalleRegionContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('modalVerRegion')).show();
            });
    }

    // Función para eliminar región
    function eliminarRegion(id, nombre, countUniversidades) {
        if (countUniversidades > 0) {
            Swal.fire({
                icon: 'error',
                title: 'No se puede eliminar',
                html: `La región <strong>${nombre}</strong> tiene ${countUniversidades} universidades asociadas.<br>
                   Elimina primero las universidades para poder eliminar esta región.`,
                confirmButtonColor: '#667eea',
                background: 'white',
                iconColor: '#dc3545'
            });
            return;
        }

        Swal.fire({
            title: '¿Eliminar región?',
            html: `¿Estás seguro de eliminar la región <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: 'white'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?action=region-eliminar&id=${id}`;
            }
        });
    }

    // Función para filtrar regiones
    function filtrarRegiones() {
        const busqueda = document.getElementById('buscarRegion').value.toLowerCase();
        const filas = document.querySelectorAll('#tablaRegiones tbody tr');
        let visibleCount = 0;

        filas.forEach(fila => {
            if (fila.cells.length > 1) {
                const nombre = fila.cells[1].textContent.toLowerCase();
                if (nombre.includes(busqueda)) {
                    fila.style.display = '';
                    visibleCount++;
                } else {
                    fila.style.display = 'none';
                }
            }
        });

        // Mostrar mensaje si no hay resultados
        const tbody = document.querySelector('#tablaRegiones tbody');
        const mensajeExistente = document.getElementById('noResultsMessage');

        if (visibleCount === 0 && filas.length > 0 && filas[0].cells.length > 1) {
            if (!mensajeExistente) {
                const msg = document.createElement('tr');
                msg.id = 'noResultsMessage';
                msg.innerHTML = `
                <td colspan="5" class="text-center py-4">
                    <div class="empty-state">
                        <i class="bi bi-search"></i>
                        <p>No se encontraron regiones que coincidan con la búsqueda.</p>
                    </div>
                </td>
            `;
                tbody.appendChild(msg);
            }
        } else if (mensajeExistente) {
            mensajeExistente.remove();
        }
    }

    // Validar formulario antes de enviar
    document.getElementById('formRegion').addEventListener('submit', function (e) {
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