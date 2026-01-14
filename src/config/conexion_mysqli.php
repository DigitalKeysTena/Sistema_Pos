<?php
/**
 * Archivo: config/conexion_mysqli.php
 * Descripción: Conexión MySQLi para compatibilidad con sistema de ventas
 */

$host = "localhost";
$dbname = "ccgardco_naye";
$username = "ccgardco_Naye";
$password = "}HEh2;BiB]rV";


// Crear conexión MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");

// Variable disponible: $conn
?>