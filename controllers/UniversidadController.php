<?php
require_once 'models/Universidad.php';
require_once 'models/Region.php';

class UniversidadController {

    public function index() {
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $universidad = new Universidad();
        $region = new Region();
        
        $universidades = $universidad->getAllWithRegion();
        $regiones = $region->getAll();

        if (Session::isAdmin()) {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/admin/universidades/index.php';
            require_once 'views/layouts/footer.php';
        } else {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/personal/universidades/index.php';
            require_once 'views/layouts/footer.php';
        }
    }

    public function guardar() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $universidad = new Universidad();
            $id = $_POST['id'] ?? null;
            
            // Manejar subida de logo
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $target_dir = "assets/uploads/logos/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $logo = time() . '_' . basename($_FILES["logo"]["name"]);
                $target_file = $target_dir . $logo;
                move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
            }

            $data = [
                'nombre' => $_POST['nombre'],
                'id_region' => $_POST['id_region'],
                'descripcion' => $_POST['descripcion'],
                'link_plataforma' => $_POST['link_plataforma'],
                'direccion' => $_POST['direccion'],
                'telefono' => $_POST['telefono'],
                'email' => $_POST['email'],
                'logo' => $logo
            ];

            if ($id) {
                if ($universidad->update($id, $data)) {
                    header("Location: index.php?action=universidades&mensaje=Universidad+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=universidades&error=Error+al+actualizar+universidad");
                }
            } else {
                if ($universidad->create($data)) {
                    header("Location: index.php?action=universidades&mensaje=Universidad+creada+correctamente");
                } else {
                    header("Location: index.php?action=universidades&error=Error+al+crear+universidad");
                }
            }
        }
        exit();
    }

    public function eliminar() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $universidad = new Universidad();
            
            // Verificar si tiene carreras asociadas
            $count = $universidad->getCarrerasCount($id);
            if ($count > 0) {
                header("Location: index.php?action=universidades&error=No+se+puede+eliminar+la+universidad+porque+tiene+carreras+asociadas");
                exit();
            }

            if ($universidad->delete($id)) {
                header("Location: index.php?action=universidades&mensaje=Universidad+eliminada+correctamente");
            } else {
                header("Location: index.php?action=universidades&error=Error+al+eliminar+universidad");
            }
        }
        exit();
    }
}
?>