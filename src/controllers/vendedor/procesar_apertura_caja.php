<?php
// src/controllers/vendedor/procesar_apertura_caja.php
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

// Verificar autenticación
require_role([2, 1]); // Vendedor y Admin

header('Content-Type: application/json; charset=utf-8');

// Obtener datos del POST
$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos inválidos'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    $monto_inicial = floatval($datos['monto_inicial'] ?? 0);
    $observaciones = trim($datos['observaciones'] ?? '');
    $fecha_actual = date('Y-m-d H:i:s');
    $fecha_dia = date('Y-m-d');
    
    // Validar monto
    if ($monto_inicial < 0) {
        throw new Exception('El monto inicial no puede ser negativo');
    }
    
    // Verificar si ya hay caja abierta para hoy
    $sql_verificar = "
        SELECT Id_Apertura 
        FROM apertura_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Apertura) = ?
        AND Estado = 'ABIERTA'
    ";
    
    $stmt = $pdo->prepare($sql_verificar);
    $stmt->execute([$usuario_id, $fecha_dia]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existe) {
        throw new Exception('Ya existe una caja abierta para hoy. Debe cerrar la caja anterior antes de abrir una nueva.');
    }
    
    // Insertar apertura de caja
    $sql_insertar = "
        INSERT INTO apertura_caja (
            Id_Usuario,
            Fecha_Apertura,
            Monto_Inicial,
            Estado,
            Observaciones
        ) VALUES (?, ?, ?, 'ABIERTA', ?)
    ";
    
    $stmt = $pdo->prepare($sql_insertar);
    $stmt->execute([
        $usuario_id,
        $fecha_actual,
        $monto_inicial,
        $observaciones
    ]);
    
    $apertura_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Caja abierta correctamente',
        'apertura_id' => $apertura_id,
        'monto_inicial' => number_format($monto_inicial, 2, '.', '')
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>