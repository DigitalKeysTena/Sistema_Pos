<?php
require_once __DIR__ . '/../../config/conection.php';
header('Content-Type: application/json; charset=utf-8');

// ======================================================
// VALIDAR PARÁMETRO ?codigo
// ======================================================
if (!isset($_GET['codigo']) || trim($_GET['codigo']) === '') {
    echo json_encode([
        "found" => false,
        "error" => "Código no proporcionado"
    ]);
    exit;
}

$codigo = trim($_GET['codigo']);

try {

    // ======================================================
    // BUSCAR PRODUCTO POR CÓDIGO DE BARRAS
    // ======================================================
    $sql = "
        SELECT 
            Id_Inventario,
            Nombre_Producto,
            Codigo_Producto,
            Codigo_Barras,
            Precio_Compra_Producto,
            Precio_Venta_Producto,
            Stock_Producto,
            Fecha_Caducidad
        FROM inventario
        WHERE Codigo_Barras = ?
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // ======================================================
    // RESPUESTA SI EXISTE
    // ======================================================
    if ($producto) {
        echo json_encode([
            "found" => true,
            "producto" => $producto
        ]);
        exit;
    }

    // ======================================================
    // SI NO EXISTE
    // ======================================================
    echo json_encode([
        "found" => false,
        "message" => "No existe un producto con este código de barras"
    ]);

} catch (PDOException $e) {

    // Error interno
    echo json_encode([
        "found" => false,
        "error" => "Error en la consulta",
        "debug" => $e->getMessage()
    ]);
}
