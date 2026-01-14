<?php
/**
 * BUSCAR PRODUCTO - Versión Corregida Final
 * Ubicación: /api/vendedor/buscar_producto.php
 * 
 * Funcionalidades:
 * - Búsqueda por código de barras
 * - Búsqueda por nombre/código de producto
 * - Filtrado por stock disponible
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

try {
    // ===================================
    // 1. VALIDAR SESIÓN
    // ===================================
    if (!isset($_SESSION['Id_Login_Usuario'])) {
        throw new Exception('Sesión no válida');
    }

    // ===================================
    // 2. OBTENER PARÁMETROS
    // ===================================
    $codigo = $_GET['codigo'] ?? null;
    $query = $_GET['query'] ?? null;
    $sugerencias = isset($_GET['sugerencias']);
    
    if (!$codigo && !$query) {
        throw new Exception('Parámetros inválidos. Envía "codigo" o "query"');
    }
    
    // ===================================
    // 3. CONECTAR A BASE DE DATOS
    // ===================================
    require_once __DIR__ . '/../../config/conexion_mysqli.php';

    if (!$conn || $conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos");
    }
    
    // ========================================
    // 4. BÚSQUEDA POR CÓDIGO DE BARRAS
    // ========================================
    if ($codigo) {
        $sql = "SELECT 
                    i.Id_Inventario as id,
                    i.Nombre_Producto as nombre,
                    i.Codigo_Producto as codigo,
                    i.Codigo_Barras as codigo_barras,
                    i.Precio_Venta_Producto as precio_venta,
                    i.Stock_Producto as stock,
                    i.Precio_Compra_Producto as precio_compra,
                    c.Tipo_Categoria as categoria
                FROM inventario i
                LEFT JOIN categoria c ON i.Id_Inventario_Categoria = c.Id_Categoria
                WHERE (i.Codigo_Barras = ? OR i.Codigo_Producto = ?)
                AND i.Stock_Producto > 0
                LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $conn->error);
        }
        
        $stmt->bind_param('ss', $codigo, $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();
        
        if ($producto) {
            // Log de búsqueda exitosa
            error_log("Producto encontrado por código: {$codigo} - ID: {$producto['id']}");
            
            echo json_encode([
                'success' => true,
                'encontrado' => true,
                'producto' => [
                    'id' => (int)$producto['id'],
                    'nombre' => $producto['nombre'],
                    'codigo' => $producto['codigo'],
                    'codigo_barras' => $producto['codigo_barras'],
                    'precio_venta' => (float)$producto['precio_venta'],
                    'stock' => (int)$producto['stock'],
                    'categoria' => $producto['categoria']
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => true,
                'encontrado' => false,
                'mensaje' => 'Producto no encontrado o sin stock'
            ], JSON_UNESCAPED_UNICODE);
        }
        
        $conn->close();
        exit;
    }
    
    // ========================================
    // 5. BÚSQUEDA POR NOMBRE O CÓDIGO
    // ========================================
    if ($query) {
        $sql = "SELECT 
                    i.Id_Inventario as id,
                    i.Nombre_Producto as nombre,
                    i.Codigo_Producto as codigo,
                    i.Codigo_Barras as codigo_barras,
                    i.Precio_Venta_Producto as precio_venta,
                    i.Stock_Producto as stock,
                    i.Precio_Compra_Producto as precio_compra,
                    c.Tipo_Categoria as categoria
                FROM inventario i
                LEFT JOIN categoria c ON i.Id_Inventario_Categoria = c.Id_Categoria
                WHERE (
                    i.Nombre_Producto LIKE ? 
                    OR i.Codigo_Producto LIKE ?
                    OR i.Codigo_Barras LIKE ?
                )
                AND i.Stock_Producto > 0
                ORDER BY i.Nombre_Producto ASC
                LIMIT 20";
        
        $searchTerm = '%' . $query . '%';
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error preparando consulta: " . $conn->error);
        }
        
        $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $productos = [];
        
        while ($row = $result->fetch_assoc()) {
            $productos[] = [
                'id' => (int)$row['id'],
                'nombre' => $row['nombre'],
                'codigo' => $row['codigo'],
                'codigo_barras' => $row['codigo_barras'],
                'precio_venta' => (float)$row['precio_venta'],
                'stock' => (int)$row['stock'],
                'categoria' => $row['categoria']
            ];
        }
        
        $stmt->close();
        $conn->close();
        
        echo json_encode([
            'success' => true,
            'productos' => $productos,
            'total' => count($productos),
            'query' => $query
        ], JSON_UNESCAPED_UNICODE);
        
        exit;
    }
    
} catch (Exception $e) {
    // ===================================
    // 6. MANEJO DE ERRORES
    // ===================================
    
    error_log("ERROR en buscar_producto.php: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>