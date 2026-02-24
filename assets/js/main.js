// Funciones globales con SweetAlert
function mostrarExito(mensaje) {
    Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: mensaje,
        timer: 3000,
        showConfirmButton: false
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: mensaje,
        confirmButtonColor: '#e74a3b'
    });
}

function mostrarAdvertencia(mensaje) {
    Swal.fire({
        icon: 'warning',
        title: 'Advertencia',
        text: mensaje,
        confirmButtonColor: '#f6c23e'
    });
}

function mostrarInfo(mensaje) {
    Swal.fire({
        icon: 'info',
        title: 'Información',
        text: mensaje,
        confirmButtonColor: '#36b9cc'
    });
}

function confirmarAccion(titulo, texto, callback) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}

function mostrarCargando(mensaje = 'Cargando...') {
    Swal.fire({
        title: mensaje,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function cerrarCargando() {
    Swal.close();
}

// Función para notificaciones toast
function mostrarToast(mensaje, tipo = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: tipo,
        title: mensaje
    });
}

// Ejemplo de uso para confirmar eliminación
function confirmarEliminacion(nombre, callback) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar ${nombre}? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        cancelButtonColor: '#858796',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed && callback) {
            callback();
        }
    });
}