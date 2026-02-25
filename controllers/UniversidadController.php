<?php
require_once 'models/Universidad.php';
require_once 'models/Region.php';

class UniversidadController {

    public function index() {
        // Verificar acceso
        if (!Session::isLoggedIn()) {
            header("Location: index.php?action=login");
            exit();
        }

        $universidad = new Universidad();
        $region = new Region();
        
        $universidades = $universidad->getAllWithRegion();
        $regiones = $region->getAll();

        // Determinar vista según rol
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
        // Solo admin puede guardar
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $universidad = new Universidad();
            $id = $_POST['id'] ?? null;
            
            // Validar campos obligatorios
            if (empty($_POST['nombre'])) {
                header("Location: index.php?action=universidades&error=El+nombre+de+la+universidad+es+obligatorio");
                exit();
            }
            
            if (empty($_POST['id_region'])) {
                header("Location: index.php?action=universidades&error=Debe+seleccionar+una+región");
                exit();
            }
            
            // Manejar subida de logo
            $logo = null;
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $filename = $_FILES['logo']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($ext, $allowed)) {
                    $target_dir = "assets/uploads/logos/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $logo = time() . '_' . uniqid() . '.' . $ext;
                    $target_file = $target_dir . $logo;
                    
                    if (!move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                        $logo = null;
                    }
                }
            }

            $data = [
                'nombre' => trim($_POST['nombre']),
                'id_region' => $_POST['id_region'],
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'link_plataforma' => trim($_POST['link_plataforma'] ?? ''),
                'direccion' => trim($_POST['direccion'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'logo' => $logo
            ];

            if ($id) {
                // Actualizar - si no se subió nuevo logo, mantener el anterior
                if (!$logo) {
                    unset($data['logo']);
                }
                
                if ($universidad->update($id, $data)) {
                    header("Location: index.php?action=universidades&mensaje=Universidad+actualizada+correctamente");
                } else {
                    header("Location: index.php?action=universidades&error=Error+al+actualizar+universidad");
                }
            } else {
                // Crear
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
        // Solo admin puede eliminar
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
                header("Location: index.php?action=universidades&error=No+se+puede+eliminar+la+universidad+porque+tiene+$count+carreras+asociadas");
                exit();
            }

            // Obtener información para eliminar logo
            $data = $universidad->getById($id);
            
            if ($universidad->delete($id)) {
                // Eliminar logo si existe
                if ($data && $data['logo']) {
                    $logo_path = "assets/uploads/logos/" . $data['logo'];
                    if (file_exists($logo_path)) {
                        unlink($logo_path);
                    }
                }
                header("Location: index.php?action=universidades&mensaje=Universidad+eliminada+correctamente");
            } else {
                header("Location: index.php?action=universidades&error=Error+al+eliminar+universidad");
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
            $universidad = new Universidad();
            $data = $universidad->getByIdWithDetails($id);
            $data['carreras_count'] = $universidad->getCarrerasCount($id);
            
            // Obtener carreras de esta universidad
            require_once 'models/Carrera.php';
            $carrera = new Carrera();
            $carreras = $carrera->getByUniversidad($id);
            
            // Agregar modalidades a cada carrera
            foreach ($carreras as &$c) {
                $c['modalidades'] = $carrera->getModalidades($c['id']);
            }
            $data['carreras'] = $carreras;
            
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }
}
?>