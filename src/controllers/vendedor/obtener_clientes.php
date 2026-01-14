<?php
// api/vendedor/obtener_clientes.php
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
    // ✅ COLUMNAS CORRECTAS según tu base de datos
    $sql = "SELECT 
                Id_Clientes as id, 
                CONCAT(Nombre_Cliente, ' ', Apellido_Cliente) as nombre, 
                Cedula_Cliente as cedula,
                Telefono_Cliente as telefono,
                Correo_Cliente as correo
            FROM clientes 
            ORDER BY Nombre_Cliente ASC";
    
    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }
    
    $clientes = [];
    while ($row = $result->fetch_assoc()) {
        $clientes[] = [
            'id' => (int)$row['id'],
            'nombre' => trim($row['nombre']),
            'cedula' => $row['cedula'],
            'telefono' => $row['telefono'],
            'correo' => $row['correo']
        ];
    }
    
    $conn->close();
    
    echo json_encode([
        'success' => true,
        'clientes' => $clientes,
        'total' => count($clientes)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    error_log("Error en obtener_clientes.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>