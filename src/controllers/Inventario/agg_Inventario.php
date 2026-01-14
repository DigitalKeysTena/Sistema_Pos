<?php
session_start();

// Cargar configuración y seguridad
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/csrf.php';
require_once __DIR__ . '/../../security/auth_fixed.php';

// Verificar autenticación y rol
require_role([3, 1]); // Solo inventario y admin

/* ============================================================
   🔒 VALIDAR MÉTODO POST
============================================================ */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    security_log('Invalid request method to add product', [
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
    
    $_SESSION['swal'] = [
        "icon"  => "error",
        "title" => "Método no permitido",
        "text"  => "Solo se permiten peticiones POST",
        "timer" => 2000
    ];
    redirect(url('/model/inventario/php/agg_Productos.php'));
}

/* ============================================================
   🔒 VALIDAR CSRF TOKEN
============================================================ */
try {
    CSRF::validateRequest();
} catch (Exception $e) {
    $_SESSION['swal'] = [
        "icon"  => "error",
        "title" => "Error de Seguridad",
        "text"  => $e->getMessage(),
        "timer" => 3000
    ];
    redirect(url('/model/inventario/php/agg_Productos.php'));
}

/* ============================================================
   🧼 RECIBIR Y VALIDAR DATOS
============================================================ */
// Función para sanitizar entrada
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

$Id_Categoria             = filter_input(INPUT_POST, 'Id_Categoria', FILTER_VALIDATE_INT);
$Id_Descripcion_Categoria = filter_input(INPUT_POST, 'Id_Descripcion_Categoria', FILTER_VALIDATE_INT);
$Nombre_Producto          = sanitize_input($_POST['Nombre_Producto'] ?? '');
$Codigo_Barras            = trim($_POST['Codigo_Barras'] ?? ''); // 🆕 NUEVO CAMPO
$Margen_Utilidad          = filter_input(INPUT_POST, 'Margen_Utilidad', FILTER_VALIDATE_FLOAT);
$Precio_Compra_Producto   = filter_input(INPUT_POST, 'Precio_Compra_Producto', FILTER_VALIDATE_FLOAT);
$Precio_Venta_Producto    = filter_input(INPUT_POST, 'Precio_Venta_Producto', FILTER_VALIDATE_FLOAT);
$Stock_Producto           = filter_input(INPUT_POST, 'Stock_Producto', FILTER_VALIDATE_INT);
$Fecha_Entrada            = sanitize_input($_POST['Fecha_Entrada'] ?? '');
$Fecha_Caducidad          = sanitize_input($_POST['Fecha_Caducidad'] ?? '');
$Codigo_Producto          = sanitize_input($_POST['Codigo_Producto'] ?? '');

/* ============================================================
   ⚠️ VALIDAR CAMPOS OBLIGATORIOS
============================================================ */
$errores = [];

if (!$Id_Categoria || $Id_Categoria < 1) {
    $errores[] = "Categoría inválida";
}

if (empty($Nombre_Producto) || strlen($Nombre_Producto) < 3) {
    $errores[] = "Nombre de producto debe tener al menos 3 caracteres";
}

if (strlen($Nombre_Producto) > 50) {
    $errores[] = "Nombre de producto no puede exceder 50 caracteres";
}

// 🆕 VALIDAR CÓDIGO DE BARRAS (OPCIONAL)
if (!empty($Codigo_Barras)) {
    // Validar longitud mínima
    if (strlen($Codigo_Barras) < 8) {
        $errores[] = "Código de barras debe tener al menos 8 caracteres";
    }
    
    // Validar que solo contenga números y guiones
    if (!preg_match('/^[0-9\-]+$/', $Codigo_Barras)) {
        $errores[] = "Código de barras solo puede contener números y guiones";
    }
    
    // Validar longitud máxima
    if (strlen($Codigo_Barras) > 50) {
        $errores[] = "Código de barras no puede exceder 50 caracteres";
    }
}

if (!$Margen_Utilidad || $Margen_Utilidad < 0 || $Margen_Utilidad >= 100) {
    $errores[] = "Margen de utilidad inválido (0-99)";
}

if (!$Precio_Compra_Producto || $Precio_Compra_Producto <= 0) {
    $errores[] = "Precio de compra inválido";
}

if (!$Precio_Venta_Producto || $Precio_Venta_Producto <= 0) {
    $errores[] = "Precio de venta inválido";
}

if (!$Stock_Producto || $Stock_Producto < 0) {
    $errores[] = "Stock inválido";
}

if (empty($Fecha_Entrada) || !strtotime($Fecha_Entrada)) {
    $errores[] = "Fecha de entrada inválida";
}

if (empty($Fecha_Caducidad) || !strtotime($Fecha_Caducidad)) {
    $errores[] = "Fecha de caducidad inválida";
}

if (empty($Codigo_Producto) || strlen($Codigo_Producto) < 5) {
    $errores[] = "Código de producto inválido";
}

// Validar que la fecha de caducidad sea posterior a la de entrada
if (strtotime($Fecha_Caducidad) < strtotime($Fecha_Entrada)) {
    $errores[] = "Fecha de caducidad debe ser posterior a la fecha de entrada";
}

if (!empty($errores)) {
    security_log('Product validation failed', [
        'errors' => $errores,
        'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
    ]);
    
    $_SESSION['swal'] = [
        "icon"  => "warning",
        "title" => "⚠️ Datos Inválidos",
        "text"  => implode(', ', $errores),
        "timer" => 4000
    ];
    redirect(url('/model/inventario/php/agg_Productos.php'));
}

/* ============================================================
   🔍 FUNCIÓN DE NORMALIZACIÓN (igual que en verificar)
============================================================ */
function normalizar($texto) {
    $texto = trim($texto);
    $texto = preg_replace('/\s+/', ' ', $texto);
    $texto = str_replace(['-', '_'], ' ', $texto);
    return mb_strtoupper($texto, 'UTF-8');
}

/* ============================================================
   ⛔ VALIDAR DUPLICADO CON NORMALIZACIÓN (BACKEND)
============================================================ */
try {
    $nombreNormalizado = normalizar($Nombre_Producto);
    
    // Verificar por nombre normalizado
    $checkSql = "
        SELECT 
            Codigo_Producto, 
            Nombre_Producto 
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
    
    $check = $pdo->prepare($checkSql);
    $check->execute([$nombreNormalizado]);
    $producto_existente = $check->fetch(PDO::FETCH_ASSOC);

    if ($producto_existente) {
        security_log('Duplicate product attempt', [
            'attempted_name' => $Nombre_Producto,
            'existing_code' => $producto_existente['Codigo_Producto'],
            'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
        ]);
        
        $_SESSION['swal'] = [
            "icon"  => "error",
            "title" => "⚠️ Producto Duplicado",
            "text"  => "Este producto ya existe: " . $producto_existente['Nombre_Producto'] . 
                       " (Código: " . $producto_existente['Codigo_Producto'] . ")",
            "timer" => 4000
        ];
        redirect(url('/model/inventario/php/agg_Productos.php'));
    }
    
    // Verificar por código de producto
    $checkCode = $pdo->prepare("SELECT Id_Inventario FROM inventario WHERE Codigo_Producto = ? LIMIT 1");
    $checkCode->execute([$Codigo_Producto]);
    
    if ($checkCode->fetch()) {
        security_log('Duplicate product code attempt', [
            'code' => $Codigo_Producto,
            'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
        ]);
        
        $_SESSION['swal'] = [
            "icon"  => "error",
            "title" => "⚠️ Código Duplicado",
            "text"  => "El código de producto ya está en uso. Genera uno nuevo.",
            "timer" => 3000
        ];
        redirect(url('/model/inventario/php/agg_Productos.php'));
    }
    
    // 🆕 VERIFICAR SI EL CÓDIGO DE BARRAS YA EXISTE (solo si se proporcionó)
    if (!empty($Codigo_Barras)) {
        $checkBarcode = $pdo->prepare("SELECT Id_Inventario, Nombre_Producto FROM inventario WHERE Codigo_Barras = ? LIMIT 1");
        $checkBarcode->execute([$Codigo_Barras]);
        $barcode_existente = $checkBarcode->fetch(PDO::FETCH_ASSOC);
        
        if ($barcode_existente) {
            security_log('Duplicate barcode attempt', [
                'barcode' => $Codigo_Barras,
                'existing_product' => $barcode_existente['Nombre_Producto'],
                'user_id' => $_SESSION['Id_Login_Usuario'] ?? 'unknown'
            ]);
            
            $_SESSION['swal'] = [
                "icon"  => "error",
                "title" => "⚠️ Código de Barras Duplicado",
                "text"  => "Este código de barras ya existe en: " . $barcode_existente['Nombre_Producto'],
                "timer" => 4000
            ];
            redirect(url('/model/inventario/php/agg_Productos.php'));
        }
    }
    
} catch (PDOException $e) {
    app_log('Error checking duplicate product: ' . $e->getMessage(), 'ERROR');
    
    $_SESSION['swal'] = [
        "icon"  => "error",
        "title" => "Error",
        "text"  => "Error al verificar producto duplicado",
        "timer" => 3000
    ];
    redirect(url('/model/inventario/php/agg_Productos.php'));
}

/* ============================================================
   ✔️ INSERTAR PRODUCTO CON TRANSACCIÓN
============================================================ */
try {
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // 🆕 NOTA: Los nombres de columnas en tu BD son diferentes
    // Tu BD usa: Id_Inventario_Categoria e Id_Tipo_Categoria
    // Pero tu formulario envía: Id_Categoria e Id_Descripcion_Categoria
    // Por eso aquí usamos las variables que vienen del formulario
    
    $stmt = $pdo->prepare("
        INSERT INTO inventario (
            Id_Inventario_Categoria,        
            Id_Tipo_Categoria,              
            Nombre_Producto,
            Codigo_Barras,                  
            Margen_Utilidad,
            Precio_Compra_Producto,
            Precio_Venta_Producto,
            Stock_Producto,
            Fecha_Entrada,
            Fecha_Caducidad,
            Codigo_Producto
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // 🆕 Si el código de barras está vacío, insertar NULL
    $codigo_barras_final = empty($Codigo_Barras) ? null : $Codigo_Barras;

    $result = $stmt->execute([
        $Id_Categoria,                  // Id_Inventario_Categoria
        $Id_Descripcion_Categoria,      // Id_Tipo_Categoria (puede ser NULL)
        $Nombre_Producto,
        $codigo_barras_final,           // ✅ CÓDIGO DE BARRAS AGREGADO
        $Margen_Utilidad,
        $Precio_Compra_Producto,
        $Precio_Venta_Producto,
        $Stock_Producto,
        $Fecha_Entrada,
        $Fecha_Caducidad,
        $Codigo_Producto
    ]);

    if ($result) {
        $producto_id = $pdo->lastInsertId();
        
        // Registrar en movimientos de inventario
        $notas = 'Producto creado: ' . $Nombre_Producto;
        if (!empty($Codigo_Barras)) {
            $notas .= ' (Código de barras: ' . $Codigo_Barras . ')';
        }
        
        $stmt_movimiento = $pdo->prepare("
            INSERT INTO movimientos_inventario (
                Id_Producto,
                Tipo_Movimiento,
                Cantidad,
                Stock_Anterior,
                Stock_Nuevo,
                Notas,
                Usuario_Id,
                Fecha_Movimiento
            ) VALUES (?, 'INGRESO', ?, 0, ?, ?, ?, NOW())
        ");
        
        $stmt_movimiento->execute([
            $producto_id,
            $Stock_Producto,
            $Stock_Producto,
            $notas,
            $_SESSION['Id_Login_Usuario']
        ]);
        
        // Commit transacción
        $pdo->commit();
        
        app_log('Product created successfully', 'INFO', [
            'product_id' => $producto_id,
            'product_name' => $Nombre_Producto,
            'barcode' => $codigo_barras_final,
            'user_id' => $_SESSION['Id_Login_Usuario']
        ]);
        
        $_SESSION['producto_guardado'] = [
            "nombre"   => $Nombre_Producto,
            "codigo"   => $Codigo_Producto,
            "barcode"  => $codigo_barras_final,  // 🆕 Agregado
            "stock"    => $Stock_Producto,
            "precio"   => $Precio_Venta_Producto
        ];

        // 🆕 Mensaje mejorado con código de barras
        $mensaje = "El producto se registró correctamente.";
        if (!empty($Codigo_Barras)) {
            $mensaje .= " Código de barras: " . $Codigo_Barras;
        }

        $_SESSION['swal'] = [
            "icon"  => "success",
            "title" => "¡Producto Guardado!",
            "text"  => $mensaje,
            "timer" => 3000
        ];
    }

} catch (PDOException $e) {
    // Rollback en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    app_log('Error inserting product: ' . $e->getMessage(), 'ERROR', [
        'product_name' => $Nombre_Producto,
        'error' => $e->getMessage()
    ]);
    
    $_SESSION['swal'] = [
        "icon"  => "error",
        "title" => "Error",
        "text"  => "Error al guardar el producto: " . $e->getMessage(),
        "timer" => 4000
    ];
}

/* ============================================================
   🔙 REDIRECCIÓN FINAL
============================================================ */
redirect(url('/model/inventario/php/agg_Productos.php'));