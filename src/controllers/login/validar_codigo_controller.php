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
    $_SESSION['otp_msg'] = "Error de seguridad. Intenta nuevamente.";
    redirect('/login/validar_codigo.php');
    exit;
}

$email = $_POST['email'] ?? '';
$otp   = trim($_POST['otp'] ?? '');

if ($email === '' || $otp === '') {
    $_SESSION['otp_msg'] = "⚠️ Código inválido.";
    redirect('/login/validar_codigo.php');
    exit;
}

// Validar que el código sea numérico y de 6 dígitos
if (!preg_match('/^\d{6}$/', $otp)) {
    $_SESSION['otp_msg'] = "⚠️ El código debe ser de 6 dígitos.";
    redirect('/login/validar_codigo.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT token, expires_at
    FROM password_reset
    WHERE email = :email
    LIMIT 1
");
$stmt->execute([':email' => $email]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    $_SESSION['otp_msg'] = "❌ No existe una solicitud activa.";
    redirect('/login/recuperar.php');
    exit;
}

if ($data['expires_at'] < date('Y-m-d H:i:s')) {
    $_SESSION['otp_msg'] = "⛔ El código expiró.";
    redirect('/login/recuperar.php');
    exit;
}

if ($data['token'] !== $otp) {
    $_SESSION['otp_msg'] = "❌ Código incorrecto.";
    redirect('/login/validar_codigo.php');
    exit;
}

// ✔ Código correcto
$_SESSION['reset_email'] = $email;

redirect('/login/nueva_contrasena.php');
exit;