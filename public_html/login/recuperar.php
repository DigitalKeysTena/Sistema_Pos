
<?php

session_start();

define("BASE_PATH", dirname(__DIR__, 2));  // Sube 2 niveles desde /public_html/login

require_once BASE_PATH . "/src/config/app_config.php";
require_once BASE_PATH . "/src/config/conection.php";
require_once BASE_PATH . "/src/security/csrf.php";

// Mensajes de recuperación
$message = $_SESSION['recover_msg'] ?? null;
unset($_SESSION['recover_msg']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta para SEO + Lighthouse -->
    <meta name="description" content="Recupera tu contraseña de forma segura ingresando tu correo o nombre de usuario.">
    <title>Recuperar contraseña</title>

    <!-- CSS optimizado -->
    <link rel="stylesheet" href="./css/recupera.css?v=2.3">
</head>
<body>
<main class="login-card">

    <img src="./img/logo_110.webp"
         srcset="./img/logo_110.webp 1x, ./img/logo_220.webp 2x"
         width="110"
         height="110"
         class="logo"
         alt="Logo Chingu Market">

    <h1>Recuperar contraseña</h1>

    <p class="subtitle">
        Ingresa tu correo electrónico para enviarte un código de verificación.
    </p>

    <?php if (!empty($message)): ?>
        <div class="alert">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form action="/api/login/recuperar.php"

          method="POST"
          autocomplete="off"
          novalidate>

        <?= csrf_field() ?>

        <div class="input-box">
            <input type="text"
                   id="email_or_user"
                   name="email_or_user"
                   required
                   maxlength="150"
                   autocomplete="off">
            <label for="email_or_user">Correo Electrónico</label>
        </div>

        <button class="btn" type="submit">Enviar código</button>

        <a href="./login.php" class="forgot">← Volver al inicio de sesión</a>
    </form>

</main>

</body>
</html>