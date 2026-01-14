<?php
// src/controllers/vendedor/obtener_retiros.php
// VERSIÓN CORREGIDA - Busca retiros asociados a la apertura actual
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

require_role([2, 1]);

header('Content-Type: application/json; charset=utf-8');

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    $fecha_actual = date('Y-m-d');
    
    // ====================================
    // BUSCAR APERTURA ABIERTA HOY
    // ====================================
    $sql_apertura = "
        SELECT Id_Apertura, Fecha_Apertura
        FROM apertura_caja
        WHERE Id_Usuario = ?
        AND DATE(Fecha_Apertura) = ?
        AND Estado = 'ABIERTA'
        ORDER BY Fecha_Apertura DESC
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql_apertura);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $apertura = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$apertura) {
        echo json_encode([
            'success' => true,
            'retiros' => [],
            'total' => '0.00'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $id_apertura = $apertura['Id_Apertura'];
    
    // ====================================
    // OBTENER RETIROS DE LA APERTURA ACTUAL
    // Busca por Id_Apertura_Caja O por fecha si no tiene Id_Apertura_Caja
    // ====================================
    $sql_retiros = "
        SELECT 
            Id_Retiro,
            Monto,
            Motivo,
            Observaciones,
            DATE_FORMAT(Fecha_Retiro, '%H:%i') as hora
        FROM retiros_caja
        WHERE Id_Usuario = ?
        AND (
            Id_Apertura_Caja = ?
            OR (Id_Apertura_Caja IS NULL AND DATE(Fecha_Retiro) = ?)
        )
        ORDER BY Fecha_Retiro DESC
    ";
    
    $stmt = $pdo->prepare($sql_retiros);
    $stmt->execute([$usuario_id, $id_apertura, $fecha_actual]);
    $retiros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular total
    $total = 0;
    foreach ($retiros as $retiro) {
        $total += floatval($retiro['Monto']);
    }
    
    error_log("RETIROS - Apertura: $id_apertura, Total encontrados: " . count($retiros) . ", Total: $total");
    
    echo json_encode([
        'success' => true,
        'retiros' => $retiros,
        'total' => number_format($total, 2, '.', ''),
        'id_apertura' => $id_apertura
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("ERROR obtener_retiros: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener retiros'
    ], JSON_UNESCAPED_UNICODE);
}
?>
