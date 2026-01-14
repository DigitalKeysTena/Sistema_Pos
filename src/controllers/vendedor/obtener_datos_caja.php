<?php
// src/controllers/vendedor/obtener_datos_caja.php
// VERSIÓN POR DÍA - Muestra todas las aperturas del día actual
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
        SELECT 
            Id_Apertura,
            Monto_Inicial,
            DATE_FORMAT(Fecha_Apertura, '%H:%i') as hora_apertura,
            Fecha_Apertura,
            Estado
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
        throw new Exception('No hay caja abierta para el día de hoy');
    }
    
    $id_apertura_actual = $apertura['Id_Apertura'];
    $estado_caja = $apertura['Estado'];
    
    error_log("INFO - Apertura actual: ID $id_apertura_actual, Estado: $estado_caja");
    
    // ====================================
    // OBTENER MONTO INICIAL DE LA PRIMERA APERTURA DEL DÍA
    // ====================================
    $sql_primera_apertura = "
        SELECT Monto_Inicial
        FROM apertura_caja
        WHERE Id_Usuario = ?
        AND DATE(Fecha_Apertura) = ?
        ORDER BY Fecha_Apertura ASC
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql_primera_apertura);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $primera_apertura = $stmt->fetch(PDO::FETCH_ASSOC);
    $monto_inicial_dia = $primera_apertura ? floatval($primera_apertura['Monto_Inicial']) : 0;
    
    // ====================================
    // OBTENER VENTAS DEL DÍA COMPLETO (todas las aperturas)
    // ====================================
    $sql_ventas = "
        SELECT 
            COUNT(*) as numero_ventas,
            COALESCE(SUM(Total_Venta), 0) as total_ventas,
            COALESCE(SUM(CASE WHEN Metodo_Pago = 'EFECTIVO' THEN Total_Venta ELSE 0 END), 0) as ventas_efectivo,
            COALESCE(SUM(CASE WHEN Metodo_Pago = 'TRANSFERENCIA' THEN Total_Venta ELSE 0 END), 0) as ventas_transferencia
        FROM venta 
        WHERE Id_Usuario_Vendedor = ? 
        AND DATE(Fecha_Venta) = ?
        AND Estado_Venta = 'COMPLETADA'
    ";
    
    $stmt = $pdo->prepare($sql_ventas);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $ventas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // ====================================
    // OBTENER GASTOS DEL DÍA COMPLETO
    // ====================================
    $sql_gastos = "
        SELECT COALESCE(SUM(Monto), 0) as total_gastos
        FROM gastos_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Gasto) = ?
    ";
    
    $stmt = $pdo->prepare($sql_gastos);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $gastos = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // ====================================
    // OBTENER RETIROS DEL DÍA COMPLETO
    // ====================================
    $sql_retiros = "
        SELECT COALESCE(SUM(Monto), 0) as total_retiros
        FROM retiros_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Retiro) = ?
    ";
    
    $stmt = $pdo->prepare($sql_retiros);
    $stmt->execute([$usuario_id, $fecha_actual]);
    $retiros = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // ====================================
    // OBTENER SALDO ACUMULADO
    // ====================================
    $sql_saldo = "
        SELECT Diferencia 
        FROM cierre_caja 
        WHERE Id_Usuario = ? 
        ORDER BY Fecha_Cierre DESC 
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql_saldo);
    $stmt->execute([$usuario_id]);
    $ultimo_cierre = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $saldo_acumulado = $ultimo_cierre ? floatval($ultimo_cierre['Diferencia']) : 0;
    
    // ====================================
    // GUARDAR EN SESIÓN
    // ====================================
    $_SESSION['Id_Apertura_Actual'] = $id_apertura_actual;
    
    // ====================================
    // RESPUESTA
    // ====================================
    $response = [
        'success' => true,
        'estado' => $estado_caja,
        'id_apertura' => $id_apertura_actual,
        'monto_inicial' => $apertura['Monto_Inicial'], // Monto de la apertura actual
        'monto_inicial_dia' => $monto_inicial_dia, // Monto de la primera apertura del día
        'hora_apertura' => $apertura['hora_apertura'],
        'total_ventas' => $ventas['total_ventas'],
        'numero_ventas' => $ventas['numero_ventas'],
        'ventas_efectivo' => $ventas['ventas_efectivo'],
        'ventas_transferencia' => $ventas['ventas_transferencia'],
        'gastos' => $gastos['total_gastos'],
        'retiros' => $retiros['total_retiros'],
        'saldo_acumulado' => $saldo_acumulado
    ];
    
    error_log("INFO - Respuesta: " . json_encode($response));
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("ERROR obtener_datos_caja: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>