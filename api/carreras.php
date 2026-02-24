<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Carrera.php';
require_once __DIR__ . '/../models/Modalidad.php';

if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$carrera = new Carrera();
$modalidad = new Modalidad();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $data = $carrera->getByIdWithDetails($_GET['id']);
            $data['modalidades'] = $carrera->getModalidades($_GET['id']);
        } elseif (isset($_GET['universidad'])) {
            $data = $carrera->getByUniversidad($_GET['universidad']);
        } else {
            $data = $carrera->getAllWithDetails();
            foreach ($data as &$item) {
                $item['modalidades'] = $carrera->getModalidades($item['id']);
            }
        }
        echo json_encode($data);
        break;

    case 'POST':
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $modalidades = $data['modalidades'] ?? [];
        unset($data['modalidades']);
        
        $id_carrera = $carrera->create($data);
        
        if ($id_carrera) {
            if (!empty($modalidades)) {
                $carrera->asignarModalidades($id_carrera, $modalidades);
            }
            echo json_encode(['success' => true, 'message' => 'Carrera creada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear carrera']);
        }
        break;

    case 'PUT':
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        $modalidades = $data['modalidades'] ?? [];
        unset($data['modalidades']);
        
        if ($id && $carrera->update($id, $data)) {
            if (!empty($modalidades)) {
                $carrera->asignarModalidades($id, $modalidades);
            }
            echo json_encode(['success' => true, 'message' => 'Carrera actualizada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar carrera']);
        }
        break;

    case 'DELETE':
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $id = $_GET['id'] ?? null;
        
        if ($id && $carrera->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Carrera eliminada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar carrera']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>