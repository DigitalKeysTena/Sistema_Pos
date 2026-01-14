<?php
session_start();

// Si el usuario no está logueado, redirigir al login
if (!isset($_SESSION['usuario']) || empty($_SESSION['usuario'])) {
    header("Location: ./login/login.php");
    exit();
}
?>