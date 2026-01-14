<?php
// src/controllers/vendedor/verificar_estado_caja.php
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

// Verificar autenticación
require_role([2, 1]); // Vendedor y Admin

header('Content-Type: application/json; charset=utf-8');

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    $fecha_dia = date('Y-m-d');
    
    // Verificar si hay caja abierta para hoy
    $sql = "
        SELECT 
            Id_Apertura,
            Monto_Inicial,
            DATE_FORMAT(Fecha_Apertura, '%H:%i') as hora_apertura,
            Estado,
            Observaciones
        FROM apertura_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Apertura) = ?
        ORDER BY Fecha_Apertura DESC
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id, $fecha_dia]);
    $caja = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($caja) {
        echo json_encode([
            'success' => true,
            'tiene_caja_abierta' => ($caja['Estado'] === 'ABIERTA'),
            'caja' => $caja
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => true,
            'tiene_caja_abierta' => false,
            'caja' => null
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al verificar estado de caja'
    ], JSON_UNESCAPED_UNICODE);
}
?>