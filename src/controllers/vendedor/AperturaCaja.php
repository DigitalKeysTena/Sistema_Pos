<?php
/**
 * Controlador: Apertura de Caja
 * Ruta: /src/controllers/vendedor/AperturaCaja.php
 * Descripción: Gestiona la apertura de caja con validaciones mejoradas
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
        $monto_inicial = isset($data['monto_inicial']) ? floatval($data['monto_inicial']) : 0.00;
        $observaciones = isset($data['observaciones']) ? trim($data['observaciones']) : '';
        
        // Validar que no haya una caja abierta
        $query_check = "SELECT Id_Apertura, Estado, Fecha_Apertura 
                       FROM apertura_caja 
                       WHERE Id_Usuario = :usuario_id 
                       AND Estado = 'ABIERTA' 
                       LIMIT 1";
        
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_check->execute();
        
        if ($stmt_check->rowCount() > 0) {
            $caja_abierta = $stmt_check->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'success' => false,
                'message' => 'Ya tienes una caja abierta desde ' . date('H:i', strtotime($caja_abierta['Fecha_Apertura'])),
                'caja_actual' => $caja_abierta
            ]);
            exit;
        }
        
        // ====================================
        // CÁLCULO DEL SALDO ACUMULADO CORREGIDO
        // ====================================
        
        // Obtener el último cierre de caja
        $query_ultimo_cierre = "SELECT 
                                    c.Total_Esperado,
                                    c.Total_Contado,
                                    c.Diferencia,
                                    c.Monto_Inicial,
                                    c.Total_Ventas,
                                    c.Total_Efectivo,
                                    c.Total_Transferencias,
                                    c.Gastos,
                                    c.Retiros,
                                    c.Fecha_Cierre
                                FROM cierre_caja c
                                WHERE c.Id_Usuario = :usuario_id
                                ORDER BY c.Fecha_Cierre DESC
                                LIMIT 1";
        
        $stmt_ultimo = $conn->prepare($query_ultimo_cierre);
        $stmt_ultimo->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_ultimo->execute();
        
        $saldo_acumulado = 0.00;
        
        if ($stmt_ultimo->rowCount() > 0) {
            $ultimo_cierre = $stmt_ultimo->fetch(PDO::FETCH_ASSOC);
            
            // CÁLCULO CORRECTO:
            // Saldo Acumulado = Diferencia del último cierre
            // La diferencia ya refleja: (Total Contado) - (Total Esperado)
            // Donde Total Esperado = Monto Inicial + Ventas Efectivo - Gastos - Retiros
            $saldo_acumulado = floatval($ultimo_cierre['Diferencia']);
        }
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        // Insertar apertura de caja
        $query_insert = "INSERT INTO apertura_caja 
                        (Id_Usuario, Fecha_Apertura, Monto_Inicial, Estado, Observaciones) 
                        VALUES 
                        (:usuario_id, NOW(), :monto_inicial, 'ABIERTA', :observaciones)";
        
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':monto_inicial', $monto_inicial, PDO::PARAM_STR);
        $stmt_insert->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        
        if (!$stmt_insert->execute()) {
            throw new Exception('Error al registrar la apertura de caja');
        }
        
        $id_apertura = $conn->lastInsertId();
        
        // Actualizar saldo acumulado en la tabla correspondiente
        $query_saldo = "INSERT INTO saldo_acumulado_vendedor 
                       (Id_Usuario, Saldo_Actual, Fecha_Actualizacion)
                       VALUES (:usuario_id, :saldo, NOW())
                       ON DUPLICATE KEY UPDATE 
                       Saldo_Actual = :saldo2,
                       Fecha_Actualizacion = NOW()";
        
        $stmt_saldo = $conn->prepare($query_saldo);
        $stmt_saldo->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt_saldo->bindParam(':saldo', $saldo_acumulado, PDO::PARAM_STR);
        $stmt_saldo->bindParam(':saldo2', $saldo_acumulado, PDO::PARAM_STR);
        $stmt_saldo->execute();
        
        // Confirmar transacción
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Caja abierta exitosamente',
            'id_apertura' => $id_apertura,
            'monto_inicial' => number_format($monto_inicial, 2, '.', ''),
            'saldo_acumulado' => number_format($saldo_acumulado, 2, '.', ''),
            'fecha_apertura' => date('Y-m-d H:i:s')
        ]);
        
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode([
            'success' => false,
            'message' => 'Error al abrir caja: ' . $e->getMessage()
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // ====================================
    // VERIFICAR ESTADO DE CAJA ACTUAL
    // ====================================
    
    try {
        $usuario_id = $_SESSION['usuario_id'];
        
        // Consulta CORREGIDA - Asegurando que devuelve el Estado
        $query = "SELECT 
                    Id_Apertura,
                    Estado,
                    Fecha_Apertura,
                    Fecha_Cierre,
                    Monto_Inicial,
                    Observaciones
                  FROM apertura_caja 
                  WHERE Id_Usuario = :usuario_id 
                  AND DATE(Fecha_Apertura) = CURDATE() 
                  ORDER BY Fecha_Apertura DESC 
                  LIMIT 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $caja = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Obtener saldo acumulado
            $query_saldo = "SELECT Saldo_Actual 
                           FROM saldo_acumulado_vendedor 
                           WHERE Id_Usuario = :usuario_id 
                           LIMIT 1";
            
            $stmt_saldo = $conn->prepare($query_saldo);
            $stmt_saldo->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt_saldo->execute();
            
            $saldo_acumulado = 0.00;
            if ($stmt_saldo->rowCount() > 0) {
                $saldo_data = $stmt_saldo->fetch(PDO::FETCH_ASSOC);
                $saldo_acumulado = floatval($saldo_data['Saldo_Actual']);
            }
            
            // Calcular totales si la caja está ABIERTA
            $total_ventas = 0;
            $numero_ventas = 0;
            $ventas_efectivo = 0;
            $ventas_transferencia = 0;
            $gastos = 0;
            $retiros = 0;
            
            if ($caja['Estado'] === 'ABIERTA') {
                // Obtener ventas del día
                $query_ventas = "SELECT 
                                    COUNT(*) as numero_ventas,
                                    COALESCE(SUM(CASE WHEN Metodo_Pago = 'Efectivo' THEN Total_Venta ELSE 0 END), 0) as ventas_efectivo,
                                    COALESCE(SUM(CASE WHEN Metodo_Pago = 'Transferencia' THEN Total_Venta ELSE 0 END), 0) as ventas_transferencia,
                                    COALESCE(SUM(Total_Venta), 0) as total_ventas
                                FROM venta 
                                WHERE Id_Usuario_Venta = :usuario_id 
                                AND DATE(Fecha_Venta) = CURDATE()";
                
                $stmt_ventas = $conn->prepare($query_ventas);
                $stmt_ventas->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_ventas->execute();
                
                if ($stmt_ventas->rowCount() > 0) {
                    $ventas_data = $stmt_ventas->fetch(PDO::FETCH_ASSOC);
                    $total_ventas = floatval($ventas_data['total_ventas']);
                    $numero_ventas = intval($ventas_data['numero_ventas']);
                    $ventas_efectivo = floatval($ventas_data['ventas_efectivo']);
                    $ventas_transferencia = floatval($ventas_data['ventas_transferencia']);
                }
                
                // Obtener gastos del día
                $query_gastos = "SELECT COALESCE(SUM(Monto), 0) as total_gastos 
                                FROM gastos_caja 
                                WHERE Id_Usuario = :usuario_id 
                                AND DATE(Fecha_Gasto) = CURDATE()";
                
                $stmt_gastos = $conn->prepare($query_gastos);
                $stmt_gastos->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_gastos->execute();
                
                if ($stmt_gastos->rowCount() > 0) {
                    $gastos_data = $stmt_gastos->fetch(PDO::FETCH_ASSOC);
                    $gastos = floatval($gastos_data['total_gastos']);
                }
                
                // Obtener retiros del día
                $query_retiros = "SELECT COALESCE(SUM(Monto), 0) as total_retiros 
                                 FROM retiros_caja 
                                 WHERE Id_Usuario = :usuario_id 
                                 AND DATE(Fecha_Retiro) = CURDATE()";
                
                $stmt_retiros = $conn->prepare($query_retiros);
                $stmt_retiros->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                $stmt_retiros->execute();
                
                if ($stmt_retiros->rowCount() > 0) {
                    $retiros_data = $stmt_retiros->fetch(PDO::FETCH_ASSOC);
                    $retiros = floatval($retiros_data['total_retiros']);
                }
            }
            
            echo json_encode([
                'success' => true,
                'estado' => $caja['Estado'], // CAMPO CRÍTICO - Debe estar presente
                'monto_inicial' => number_format(floatval($caja['Monto_Inicial']), 2, '.', ''),
                'hora_apertura' => date('H:i', strtotime($caja['Fecha_Apertura'])),
                'total_ventas' => number_format($total_ventas, 2, '.', ''),
                'numero_ventas' => $numero_ventas,
                'ventas_efectivo' => number_format($ventas_efectivo, 2, '.', ''),
                'ventas_transferencia' => number_format($ventas_transferencia, 2, '.', ''),
                'gastos' => number_format($gastos, 2, '.', ''),
                'retiros' => number_format($retiros, 2, '.', ''),
                'saldo_acumulado' => $saldo_acumulado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No hay caja abierta hoy'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al verificar estado de caja: ' . $e->getMessage()
        ]);
    }
}
?>
