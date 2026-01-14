<?php
session_start();
require_once __DIR__ . '/../../src/security/csrf.php';

if (!isset($_SESSION['reset_email'])) {
    redirect("recuperar.php"); 
    exit;
}

$email = $_SESSION['reset_email'];
$message = $_SESSION['pass_msg'] ?? null;
unset($_SESSION['pass_msg']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva contraseña</title>
<link rel="stylesheet" href="./css/recupera.css">
</head>
<body>
<main class="login-card">

<h1>Crear nueva contraseña</h1>

<?php if ($message): ?>
<div class="alert"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<form action="/api/login/nueva_contrasena.php" method="POST">
    
    <!-- ⭐ CSRF TOKEN -->
    <?= csrf_field() ?>
    
    <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">

    <div class="input-box">
        <input type="password" name="pass1" required maxlength="100">
        <label>Nueva contraseña</label>
    </div>

    <div class="input-box">
        <input type="password" name="pass2" required maxlength="100">
        <label>Confirmar contraseña</label>
    </div>

    <button class="btn">Actualizar</button>
</form>

</main>
</body>
</html>