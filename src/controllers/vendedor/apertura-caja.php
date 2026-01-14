<?php
/**
 * API Bridge: Apertura de Caja
 * Ruta: /public_html/api/vendedor/apertura-caja.php
 * Descripción: Puente público para el controlador de apertura de caja
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Headers CORS y seguridad
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Validar método HTTP
$allowed_methods = ['GET', 'POST', 'OPTIONS'];
if (!in_array($_SERVER['REQUEST_METHOD'], $allowed_methods)) {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit;
}

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Validar origen de la petición
$allowed_origins = [
    'https://chingumarket.online',
    'https://www.chingumarket.online'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
}

// Incluir el controlador protegido
$controller_path = __DIR__ . '/../../../src/controllers/vendedor/AperturaCaja.php';

if (!file_exists($controller_path)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Controlador no encontrado'
    ]);
    exit;
}

// Ejecutar el controlador
require_once $controller_path;
?>
