<?php
// src/controllers/vendedor/obtener_saldo_anterior.php
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

require_role([2, 1]); // Vendedor y Admin

header('Content-Type: application/json; charset=utf-8');

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    
    // Obtener el último cierre de caja (día anterior o más reciente)
    $sql_ultimo_cierre = "
        SELECT 
            c.Id_Cierre,
            c.Fecha_Cierre,
            c.Total_Contado,
            c.Total_Esperado,
            c.Diferencia,
            a.Monto_Inicial,
            DATE(c.Fecha_Cierre) as fecha_cierre
        FROM cierre_caja c
        INNER JOIN apertura_caja a ON c.Id_Apertura = a.Id_Apertura
        WHERE c.Id_Usuario = ?
        ORDER BY c.Fecha_Cierre DESC
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql_ultimo_cierre);
    $stmt->execute([$usuario_id]);
    $ultimo_cierre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$ultimo_cierre) {
        echo json_encode([
            'success' => true,
            'tiene_saldo_anterior' => false,
            'saldo_anterior' => 0,
            'mensaje' => 'No hay cierres anteriores'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Calcular el saldo real del día anterior
    // Saldo = Total Contado (lo que quedó físicamente en caja)
    $saldo_anterior = floatval($ultimo_cierre['Total_Contado']);
    
    // Si el total esperado era negativo (déficit), el saldo es negativo
    if (floatval($ultimo_cierre['Total_Esperado']) < 0 && $saldo_anterior == 0) {
        $saldo_anterior = floatval($ultimo_cierre['Total_Esperado']);
    }
    
    echo json_encode([
        'success' => true,
        'tiene_saldo_anterior' => true,
        'saldo_anterior' => $saldo_anterior,
        'fecha_cierre' => $ultimo_cierre['fecha_cierre'],
        'diferencia_anterior' => $ultimo_cierre['Diferencia'],
        'monto_inicial_anterior' => $ultimo_cierre['Monto_Inicial']
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("ERROR obtener_saldo_anterior: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>