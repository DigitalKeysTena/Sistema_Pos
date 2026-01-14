<?php
session_start();

// Cargar configuración y seguridad
require_once __DIR__ . '/../../src/config/app_config.php';
require_once __DIR__ . '/../../src/config/email_config.php';
require_once __DIR__ . '/../../src/security/csrf.php';

/* ============================================
   ⭐ Recuperar variables de sesión
   ============================================ */
$lock_remaining = $_SESSION['lock_remaining'] ?? null;
$login_message  = $_SESSION['login_message'] ?? null;
$show_captcha   = $_SESSION['show_captcha'] ?? false;

/* Borrar después de leer */
unset($_SESSION['lock_remaining'], $_SESSION['login_message'], $_SESSION['show_captcha']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Login | Chingu Market</title>

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="./img/fabicon.ico">

  <!-- Descripción SEO -->
  <meta name="description" content="Accede de forma segura a Chingu Market. Sistema de autenticación con verificación y protección contra ataques por fuerza bruta.">

  <!-- Mejoras recomendadas por Lighthouse -->
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="Referrer-Policy" content="no-referrer">
  <meta http-equiv="Permissions-Policy" content="geolocation=()">

  <!-- Optimización Google Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://fonts.googleapis.com">

  <link rel="preload"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
        as="style">

  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap"
        media="print"
        onload="this.media='all'">

  <noscript>
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  </noscript>

  <!-- CSS principal -->
  <link rel="preload"
      href="./css/login.min.css"
      as="style">

  <link rel="stylesheet"
      href="./css/login.min.css"
      media="print"
      onload="this.media='all'">

  <noscript>
    <link rel="stylesheet" href="./css/login.min.css">
  </noscript>

</head>

<body>

  <main class="login-card" aria-label="Formulario de inicio de sesión">

    <img src="./img/logo_small_150.webp"
     width="110"
     alt="Logo de Chingu Market"
     class="logo"
     loading="lazy"
     decoding="async">

    <h1 id="form-title">Bienvenido</h1>

    <form action="/api/login/login.php"
          method="POST"
          aria-labelledby="form-title"
          autocomplete="off"
          novalidate>

      <!-- ⭐ CSRF TOKEN -->
      <?= csrf_field() ?>

      <!-- USUARIO -->
      <div class="input-box">
        <input type="text"
               id="usuario"
               name="usuario"
               required
               maxlength="50"
               autocomplete="username">
        <label for="usuario">Usuario</label>
      </div>

      <!-- CONTRASEÑA -->
      <div class="input-box">
        <input type="password"
               id="password"
               name="password"
               required
               maxlength="100"
               autocomplete="current-password">
        <label for="password">Contraseña</label>

        <!-- BOTÓN MOSTRAR / OCULTAR CONTRASEÑA -->
        <button type="button"
                id="togglePassword"
                class="toggle"
                aria-label="Mostrar u ocultar contraseña"
                aria-pressed="false">
          👁️
        </button>
      </div>

      <!-- CAPTCHA -->
      <div class="captcha-space">
        <?php if ($show_captcha): ?>
          <div class="g-recaptcha captcha-animated"
               data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY, ENT_QUOTES, 'UTF-8') ?>">
          </div>
        <?php endif; ?>
      </div>

      <!-- BOTÓN -->
      <button type="submit" class="btn">Entrar</button>

      <!-- RECUPERAR CONTRASEÑA -->
      <a href="recuperar.php" class="forgot">¿Olvidaste tu contraseña?</a>

    </form>

    <!-- ALERTAS -->
    <?php if (!empty($login_message)): ?>
      <div class="alert"><?= htmlspecialchars($login_message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($lock_remaining)): ?>
      <div id="countdown"
           class="alert-timer"
           data-seconds="<?= (int)$lock_remaining ?>">
        ⏳ Tiempo restante: <span id="timer-display"></span>
      </div>
    <?php endif; ?>

  </main>

  <!-- Cargar reCAPTCHA solo si se necesita -->
  <?php if ($show_captcha): ?>
    <script src="https://www.google.com/recaptcha/api.js" defer></script>
  <?php endif; ?>

  <!-- JS -->
  <script src="./js/login.js" defer></script>
  <script src="./js/tiempo_atras_bloque.js" defer></script>

</body>
</html>