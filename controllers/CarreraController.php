<?php
require_once 'models/Carrera.php';
require_once 'models/Universidad.php';
require_once 'models/Modalidad.php';

class CarreraController {

    public function index() {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $carrera = new Carrera();
        $universidad = new Universidad();
        $modalidad = new Modalidad();
        
        $carreras = $carrera->getAllWithDetails();
        $universidades = $universidad->getAllWithRegion();
        $modalidades = $modalidad->getAll();

        // Determinar vista según rol
        if (Session::isAdmin()) {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/admin/carreras/index.php';
            require_once 'views/layouts/footer.php';
        } else {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/personal/carreras/index.php';
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
            $carrera = new Carrera();
            $id = $_POST['id'] ?? null;
            
            // Validar campos obligatorios
            if (empty($_POST['nombre'])) {
                header("Location: index.php?action=carreras&error=El+nombre+de+la+carrera+es+obligatorio");
                exit();
            }
            
            if (empty($_POST['id_universidad'])) {
                header("Location: index.php?action=carreras&error=Debe+seleccionar+una+universidad");
                exit();
            }
            
            $data = [
                'nombre' => trim($_POST['nombre']),
                'id_universidad' => $_POST['id_universidad'],
                'titulo_otorgado' => trim($_POST['titulo_otorgado'] ?? ''),
                'duracion' => trim($_POST['duracion'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'perfil_egreso' => trim($_POST['perfil_egreso'] ?? ''),
                'campo_laboral' => trim($_POST['campo_laboral'] ?? '')
            ];

            // Obtener modalidades seleccionadas
            $modalidades = $_POST['modalidades'] ?? [];

            if ($id) {
                // Actualizar
                if ($carrera->update($id, $data)) {
                    // Actualizar modalidades
                    $carrera->asignarModalidades($id, $modalidades);
                    header("Location: index.php?action=carreras&mensaje=Carrera+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=carreras&error=Error+al+actualizar+carrera");
                }
            } else {
                // Crear
                $nuevoId = $carrera->create($data);
                if ($nuevoId) {
                    // Asignar modalidades
                    $carrera->asignarModalidades($nuevoId, $modalidades);
                    header("Location: index.php?action=carreras&mensaje=Carrera+creada+correctamente");
                } else {
                    header("Location: index.php?action=carreras&error=Error+al+crear+carrera");
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
            $carrera = new Carrera();
            
            if ($carrera->delete($id)) {
                header("Location: index.php?action=carreras&mensaje=Carrera+eliminada+correctamente");
            } else {
                header("Location: index.php?action=carreras&error=Error+al+eliminar+carrera");
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
            $carrera = new Carrera();
            $data = $carrera->getByIdWithDetails($id);
            $data['modalidades'] = $carrera->getModalidades($id);
            
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }

    public function getPorUniversidad() {
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $id_universidad = $_GET['id_universidad'] ?? null;
        if ($id_universidad) {
            $carrera = new Carrera();
            $carreras = $carrera->getByUniversidad($id_universidad);
            
            // Agregar modalidades a cada carrera
            foreach ($carreras as &$c) {
                $c['modalidades'] = $carrera->getModalidades($c['id']);
            }
            
            header('Content-Type: application/json');
            echo json_encode($carreras);
        }
        exit();
    }
}
?>