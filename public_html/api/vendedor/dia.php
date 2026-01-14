<?php
// test_session.php - Subir a public_html/
session_start();
echo "<pre>";
echo "SESSION DATA:\n";
print_r($_SESSION);
echo "\n\nBUSCANDO EN BD:\n";

require_once __DIR__ . '/src/config/conection.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE Id_Usuario = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($user);
}
echo "</pre>";
?>