<?php
// Verificar que las variables existen
if (!isset($usuarios)) $usuarios = [];
if (!isset($roles)) $roles = [];
?>

<!-- Mensajes con SweetAlert -->
<?php if (isset($_GET['mensaje'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?php echo $_GET['mensaje']; ?>',
        timer: 5000,
        showConfirmButton: true
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
            <i class="bi bi-people"></i> Gestión de Usuarios
        </h2>
    </div>
</div>

<!-- Botón Nuevo Usuario -->
<div class="row mb-4">
    <div class="col-12">
        <button class="btn btn-primary" onclick="abrirModalNuevoUsuario()">
            <i class="bi bi-person-plus"></i> Nuevo Usuario
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-md-4">
        <select class="form-select" id="filtroRol" onchange="filtrarUsuarios()">
            <option value="">Todos los roles</option>
            <?php foreach ($roles as $rol): ?>
            <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="filtroEstado" onchange="filtrarUsuarios()">
            <option value="">Todos los estados</option>
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>
    <div class="col-md-4">
        <input type="text" class="form-control" id="buscarUsuario" placeholder="Buscar usuario..." onkeyup="filtrarUsuarios()">
    </div>
</div>

<!-- Tabla de Usuarios -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Lista de Usuarios</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tablaUsuarios" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
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
                                <td colspan="8" class="text-center">No hay usuarios registrados</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $u): ?>
                                <tr data-id="<?php echo $u['id']; ?>" 
                                    data-rol="<?php echo $u['id_rol']; ?>" 
                                    data-estado="<?php echo $u['activo']; ?>"
                                    data-nombre="<?php echo htmlspecialchars($u['nombre']); ?>"
                                    data-email="<?php echo htmlspecialchars($u['email']); ?>">
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $u['id_rol'] == 1 ? 'bg-danger' : 'bg-info'; ?>">
                                            <?php echo $u['rol_nombre']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($u['activo']): ?>
                                            <span class="badge bg-success">Activo</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $u['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($u['ultimo_acceso'])) : 'Nunca'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Ver detalle -->
                                            <button class="btn btn-sm btn-info" onclick="verUsuario(<?php echo $u['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            
                                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                                <!-- Editar (solo nombre y email) -->
                                                <button class="btn btn-sm btn-warning" onclick="editarUsuario(<?php echo $u['id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                
                                                <!-- Resetear contraseña -->
                                                <button class="btn btn-sm btn-secondary" onclick="resetPassword(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>')">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                                
                                                <!-- Activar/Desactivar -->
                                                <?php if ($u['activo']): ?>
                                                    <button class="btn btn-sm btn-dark" onclick="cambiarEstado(<?php echo $u['id']; ?>, 0, '<?php echo htmlspecialchars($u['nombre']); ?>')">
                                                        <i class="bi bi-person-x"></i> Desactivar
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-success" onclick="cambiarEstado(<?php echo $u['id']; ?>, 1, '<?php echo htmlspecialchars($u['nombre']); ?>')">
                                                        <i class="bi bi-person-check"></i> Activar
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <!-- Eliminar -->
                                                <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['nombre']); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="badge bg-info p-2">Usuario actual</span>
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
</div>

<!-- Modal para Ver Detalle -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge"></i> Detalle del Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleUsuarioContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalUsuarioTitle">
                    <i class="bi bi-person-plus"></i> Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=usuario-guardar" id="formUsuario">
                <div class="modal-body">
                    <input type="hidden" name="id" id="usuarioId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3" id="campoPassword">
                        <label for="password" class="form-label">Contraseña *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" minlength="6">
                            <button class="btn btn-outline-secondary" type="button" onclick="generarPassword()">
                                <i class="bi bi-dice-6"></i> Generar
                            </button>
                        </div>
                        <small class="text-muted" id="passwordHelp">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3" id="campoRol">
                        <label for="id_rol" class="form-label">Rol *</label>
                        <select class="form-select" id="id_rol" name="id_rol">
                            <option value="">Seleccione un rol</option>
                            <?php foreach ($roles as $rol): ?>
                            <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="campoActivo">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="activo" name="activo" checked>
                            <label class="form-check-label" for="activo">
                                Usuario activo
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar Usuario</button>
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
    document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-person-plus"></i> Nuevo Usuario';
    
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
    document.getElementById('modalUsuarioTitle').innerHTML = '<i class="bi bi-pencil"></i> Editar Usuario';
    
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
                    <i class="bi bi-person-circle fs-1 text-primary"></i>
                </div>
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%">ID:</th>
                        <td>${data.id}</td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>${data.nombre}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>${data.email}</td>
                    </tr>
                    <tr>
                        <th>Rol:</th>
                        <td>
                            <span class="badge ${data.id_rol == 1 ? 'bg-danger' : 'bg-info'}">
                                ${data.rol_nombre}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            ${data.activo ? 
                                '<span class="badge bg-success">Activo</span>' : 
                                '<span class="badge bg-secondary">Inactivo</span>'}
                        </td>
                    </tr>
                    <tr>
                        <th>Último Acceso:</th>
                        <td>${fechaAcceso}</td>
                    </tr>
                    <tr>
                        <th>Fecha Creación:</th>
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
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
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
        confirmButtonColor: '#f6c23e',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, resetear',
        cancelButtonText: 'Cancelar'
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
        title: `¿${accion} usuario?`,
        html: `¿Estás seguro de ${accion} al usuario <strong>${nombre}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: estado ? '#1cc88a' : '#858796',
        cancelButtonColor: '#858796',
        confirmButtonText: `Sí, ${accion}`,
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `index.php?action=usuario-cambiar-estado&id=${id}&estado=${estado}`;
        }
    });
}

// Función para generar contraseña aleatoria
function generarPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
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
    
    filas.forEach(fila => {
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
    });
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
            text: 'El nombre debe tener al menos 3 caracteres'
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
            text: 'Por favor ingresa un correo electrónico válido'
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
                text: 'La contraseña debe tener al menos 6 caracteres'
            });
            return;
        }
        
        if (!document.getElementById('id_rol').value) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Rol requerido',
                text: 'Debes seleccionar un rol para el usuario'
            });
            return;
        }
    }
});
</script>

<style>
/* Estilos adicionales para el modal */
.btn-group {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.btn-group .btn {
    margin: 0;
    border-radius: 0.5rem !important;
}

#tablaUsuarios td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
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