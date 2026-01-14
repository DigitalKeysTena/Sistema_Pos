<?php
session_start();

// ⭐ Cargar configuración centralizada
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/csrf.php';

// ⭐ Validar CSRF
try {
    CSRF::validateRequest();
} catch (Exception $e) {
    $_SESSION['pass_msg'] = "Error de seguridad. Intenta nuevamente.";
    redirect('/login/nueva_contrasena.php');
    exit;
}

if (!isset($_SESSION['reset_email'])) {
    redirect('/login/recuperar.php');
    exit;
}

$email = $_SESSION['reset_email'];
$pass1 = trim($_POST['pass1'] ?? '');
$pass2 = trim($_POST['pass2'] ?? '');

if ($pass1 === '' || $pass2 === '') {
    $_SESSION['pass_msg'] = "⚠️ Complete los campos.";
    redirect('/login/nueva_contrasena.php');
    exit;
}

// Validar longitud mínima
if (strlen($pass1) < 8) {
    $_SESSION['pass_msg'] = "⚠️ La contraseña debe tener al menos 8 caracteres.";
    redirect('/login/nueva_contrasena.php');
    exit;
}

if ($pass1 !== $pass2) {
    $_SESSION['pass_msg'] = "❌ Las contraseñas no coinciden.";
    redirect('/login/nueva_contrasena.php');
    exit;
}

// Obtener Id_Login por correo
$stmt = $pdo->prepare("
    SELECT login.Id_Login
    FROM login
    INNER JOIN usuario ON usuario.Id_Usuario = login.Id_Login_Usuario
    WHERE usuario.Correo_Usuario = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$loginRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$loginRow) {
    $_SESSION['pass_msg'] = "❌ Error interno.";
    redirect('/login/recuperar.php');
    exit;
}

$loginId = $loginRow['Id_Login'];

// Hash de la contraseña
$hash = password_hash($pass1, PASSWORD_DEFAULT);

// Actualizar password
$pdo->prepare("
    UPDATE login 
    SET Password = :p
    WHERE Id_Login = :id
")->execute([
    ':p' => $hash,
    ':id' => $loginId
]);

// Borrar token
$pdo->prepare("DELETE FROM password_reset WHERE email = :email")
    ->execute([':email' => $email]);

unset($_SESSION['reset_email']);

$_SESSION['login_message'] = "✅ Contraseña actualizada exitosamente.";
 redirect('/login/login.php');
exit;