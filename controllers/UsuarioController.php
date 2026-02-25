<?php
require_once 'models/Usuario.php';

class UsuarioController {

    public function index() {
        // Solo admin puede acceder
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $usuario = new Usuario();
        $usuarios = $usuario->getWithRol();
        $roles = $usuario->getRoles();

        require_once 'views/layouts/header.php';
        require_once 'views/layouts/navbar.php';
        require_once 'views/admin/usuarios/index.php';
        require_once 'views/layouts/footer.php';
    }

    public function guardar() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario = new Usuario();
            $id = $_POST['id'] ?? null;
            
            $data = [
                'nombre' => $_POST['nombre'],
                'email' => $_POST['email']
            ];

            if ($id) {
                // Actualizar - solo nombre y email
                if ($usuario->update($id, $data)) {
                    header("Location: index.php?action=usuarios&mensaje=Usuario+actualizado+correctamente");
                } else {
                    header("Location: index.php?action=usuarios&error=Error+al+actualizar+usuario");
                }
            } else {
                // Crear nuevo usuario
                $data['password'] = $_POST['password'];
                $data['id_rol'] = $_POST['id_rol'];
                $data['activo'] = isset($_POST['activo']) ? 1 : 0;
                
                if ($usuario->create($data)) {
                    header("Location: index.php?action=usuarios&mensaje=Usuario+creado+correctamente");
                } else {
                    header("Location: index.php?action=usuarios&error=Error+al+crear+usuario");
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
        
        // No permitir eliminar al propio usuario
        if ($id == Session::get('user_id')) {
            header("Location: index.php?action=usuarios&error=No+puedes+eliminar+tu+propio+usuario");
            exit();
        }

        if ($id) {
            $usuario = new Usuario();
            if ($usuario->delete($id)) {
                header("Location: index.php?action=usuarios&mensaje=Usuario+eliminado+correctamente");
            } else {
                header("Location: index.php?action=usuarios&error=Error+al+eliminar+usuario");
            }
        }
        exit();
    }

    public function cambiarEstado() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        $estado = $_GET['estado'] ?? 0;

        if ($id && $id != Session::get('user_id')) {
            $usuario = new Usuario();
            // Solo actualizar el estado
            $data = ['activo' => $estado];
            if ($usuario->update($id, $data)) {
                $mensaje = $estado ? 'Usuario activado correctamente' : 'Usuario desactivado correctamente';
                header("Location: index.php?action=usuarios&mensaje=" . urlencode($mensaje));
            } else {
                header("Location: index.php?action=usuarios&error=Error+al+cambiar+estado");
            }
        } else {
            header("Location: index.php?action=usuarios&error=No+puedes+cambiar+tu+propio+estado");
        }
        exit();
    }

    public function resetPassword() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuario = new Usuario();
            // Generar contraseña temporal
            $tempPassword = $this->generateTempPassword();
            if ($usuario->updatePassword($id, $tempPassword)) {
                header("Location: index.php?action=usuarios&mensaje=" . urlencode("Contraseña reseteada correctamente. Nueva contraseña: $tempPassword"));
            } else {
                header("Location: index.php?action=usuarios&error=Error+al+resetear+contraseña");
            }
        }
        exit();
    }

    public function ver() {
        if (!Session::isAdmin()) {
            header("Location: index.php?action=acceso-denegado");
            exit();
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $usuario = new Usuario();
            $data = $usuario->getById($id);
            header('Content-Type: application/json');
            echo json_encode($data);
        }
        exit();
    }

    private function generateTempPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
?>