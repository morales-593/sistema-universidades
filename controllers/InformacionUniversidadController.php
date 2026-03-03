<?php
require_once 'models/InformacionUniversidad.php';
require_once 'models/Universidad.php';

class InformacionUniversidadController {

    public function index() {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $infoModel = new InformacionUniversidad();
        $universidadModel = new Universidad();
        
        $informaciones = $infoModel->getAllWithDetails();
        $universidades = $universidadModel->getAllWithRegion();

        // Determinar vista según rol
        if (Session::isAdmin()) {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/admin/informacion/index.php';
            require_once 'views/layouts/footer.php';
        } else {
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/navbar.php';
            require_once 'views/personal/informacion/index.php';
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
            $infoModel = new InformacionUniversidad();
            $id = $_POST['id'] ?? null;
            
            // Validar campos obligatorios
            if (empty($_POST['id_universidad'])) {
                header("Location: index.php?action=informacion&error=Debe+seleccionar+una+universidad");
                exit();
            }
            
            $data = [
                'id_universidad' => $_POST['id_universidad'],
                'tipo_proceso' => trim($_POST['tipo_proceso'] ?? ''),
                'tipo_prueba' => trim($_POST['tipo_prueba'] ?? ''),
                'temas' => trim($_POST['temas'] ?? ''),
                'incidencia' => trim($_POST['incidencia'] ?? ''),
                'registro' => trim($_POST['registro'] ?? ''),
                'inscripciones' => trim($_POST['inscripciones'] ?? ''),
                'examen' => trim($_POST['examen'] ?? ''),
                'postulacion' => trim($_POST['postulacion'] ?? ''),
                'asignacion_cupos' => trim($_POST['asignacion_cupos'] ?? ''),
                'matricula' => trim($_POST['matricula'] ?? '')
            ];

            if ($id) {
                // Actualizar
                $data['actualizado_por'] = Session::get('user_id');
                if ($infoModel->update($id, $data)) {
                    header("Location: index.php?action=informacion&mensaje=Información+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=informacion&error=Error+al+actualizar+información");
                }
            } else {
                // Crear
                $data['creado_por'] = Session::get('user_id');
                if ($infoModel->create($data)) {
                    header("Location: index.php?action=informacion&mensaje=Información+creada+correctamente");
                } else {
                    header("Location: index.php?action=informacion&error=Error+al+crear+información");
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
            $infoModel = new InformacionUniversidad();
            
            if ($infoModel->delete($id)) {
                header("Location: index.php?action=informacion&mensaje=Información+eliminada+correctamente");
            } else {
                header("Location: index.php?action=informacion&error=Error+al+eliminar+información");
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
            $infoModel = new InformacionUniversidad();
            $data = $infoModel->getByUniversidad($id);
            
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }

    public function getUniversidadesSinInfo() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $infoModel = new InformacionUniversidad();
        $universidades = $infoModel->getUniversidadesSinInfo();
        
        header('Content-Type: application/json');
        echo json_encode($universidades);
        exit();
    }
}
?>