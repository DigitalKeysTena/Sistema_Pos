<?php
/**
 * Archivo: config/conexion.php
 * Descripción: Conexión centralizada con PDO para toda la aplicación
 */

$host = "localhost";
$dbname = "ccgardco_naye";
$username = "ccgardco_Naye";
$password = "}HEh2;BiB]rV";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Conexión exitosa"; // ← descomentar solo para pruebas
} catch (PDOException $e) {
    die("❌ Error al conectar a la base de datos: " . $e->getMessage());
}
?>
