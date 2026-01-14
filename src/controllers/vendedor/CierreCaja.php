<?php
/**
 * Controlador: Cierre de Caja
 * Ruta: /src/controllers/vendedor/CierreCaja.php
 * Descripción: Gestiona el cierre de caja con cálculos corregidos
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/security.php';

// Iniciar sesión segura
iniciarSesionSegura();

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
    exit;
}

header('Content-Type: application/json');

// Conectar a la base de datos
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del POST
        $data = json_decode(file_get_contents('php://input'), true);
        
        $usuario_id = $_SESSION['usuario_id'];
        
        // Validaciones
        if (!isset($data['desglose']) || !is_array($data['desglose'])) {
            throw new Exception('El desglose de denominaciones es requerido');
        }
        
        $observaciones = isset($data['observaciones']) ? trim($data['observaciones']) : '';
        
        // ====================================
        // VERIFICAR QUE EXISTE UNA CAJA ABIERTA
        // ====================================
        
        $query_apertura = "SELECT Id_Apertura, Monto_Inicial, Fecha_Apertura, Estado
                          FROM apertura_caja 
                          WHERE Id_Usuario = :usuario_id 
                          AND Estado = 'ABIERTA' 
                          ORDER BY Fecha_Apertura DESC 
                          LIMIT 1";
        
        $stmt_apertura = $conn->prepare($query_apertura);
        $stmt_apertura->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_apertura->execute();
        
        if ($stmt_apertura->rowCount() === 0) {
            throw new Exception('No hay una caja abierta para cerrar');
        }
        
        $apertura = $stmt_apertura->fetch(PDO::FETCH_ASSOC);
        $id_apertura = $apertura['Id_Apertura'];
        $monto_inicial = floatval($apertura['Monto_Inicial']);
        
        // ====================================
        // CALCULAR TOTALES DE VENTAS
        // ====================================
        
        $query_ventas = "SELECT 
                            COUNT(*) as numero_ventas,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Efectivo' THEN Total_Venta ELSE 0 END), 0) as total_efectivo,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Tarjeta' THEN Total_Venta ELSE 0 END), 0) as total_tarjetas,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Transferencia' THEN Total_Venta ELSE 0 END), 0) as total_transferencias,
                            COALESCE(SUM(Total_Venta), 0) as total_ventas
                        FROM venta 
                        WHERE Id_Usuario_Venta = :usuario_id 
                        AND DATE(Fecha_Venta) = DATE(:fecha_apertura)";
        
        $stmt_ventas = $conn->prepare($query_ventas);
        $stmt_ventas->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_ventas->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_ventas->execute();
        
        $ventas_data = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
        
        $numero_ventas = intval($ventas_data['numero_ventas']);
        $total_ventas = floatval($ventas_data['total_ventas']);
        $total_efectivo = floatval($ventas_data['total_efectivo']);
        $total_tarjetas = floatval($ventas_data['total_tarjetas']);
        $total_transferencias = floatval($ventas_data['total_transferencias']);
        
        // ====================================
        // CALCULAR GASTOS Y RETIROS
        // ====================================
        
        // Gastos del día
        $query_gastos = "SELECT COALESCE(SUM(Monto), 0) as total_gastos 
                        FROM gastos_caja 
                        WHERE Id_Usuario = :usuario_id 
                        AND DATE(Fecha_Gasto) = DATE(:fecha_apertura)";
        
        $stmt_gastos = $conn->prepare($query_gastos);
        $stmt_gastos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_gastos->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_gastos->execute();
        
        $gastos_data = $stmt_gastos->fetch(PDO::FETCH_ASSOC);
        $total_gastos = floatval($gastos_data['total_gastos']);
        
        // Retiros del día
        $query_retiros = "SELECT COALESCE(SUM(Monto), 0) as total_retiros 
                         FROM retiros_caja 
                         WHERE Id_Usuario = :usuario_id 
                         AND DATE(Fecha_Retiro) = DATE(:fecha_apertura)";
        
        $stmt_retiros = $conn->prepare($query_retiros);
        $stmt_retiros->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_retiros->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_retiros->execute();
        
        $retiros_data = $stmt_retiros->fetch(PDO::FETCH_ASSOC);
        $total_retiros = floatval($retiros_data['total_retiros']);
        
        // ====================================
        // CALCULAR EL DESGLOSE (TOTAL CONTADO)
        // ====================================
        
        $total_contado = 0.00;
        foreach ($data['desglose'] as $item) {
            if (isset($item['cantidad']) && isset($item['denominacion'])) {
                $cantidad = intval($item['cantidad']);
                $denominacion = floatval($item['denominacion']);
                $total_contado += ($cantidad * $denominacion);
            }
        }
        
        // ====================================
        // CÁLCULO CORRECTO DEL TOTAL ESPERADO
        // ====================================
        
        // FÓRMULA CORRECTA:
        // Total Esperado en Efectivo = Monto Inicial + Ventas en Efectivo - Gastos - Retiros
        //
        // Explicación:
        // - Comenzamos con el Monto Inicial
        // - Sumamos SOLO las ventas en EFECTIVO (no tarjetas ni transferencias)
        // - Restamos los Gastos (dinero que salió)
        // - Restamos los Retiros (dinero que salió)
        
        $total_esperado = $monto_inicial + $total_efectivo - $total_gastos - $total_retiros;
        
        // ====================================
        // CALCULAR LA DIFERENCIA
        // ====================================
        
        // Diferencia = Total Contado - Total Esperado
        // - Si es positivo: SOBRANTE
        // - Si es negativo: FALTANTE
        
        $diferencia = $total_contado - $total_esperado;
        
        // ====================================
        // INICIAR TRANSACCIÓN
        // ====================================
        
        $conn->beginTransaction();
        
        // Actualizar el estado de la apertura
        $query_update_apertura = "UPDATE apertura_caja 
                                 SET Estado = 'CERRADA', 
                                     Fecha_Cierre = NOW() 
                                 WHERE Id_Apertura = :id_apertura";
        
        $stmt_update_apertura = $conn->prepare($query_update_apertura);
        $stmt_update_apertura->bindParam(':id_apertura', $id_apertura, PDO::PARAM_INT);
        
        if (!$stmt_update_apertura->execute()) {
            throw new Exception('Error al actualizar el estado de apertura');
        }
        
        // Insertar el registro de cierre
        $query_cierre = "INSERT INTO cierre_caja (
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
                            Observaciones,
                            Estado
                        ) VALUES (
                            :id_apertura,
                            :usuario_id,
                            NOW(),
                            :monto_inicial,
                            :total_ventas,
                            :numero_ventas,
                            :total_efectivo,
                            :total_tarjetas,
                            :total_transferencias,
                            :gastos,
                            :retiros,
                            :total_esperado,
                            :total_contado,
                            :diferencia,
                            :observaciones,
                            'CERRADA'
                        )";
        
        $stmt_cierre = $conn->prepare($query_cierre);
        $stmt_cierre->bindParam(':id_apertura', $id_apertura, PDO::PARAM_INT);
        $stmt_cierre->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_cierre->bindParam(':monto_inicial', $monto_inicial, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':total_ventas', $total_ventas, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':numero_ventas', $numero_ventas, PDO::PARAM_INT);
        $stmt_cierre->bindParam(':total_efectivo', $total_efectivo, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':total_tarjetas', $total_tarjetas, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':total_transferencias', $total_transferencias, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':gastos', $total_gastos, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':retiros', $total_retiros, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':total_esperado', $total_esperado, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':total_contado', $total_contado, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':diferencia', $diferencia, PDO::PARAM_STR);
        $stmt_cierre->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        
        if (!$stmt_cierre->execute()) {
            throw new Exception('Error al registrar el cierre de caja');
        }
        
        $id_cierre = $conn->lastInsertId();
        
        // Guardar el desglose de denominaciones
        $query_desglose = "INSERT INTO desglose_denominaciones 
                          (Id_Cierre, Denominacion, Cantidad, Total, Tipo) 
                          VALUES (:id_cierre, :denominacion, :cantidad, :total, :tipo)";
        
        $stmt_desglose = $conn->prepare($query_desglose);
        
        foreach ($data['desglose'] as $item) {
            if (isset($item['cantidad']) && isset($item['denominacion']) && isset($item['tipo'])) {
                $cantidad = intval($item['cantidad']);
                $denominacion = floatval($item['denominacion']);
                $tipo = $item['tipo'];
                $total_item = $cantidad * $denominacion;
                
                if ($cantidad > 0) {
                    $stmt_desglose->bindParam(':id_cierre', $id_cierre, PDO::PARAM_INT);
                    $stmt_desglose->bindParam(':denominacion', $denominacion, PDO::PARAM_STR);
                    $stmt_desglose->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                    $stmt_desglose->bindParam(':total_item', $total_item, PDO::PARAM_STR);
                    $stmt_desglose->bindParam(':tipo', $tipo, PDO::PARAM_STR);
                    $stmt_desglose->execute();
                }
            }
        }
        
        // ====================================
        // ACTUALIZAR SALDO ACUMULADO
        // ====================================
        
        // El saldo acumulado es simplemente la diferencia
        // (lo que sobra o falta en la caja)
        
        $query_actualizar_saldo = "INSERT INTO saldo_acumulado_vendedor 
                                  (Id_Usuario, Saldo_Actual, Fecha_Actualizacion)
                                  VALUES (:usuario_id, :saldo, NOW())
                                  ON DUPLICATE KEY UPDATE 
                                  Saldo_Actual = :saldo2,
                                  Fecha_Actualizacion = NOW()";
        
        $stmt_saldo = $conn->prepare($query_actualizar_saldo);
        $stmt_saldo->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_saldo->bindParam(':saldo', $diferencia, PDO::PARAM_STR);
        $stmt_saldo->bindParam(':saldo2', $diferencia, PDO::PARAM_STR);
        $stmt_saldo->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        // Preparar respuesta
        $tipo_diferencia = '';
        if ($diferencia > 0) {
            $tipo_diferencia = 'SOBRANTE';
        } elseif ($diferencia < 0) {
            $tipo_diferencia = 'FALTANTE';
        } else {
            $tipo_diferencia = 'EXACTO';
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Caja cerrada exitosamente',
            'id_cierre' => $id_cierre,
            'resumen' => [
                'monto_inicial' => number_format($monto_inicial, 2, '.', ''),
                'total_ventas' => number_format($total_ventas, 2, '.', ''),
                'numero_ventas' => $numero_ventas,
                'total_efectivo' => number_format($total_efectivo, 2, '.', ''),
                'total_tarjetas' => number_format($total_tarjetas, 2, '.', ''),
                'total_transferencias' => number_format($total_transferencias, 2, '.', ''),
                'gastos' => number_format($total_gastos, 2, '.', ''),
                'retiros' => number_format($total_retiros, 2, '.', ''),
                'total_esperado' => number_format($total_esperado, 2, '.', ''),
                'total_contado' => number_format($total_contado, 2, '.', ''),
                'diferencia' => number_format($diferencia, 2, '.', ''),
                'tipo_diferencia' => $tipo_diferencia
            ]
        ]);
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error al cerrar caja: ' . $e->getMessage()
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ====================================
    // OBTENER DATOS PARA EL CIERRE
    // ====================================
    
    try {
        $usuario_id = $_SESSION['usuario_id'];
        
        // Verificar que existe una caja abierta
        $query_apertura = "SELECT Id_Apertura, Monto_Inicial, Fecha_Apertura
                          FROM apertura_caja 
                          WHERE Id_Usuario = :usuario_id 
                          AND Estado = 'ABIERTA' 
                          ORDER BY Fecha_Apertura DESC 
                          LIMIT 1";
        
        $stmt_apertura = $conn->prepare($query_apertura);
        $stmt_apertura->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_apertura->execute();
        
        if ($stmt_apertura->rowCount() === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'No hay una caja abierta para cerrar'
            ]);
            exit;
        }
        
        $apertura = $stmt_apertura->fetch(PDO::FETCH_ASSOC);
        $monto_inicial = floatval($apertura['Monto_Inicial']);
        
        // Obtener datos de ventas
        $query_ventas = "SELECT 
                            COUNT(*) as numero_ventas,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Efectivo' THEN Total_Venta ELSE 0 END), 0) as total_efectivo,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Tarjeta' THEN Total_Venta ELSE 0 END), 0) as total_tarjetas,
                            COALESCE(SUM(CASE WHEN Metodo_Pago = 'Transferencia' THEN Total_Venta ELSE 0 END), 0) as total_transferencias,
                            COALESCE(SUM(Total_Venta), 0) as total_ventas
                        FROM venta 
                        WHERE Id_Usuario_Venta = :usuario_id 
                        AND DATE(Fecha_Venta) = DATE(:fecha_apertura)";
        
        $stmt_ventas = $conn->prepare($query_ventas);
        $stmt_ventas->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_ventas->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_ventas->execute();
        
        $ventas_data = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
        
        // Obtener gastos
        $query_gastos = "SELECT COALESCE(SUM(Monto), 0) as total_gastos 
                        FROM gastos_caja 
                        WHERE Id_Usuario = :usuario_id 
                        AND DATE(Fecha_Gasto) = DATE(:fecha_apertura)";
        
        $stmt_gastos = $conn->prepare($query_gastos);
        $stmt_gastos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_gastos->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_gastos->execute();
        
        $gastos_data = $stmt_gastos->fetch(PDO::FETCH_ASSOC);
        
        // Obtener retiros
        $query_retiros = "SELECT COALESCE(SUM(Monto), 0) as total_retiros 
                         FROM retiros_caja 
                         WHERE Id_Usuario = :usuario_id 
                         AND DATE(Fecha_Retiro) = DATE(:fecha_apertura)";
        
        $stmt_retiros = $conn->prepare($query_retiros);
        $stmt_retiros->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_retiros->bindParam(':fecha_apertura', $apertura['Fecha_Apertura'], PDO::PARAM_STR);
        $stmt_retiros->execute();
        
        $retiros_data = $stmt_retiros->fetch(PDO::FETCH_ASSOC);
        
        // Calcular el total esperado
        $total_efectivo = floatval($ventas_data['total_efectivo']);
        $total_gastos = floatval($gastos_data['total_gastos']);
        $total_retiros = floatval($retiros_data['total_retiros']);
        
        $total_esperado = $monto_inicial + $total_efectivo - $total_gastos - $total_retiros;
        
        echo json_encode([
            'success' => true,
            'datos' => [
                'monto_inicial' => number_format($monto_inicial, 2, '.', ''),
                'total_ventas' => number_format(floatval($ventas_data['total_ventas']), 2, '.', ''),
                'numero_ventas' => intval($ventas_data['numero_ventas']),
                'total_efectivo' => number_format($total_efectivo, 2, '.', ''),
                'total_tarjetas' => number_format(floatval($ventas_data['total_tarjetas']), 2, '.', ''),
                'total_transferencias' => number_format(floatval($ventas_data['total_transferencias']), 2, '.', ''),
                'gastos' => number_format($total_gastos, 2, '.', ''),
                'retiros' => number_format($total_retiros, 2, '.', ''),
                'total_esperado' => number_format($total_esperado, 2, '.', ''),
                'hora_apertura' => date('H:i', strtotime($apertura['Fecha_Apertura']))
            ]
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener datos de cierre: ' . $e->getMessage()
        ]);
    }
}
?>
