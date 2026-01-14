<?php
// src/controllers/vendedor/procesar_cierre_caja.php
// VERSIÓN MEJORADA: Con dos opciones de cierre
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

require_role([2, 1]);

header('Content-Type: application/json; charset=utf-8');

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    
    if (!$datos) {
        throw new Exception('Datos inválidos');
    }
    
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    $fecha_actual = date('Y-m-d H:i:s');
    $fecha_dia = date('Y-m-d');
    
    // ====================================
    // EXTRAER OPCIÓN DE CIERRE
    // ====================================
    // tipo_cierre: 'DEPOSITO' o 'CONTINUACION'
    $tipo_cierre = trim($datos['tipo_cierre'] ?? 'DEPOSITO');
    
    if (!in_array($tipo_cierre, ['DEPOSITO', 'CONTINUACION'])) {
        throw new Exception('Tipo de cierre inválido');
    }
    
    // ====================================
    // BUSCAR APERTURA DEL DÍA
    // ====================================
    $sql_verificar = "
        SELECT Id_Apertura, Monto_Inicial, Estado, Fecha_Apertura
        FROM apertura_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Apertura) = ?
        ORDER BY Fecha_Apertura DESC 
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql_verificar);
    $stmt->execute([$usuario_id, $fecha_dia]);
    $apertura = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$apertura) {
        throw new Exception('No hay apertura de caja para hoy');
    }
    
    if ($apertura['Estado'] !== 'ABIERTA') {
        throw new Exception('La caja ya fue cerrada previamente');
    }
    
    $id_apertura = $apertura['Id_Apertura'];
    $monto_inicial = floatval($apertura['Monto_Inicial']);
    $fecha_apertura = $apertura['Fecha_Apertura'];
    
    // ====================================
    // OBTENER VENTAS DEL DÍA
    // ====================================
    $sql_ventas = "
        SELECT 
            COUNT(*) as numero_ventas,
            COALESCE(SUM(Total_Venta), 0) as total_ventas,
            COALESCE(SUM(CASE WHEN Metodo_Pago = 'EFECTIVO' THEN Total_Venta ELSE 0 END), 0) as total_efectivo,
            COALESCE(SUM(CASE WHEN Metodo_Pago = 'TRANSFERENCIA' THEN Total_Venta ELSE 0 END), 0) as total_transferencias,
            COALESCE(SUM(CASE WHEN Metodo_Pago = 'TARJETA' THEN Total_Venta ELSE 0 END), 0) as total_tarjetas
        FROM venta 
        WHERE Id_Usuario_Vendedor = ? 
        AND DATE(Fecha_Venta) = DATE(?)
        AND Estado_Venta = 'COMPLETADA'
    ";
    
    $stmt = $pdo->prepare($sql_ventas);
    $stmt->execute([$usuario_id, $fecha_apertura]);
    $ventas_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $numero_ventas = intval($ventas_data['numero_ventas']);
    $total_ventas = floatval($ventas_data['total_ventas']);
    $total_efectivo = floatval($ventas_data['total_efectivo']);
    $total_transferencias = floatval($ventas_data['total_transferencias']);
    $total_tarjetas = floatval($ventas_data['total_tarjetas']);
    
    // ====================================
    // OBTENER GASTOS Y RETIROS DEL DÍA
    // ====================================
    $sql_gastos = "
        SELECT COALESCE(SUM(Monto), 0) as total_gastos
        FROM gastos_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Gasto) = DATE(?)
    ";
    
    $stmt = $pdo->prepare($sql_gastos);
    $stmt->execute([$usuario_id, $fecha_apertura]);
    $gastos_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $gastos = floatval($gastos_data['total_gastos']);
    
    $sql_retiros = "
        SELECT COALESCE(SUM(Monto), 0) as total_retiros
        FROM retiros_caja 
        WHERE Id_Usuario = ? 
        AND DATE(Fecha_Retiro) = DATE(?)
    ";
    
    $stmt = $pdo->prepare($sql_retiros);
    $stmt->execute([$usuario_id, $fecha_apertura]);
    $retiros_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $retiros = floatval($retiros_data['total_retiros']);
    
    // ====================================
    // EXTRAER DATOS DEL FRONTEND
    // ====================================
    $total_contado = floatval($datos['totalContado']);
    $observaciones = trim($datos['observaciones'] ?? '');
    
    // ====================================
    // CALCULAR TOTAL ESPERADO CORRECTO
    // ====================================
    $total_esperado = $monto_inicial + $total_efectivo - $gastos - $retiros;
    $diferencia = $total_contado - $total_esperado;
    
    error_log("DEBUG CIERRE - Monto Inicial: $monto_inicial");
    error_log("DEBUG CIERRE - Efectivo Ventas: $total_efectivo");
    error_log("DEBUG CIERRE - Gastos: $gastos");
    error_log("DEBUG CIERRE - Retiros: $retiros");
    error_log("DEBUG CIERRE - Total Esperado: $total_esperado");
    error_log("DEBUG CIERRE - Total Contado: $total_contado");
    error_log("DEBUG CIERRE - Diferencia: $diferencia");
    error_log("DEBUG CIERRE - Tipo Cierre: $tipo_cierre");
    
    $tipo_diferencia = 'EXACTO';
    if (abs($diferencia) >= 0.01) {
        $tipo_diferencia = ($diferencia > 0) ? 'SOBRANTE' : 'FALTANTE';
    }
    
    // ====================================
    // INICIAR TRANSACCIÓN
    // ====================================
    $pdo->beginTransaction();
    
    try {
        // ====================================
        // ACTUALIZAR ESTADO DE APERTURA
        // ====================================
        $sql_cerrar = "
            UPDATE apertura_caja 
            SET Estado = 'CERRADA', 
                Fecha_Cierre = ? 
            WHERE Id_Apertura = ?
        ";
        
        $stmt = $pdo->prepare($sql_cerrar);
        $stmt->execute([$fecha_actual, $id_apertura]);
        
        // ====================================
        // INSERTAR CIERRE
        // ====================================
        $sql_cierre = "
            INSERT INTO cierre_caja (
                Id_Apertura, 
                Id_Usuario, 
                Fecha_Cierre, 
                Monto_Inicial, 
                Total_Ventas, 
                Numero_Ventas,
                Total_Efectivo, 
                Total_Tarjetas, 
                Total_Transferencias, 
                Gastos, 
                Retiros,
                Total_Esperado, 
                Total_Contado, 
                Diferencia, 
                Tipo_Cierre,
                Observaciones, 
                Estado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'CERRADA')
        ";
        
        $stmt = $pdo->prepare($sql_cierre);
        $stmt->execute([
            $id_apertura,
            $usuario_id,
            $fecha_actual,
            $monto_inicial,
            $total_ventas,
            $numero_ventas,
            $total_efectivo,
            $total_tarjetas,
            $total_transferencias,
            $gastos,
            $retiros,
            $total_esperado,
            $total_contado,
            $diferencia,
            $tipo_cierre,
            $observaciones
        ]);
        
        $cierre_id = $pdo->lastInsertId();
        
        // ====================================
        // REGISTRAR DIFERENCIA SI EXISTE
        // ====================================
        if (abs($diferencia) >= 0.01) {
            $sql_dif = "
                INSERT INTO diferencias_caja (
                    Id_Cierre, 
                    Id_Usuario, 
                    Fecha_Diferencia, 
                    Tipo_Diferencia, 
                    Monto_Diferencia, 
                    Observaciones
                ) VALUES (?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $pdo->prepare($sql_dif);
            $stmt->execute([
                $cierre_id,
                $usuario_id,
                $fecha_actual,
                $tipo_diferencia,
                abs($diferencia),
                $observaciones
            ]);
        }
        
        // ====================================
        // ACTUALIZAR SALDO ACUMULADO
        // ====================================
        $sql_actualizar_saldo = "
            INSERT INTO saldo_acumulado_vendedor (
                Id_Usuario, 
                Saldo_Actual, 
                Fecha_Actualizacion
            ) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                Saldo_Actual = ?,
                Fecha_Actualizacion = ?
        ";
        
        $stmt = $pdo->prepare($sql_actualizar_saldo);
        $stmt->execute([
            $usuario_id,
            $diferencia,
            $fecha_actual,
            $diferencia,
            $fecha_actual
        ]);
        
        // ====================================
        // SI ES CONTINUACIÓN, CREAR NUEVA APERTURA
        // ====================================
        if ($tipo_cierre === 'CONTINUACION') {
            // El monto inicial de la nueva apertura es el total contado
            $nuevo_monto_inicial = $total_contado;
            
            $sql_nueva_apertura = "
                INSERT INTO apertura_caja (
                    Id_Usuario,
                    Fecha_Apertura,
                    Monto_Inicial,
                    Estado,
                    Observaciones
                ) VALUES (?, ?, ?, 'ABIERTA', 'Continuación automática del cierre anterior')
            ";
            
            $stmt = $pdo->prepare($sql_nueva_apertura);
            $stmt->execute([
                $usuario_id,
                $fecha_actual,
                $nuevo_monto_inicial
            ]);
            
            $nueva_apertura_id = $pdo->lastInsertId();
            
            error_log("CONTINUACIÓN - Nueva apertura creada ID: $nueva_apertura_id con monto: $nuevo_monto_inicial");
        }
        
        // Confirmar transacción
        $pdo->commit();
        
        error_log("SUCCESS - Cierre registrado correctamente. ID: $cierre_id, Tipo: $tipo_cierre");
        
        // ====================================
        // RESPUESTA EXITOSA
        // ====================================
        echo json_encode([
            'success' => true,
            'message' => 'Cierre registrado correctamente',
            'cierre_id' => $cierre_id,
            'tipo_cierre' => $tipo_cierre,
            'nueva_apertura' => ($tipo_cierre === 'CONTINUACION'),
            'resumen' => [
                'monto_inicial' => number_format($monto_inicial, 2, '.', ''),
                'total_ventas' => number_format($total_ventas, 2, '.', ''),
                'numero_ventas' => $numero_ventas,
                'total_efectivo' => number_format($total_efectivo, 2, '.', ''),
                'total_tarjetas' => number_format($total_tarjetas, 2, '.', ''),
                'total_transferencias' => number_format($total_transferencias, 2, '.', ''),
                'gastos' => number_format($gastos, 2, '.', ''),
                'retiros' => number_format($retiros, 2, '.', ''),
                'total_esperado' => number_format($total_esperado, 2, '.', ''),
                'total_contado' => number_format($total_contado, 2, '.', ''),
                'diferencia' => number_format($diferencia, 2, '.', ''),
                'tipo_diferencia' => $tipo_diferencia
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("ERROR EN TRANSACCIÓN - " . $e->getMessage());
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("ERROR GENERAL - " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'linea' => $e->getLine(),
        'archivo' => basename($e->getFile())
    ], JSON_UNESCAPED_UNICODE);
}
?>