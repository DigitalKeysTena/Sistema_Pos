<?php
// src/controllers/vendedor/verificar_caja_activa.php
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

// Verificar autenticación
require_role([2, 1]); // Vendedor y Admin

header('Content-Type: application/json; charset=utf-8');

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    $fecha_actual = date('Y-m-d');
    
    // Verificar si hay caja abierta
    $sql = "
        SELECT 
            Id_Apertura,
            Monto_Inicial,
            Estado,
            DATE_FORMAT(Fecha_Apertura, '%H:%i') as hora_apertura,
            DATE_FORMAT(Fecha_Apertura, '%Y-%m-%d') as fecha_apertura
        FROM apertura_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Apertura) = ?
        AND Estado = 'ABIERTA'
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $caja = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($caja) {
        echo json_encode([
            'success' => true,
            'tiene_caja_abierta' => true,
            'caja' => [
                'id' => $caja['Id_Apertura'],
                'monto_inicial' => $caja['Monto_Inicial'],
                'hora_apertura' => $caja['hora_apertura'],
                'fecha_apertura' => $caja['fecha_apertura']
            ],
            'puede_vender' => true
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => true,
            'tiene_caja_abierta' => false,
            'caja' => null,
            'puede_vender' => false,
            'mensaje' => 'Debe abrir caja antes de realizar ventas'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    error_log("Error verificando caja activa: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'tiene_caja_abierta' => false,
        'puede_vender' => false,
        'mensaje' => 'Error al verificar estado de caja'
    ], JSON_UNESCAPED_UNICODE);
}
?>