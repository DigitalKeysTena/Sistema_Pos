<?php
// src/controllers/vendedor/obtener_ventas.php
// VERSIÓN CORREGIDA - Soluciona problema de fechas iguales

session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth_fixed.php';

require_role([2, 1]); // Vendedor y Admin
header('Content-Type: application/json; charset=utf-8');

try {
    $vendedor_id = $_SESSION['Id_Login_Usuario'];
    
    // Obtener filtros
    $fecha_desde = $_GET['desde'] ?? date('Y-m-d', strtotime('-7 days'));
    $fecha_hasta = $_GET['hasta'] ?? date('Y-m-d');
    $metodo = $_GET['metodo'] ?? '';
    
    // Log de debug
    error_log("=== OBTENER VENTAS ===");
    error_log("Vendedor: $vendedor_id");
    error_log("Desde: $fecha_desde | Hasta: $fecha_hasta");
    error_log("Método: " . ($metodo ?: 'TODOS'));
    
    // ⭐ CORRECCIÓN: Usar >= y <= en lugar de BETWEEN para evitar problemas de timezone
    // BETWEEN puede excluir registros si hay diferencias de hora
    $sql = "
        SELECT 
            v.Id_Venta as id,
            v.Fecha_Venta as fecha,
            v.Subtotal as subtotal,
            v.Descuento as descuento,
            v.Impuesto as iva,
            v.Total_Venta as total,
            v.Metodo_Pago as metodo_pago,
            v.Estado_Venta as estado,
            v.Notas as notas,
            COUNT(dv.Id_Detalle) as cantidad_productos,
            COALESCE(SUM(dv.Cantidad), 0) as total_items
        FROM venta v
        LEFT JOIN detalle_venta dv ON v.Id_Venta = dv.Id_Venta_Detalle
        WHERE v.Id_Usuario_Vendedor = ?
        AND DATE(v.Fecha_Venta) >= ?
        AND DATE(v.Fecha_Venta) <= ?
    ";
    
    $params = [$vendedor_id, $fecha_desde, $fecha_hasta];
    
    // Filtro opcional de método de pago
    if ($metodo) {
        $sql .= " AND v.Metodo_Pago = ?";
        $params[] = $metodo;
    }
    
    $sql .= " GROUP BY v.Id_Venta ORDER BY v.Fecha_Venta DESC, v.Id_Venta DESC";
    
    error_log("SQL ejecutado: " . preg_replace('/\s+/', ' ', $sql));
    error_log("Parámetros: " . json_encode($params));
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("✅ Ventas encontradas: " . count($ventas));
    
    // Si no hay ventas, hacer diagnóstico
    if (count($ventas) === 0) {
        error_log("⚠️ No hay ventas - Diagnóstico:");
        
        // Total de ventas del vendedor (sin filtros)
        $sqlTotal = "SELECT COUNT(*) as total FROM venta WHERE Id_Usuario_Vendedor = ?";
        $stmtTotal = $pdo->prepare($sqlTotal);
        $stmtTotal->execute([$vendedor_id]);
        $total = $stmtTotal->fetch(PDO::FETCH_ASSOC);
        error_log("   Total ventas del vendedor: " . $total['total']);
        
        // Ventas exactas en las fechas solicitadas
        $sqlDebug = "
            SELECT 
                DATE(Fecha_Venta) as fecha, 
                COUNT(*) as total 
            FROM venta 
            WHERE Id_Usuario_Vendedor = ? 
            AND DATE(Fecha_Venta) >= ? 
            AND DATE(Fecha_Venta) <= ?
            GROUP BY DATE(Fecha_Venta)
        ";
        $stmtDebug = $pdo->prepare($sqlDebug);
        $stmtDebug->execute([$vendedor_id, $fecha_desde, $fecha_hasta]);
        $debug = $stmtDebug->fetchAll(PDO::FETCH_ASSOC);
        error_log("   Desglose por fecha: " . json_encode($debug));
        
        // Última venta del vendedor
        $sqlUltima = "SELECT Fecha_Venta FROM venta WHERE Id_Usuario_Vendedor = ? ORDER BY Fecha_Venta DESC LIMIT 1";
        $stmtUltima = $pdo->prepare($sqlUltima);
        $stmtUltima->execute([$vendedor_id]);
        $ultima = $stmtUltima->fetch(PDO::FETCH_ASSOC);
        if ($ultima) {
            error_log("   Última venta: " . $ultima['Fecha_Venta']);
        }
    }
    
    // Calcular resumen
    $resumen = [
        'total_ventas' => count($ventas),
        'monto_total' => 0,
        'productos_total' => 0
    ];
    
    foreach ($ventas as $venta) {
        $resumen['monto_total'] += floatval($venta['total']);
        $resumen['productos_total'] += intval($venta['total_items']);
    }
    
    // Información de debug
    $debug_info = [
        'vendedor_id' => $vendedor_id,
        'fecha_desde' => $fecha_desde,
        'fecha_hasta' => $fecha_hasta,
        'metodo_filtro' => $metodo ?: 'TODOS',
        'ventas_encontradas' => count($ventas),
        'hora_consulta' => date('Y-m-d H:i:s'),
        'mismo_dia' => ($fecha_desde === $fecha_hasta)
    ];
    
    echo json_encode([
        'success' => true,
        'ventas' => $ventas,
        'resumen' => $resumen,
        'debug' => $debug_info
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("❌ ERROR PDO: " . $e->getMessage());
    error_log("Trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos',
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("❌ ERROR: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error del servidor',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>