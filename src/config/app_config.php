<?php
/**
 * Configuración centralizada de la aplicación
 * Define rutas dinámicas, constantes y configuración general
 */

// ==========================================
// CONFIGURACIÓN DE RUTAS BASE
// ==========================================

// Detectar automáticamente la ruta base del proyecto
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];

// Detectar el directorio base del proyecto
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname(dirname(dirname($scriptName))); // Sube 3 niveles desde src/config/

// Si estamos en localhost con subdirectorio
if (strpos($basePath, '/php/') !== false) {
    $basePath = substr($basePath, 0, strpos($basePath, '/php/') + strlen('/php/Inventario_Naye'));
} else {
    $basePath = rtrim($basePath, '/');
}

// Definir constantes globales
define('BASE_URL', $protocol . $host . $basePath);

// Solo definir BASE_PATH si no está definido
if (!defined('BASE_PATH')) {
    define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . $basePath);
}

// Rutas específicas
define('PUBLIC_URL', BASE_URL . '/public_html');
define('LOGIN_URL',  '../login/login.php');

// Rutas por rol
define('ADMIN_URL', BASE_URL . '/model/administrador/php/administrador.php');
define('VENDEDOR_URL', BASE_URL . '/model/vendedor/php/vendedor.php');
define('INVENTARIO_URL', BASE_URL . '/model/inventario/php/inventario.php');

// ==========================================
// CONFIGURACIÓN DE SESIÓN SEGURA
// ==========================================

// Configuración de sesión mejorada
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', ($protocol === 'https://') ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
    
    // Regenerar ID de sesión periódicamente
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } else if (time() - $_SESSION['CREATED'] > 1800) { // 30 minutos
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
    
    // Timeout de sesión
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
        // 1 hora de inactividad
        session_unset();
        session_destroy();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// ==========================================
// CONFIGURACIÓN DE ERRORES
// ==========================================

// Modo de desarrollo vs producción
$isDevelopment = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);

if ($isDevelopment) {
    // Desarrollo: Mostrar errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
} else {
    // Producción: Ocultar errores, solo log
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

define('IS_DEVELOPMENT', $isDevelopment);

// ==========================================
// FUNCIONES HELPER DE RUTAS
// ==========================================

/**
 * Genera URL completa basada en ruta relativa
 * @param string $path Ruta relativa
 * @return string URL completa
 */
function url($path = '') {
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

/**
 * Redirige a una URL
 * @param string $path Ruta a redirigir
 * @param int $code Código HTTP
 */
function redirect($path, $code = 302) {
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        header("Location: $path", true, $code);
    } else {
        header("Location: " . url($path), true, $code);
    }
    exit;
}

/**
 * Redirige según rol del usuario
 * @param int $roleId ID del rol
 */
function redirect_by_role($roleId) {
    switch ((int)$roleId) {
        case 1:
            redirect(ADMIN_URL);
            break;
        case 2:
            redirect(VENDEDOR_URL);
            break;
        case 3:
            redirect(INVENTARIO_URL);
            break;
        default:
            redirect(LOGIN_URL);
    }
}

// ==========================================
// CONFIGURACIÓN DE BASE DE DATOS
// ==========================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'ccgardco_naye');
define('DB_USER', 'ccgardco_Naye');
define('DB_PASS', '}HEh2;BiB]rV');
define('DB_CHARSET', 'utf8mb4');

// ==========================================
// FUNCIÓN DE CONEXIÓN A BASE DE DATOS
// ==========================================

/**
 * Obtiene una conexión MySQLi a la base de datos
 * @return mysqli Objeto de conexión
 * @throws Exception Si hay error de conexión
 */
function get_db_connection() {
    static $conn = null;
    
    // Reutilizar conexión existente si está activa
    if ($conn !== null && $conn->ping()) {
        return $conn;
    }
    
    // Crear nueva conexión usando las constantes definidas
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Verificar errores de conexión
    if ($conn->connect_error) {
        error_log('Error de conexión MySQL: ' . $conn->connect_error);
        throw new Exception('Error de conexión a la base de datos');
    }
    
    // Configurar charset
    if (!$conn->set_charset(DB_CHARSET)) {
        error_log('Error al configurar charset: ' . $conn->error);
    }
    
    return $conn;
}

/**
 * Cierra la conexión a la base de datos (opcional)
 */
function close_db_connection() {
    global $conn;
    if ($conn instanceof mysqli) {
        $conn->close();
        $conn = null;
    }
}

// ==========================================
// CONSTANTES DE LA APLICACIÓN
// ==========================================

define('APP_NAME', 'Chingu Market');
define('APP_VERSION', '1.1.1');
define('APP_TIMEZONE', 'America/Guayaquil');

// Establecer zona horaria
date_default_timezone_set(APP_TIMEZONE);

// ==========================================
// CONFIGURACIÓN DE LOGS
// ==========================================

/**
 * Registrar evento en log
 * @param string $message Mensaje
 * @param string $level Nivel: INFO, WARNING, ERROR, CRITICAL
 * @param array $context Contexto adicional
 */
function app_log($message, $level = 'INFO', $context = []) {
    $logFile = BASE_PATH . '/logs/app.log';
    $logDir = dirname($logFile);
    
    // Crear directorio si no existe
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
    
    $logMessage = sprintf(
        "[%s] [%s] %s %s\n",
        $timestamp,
        $level,
        $message,
        $contextStr
    );
    
    error_log($logMessage, 3, $logFile);
}

/**
 * Registrar error de seguridad
 * @param string $message Mensaje
 * @param array $context Contexto
 */
function security_log($message, $context = []) {
    $logFile = BASE_PATH . '/logs/security.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200);
    
    $context['ip'] = $ip;
    $context['user_agent'] = $ua;
    
    $logMessage = sprintf(
        "[%s] [SECURITY] %s %s\n",
        $timestamp,
        $message,
        json_encode($context, JSON_UNESCAPED_UNICODE)
    );
    
    error_log($logMessage, 3, $logFile);
}

// ==========================================
// INICIALIZACIÓN
// ==========================================

// Registrar inicio de sesión
app_log('Application initialized', 'INFO', [
    'base_url' => BASE_URL,
    'is_dev' => IS_DEVELOPMENT
]);