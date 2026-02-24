<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../models/Region.php';

// Verificar autenticación
if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$region = new Region();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $data = $region->getById($_GET['id']);
        } else {
            $data = $region->getAll();
        }
        echo json_encode($data);
        break;

    case 'POST':
        // Solo admin puede crear
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if ($region->create($data)) {
            echo json_encode(['success' => true, 'message' => 'Región creada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al crear región']);
        }
        break;

    case 'PUT':
        // Solo admin puede editar
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;
        
        if ($id && $region->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Región actualizada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al actualizar región']);
        }
        break;

    case 'DELETE':
        // Solo admin puede eliminar
        if (!Session::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Acceso denegado']);
            exit();
        }

        $id = $_GET['id'] ?? null;
        
        // Verificar si tiene universidades asociadas
        $count = $region->getUniversidadesCount($id);
        if ($count > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No se puede eliminar la región porque tiene universidades asociadas']);
            exit();
        }

        if ($id && $region->delete($id)) {
            echo json_encode(['success' => true, 'message' => 'Región eliminada correctamente']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Error al eliminar región']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
        break;
}
?>