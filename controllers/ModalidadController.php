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
        
        // Obtener todas las modalidades
        $modalidades = $modalidad->getAll();
        
        // Obtener datos adicionales para cada modalidad - SIN usar referencia
        $modalidadesConDatos = [];
        foreach ($modalidades as $m) {
            $m['carreras_count'] = $modalidad->getCarrerasCount($m['id']);
            $m['carreras'] = $modalidad->getCarrerasWithModalidad($m['id']);
            $modalidadesConDatos[] = $m;
        }

        // Asignar el array procesado
        $modalidades = $modalidadesConDatos;

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
            $id = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : null;
            
            // Validar campos obligatorios
            if (empty($_POST['nombre'])) {
                header("Location: index.php?action=modalidades&error=El+nombre+de+la+modalidad+es+obligatorio");
                exit();
            }
            
            // Validar longitud del nombre
            if (strlen(trim($_POST['nombre'])) < 3) {
                header("Location: index.php?action=modalidades&error=El+nombre+debe+tener+al+menos+3+caracteres");
                exit();
            }
            
            if (strlen(trim($_POST['nombre'])) > 100) {
                header("Location: index.php?action=modalidades&error=El+nombre+no+puede+exceder+los+100+caracteres");
                exit();
            }
            
            $data = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'] ?? '')
            ];

            if ($id && is_numeric($id)) {
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
        
        // Validar que el ID existe y es numérico
        if ($id && is_numeric($id)) {
            $modalidad = new Modalidad();
            
            // Verificar si la modalidad existe
            $modalidadData = $modalidad->getById($id);
            if (!$modalidadData) {
                header("Location: index.php?action=modalidades&error=La+modalidad+no+existe");
                exit();
            }
            
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
        } else {
            header("Location: index.php?action=modalidades&error=ID+de+modalidad+inválido");
        }
        exit();
    }

    public function ver() {
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $id = $_GET['id'] ?? null;
        
        if ($id && is_numeric($id)) {
            $modalidad = new Modalidad();
            $data = $modalidad->getById($id);
            
            if ($data) {
                $data['carreras_count'] = $modalidad->getCarrerasCount($id);
                $data['carreras'] = $modalidad->getCarrerasWithModalidad($id);
                
                header('Content-Type: application/json');
                echo json_encode($data);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Modalidad no encontrada']);
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ID inválido']);
        }
        exit();
    }
}
?>