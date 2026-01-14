<?php
/**
 * Archivo: src/controllers/Inventario/actualizar_stock.php
 * Descripción: Actualiza el stock y registra el movimiento en historial
 */

session_start();

// ⭐ Cargar configuración y seguridad mejorada
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../security/auth_fixed.php';

// Verificar autenticación y rol
require_role([3]); // Solo inventario

// Incluir archivo de conexión PDO
require_once __DIR__ . '/../../config/conection.php';

header('Content-Type: application/json; charset=utf-8');

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
    exit;
}

try {
    // Verificar conexión PDO
    if (!isset($pdo)) {
        throw new Exception('Error: No hay conexión PDO disponible');
    }
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos requeridos
    if (!isset($data['id_producto']) || !isset($data['cantidad'])) {
        throw new Exception('Datos incompletos');
    }
    
    $id_producto = (int)$data['id_producto'];
    $cantidad = (int)$data['cantidad'];
    $notas = isset($data['notas']) ? trim($data['notas']) : '';
    
    // Validar que la cantidad sea positiva
    if ($cantidad <= 0) {
        throw new Exception('La cantidad debe ser mayor a 0');
    }
    
    // ✅ CORRECCIÓN: Obtener el ID del usuario de la sesión
    // Usar Id_Login_Usuario que es como se guarda en user.php
    $usuario_id = isset($_SESSION['Id_Login_Usuario']) ? (int)$_SESSION['Id_Login_Usuario'] : null;
    
    // Validar que exista el usuario en sesión
    if ($usuario_id === null) {
        throw new Exception('No se pudo identificar al usuario. Sesión inválida.');
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 1. Obtener el producto actual
    $sql = "SELECT Id_Inventario, Nombre_Producto, Stock_Producto 
            FROM inventario 
            WHERE Id_Inventario = :id_producto";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt->execute();
    
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        throw new Exception('Producto no encontrado');
    }
    
    $stock_anterior = (int)$producto['Stock_Producto'];
    $nuevo_stock = $stock_anterior + $cantidad;
    
    // 2. Actualizar el stock en la tabla inventario
    $sql_update = "UPDATE inventario 
                   SET Stock_Producto = :nuevo_stock 
                   WHERE Id_Inventario = :id_producto";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->bindParam(':nuevo_stock', $nuevo_stock, PDO::PARAM_INT);
    $stmt_update->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    
    if (!$stmt_update->execute()) {
        throw new Exception('Error al actualizar el stock');
    }
    
    // 3. Registrar el movimiento en la tabla movimientos_inventario
    $sql_log = "INSERT INTO movimientos_inventario 
                (Id_Producto, Tipo_Movimiento, Cantidad, Stock_Anterior, Stock_Nuevo, Notas, Usuario_Id, Fecha_Movimiento) 
                VALUES (:id_producto, 'INGRESO', :cantidad, :stock_anterior, :stock_nuevo, :notas, :usuario_id, NOW())";
    
    $stmt_log = $pdo->prepare($sql_log);
    $stmt_log->bindParam(':id_producto', $id_producto, PDO::PARAM_INT);
    $stmt_log->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt_log->bindParam(':stock_anterior', $stock_anterior, PDO::PARAM_INT);
    $stmt_log->bindParam(':stock_nuevo', $nuevo_stock, PDO::PARAM_INT);
    $stmt_log->bindParam(':notas', $notas, PDO::PARAM_STR);
    $stmt_log->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    
    if (!$stmt_log->execute()) {
        throw new Exception('Error al registrar el movimiento');
    }
    
    // 4. Confirmar transacción
    $pdo->commit();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'mensaje' => 'Stock actualizado y movimiento registrado correctamente',
        'data' => [
            'producto' => $producto['Nombre_Producto'],
            'stock_anterior' => $stock_anterior,
            'cantidad_agregada' => $cantidad,
            'nuevo_stock' => $nuevo_stock,
            'usuario_id' => $usuario_id
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    // Revertir transacción en caso de error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error de base de datos: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

exit;
?>