<?php
require_once 'models/Region.php';

class RegionController
{

    public function index()
    {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $region = new Region();
        $regiones = $region->getAll();

        // Determinar vista según rol
        if (Session::isAdmin()) {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/admin/regiones/index.php';
            require_once 'views/layouts/footer.php';
        } else {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/personal/regiones/index.php';
            require_once 'views/layouts/footer.php';
        }
    }

    public function guardar()
    {
        // Solo admin puede guardar
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $region = new Region();
            $id = $_POST['id'] ?? null;

            // Validar que el nombre no esté vacío
            if (empty($_POST['nombre'])) {
                header("Location: index.php?action=regiones&error=El+nombre+de+la+región+es+obligatorio");
                exit();
            }

            $data = [
                'nombre' => trim($_POST['nombre'])
            ];

            if ($id) {
                // Actualizar
                if ($region->update($id, $data)) {
                    header("Location: index.php?action=regiones&mensaje=Región+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=regiones&error=Error+al+actualizar+región");
                }
            } else {
                // Crear
                if ($region->create($data)) {
                    header("Location: index.php?action=regiones&mensaje=Región+creada+correctamente");
                } else {
                    header("Location: index.php?action=regiones&error=Error+al+crear+región");
                }
            }
        }
        exit();
    }

    public function eliminar()
    {
        // Solo admin puede eliminar
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $region = new Region();

            // Verificar si tiene universidades asociadas
            $count = $region->getUniversidadesCount($id);
            if ($count > 0) {
                header("Location: index.php?action=regiones&error=No+se+puede+eliminar+la+región+porque+tiene+$count+universidades+asociadas");
                exit();
            }

            if ($region->delete($id)) {
                header("Location: index.php?action=regiones&mensaje=Región+eliminada+correctamente");
            } else {
                header("Location: index.php?action=regiones&error=Error+al+eliminar+región");
            }
        }
        exit();
    }

    public function ver()
    {
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $region = new Region();
            $data = $region->getById($id);
            $data['universidades_count'] = $region->getUniversidadesCount($id);

            // Obtener universidades de esta región
            require_once 'models/Universidad.php';
            $universidad = new Universidad();
            $data['universidades'] = $universidad->getByRegion($id);

            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }
}
?>