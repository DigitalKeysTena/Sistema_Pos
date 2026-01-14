<?php
session_start();

// ⭐ Cargar configuración centralizada
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/conection.php';
require_once __DIR__ . '/../../security/csrf.php';
require_once __DIR__ . '../../../../public_html/utils/mailer.php';

// ⭐ Validar CSRF
try {
    CSRF::validateRequest();
} catch (Exception $e) {
    $_SESSION['recover_msg'] = "Error de seguridad. Intenta nuevamente.";
    redirect('/login/recuperar.php');
    exit;
}

$email_input = trim($_POST['email_or_user'] ?? '');

if ($email_input === '') {
    $_SESSION['recover_msg'] = "⚠️ Ingresa tu correo.";
    redirect('/login/recuperar.php');
    exit;
}

// Validar formato de email
if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['recover_msg'] = "⚠️ Formato de correo inválido.";
    redirect('/login/recuperar.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT Id_Usuario, Correo_Usuario, Nombre_Usuario
    FROM usuario
    WHERE Correo_Usuario = :email
    LIMIT 1
");
$stmt->execute([':email' => $email_input]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['recover_msg'] = "❌ Correo no registrado.";
    redirect('/login/recuperar.php');
    exit;
}

$username = $user['Nombre_Usuario'];
$email = $user['Correo_Usuario'];

$token   = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

$pdo->prepare("DELETE FROM password_reset WHERE email = :email")
    ->execute([':email' => $email]);

$pdo->prepare("
    INSERT INTO password_reset (email, token, expires_at)
    VALUES (:email, :token, :exp)
")->execute([
    ':email' => $email,
    ':token' => $token,
    ':exp'   => $expires
]);

send_recovery_code($email, $username, $token);

/* 🔥 Guardamos el email solo en sesión */
$_SESSION['reset_email'] = $email;

$_SESSION['recover_msg'] = "✅ Código enviado a tu correo.";

/* 🔥 Redirige usando sistema centralizado */
redirect('/login/validar_codigo.php');
exit;