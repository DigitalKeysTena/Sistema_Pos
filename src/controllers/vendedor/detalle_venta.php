<?php
// src/controllers/vendedor/detalle_venta.php
// VERSIÓN CORREGIDA CON DEBUG

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

error_log("=== INICIO detalle_venta.php ===");

try {
    error_log("1. Iniciando sesión...");
    session_start();
    error_log("   ✅ Sesión iniciada");
    
    error_log("2. Cargando configuración...");
    require_once __DIR__ . '/../../config/app_config.php';
    error_log("   ✅ app_config.php cargado");
    
    require_once __DIR__ . '/../../config/conection.php';
    error_log("   ✅ conection.php cargado");
    
    require_once __DIR__ . '/../../security/auth_fixed.php';
    error_log("   ✅ auth_fixed.php cargado");
    
    error_log("3. Verificando autenticación...");
    require_role([2, 1]); // Vendedor y Admin
    error_log("   ✅ Usuario autenticado");
    
    error_log("4. Configurando headers...");
    header('Content-Type: application/json; charset=utf-8');
    error_log("   ✅ Headers configurados");
    
    error_log("5. Verificando parámetro id_venta...");
    if (!isset($_GET['id_venta'])) {
        error_log("   ❌ Parámetro id_venta no proporcionado");
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID de venta no proporcionado"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $idVenta = intval($_GET['id_venta']);
    error_log("   ✅ ID Venta: $idVenta");
    
    error_log("6. Verificando variable PDO...");
    if (!isset($pdo)) {
        throw new Exception("Variable \$pdo no está definida");
    }
    error_log("   ✅ \$pdo existe");
    
    // ⭐ CONSULTA CORREGIDA
    error_log("7. Construyendo consulta SQL...");
    $sql = "
        SELECT 
            dv.Id_Detalle,
            dv.Cantidad,
            dv.Precio_Unitario,
            dv.SubTotal,
            dv.Id_Inventario_Detalle,
            inv.Nombre_Producto,
            inv.Codigo_Producto
        FROM detalle_venta dv
        LEFT JOIN inventario inv ON dv.Id_Inventario_Detalle = inv.Id_Inventario
        WHERE dv.Id_Venta_Detalle = :idVenta
    ";
    
    error_log("   SQL: " . preg_replace('/\s+/', ' ', $sql));
    error_log("   Parámetro: idVenta = $idVenta");
    
    error_log("8. Preparando statement...");
    $stmt = $pdo->prepare($sql);
    error_log("   ✅ Statement preparado");
    
    error_log("9. Ejecutando query...");
    $stmt->bindParam(':idVenta', $idVenta, PDO::PARAM_INT);
    $stmt->execute();
    error_log("   ✅ Query ejecutada");
    
    error_log("10. Obteniendo resultados...");
    $detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);
    error_log("   ✅ Detalles encontrados: " . count($detalle));
    
    // Si no hay detalles, verificar si la venta existe
    if (count($detalle) === 0) {
        error_log("11. No hay detalles - Verificando si la venta existe...");
        
        $sqlCheck = "SELECT COUNT(*) as total FROM venta WHERE Id_Venta = :idVenta";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':idVenta', $idVenta, PDO::PARAM_INT);
        $stmtCheck->execute();
        $ventaExiste = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($ventaExiste['total'] === 0) {
            error_log("   ❌ La venta no existe");
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Venta no encontrada"
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        error_log("   ⚠️ La venta existe pero no tiene detalles");
    }
    
    error_log("12. Generando respuesta JSON...");
    $response = [
        "success" => true,
        "data" => $detalle,
        "total_items" => count($detalle)
    ];
    
    $json = json_encode($response, JSON_UNESCAPED_UNICODE);
    
    if ($json === false) {
        throw new Exception("Error al generar JSON: " . json_last_error_msg());
    }
    
    error_log("   ✅ JSON generado");
    error_log("   Tamaño: " . strlen($json) . " bytes");
    
    error_log("13. Enviando respuesta...");
    echo $json;
    error_log("   ✅ Respuesta enviada");
    
    error_log("=== FIN EXITOSO detalle_venta.php ===");
    
} catch (PDOException $e) {
    error_log("❌ ERROR PDO: " . $e->getMessage());
    error_log("   Código: " . $e->getCode());
    error_log("   Archivo: " . $e->getFile() . ":" . $e->getLine());
    error_log("   Trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error en la base de datos",
        "message" => $e->getMessage(),
        "code" => $e->getCode()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("❌ ERROR GENERAL: " . $e->getMessage());
    error_log("   Archivo: " . $e->getFile() . ":" . $e->getLine());
    error_log("   Trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error del servidor",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>