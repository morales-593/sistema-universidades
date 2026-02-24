<?php
require_once 'models/Usuario.php';

class AuthController {

    public function login() {
        // Si ya está logueado, redirigir al dashboard
        if (Session::isLoggedIn()) {
            header("Location: index.php?action=dashboard");
            exit();
        }

        // Procesar formulario de login
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $usuario = new Usuario();
            $user = $usuario->login($email, $password);
            
            if ($user) {
                Session::set('user_id', $user['id']);
                Session::set('user_nombre', $user['nombre']);
                Session::set('user_email', $user['email']);
                Session::set('user_rol', $user['id_rol']);
                Session::set('user_rol_nombre', $user['rol_nombre']);
                
                header("Location: index.php?action=dashboard&bienvenido=1");
                exit();
            } else {
                header("Location: index.php?action=login&error=1");
                exit();
            }
        }

        // Mostrar formulario de login
        require_once 'views/layouts/header.php';
        require_once 'login.php';
        require_once 'views/layouts/footer.php';
    }

    public function logout() {
        Session::destroy();
        header("Location: index.php?action=login");
        exit();
    }
}
?>