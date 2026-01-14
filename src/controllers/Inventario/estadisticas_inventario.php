<?php
session_start();

// ⭐ HEADERS ANTI-CACHÉ
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/conection.php';

// ⭐ FECHA ACTUAL EN PHP (zona horaria Ecuador)
date_default_timezone_set('America/Guayaquil');
$fechaHoy = date('Y-m-d');
$fecha7Dias = date('Y-m-d', strtotime('+7 days'));
$fecha30Dias = date('Y-m-d', strtotime('+30 days'));

try {
    if (!isset($pdo)) {
        throw new Exception("No hay conexión PDO");
    }
    
    // Total de productos
    $sqlTotal = "SELECT COUNT(*) as total FROM inventario";
    $stmtTotal = $pdo->query($sqlTotal);
    $totalProductos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Stock Normal (stock >= 10)
    $sqlNormal = "SELECT COUNT(*) as total FROM inventario WHERE Stock_Producto >= 10";
    $stmtNormal = $pdo->query($sqlNormal);
    $stockNormal = $stmtNormal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Stock Bajo (stock > 0 AND stock < 10)
    $sqlBajo = "SELECT COUNT(*) as total FROM inventario WHERE Stock_Producto > 0 AND Stock_Producto < 10";
    $stmtBajo = $pdo->query($sqlBajo);
    $stockBajo = $stmtBajo->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Sin Stock (stock = 0)
    $sqlSinStock = "SELECT COUNT(*) as total FROM inventario WHERE Stock_Producto = 0";
    $stmtSinStock = $pdo->query($sqlSinStock);
    $sinStock = $stmtSinStock->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Stock total (suma de todos los productos)
    $sqlStockTotal = "SELECT COALESCE(SUM(Stock_Producto), 0) as total FROM inventario";
    $stmtStockTotal = $pdo->query($sqlStockTotal);
    $stockTotal = $stmtStockTotal->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total de categorías
    $sqlCategorias = "SELECT COUNT(DISTINCT Id_Tipo_Categoria) as total FROM inventario WHERE Id_Tipo_Categoria IS NOT NULL";
    $stmtCategorias = $pdo->query($sqlCategorias);
    $totalCategorias = $stmtCategorias->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Productos con stock crítico (menos de 5)
    $sqlCritico = "SELECT COUNT(*) as total FROM inventario WHERE Stock_Producto > 0 AND Stock_Producto < 5";
    $stmtCritico = $pdo->query($sqlCritico);
    $stockCritico = $stmtCritico->fetch(PDO::FETCH_ASSOC)['total'];
    
    // ⭐ PRODUCTOS CADUCADOS (fecha de caducidad menor a hoy)
    $sqlCaducados = "SELECT COUNT(*) as total FROM inventario 
                     WHERE Fecha_Caducidad IS NOT NULL 
                     AND Fecha_Caducidad > '1900-01-01'
                     AND Fecha_Caducidad < :fechaHoy";
    $stmtCaducados = $pdo->prepare($sqlCaducados);
    $stmtCaducados->execute([':fechaHoy' => $fechaHoy]);
    $productosCaducados = $stmtCaducados->fetch(PDO::FETCH_ASSOC)['total'];
    
    // ⭐ PRODUCTOS POR CADUCAR (en los próximos 30 días)
    $sqlPorCaducar = "SELECT COUNT(*) as total FROM inventario 
                      WHERE Fecha_Caducidad IS NOT NULL 
                      AND Fecha_Caducidad > '1900-01-01'
                      AND Fecha_Caducidad >= :fechaHoy 
                      AND Fecha_Caducidad <= :fecha30Dias";
    $stmtPorCaducar = $pdo->prepare($sqlPorCaducar);
    $stmtPorCaducar->execute([':fechaHoy' => $fechaHoy, ':fecha30Dias' => $fecha30Dias]);
    $productosPorCaducar = $stmtPorCaducar->fetch(PDO::FETCH_ASSOC)['total'];
    
    // ⭐ PRODUCTOS POR CADUCAR EN 7 DÍAS (urgente)
    $sqlPorCaducar7 = "SELECT COUNT(*) as total FROM inventario 
                       WHERE Fecha_Caducidad IS NOT NULL 
                       AND Fecha_Caducidad > '1900-01-01'
                       AND Fecha_Caducidad >= :fechaHoy 
                       AND Fecha_Caducidad <= :fecha7Dias";
    $stmtPorCaducar7 = $pdo->prepare($sqlPorCaducar7);
    $stmtPorCaducar7->execute([':fechaHoy' => $fechaHoy, ':fecha7Dias' => $fecha7Dias]);
    $productosPorCaducar7 = $stmtPorCaducar7->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        "success" => true,
        "estadisticas" => [
            "totalProductos" => (int)$totalProductos,
            "stockNormal" => (int)$stockNormal,
            "stockBajo" => (int)$stockBajo,
            "sinStock" => (int)$sinStock,
            "stockTotal" => (int)$stockTotal,
            "categorias" => (int)$totalCategorias,
            "stockCritico" => (int)$stockCritico,
            "productosCaducados" => (int)$productosCaducados,
            "productosPorCaducar" => (int)$productosPorCaducar,
            "productosPorCaducar7" => (int)$productosPorCaducar7
        ],
        "timestamp" => time()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error estadisticas_inventario: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Error al obtener estadísticas",
        "timestamp" => time()
    ]);
}