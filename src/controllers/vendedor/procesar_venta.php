<?php
/**
 * PROCESAR VENTA - Versión con Fecha/Hora Actual y Sin IVA
 * Ubicación: /api/vendedor/procesar_venta.php
 * 
 * CAMBIOS:
 * 1. ✅ Fecha con hora actual usando NOW()
 * 2. ✅ IVA eliminado (Impuesto = 0)
 * 3. ✅ Número de comprobante para transferencias
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_errors.log');

session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    // ===================================
    // 1. VALIDAR SESIÓN
    // ===================================
    if (!isset($_SESSION['Id_Login_Usuario'])) {
        throw new Exception('Sesión no válida. Por favor inicia sesión nuevamente.');
    }

    $idVendedor = intval($_SESSION['Id_Login_Usuario']);
    
    // ===================================
    // 2. OBTENER Y VALIDAR DATOS JSON
    // ===================================
    $input = file_get_contents('php://input');
    
    if (empty($input)) {
        throw new Exception('No se recibieron datos');
    }
    
    $datos = json_decode($input, true);

    if (!is_array($datos)) {
        throw new Exception('Datos JSON inválidos: ' . json_last_error_msg());
    }

    // ===================================
    // 3. VALIDAR PRODUCTOS
    // ===================================
    if (empty($datos['productos']) || !is_array($datos['productos'])) {
        throw new Exception('No hay productos en el carrito');
    }

    if (count($datos['productos']) === 0) {
        throw new Exception('Debe agregar al menos un producto');
    }

    // ===================================
    // 4. VALIDAR CLIENTE (OBLIGATORIO)
    // ===================================
    $idCliente = intval($datos['id_cliente'] ?? $datos['cliente_id'] ?? 0);
    
    if ($idCliente === 0 || $idCliente < 1) {
        throw new Exception('Debe seleccionar un cliente válido');
    }

    // ===================================
    // 5. OBTENER Y VALIDAR VALORES (⭐ SIN IVA)
    // ===================================
    $productos  = $datos['productos'];
    $subtotal   = floatval($datos['subtotal'] ?? 0);
    $descuento  = floatval($datos['descuento'] ?? 0);
    // ⭐ IVA ELIMINADO - Siempre 0
    $iva        = 0;
    $total      = floatval($datos['total'] ?? 0);
    $metodoPago = trim($datos['metodo_pago'] ?? 'EFECTIVO');
    $notas      = trim($datos['notas'] ?? '');
    
    $numero_comprobante = isset($datos['numero_comprobante']) ? trim($datos['numero_comprobante']) : null;

    // Validar montos
    if ($subtotal < 0) {
        throw new Exception('El subtotal no puede ser negativo');
    }

    if ($total <= 0) {
        throw new Exception('El total de la venta debe ser mayor a 0');
    }

    // Validar método de pago
    $metodosValidos = ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA'];
    if (!in_array($metodoPago, $metodosValidos)) {
        throw new Exception('Método de pago inválido');
    }

    if ($metodoPago === 'TRANSFERENCIA' && empty($numero_comprobante)) {
        throw new Exception('Se requiere número de comprobante para transferencias');
    }

    // ===================================
    // 6. CONECTAR A BASE DE DATOS
    // ===================================
    require_once __DIR__ . '/../../config/conexion_mysqli.php';

    if (!$conn || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // ===================================
    // 6.1. CONFIGURAR ZONA HORARIA
    // ===================================
    date_default_timezone_set('America/Guayaquil');
    $conn->query("SET time_zone = '-05:00'");

    // ===================================
    // 7. VERIFICAR QUE CLIENTE EXISTE
    // ===================================
    $stmtCheckCliente = $conn->prepare("SELECT Id_Clientes FROM clientes WHERE Id_Clientes = ?");
    $stmtCheckCliente->bind_param("i", $idCliente);
    $stmtCheckCliente->execute();
    $resultCliente = $stmtCheckCliente->get_result();
    
    if ($resultCliente->num_rows === 0) {
        throw new Exception("El cliente seleccionado no existe (ID: {$idCliente})");
    }
    $stmtCheckCliente->close();

    // ===================================
    // 8. VALIDAR PRODUCTOS Y STOCK
    // ===================================
    foreach ($productos as $index => $p) {
        if (!isset($p['id']) || !isset($p['cantidad']) || !isset($p['precio'])) {
            throw new Exception("Datos incompletos en producto #{$index}");
        }

        $prodId = intval($p['id']);
        $prodCant = intval($p['cantidad']);
        $prodPrecio = floatval($p['precio']);

        if ($prodCant <= 0) {
            throw new Exception("Cantidad inválida para producto ID {$prodId}");
        }

        if ($prodPrecio < 0) {
            throw new Exception("Precio inválido para producto ID {$prodId}");
        }

        $stmtCheck = $conn->prepare("SELECT Stock_Producto, Nombre_Producto FROM inventario WHERE Id_Inventario = ?");
        $stmtCheck->bind_param("i", $prodId);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Producto ID {$prodId} no existe");
        }

        $producto = $result->fetch_assoc();
        
        if ($producto['Stock_Producto'] < $prodCant) {
            throw new Exception("Stock insuficiente para '{$producto['Nombre_Producto']}'. Disponible: {$producto['Stock_Producto']}, Solicitado: {$prodCant}");
        }

        $stmtCheck->close();
    }

    // ===================================
    // 9. INICIAR TRANSACCIÓN
    // ===================================
    $conn->begin_transaction();

    try {
        // ===================================
        // 10. INSERTAR VENTA (⭐ CON FECHA/HORA SIN SEGUNDOS Y SIN IVA)
        // ===================================
        $sqlVenta = "INSERT INTO venta 
            (Id_Cliente_Venta, Fecha_Venta, Total_Venta, Metodo_Pago, 
             Id_Usuario_Vendedor, Subtotal, Descuento, Impuesto, Numero_Comprobante, Notas) 
            VALUES (?, DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:00'), ?, ?, ?, ?, ?, 0, ?, ?)";

        $stmtVenta = $conn->prepare($sqlVenta);
        
        if (!$stmtVenta) {
            throw new Exception("Error preparando inserción de venta: " . $conn->error);
        }

        // ⭐ NOTA: Impuesto se pone directamente como 0 en el SQL
        // Tipos: "idsiddss" = i, d, s, i, d, d, s, s (8 parámetros)
        $stmtVenta->bind_param(
            "idsiddss",          
            $idCliente,          // i - Id_Cliente_Venta
            $total,              // d - Total_Venta
            $metodoPago,         // s - Metodo_Pago
            $idVendedor,         // i - Id_Usuario_Vendedor
            $subtotal,           // d - Subtotal
            $descuento,          // d - Descuento
            // Impuesto = 0 (directo en SQL)
            $numero_comprobante, // s - Numero_Comprobante
            $notas               // s - Notas
        );

        if (!$stmtVenta->execute()) {
            throw new Exception("Error al guardar venta: " . $stmtVenta->error);
        }

        $ventaId = $stmtVenta->insert_id;
        $stmtVenta->close();

        if ($ventaId === 0) {
            throw new Exception("No se pudo obtener el ID de la venta");
        }

        // Log para debug
        error_log("Venta creada - ID: {$ventaId}, Método: {$metodoPago}, Fecha: " . date('Y-m-d H:i:s'));

        // ===================================
        // 11. INSERTAR DETALLES DE VENTA
        // ===================================
        $sqlDetalle = "INSERT INTO detalle_venta 
            (Id_Venta_Detalle, Id_Inventario_Detalle, Cantidad, Precio_Unitario, SubTotal)
            VALUES (?, ?, ?, ?, ?)";

        $stmtDetalle = $conn->prepare($sqlDetalle);
        
        if (!$stmtDetalle) {
            throw new Exception("Error preparando inserción de detalles: " . $conn->error);
        }

        foreach ($productos as $p) {
            $prodId = intval($p['id']);
            $prodCant = intval($p['cantidad']);
            $prodPrecio = floatval($p['precio']);
            $subtotalProducto = $prodCant * $prodPrecio;

            $stmtDetalle->bind_param(
                "iiidd",
                $ventaId,           
                $prodId,            
                $prodCant,          
                $prodPrecio,        
                $subtotalProducto   
            );

            if (!$stmtDetalle->execute()) {
                throw new Exception("Error al guardar detalle para producto ID {$prodId}: " . $stmtDetalle->error);
            }

            // ===================================
            // 12. ACTUALIZAR STOCK
            // ===================================
            $sqlStock = "UPDATE inventario 
                        SET Stock_Producto = Stock_Producto - ? 
                        WHERE Id_Inventario = ?";
            
            $stmtStock = $conn->prepare($sqlStock);
            
            if (!$stmtStock) {
                throw new Exception("Error preparando actualización de stock: " . $conn->error);
            }

            $stmtStock->bind_param("ii", $prodCant, $prodId);
            
            if (!$stmtStock->execute()) {
                throw new Exception("Error actualizando stock del producto ID {$prodId}: " . $stmtStock->error);
            }

            $stmtStock->close();
        }

        $stmtDetalle->close();

        // ===================================
        // 13. CONFIRMAR TRANSACCIÓN
        // ===================================
        $conn->commit();

        // ===================================
        // 14. RESPUESTA EXITOSA
        // ===================================
        $respuesta = [
            "success" => true,
            "venta_id" => $ventaId,
            "total" => round($total, 2),
            "mensaje" => "Venta procesada exitosamente",
            "metodo_pago" => $metodoPago,
            "fecha" => date('d/m/Y'), // ⭐ Formato: 03/01/2026
            "fecha_hora" => date('d/m/Y H:i') // ⭐ Formato: 03/01/2026 19:00
        ];

        if (!empty($numero_comprobante)) {
            $respuesta['numero_comprobante'] = $numero_comprobante;
        }

        echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    error_log("ERROR en procesar_venta.php: " . $e->getMessage());
    error_log("Datos recibidos: " . print_r($datos ?? [], true));
    error_log("Usuario: " . ($_SESSION['Id_Login_Usuario'] ?? 'No definido'));
    error_log("Fecha/hora del servidor: " . date('Y-m-d H:i:s'));

    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

} finally {
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>