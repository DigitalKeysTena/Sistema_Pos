<?php
session_start();

// ⭐ Cargar configuración centralizada
require_once __DIR__ . '/../../src/config/app_config.php';

// Registrar logout en logs
security_log('User logged out', [
    'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown',
    'username' => $_SESSION['Username'] ?? 'unknown',
    'role' => $_SESSION['Id_Rol'] ?? 'unknown'
]);

// Limpiar todas las variables de sesión
$_SESSION = [];

// Destruir cookie de sesión
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destruir sesión
session_destroy();

// ⭐ Redirigir usando sistema centralizado
redirect(LOGIN_URL);
exit;