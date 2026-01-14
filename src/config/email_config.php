<?php
/**
 * /src/config/email_config.php
 * Configuración segura para PHPMailer usando variables de entorno (.env)
 */

require_once __DIR__ . '/env.php';

// Cargar .env desde rutas posibles
load_dotenv_auto([
    __DIR__ . '/../../.env',
    __DIR__ . '/../../../.env',
    getcwd() . '/.env'
]);

// Lectura segura
$smtp_host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$smtp_port       = getenv('SMTP_PORT') ?: 587;
$smtp_user       = getenv('SMTP_USER') ?: '';
$smtp_pass       = getenv('SMTP_PASS') ?: '';
$smtp_from       = getenv('SMTP_FROM') ?: $smtp_user;
$smtp_from_name  = getenv('SMTP_FROM_NAME') ?: 'Sistema';
$admin_email     = getenv('ADMIN_EMAIL') ?: $smtp_user;

$recaptcha_site  = getenv('RECAPTCHA_SITE_KEY') ?: '';
$recaptcha_secret = getenv('RECAPTCHA_SECRET') ?: '';

// Definir constantes
define('SMTP_HOST', $smtp_host);
define('SMTP_PORT', (int)$smtp_port);
define('SMTP_USER', $smtp_user);
define('SMTP_PASS', $smtp_pass);
define('SMTP_FROM', $smtp_from);
define('SMTP_FROM_NAME', $smtp_from_name);
define('ADMIN_EMAIL', $admin_email);

define('RECAPTCHA_SITE_KEY', $recaptcha_site);
define('RECAPTCHA_SECRET', $recaptcha_secret);
