<?php
require_once 'models/Modalidad.php';
require_once 'models/Carrera.php';

class ModalidadController {

    public function index() {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $modalidad = new Modalidad();
        $carrera = new Carrera();
        
        $modalidades = $modalidad->getAll();
        
        // Obtener carreras para cada modalidad
        foreach ($modalidades as &$m) {
            $m['carreras_count'] = $modalidad->getCarrerasCount($m['id']);
            $m['carreras'] = $modalidad->getCarrerasWithModalidad($m['id']);
        }

        // Determinar vista según rol
        if (Session::isAdmin()) {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/admin/modalidades/index.php';
            require_once 'views/layouts/footer.php';
        } else {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/personal/modalidades/index.php';
            require_once 'views/layouts/footer.php';
        }
    }

    public function guardar() {
        // Solo admin puede guardar
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $modalidad = new Modalidad();
            $id = $_POST['id'] ?? null;
            
            // Validar campos obligatorios
            if (empty($_POST['nombre'])) {
                header("Location: index.php?action=modalidades&error=El+nombre+de+la+modalidad+es+obligatorio");
                exit();
            }
            
            $data = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            if ($id) {
                // Actualizar
                if ($modalidad->update($id, $data)) {
                    header("Location: index.php?action=modalidades&mensaje=Modalidad+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=modalidades&error=Error+al+actualizar+modalidad");
                }
            } else {
                // Crear
                if ($modalidad->create($data)) {
                    header("Location: index.php?action=modalidades&mensaje=Modalidad+creada+correctamente");
                } else {
                    header("Location: index.php?action=modalidades&error=Error+al+crear+modalidad");
                }
            }
        }
        exit();
    }

    public function eliminar() {
        // Solo admin puede eliminar
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $modalidad = new Modalidad();
            
            // Verificar si tiene carreras asociadas
            $count = $modalidad->getCarrerasCount($id);
            if ($count > 0) {
                header("Location: index.php?action=modalidades&error=No+se+puede+eliminar+la+modalidad+porque+tiene+$count+carreras+asociadas");
                exit();
            }

            if ($modalidad->delete($id)) {
                header("Location: index.php?action=modalidades&mensaje=Modalidad+eliminada+correctamente");
            } else {
                header("Location: index.php?action=modalidades&error=Error+al+eliminar+modalidad");
            }
        }
        exit();
    }

    public function ver() {
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $modalidad = new Modalidad();
            $data = $modalidad->getById($id);
            $data['carreras_count'] = $modalidad->getCarrerasCount($id);
            $data['carreras'] = $modalidad->getCarrerasWithModalidad($id);
            
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }
}
?>