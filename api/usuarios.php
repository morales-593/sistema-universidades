<?php
header('Content-Type: application/json');
require_once '../config/session.php';
require_once '../models/Usuario.php';

if (!Session::isLoggedIn() || !Session::isAdmin()) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$usuario = new Usuario();

if (isset($_GET['id'])) {
    $data = $usuario->getById($_GET['id']);
    if ($data) {
        // No enviar la contraseña
        unset($data['password']);
        echo json_encode($data);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Usuario no encontrado']);
    }
} else {
    $data = $usuario->getWithRol();
    echo json_encode($data);
}
?>