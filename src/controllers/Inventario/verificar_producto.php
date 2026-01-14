<?php
require_once __DIR__ . '/../../config/conection.php';
header('Content-Type: application/json; charset=utf-8');

// ============================================
// FUNCIÓN PARA NORMALIZAR TEXTO
// ============================================
function normalizar($texto) {
    $texto = trim($texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    $texto = str_replace(['-', '_'], ' ', $texto);
    $texto = mb_strtoupper($texto, 'UTF-8');
    return $texto;
}

try {
    // ============================================
    // OPCIÓN 1: VERIFICAR POR NOMBRE
    // ============================================
    if (isset($_GET['nombre']) && trim($_GET['nombre']) !== "") {
        $nombreBuscado = normalizar($_GET['nombre']);
        
        $sql = "
            SELECT 
                Nombre_Producto, 
                Codigo_Producto
            FROM inventario
            WHERE UPPER(
                    TRIM(
                        REPLACE(
                            REPLACE(Nombre_Producto, '-', ' '), 
                        '_', ' ')
                    )
                  ) = UPPER(TRIM(?))
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nombreBuscado]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            echo json_encode([
                "existe" => true,
                "tipo" => "nombre",
                "codigo" => $producto['Codigo_Producto'],
                "nombre" => $producto['Nombre_Producto']
            ]);
        } else {
            echo json_encode([
                "existe" => false
            ]);
        }
        exit;
    }
    
    // ============================================
    // OPCIÓN 2: VERIFICAR POR CÓDIGO DE BARRAS
    // ============================================
    if (isset($_GET['codigo_barras']) && trim($_GET['codigo_barras']) !== "") {
        $codigoBarras = trim($_GET['codigo_barras']);
        
        // Validar que sea numérico y de 13 dígitos
        if (!preg_match('/^\d{13}$/', $codigoBarras)) {
            echo json_encode([
                "existe" => false,
                "error" => "Formato inválido"
            ]);
            exit;
        }
        
        $sql = "
            SELECT 
                Nombre_Producto, 
                Codigo_Producto,
                Codigo_Barras
            FROM inventario
            WHERE Codigo_Barras = ?
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$codigoBarras]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($producto) {
            echo json_encode([
                "existe" => true,
                "tipo" => "codigo_barras",
                "codigo_barras" => $producto['Codigo_Barras'],
                "codigo_interno" => $producto['Codigo_Producto'],
                "nombre" => $producto['Nombre_Producto']
            ]);
        } else {
            echo json_encode([
                "existe" => false
            ]);
        }
        exit;
    }
    
    // ============================================
    // SI NO SE ENVIÓ NADA
    // ============================================
    echo json_encode([
        "existe" => false,
        "error" => "Parámetros faltantes"
    ]);
    
} catch (PDOException $e) {
    error_log("Error en verificar_producto.php: " . $e->getMessage());
    
    echo json_encode([
        "existe" => false, 
        "error" => "Error en la consulta"
    ]);
}
?>