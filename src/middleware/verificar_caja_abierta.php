<?php
// src/middleware/verificar_caja_abierta.php
/**
 * Middleware: Verificar Caja Abierta
 * Verifica que el vendedor tenga caja abierta antes de permitir ventas
 */

function verificarCajaAbierta($pdo, $usuario_id) {
    try {
        $fecha_actual = date('Y-m-d');
        
        $sql = "
            SELECT 
                Id_Apertura,
                Monto_Inicial,
                Estado,
                DATE_FORMAT(Fecha_Apertura, '%H:%i') as hora_apertura
            FROM apertura_caja 
            WHERE Id_Usuario = ? 
            AND DATE(Fecha_Apertura) = ?
            AND Estado = 'ABIERTA'
            LIMIT 1
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario_id, $fecha_actual]);
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'tiene_caja_abierta' => ($caja !== false),
            'caja' => $caja
        ];
        
    } catch (PDOException $e) {
        error_log("Error verificando caja: " . $e->getMessage());
        return [
            'tiene_caja_abierta' => false,
            'caja' => null
        ];
    }
}

/**
 * Función helper para usar en las páginas
 */
function requiereCajaAbierta($pdo, $usuario_id, $redirigir = true) {
    $resultado = verificarCajaAbierta($pdo, $usuario_id);
    
    if (!$resultado['tiene_caja_abierta']) {
        if ($redirigir) {
            // Guardar URL actual para redirigir después
            $_SESSION['redirect_after_apertura'] = $_SERVER['REQUEST_URI'];
            
            // Redirigir a apertura de caja
            header('Location: /model/vendedor/php/apertura_caja.php');
            exit;
        }
        return false;
    }
    
    return $resultado['caja'];
}
?>