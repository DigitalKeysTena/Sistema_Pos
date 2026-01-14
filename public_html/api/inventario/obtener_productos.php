<?php
header("Content-Type: application/json; charset=utf-8");

$basePath = dirname(__DIR__, 2);
require_once $basePath . "/../src/config/conection.php";

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

$estadisticas = [
    "totalProductos" => count($productos),
    "stockTotal" => (int) array_sum(array_column($productos, "stock")),
    "stockBajo" => count(array_filter($productos, fn($p) => intval($p["stock"]) < 5)),
    "categorias" => count(array_unique(array_column($productos, "tipoCategoria")))
];

echo json_encode([
    "success" => true,
    "productos" => $productos,
    "estadisticas" => $estadisticas
], JSON_UNESCAPED_UNICODE);