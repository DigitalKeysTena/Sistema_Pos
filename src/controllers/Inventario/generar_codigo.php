<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/conection.php';

try {
    $nombre = $_GET['nombre'] ?? '';
    
    if (empty($nombre)) {
        echo json_encode([
            'success' => false,
            'error' => 'Nombre de producto requerido'
        ]);
        exit;
    }
    
    // ==========================================
    // 1️⃣ GENERAR CÓDIGO INTERNO DEL PRODUCTO
    // ==========================================
    $fecha = date('Ymd');
    $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    $codigoProducto = "PROD-{$fecha}-{$random}";
    
    // Verificar que el código interno no exista
    $checkCodigo = $pdo->prepare("SELECT Id_Inventario FROM inventario WHERE Codigo_Producto = ?");
    $checkCodigo->execute([$codigoProducto]);
    
    if ($checkCodigo->fetch()) {
        // Si existe, generar otro
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $codigoProducto = "PROD-{$fecha}-{$random}";
    }
    
    // ==========================================
    // 2️⃣ GENERAR CÓDIGO DE BARRAS EAN-13
    // ==========================================
    
    /**
     * Formato EAN-13: 13 dígitos
     * - Primeros 3: Prefijo país (750 = México, 977-978 = Publicaciones, 200-299 = Uso interno)
     * - Siguientes 9: Código del producto
     * - Último 1: Dígito verificador (checksum)
     */
    
    function generarCodigoBarrasEAN13($pdo) {
        // Usar prefijo 200-299 para códigos internos (no conflicto con códigos reales)
        $prefijo = '200';
        
        // Generar 9 dígitos únicos para el producto
        $intentos = 0;
        $maxIntentos = 100;
        
        do {
            // Generar 9 dígitos aleatorios
            $codigoProducto = str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
            $codigoSinCheck = $prefijo . $codigoProducto;
            
            // Calcular dígito verificador EAN-13
            $digitoVerificador = calcularDigitoVerificadorEAN13($codigoSinCheck);
            $codigoBarras = $codigoSinCheck . $digitoVerificador;
            
            // Verificar que no exista en la base de datos
            $check = $pdo->prepare("SELECT Id_Inventario FROM inventario WHERE Codigo_Barras = ?");
            $check->execute([$codigoBarras]);
            
            if (!$check->fetch()) {
                return $codigoBarras; // Código único encontrado
            }
            
            $intentos++;
        } while ($intentos < $maxIntentos);
        
        // Si después de 100 intentos no encontramos uno único, usar timestamp
        $timestamp = time();
        $codigoProducto = str_pad(substr($timestamp, -9), 9, '0', STR_PAD_LEFT);
        $codigoSinCheck = $prefijo . $codigoProducto;
        $digitoVerificador = calcularDigitoVerificadorEAN13($codigoSinCheck);
        
        return $codigoSinCheck . $digitoVerificador;
    }
    
    /**
     * Calcular dígito verificador para EAN-13
     * Algoritmo estándar EAN
     */
    function calcularDigitoVerificadorEAN13($codigo) {
        if (strlen($codigo) != 12) {
            throw new Exception("El código debe tener exactamente 12 dígitos");
        }
        
        $suma = 0;
        
        // Sumar posiciones impares (1, 3, 5, 7, 9, 11) - índices 0, 2, 4, 6, 8, 10
        for ($i = 0; $i < 12; $i += 2) {
            $suma += (int)$codigo[$i];
        }
        
        // Sumar posiciones pares (2, 4, 6, 8, 10, 12) multiplicado por 3 - índices 1, 3, 5, 7, 9, 11
        for ($i = 1; $i < 12; $i += 2) {
            $suma += (int)$codigo[$i] * 3;
        }
        
        // Calcular dígito verificador
        $modulo = $suma % 10;
        $digitoVerificador = ($modulo == 0) ? 0 : (10 - $modulo);
        
        return $digitoVerificador;
    }
    
    // ==========================================
    // 3️⃣ GENERAR CÓDIGO DE BARRAS
    // ==========================================
    $codigoBarras = generarCodigoBarrasEAN13($pdo);
    
    // ==========================================
    // 4️⃣ VERIFICAR PRODUCTO DUPLICADO
    // ==========================================
    function normalizar($texto) {
        $texto = trim($texto);
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = str_replace(['-', '_'], ' ', $texto);
        return mb_strtoupper($texto, 'UTF-8');
    }
    
    $nombreNormalizado = normalizar($nombre);
    
    $checkNombre = $pdo->prepare("
        SELECT 
            Codigo_Producto, 
            Nombre_Producto,
            Codigo_Barras
        FROM inventario 
        WHERE UPPER(
                TRIM(
                    REPLACE(
                        REPLACE(Nombre_Producto, '-', ' '), 
                    '_', ' ')
                )
              ) = UPPER(TRIM(?))
        LIMIT 1
    ");
    
    $checkNombre->execute([$nombreNormalizado]);
    $productoExistente = $checkNombre->fetch(PDO::FETCH_ASSOC);
    
    if ($productoExistente) {
        // Producto ya existe
        echo json_encode([
            'existe' => true,
            'codigo' => $productoExistente['Codigo_Producto'],
            'nombre' => $productoExistente['Nombre_Producto'],
            'barcode' => $productoExistente['Codigo_Barras']
        ]);
    } else {
        // Producto NO existe - devolver ambos códigos generados
        echo json_encode([
            'existe' => false,
            'codigo' => $codigoProducto,
            'barcode' => $codigoBarras,
            'success' => true
        ]);
    }
    
} catch (Exception $e) {
    error_log("Error generando código: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Error al generar códigos: ' . $e->getMessage()
    ]);
}
?>