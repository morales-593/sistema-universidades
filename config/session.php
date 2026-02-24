<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class Session {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function destroy() {
        session_destroy();
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin() {
        return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 1;
    }

    public static function isPersonal() {
        return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] == 2;
    }

    public static function checkAccess($requiredRole = null) {
        if (!self::isLoggedIn()) {
            header("Location: " . BASE_URL . "/index.php?action=login");
            exit();
        }

        if ($requiredRole == 'admin' && !self::isAdmin()) {
            header("Location: " . BASE_URL . "/index.php?action=acceso-denegado");
            exit();
        }
    }
}

Session::init();

// Definir URL base
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $host . rtrim($scriptName, '/\\'));
?>