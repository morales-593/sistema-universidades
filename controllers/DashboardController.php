<?php
require_once 'models/Region.php';
require_once 'models/Universidad.php';
require_once 'models/Carrera.php';
require_once 'models/Modalidad.php';
require_once 'models/Usuario.php';

class DashboardController {

    public function index() {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        // Determinar qué dashboard mostrar
        if (Session::isAdmin()) {
            $this->adminDashboard();
        } else {
            $this->personalDashboard();
        }
    }

    private function adminDashboard() {
        $region = new Region();
        $universidad = new Universidad();
        $carrera = new Carrera();
        $modalidad = new Modalidad();
        $usuario = new Usuario();

        $totalRegiones = count($region->getAll());
        $totalUniversidades = count($universidad->getAll());
        $totalCarreras = count($carrera->getAll());
        $totalModalidades = count($modalidad->getAll());
        $totalUsuarios = count($usuario->getAll());

        // Datos para gráficos
        $universidadesPorRegion = [];
        $regiones = $region->getAll();
        foreach ($regiones as $r) {
            $universidadesPorRegion[$r['nombre']] = count($universidad->getByRegion($r['id']));
        }

        $carrerasRecientes = array_slice($carrera->getAllWithDetails(), 0, 5);

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/navbar.php';
        require_once 'views/admin/dashboard.php';
        require_once 'views/layouts/footer.php';
    }

    private function personalDashboard() {
        $region = new Region();
        $universidad = new Universidad();
        $carrera = new Carrera();
        $modalidad = new Modalidad();

        $totalRegiones = count($region->getAll());
        $totalUniversidades = count($universidad->getAll());
        $totalCarreras = count($carrera->getAll());
        $totalModalidades = count($modalidad->getAll());

        $universidadesDestacadas = $universidad->getAllWithRegion();
        $carrerasDestacadas = array_slice($carrera->getAllWithDetails(), 0, 5);

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/navbar.php';
        require_once 'views/personal/dashboard.php';
        require_once 'views/layouts/footer.php';
    }
}
?>