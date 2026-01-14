<?php
session_start();

// ⭐ HEADERS ANTI-CACHÉ - IMPORTANTE para que los datos se actualicen
header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../config/conection.php';

try {
    if (!isset($pdo)) {
        throw new Exception("No hay conexión PDO");
    }
    
    // Obtener todos los productos (sin filtro)
    $sql = "
        SELECT 
            Id_Inventario AS id,
            Codigo_Producto AS codigo,
            Codigo_Barras AS codigoBarras,
            Nombre_Producto AS nombre,
            Precio_Compra_Producto AS precioCompra,
            Precio_Venta_Producto AS precioVenta,
            Stock_Producto AS stock,
            Margen_Utilidad AS margen,
            Fecha_Caducidad AS fechaCaducidad,
            Id_Tipo_Categoria AS tipoCategoria
        FROM inventario
        ORDER BY Id_Inventario DESC
    ";
    
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Estadísticas
    $estadisticas = [
        "totalProductos" => count($productos),
        "stockTotal" => array_sum(array_column($productos, "stock")),
        "stockBajo" => count(array_filter($productos, fn($p) => $p["stock"] < 5)),
        "categorias" => count(array_unique(array_column($productos, "tipoCategoria")))
    ];
    
    echo json_encode([
        "success" => true,
        "productos" => $productos,
        "estadisticas" => $estadisticas,
        "timestamp" => time() // ⭐ Para verificar que los datos son frescos
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error obtener_productos: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => "Error interno",
        "timestamp" => time()
    ]);
}