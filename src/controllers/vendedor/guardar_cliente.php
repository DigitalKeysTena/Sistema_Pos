<?php
// api/vendedor/guardar_cliente.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['Id_Login_Usuario'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Sesión no válida'
    ]);
    exit;
}

require_once __DIR__ . '/../../config/conexion_mysqli.php';

try {
    $input = file_get_contents('php://input');
    $datos = json_decode($input, true);
    
    if (!is_array($datos)) {
        throw new Exception('Datos JSON inválidos');
    }
    
    $nombreCompleto = trim($datos['nombre'] ?? '');
    $cedula = trim($datos['cedula'] ?? '');
    $telefono = trim($datos['telefono'] ?? '');
    $email = trim($datos['email'] ?? '');
    
    if (empty($nombreCompleto)) {
        throw new Exception('El nombre es obligatorio');
    }
    
    // Separar nombre y apellido
    $partes = explode(' ', $nombreCompleto, 2);
    $nombre = $partes[0];
    $apellido = isset($partes[1]) ? $partes[1] : '';
    
    // Convertir cedula y telefono a enteros (tu BD usa INT)
    $cedulaInt = intval($cedula);
    $telefonoInt = intval($telefono);
    
    // ✅ COLUMNAS CORRECTAS según tu base de datos
    $sql = "INSERT INTO clientes 
            (Nombre_Cliente, Apellido_Cliente, Cedula_Cliente, Telefono_Cliente, Correo_Cliente) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Error preparando consulta: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'ssiis',
        $nombre,       // s - string
        $apellido,     // s - string
        $cedulaInt,    // i - integer
        $telefonoInt,  // i - integer
        $email         // s - string
    );
    
    if ($stmt->execute()) {
        $clienteId = $stmt->insert_id;
        
        echo json_encode([
            'success' => true,
            'cliente_id' => $clienteId,
            'mensaje' => 'Cliente guardado correctamente'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        throw new Exception('Error al guardar: ' . $stmt->error);
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    error_log("Error en guardar_cliente.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>