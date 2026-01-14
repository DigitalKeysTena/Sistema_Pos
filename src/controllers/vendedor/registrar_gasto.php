<?php
// src/controllers/vendedor/registrar_gasto.php
// VERSIÓN FINAL - Guarda Id_Apertura_Caja
session_start();
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/auth.php';

require_role([2, 1]);

header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$datos = json_decode($json, true);

if (!$datos) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Datos inválidos'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $usuario_id = $_SESSION['Id_Login_Usuario'];
    
    $concepto = trim($datos['concepto'] ?? '');
    $monto = floatval($datos['monto'] ?? 0);
    $categoria = trim($datos['categoria'] ?? '');
    $observaciones = trim($datos['observaciones'] ?? '');
    
    // Validar
    if (empty($concepto)) {
        throw new Exception('El concepto es obligatorio');
    }
    
    if ($monto <= 0) {
        throw new Exception('El monto debe ser mayor a 0');
    }
    
    // ====================================
    // OBTENER APERTURA ACTUAL
    // ====================================
    $id_apertura = $_SESSION['Id_Apertura_Actual'] ?? null;
    
    if (!$id_apertura) {
        // Buscar apertura abierta
        $sql_apertura = "
            SELECT Id_Apertura
            FROM apertura_caja
            WHERE Id_Usuario = ?
            AND Estado = 'ABIERTA'
            ORDER BY Fecha_Apertura DESC
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql_apertura);
        $stmt->execute([$usuario_id]);
        $apertura = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$apertura) {
            throw new Exception('No hay caja abierta. Abre una caja primero.');
        }
        
        $id_apertura = $apertura['Id_Apertura'];
        $_SESSION['Id_Apertura_Actual'] = $id_apertura;
    }
    
    // ====================================
    // INSERTAR GASTO CON Id_Apertura_Caja
    // ====================================
    $sql = "
        INSERT INTO gastos_caja (
            Id_Usuario,
            Id_Apertura_Caja,
            Fecha_Gasto,
            Concepto,
            Monto,
            Categoria,
            Observaciones
        ) VALUES (?, ?, NOW(), ?, ?, ?, ?)
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $usuario_id,
        $id_apertura,
        $concepto,
        $monto,
        $categoria,
        $observaciones
    ]);
    
    $gasto_id = $pdo->lastInsertId();
    
    error_log("INFO - Gasto registrado: ID $gasto_id, Apertura: $id_apertura, Monto: $monto");
    
    echo json_encode([
        'success' => true,
        'message' => 'Gasto registrado correctamente',
        'gasto_id' => $gasto_id,
        'apertura_id' => $id_apertura
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("ERROR registrar_gasto: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>