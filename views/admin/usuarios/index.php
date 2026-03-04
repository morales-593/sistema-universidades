<?php
// Verificar que las variables existen
if (!isset($usuarios)) $usuarios = [];
if (!isset($roles)) $roles = [];
?>

<link rel="stylesheet" href="assets/css/admin/usuarios/usuario.css">

<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 5000,
        showConfirmButton: true,
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

<div class="admin-usuarios">
    <!-- Header -->
    <div class="admin-header">
        <h2>
            <i class="bi bi-people"></i>
            Gestión de Usuarios
        </h2>
    </div>

    <!-- Action Bar -->
    <div class="action-bar">
        <button class="btn-nuevo" onclick="abrirModalNuevoUsuario()">
            <i class="bi bi-person-plus"></i>
            Nuevo Usuario
        </button>
        
        <div class="filter-section">
            <select class="filter-select" id="filtroRol" onchange="filtrarUsuarios()">
                <option value="">Roles</option>
                <?php foreach ($roles as $rol): ?>
                <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                <?php endforeach; ?>
            </select>
            
            <select class="filter-select" id="filtroEstado" onchange="filtrarUsuarios()">
                <option value="">Estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
            
            <input type="text" class="filter-input" id="buscarUsuario" placeholder="Buscar usuario..." onkeyup="filtrarUsuarios()">
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="table-card-admin">
        <div class="card-header">
            <h6>
                <i class="bi bi-list-task"></i>
                Lista de Usuarios
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="modern-table-admin" id="tablaUsuarios" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Último Acceso</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usuarios)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <p>No hay usuarios registrados</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($usuarios as $u): ?>
                            <tr data-id="<?php echo $u['id']; ?>" 
                                data-rol="<?php echo $u['id_rol']; ?>" 
                                data-estado="<?php echo $u['activo']; ?>"
                                data-nombre="<?php echo htmlspecialchars($u['nombre']); ?>"
                                data-email="<?php echo htmlspecialchars($u['email']); ?>">
                                <td>
                                    <span class="id-badge">#<?php echo $u['id']; ?></span>
                                </td>
                                <td>
                                    <div class="user-name"><?php echo htmlspecialchars($u['nombre']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($u['email']); ?></div>
                                </td>
                                <td>
                                    <span class="role-badge <?php echo $u['id_rol'] == 1 ? 'admin' : 'personal'; ?>">
                                        <i class="bi <?php echo $u['id_rol'] == 1 ? 'bi-shield-lock' : 'bi-person-badge'; ?>"></i>
                                        <?php echo $u['rol_nombre']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($u['activo']): ?>
                                        <span class="status-badge activo">
                                            <i class="bi bi-check-circle"></i> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactivo">
                                            <i class="bi bi-x-circle"></i> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="fecha-cell">
                                    <?php if ($u['ultimo_acceso']): ?>
                                        <i class="bi bi-clock-history me-1"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($u['ultimo_acceso'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fecha-cell">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?php echo date('d/m/Y', strtotime($u['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <!-- Ver detalle -->
                                        <button class="btn-action btn-view" onclick="verUsuario(<?php echo $u['id']; ?>)" title="Ver detalles">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <!-- Editar (solo nombre y email) -->
                                            <button class="btn-action btn-edit" onclick="editarUsuario(<?php echo $u['id']; ?>)" title="Editar usuario">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            <!-- Resetear contraseña -->
                                            <button class="btn-action btn-reset" onclick="resetPassword(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>')" title="Resetear contraseña">
                                                <i class="bi bi-key"></i>
                                            </button>
                                            
                                            <!-- Activar/Desactivar -->
                                            <?php if ($u['activo']): ?>
                                                <button class="btn-action btn-deactivate" onclick="cambiarEstado(<?php echo $u['id']; ?>, 0, '<?php echo htmlspecialchars($u['nombre']); ?>')" title="Desactivar usuario">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn-action btn-activate" onclick="cambiarEstado(<?php echo $u['id']; ?>, 1, '<?php echo htmlspecialchars($u['nombre']); ?>')" title="Activar usuario">
                                                    <i class="bi bi-person-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            
                                            <!-- Eliminar -->
                                            <button class="btn-action btn-delete" onclick="eliminarUsuario(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>')" title="Eliminar usuario">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="current-user-badge">
                                                <i class="bi bi-person-circle"></i>
                                                Usuario actual
                                            </span>
                                        <?php endif; ?>
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

<!-- Modal para Ver Detalle -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge me-2"></i>
                    Detalle del Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-admin" id="detalleUsuarioContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer modal-footer-admin">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-admin">
            <div class="modal-header modal-header-admin">
                <h5 class="modal-title" id="modalUsuarioTitle">
                    <i class="bi bi-person-plus me-2"></i>
                    Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=usuario-guardar" id="formUsuario">
                <div class="modal-body modal-body-admin">
                    <input type="hidden" name="id" id="usuarioId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label-admin">Nombre completo *</label>
                        <input type="text" class="form-control-admin" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Juan Pérez">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label-admin">Correo electrónico *</label>
                        <input type="email" class="form-control-admin" id="email" name="email" required placeholder="ejemplo@correo.com">
                    </div>
                    
                    <div class="mb-3" id="campoPassword">
                        <label for="password" class="form-label-admin">Contraseña *</label>
                        <div class="input-group-custom">
                            <input type="password" class="form-control-admin" id="password" name="password" minlength="6" placeholder="••••••••">
                            <button type="button" class="btn-generate" onclick="generarPassword()">
                                <i class="bi bi-dice-6"></i>
                                Generar
                            </button>
                        </div>
                        <small class="form-hint" id="passwordHelp">
                            <i class="bi bi-info-circle me-1"></i>
                            Mínimo 6 caracteres
                        </small>
                    </div>
                    
                    <div class="mb-3" id="campoRol">
                        <label for="id_rol" class="form-label-admin">Rol *</label>
                        <select class="form-select-admin" id="id_rol" name="id_rol">
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="campoActivo">
                        <div class="form-check-custom">
                            <input type="checkbox" id="activo" name="activo" checked>
                            <label for="activo">Usuario activo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-footer-admin">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnGuardar">
                        <i class="bi bi-check-circle me-2"></i>
                        Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Función para abrir modal de nuevo usuario
function abrirModalNuevoUsuario() {
    document.getElementById('formUsuario').reset();
    document.getElementById('usuarioId').value = '';
    document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Nuevo Usuario';
    
    // Mostrar todos los campos para nuevo usuario
    document.getElementById('campoPassword').style.display = 'block';
    document.getElementById('campoRol').style.display = 'block';
    document.getElementById('campoActivo').style.display = 'block';
    
    // Hacer campos requeridos
    document.getElementById('password').required = true;
    document.getElementById('id_rol').required = true;
    
    // Actualizar texto de ayuda
    document.getElementById('passwordHelp').style.display = 'block';
    
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

// Función para editar usuario (solo nombre y email)
function editarUsuario(id) {
    // Obtener datos de la fila
    const fila = document.querySelector(`tr[data-id="${id}"]`);
    const nombre = fila.dataset.nombre;
    const email = fila.dataset.email;
    
    // Llenar el formulario
    document.getElementById('usuarioId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('email').value = email;
    
    // Ocultar campos que no se pueden editar
    document.getElementById('campoPassword').style.display = 'none';
    document.getElementById('campoRol').style.display = 'none';
    document.getElementById('campoActivo').style.display = 'none';
    
    // Quitar required de campos ocultos
    document.getElementById('password').required = false;
    document.getElementById('id_rol').required = false;
    
    // Cambiar título
    document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
    document.getElementById('btnGuardar').innerHTML = '<i class="bi bi-check-circle me-2"></i>Actualizar Usuario';
    
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

// Función para ver detalle de usuario
function verUsuario(id) {
    fetch(`index.php?action=usuario-ver&id=${id}`)
        .then(response => response.json())
        .then(data => {
            const fechaAcceso = data.ultimo_acceso ? new Date(data.ultimo_acceso).toLocaleString() : 'Nunca';
            const fechaCreacion = new Date(data.created_at).toLocaleDateString();
            
            const html = `
                <div class="text-center mb-4">
                    <div class="detail-avatar">
                        <i class="bi bi-person-circle"></i>
                    </div>
                </div>
                <table class="info-table">
                    <tr>
                        <td><i class="bi bi-hash me-2"></i>ID:</td>
                        <td><strong>#${data.id}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-person me-2"></i>Nombre:</td>
                        <td><strong>${data.nombre}</strong></td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-envelope me-2"></i>Email:</td>
                        <td>${data.email}</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-shield me-2"></i>Rol:</td>
                        <td>
                            <span class="role-badge ${data.id_rol == 1 ? 'admin' : 'personal'}">
                                <i class="bi ${data.id_rol == 1 ? 'bi-shield-lock' : 'bi-person-badge'}"></i>
                                ${data.rol_nombre}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-activity me-2"></i>Estado:</td>
                        <td>
                            ${data.activo ? 
                                '<span class="status-badge activo"><i class="bi bi-check-circle"></i> Activo</span>' : 
                                '<span class="status-badge inactivo"><i class="bi bi-x-circle"></i> Inactivo</span>'}
                        </td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-clock-history me-2"></i>Último Acceso:</td>
                        <td>${fechaAcceso}</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-calendar me-2"></i>Fecha Creación:</td>
                        <td>${fechaCreacion}</td>
                    </tr>
                </table>
            `;
            
            document.getElementById('detalleUsuarioContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('modalVerUsuario')).show();
        });
}

// Función para eliminar usuario
function eliminarUsuario(id, nombre) {
    Swal.fire({
        title: '¿Eliminar usuario?',
        html: `¿Estás seguro de eliminar al usuario <strong>${nombre}</strong>?<br>Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: 'white'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=usuario-eliminar&id=${id}`;
        }
    });
}

// Función para resetear contraseña
function resetPassword(id, nombre) {
    Swal.fire({
        title: 'Resetear contraseña',
        html: `¿Generar una nueva contraseña para <strong>${nombre}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#fd7e14',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, resetear',
        cancelButtonText: 'Cancelar',
        background: 'white'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=usuario-reset-password&id=${id}`;
        }
    });
}

// Función para cambiar estado (activar/desactivar)
function cambiarEstado(id, estado, nombre) {
    const accion = estado ? 'activar' : 'desactivar';
    Swal.fire({
        title: `¿${accion.charAt(0).toUpperCase() + accion.slice(1)} usuario?`,
        html: `¿Estás seguro de ${accion} al usuario <strong>${nombre}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: estado ? '#28a745' : '#6c757d',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar',
        background: 'white'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=usuario-cambiar-estado&id=${id}&estado=${estado}`;
        }
    });
}

// Función para generar contraseña aleatoria
function generarPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('password').value = password;
}

// Función para filtrar usuarios
function filtrarUsuarios() {
    const rol = document.getElementById('filtroRol').value;
    const estado = document.getElementById('filtroEstado').value;
    const busqueda = document.getElementById('buscarUsuario').value.toLowerCase();
    
    const filas = document.querySelectorAll('#tablaUsuarios tbody tr');
    let visibleCount = 0;
    
    filas.forEach(fila => {
        // Saltar la fila de "no hay usuarios" si existe
        if (fila.cells.length < 7) return;
        
        let mostrar = true;
        
        // Filtrar por rol
        if (rol && fila.dataset.rol != rol) {
            mostrar = false;
        }
        
        // Filtrar por estado
        if (estado !== '' && fila.dataset.estado != estado) {
            mostrar = false;
        }
        
        // Filtrar por búsqueda
        if (busqueda) {
            const texto = fila.textContent.toLowerCase();
            if (!texto.includes(busqueda)) {
                mostrar = false;
            }
        }
        
        fila.style.display = mostrar ? '' : 'none';
        if (mostrar) visibleCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    const tbody = document.querySelector('#tablaUsuarios tbody');
    const mensajeExistente = document.getElementById('noResultsMessage');
    
    if (visibleCount === 0 && filas.length > 0 && filas[0].cells.length >= 7) {
        if (!mensajeExistente) {
            const msg = document.createElement('tr');
            msg.id = 'noResultsMessage';
            msg.innerHTML = `
                <td colspan="7" class="text-center">
                    <div class="no-results">
                        <i class="bi bi-search me-2"></i>
                        No se encontraron usuarios que coincidan con los filtros.
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
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    const id = document.getElementById('usuarioId').value;
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Validar nombre
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
    
    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Email inválido',
            text: 'Por favor ingresa un correo electrónico válido',
            confirmButtonColor: '#667eea',
            background: 'white'
        });
        return;
    }
    
    // Validar contraseña solo para nuevos usuarios
    if (!id) {
        if (password.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Contraseña muy corta',
                text: 'La contraseña debe tener al menos 6 caracteres',
                confirmButtonColor: '#667eea',
                background: 'white'
            });
            return;
        }
        
        if (!document.getElementById('id_rol').value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Rol requerido',
                text: 'Debes seleccionar un rol para el usuario',
                confirmButtonColor: '#667eea',
                background: 'white'
            });
            return;
        }
    }
});
</script>