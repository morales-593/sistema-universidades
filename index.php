<?php
require_once 'config/session.php';

// Obtener la acción de la URL
$action = $_GET['action'] ?? 'login';

// Rutas públicas (no requieren autenticación)
$publicRoutes = ['login', 'logout'];

// Verificar autenticación para rutas protegidas
if (!in_array($action, $publicRoutes) && !Session::isLoggedIn()) {
    header("Location: index.php?action=login");
    exit();
}

// Enrutador
switch ($action) {
    // Autenticación
    case 'login':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // Dashboard
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    // Módulo Regiones
// En la sección de Módulo Regiones:
    case 'regiones':
        require_once 'controllers/RegionController.php';
        $controller = new RegionController();
        $controller->index();
        break;

    case 'region-guardar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/RegionController.php';
        $controller = new RegionController();
        $controller->guardar();
        break;

    case 'region-eliminar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/RegionController.php';
        $controller = new RegionController();
        $controller->eliminar();
        break;

    case 'region-ver':
        require_once 'controllers/RegionController.php';
        $controller = new RegionController();
        $controller->ver();
        break;

    // Módulo Universidades
// En la sección de Módulo Universidades:
    case 'universidades':
        require_once 'controllers/UniversidadController.php';
        $controller = new UniversidadController();
        $controller->index();
        break;

    case 'universidad-guardar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UniversidadController.php';
        $controller = new UniversidadController();
        $controller->guardar();
        break;

    case 'universidad-eliminar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UniversidadController.php';
        $controller = new UniversidadController();
        $controller->eliminar();
        break;

    case 'universidad-ver':
        require_once 'controllers/UniversidadController.php';
        $controller = new UniversidadController();
        $controller->ver();
        break;

    // Módulo Carreras
// En la sección de Módulo Carreras:
    case 'carreras':
        require_once 'controllers/CarreraController.php';
        $controller = new CarreraController();
        $controller->index();
        break;

    case 'carrera-guardar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/CarreraController.php';
        $controller = new CarreraController();
        $controller->guardar();
        break;

    case 'carrera-eliminar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/CarreraController.php';
        $controller = new CarreraController();
        $controller->eliminar();
        break;

    case 'carrera-ver':
        require_once 'controllers/CarreraController.php';
        $controller = new CarreraController();
        $controller->ver();
        break;

    case 'carreras-por-universidad':
        require_once 'controllers/CarreraController.php';
        $controller = new CarreraController();
        $controller->getPorUniversidad();
        break;

    // Módulo Modalidades
// En la sección de Módulo Modalidades:
    case 'modalidades':
        require_once 'controllers/ModalidadController.php';
        $controller = new ModalidadController();
        $controller->index();
        break;

    case 'modalidad-guardar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/ModalidadController.php';
        $controller = new ModalidadController();
        $controller->guardar();
        break;

    case 'modalidad-eliminar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/ModalidadController.php';
        $controller = new ModalidadController();
        $controller->eliminar();
        break;

    case 'modalidad-ver':
        require_once 'controllers/ModalidadController.php';
        $controller = new ModalidadController();
        $controller->ver();
        break;

    // Módulo Usuarios (solo admin)
// En la sección de Módulo Usuarios:
    case 'usuarios':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->index();
        break;

    case 'usuario-guardar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->guardar();
        break;

    case 'usuario-eliminar':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->eliminar();
        break;

    case 'usuario-cambiar-estado':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->cambiarEstado();
        break;

    case 'usuario-reset-password':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->resetPassword();
        break;

    case 'usuario-ver':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->ver();
        break;

    // API endpoints
    case 'api-regiones':
        require_once 'api/regiones.php';
        break;

    case 'api-universidades':
        require_once 'api/universidades.php';
        break;

    case 'api-carreras':
        require_once 'api/carreras.php';
        break;

    case 'api-modalidades':
        require_once 'api/modalidades.php';
        break;

    // Acceso denegado
    case 'acceso-denegado':
        require_once 'views/layouts/header.php';
        require_once 'acceso-denegado.php';
        require_once 'views/layouts/footer.php';
        break;

    // En la sección de Módulo Usuarios, agregar:
    case 'usuario-reset-password':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->resetPassword();
        break;

    case 'usuario-cambiar-estado':
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }
        require_once 'controllers/UsuarioController.php';
        $controller = new UsuarioController();
        $controller->cambiarEstado();
        break;

    // En la sección de API endpoints, agregar:
    case 'api-usuarios':
        require_once 'api/usuarios.php';
        break;

    // Por defecto, redirigir al login
    default:
        header("Location: index.php?action=login");
        break;
}
?>