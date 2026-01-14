<?php
header("Content-Type: application/json; charset=utf-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = dirname(__DIR__, 2);
$configPath = $basePath . "/../src/config/conection.php";

$resultado = [
    "paso1" => "Script iniciado",
    "config_path" => $configPath,
    "config_existe" => file_exists($configPath)
];

try {
    require_once $configPath;
    
    $resultado["paso2"] = "Archivo cargado";
    $resultado["pdo_existe"] = isset($pdo);
    
    if (isset($pdo)) {
        // Probar consulta simple
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM inventario");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        $resultado["paso3"] = "Consulta exitosa";
        $resultado["total_productos"] = $count['total'];
        
        // Traer 2 productos de prueba
        $stmt2 = $pdo->query("SELECT Id_Inventario, Nombre_Producto FROM inventario LIMIT 2");
        $resultado["productos_muestra"] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    
} catch (Exception $e) {
    $resultado["error"] = $e->getMessage();
    $resultado["linea"] = $e->getLine();
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);