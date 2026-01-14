<?php
/**
 * Sistema de Autenticación y Control de Acceso
 * Gestiona permisos, sesiones y redirecciones por rol
 */

// Cargar configuración
require_once __DIR__ . '/../config/app_config.php';

// Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verificar si el usuario está autenticado
 * @return bool
 */
function is_authenticated() {
    return isset($_SESSION['Id_Login']) && 
           isset($_SESSION['Id_Rol']) &&
           isset($_SESSION['Username']);
}

/**
 * Requiere que el usuario esté autenticado
 * Redirige al login si no lo está
 */
function require_login() {
    if (!is_authenticated()) {
        security_log('Unauthorized access attempt', [
            'session' => $_SESSION,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? ''
        ]);
        
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect(LOGIN_URL);
    }
    
    // Validar actividad reciente
    if (isset($_SESSION['LAST_ACTIVITY'])) {
        $inactive = time() - $_SESSION['LAST_ACTIVITY'];
        if ($inactive > 3600) { // 1 hora
            session_unset();
            session_destroy();
            session_start();
            
            $_SESSION['login_message'] = 'Sesión expirada por inactividad. Por favor, inicia sesión nuevamente.';
            redirect(LOGIN_URL);
        }
    }
    
    $_SESSION['LAST_ACTIVITY'] = time();
}

/**
 * Control de accesos por rol
 * @param array $roles Array de roles permitidos
 */
function require_role($roles = []) {
    // Primero verificar que esté autenticado
    require_login();
    
    // Verificar que tenga rol asignado
    if (!isset($_SESSION['Id_Rol'])) {
        security_log('User without role trying to access', [
            'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
        ]);
        
        session_unset();
        session_destroy();
        redirect(LOGIN_URL);
    }
    
    // Verificar que su rol esté en la lista de permitidos
    if (!in_array($_SESSION['Id_Rol'], $roles)) {
        security_log('Unauthorized role access attempt', [
            'user_role' => $_SESSION['Id_Rol'],
            'allowed_roles' => $roles,
            'request_uri' => $_SERVER['REQUEST_URI'] ?? ''
        ]);
        
        // Redirigir a su panel correcto
        redirect_by_role($_SESSION['Id_Rol']);
    }
}

/**
 * Obtener información del usuario actual
 * @return array|null Array con datos del usuario o null
 */
function current_user() {
    if (!is_authenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['Id_Login'] ?? null,
        'user_id' => $_SESSION['Id_Login_Usuario'] ?? null,
        'username' => $_SESSION['Username'] ?? null,
        'role_id' => $_SESSION['Id_Rol'] ?? null,
        'nombre' => $_SESSION['Nombre_Usuario'] ?? null
    ];
}

/**
 * Verificar si el usuario tiene un rol específico
 * @param int $roleId ID del rol
 * @return bool
 */
function has_role($roleId) {
    return is_authenticated() && $_SESSION['Id_Rol'] == $roleId;
}

/**
 * Verificar si el usuario tiene alguno de los roles especificados
 * @param array $roles Array de IDs de roles
 * @return bool
 */
function has_any_role($roles) {
    if (!is_authenticated()) {
        return false;
    }
    
    return in_array($_SESSION['Id_Rol'], $roles);
}

/**
 * Regenerar ID de sesión de forma segura
 * Debe llamarse después de login, cambio de contraseña, etc.
 */
function regenerate_session() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Guardar datos importantes
        $data = $_SESSION;
        
        // Regenerar ID
        session_regenerate_id(true);
        
        // Restaurar datos
        $_SESSION = $data;
        
        // Actualizar timestamps
        $_SESSION['CREATED'] = time();
        $_SESSION['LAST_ACTIVITY'] = time();
        
        security_log('Session regenerated', [
            'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
        ]);
    }
}

/**
 * Cerrar sesión de forma segura
 */
function logout() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        security_log('User logged out', [
            'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown',
            'username' => $_SESSION['Username'] ?? 'unknown'
        ]);
        
        // Limpiar todas las variables de sesión
        $_SESSION = [];
        
        // Destruir cookie de sesión
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destruir sesión
        session_destroy();
    }
    
    redirect(LOGIN_URL);
}

/**
 * Establecer mensaje flash en sesión
 * @param string $type Tipo: success, error, warning, info
 * @param string $title Título
 * @param string $message Mensaje
 */
function set_flash($type, $title, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}

/**
 * Obtener y limpiar mensaje flash
 * @return array|null
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
