<?php
// src/controllers/vendedor/obtener_estadisticas.php
session_start();

require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth_fixed.php';

// Verificar autenticación
require_role([2]); // Solo vendedor

header('Content-Type: application/json; charset=utf-8');

try {
    $vendedor_id = $_SESSION['Id_Login_Usuario'];
    
    // Estadísticas del día actual
    $sql = "
        SELECT 
            COUNT(*) as ventas_hoy,
            COALESCE(SUM(Total_Venta), 0) as total_vendido,
            COALESCE(SUM((
                SELECT SUM(Cantidad) 
                FROM detalle_venta 
                WHERE Id_Venta = venta.Id_Venta
            )), 0) as productos_vendidos
        FROM venta
        WHERE Id_Usuario_Vendedor = ?
        AND DATE(Fecha_Venta) = CURDATE()
        AND Estado_Venta = 'COMPLETADA'
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$vendedor_id]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'ventas_hoy' => (int)$stats['ventas_hoy'],
        'total_vendido' => (float)$stats['total_vendido'],
        'productos_vendidos' => (int)$stats['productos_vendidos']
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Error obteniendo estadísticas: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos'
    ]);
}
?>
