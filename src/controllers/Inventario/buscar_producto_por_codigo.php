<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth_fixed.php';

// Verificar autenticación
if (!is_authenticated()) {
    echo json_encode([
        'success' => false,
        'error' => 'No autenticado'
    ]);
    exit;
}

try {
    $codigo = $_GET['codigo'] ?? '';
    
    if (empty($codigo)) {
        echo json_encode([
            'success' => false,
            'encontrado' => false,
            'error' => 'Código no proporcionado'
        ]);
        exit;
    }
    
    // 🔍 BUSCAR EN AMBAS COLUMNAS:
    // - Codigo_Barras (código EAN-13 generado automáticamente)
    // - Codigo_Producto (código interno que se imprime en etiquetas)
    
    $sql = "
        SELECT 
            i.Id_Inventario,
            i.Nombre_Producto,
            i.Codigo_Producto,
            i.Codigo_Barras,
            i.Stock_Producto,
            i.Precio_Venta_Producto,
            i.Precio_Compra_Producto,
            i.Margen_Utilidad,
            i.Fecha_Caducidad,
            c.Tipo_Categoria,
            dc.Descrip_Categoria
        FROM inventario i
        LEFT JOIN categoria c ON i.Id_Inventario_Categoria = c.Id_Categoria
        LEFT JOIN descripcion_categoria dc ON i.Id_Tipo_Categoria = dc.Id_Descripcion_Categoria
        WHERE i.Codigo_Barras = ? 
           OR i.Codigo_Producto = ?
        LIMIT 1
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo, $codigo]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($producto) {
        // Producto encontrado
        
        // Determinar qué código se usó para encontrarlo
        $encontrado_por = '';
        if ($producto['Codigo_Barras'] === $codigo) {
            $encontrado_por = 'codigo_barras';
        } elseif ($producto['Codigo_Producto'] === $codigo) {
            $encontrado_por = 'codigo_interno';
        }
        
        echo json_encode([
            'success' => true,
            'encontrado' => true,
            'encontrado_por' => $encontrado_por,
            'producto' => [
                'id' => $producto['Id_Inventario'],
                'nombre' => $producto['Nombre_Producto'],
                'codigo_producto' => $producto['Codigo_Producto'],
                'codigo_barras' => $producto['Codigo_Barras'],
                'stock' => (int)$producto['Stock_Producto'],
                'precio_venta' => (float)$producto['Precio_Venta_Producto'],
                'precio_compra' => (float)$producto['Precio_Compra_Producto'],
                'margen' => (float)$producto['Margen_Utilidad'],
                'fecha_caducidad' => $producto['Fecha_Caducidad'],
                'categoria' => $producto['Tipo_Categoria'],
                'tipo' => $producto['Descrip_Categoria']
            ]
        ]);
        
        // Log de búsqueda exitosa
        app_log('Producto encontrado por código', 'INFO', [
            'codigo_buscado' => $codigo,
            'encontrado_por' => $encontrado_por,
            'producto_id' => $producto['Id_Inventario'],
            'producto_nombre' => $producto['Nombre_Producto'],
            'usuario_id' => $_SESSION['Id_Login_Usuario'] ?? null
        ]);
        
    } else {
        // Producto NO encontrado
        echo json_encode([
            'success' => true,
            'encontrado' => false,
            'codigo_buscado' => $codigo,
            'mensaje' => 'No se encontró ningún producto con este código'
        ]);
        
        // Log de búsqueda sin resultados
        app_log('Producto no encontrado', 'WARNING', [
            'codigo_buscado' => $codigo,
            'usuario_id' => $_SESSION['Id_Login_Usuario'] ?? null
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Error buscando producto: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Error en la base de datos',
        'mensaje' => 'Ocurrió un error al buscar el producto'
    ]);
}
?>