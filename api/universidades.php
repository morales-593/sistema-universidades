<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Universidad.php';

if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$universidad = new Universidad();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $data = $universidad->getByIdWithDetails($_GET['id']);
            $data['carreras_count'] = $universidad->getCarrerasCount($_GET['id']);
        } elseif (isset($_GET['region'])) {
            $data = $universidad->getByRegion($_GET['region']);
        } else {
            $data = $universidad->getAllWithRegion();
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
        
        // Manejar subida de logo
        if (isset($_FILES['logo'])) {
            $target_dir = "../assets/uploads/logos/";
            $file_name = time() . '_' . basename($_FILES["logo"]["name"]);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $data['logo'] = $file_name;
            }
        }

        if ($universidad->create($data)) {
            echo json_encode(['success' => true, 'message' => 'Universidad creada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear universidad']);
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
        
        if ($id && $universidad->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Universidad actualizada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar universidad']);
        }
        break;

    case 'DELETE':
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $id = $_GET['id'] ?? null;
        
        // Verificar si tiene carreras asociadas
        $count = $universidad->getCarrerasCount($id);
        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar la universidad porque tiene carreras asociadas']);
            exit();
        }

        if ($id && $universidad->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Universidad eliminada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar universidad']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>