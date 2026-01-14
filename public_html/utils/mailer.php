<?php
/**
 * /src/utils/mailer.php
 * Sistema Empresarial de Alertas de Seguridad
 * Enviar correo a ADMIN (intento 12) y Usuario (intento 5)
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cargar PHPMailer
require_once __DIR__ . '/../phpmailer/src/Exception.php';
require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer/src/SMTP.php';

// Cargar credenciales SMTP
require_once __DIR__ . '../../../src/config/email_config.php';

/* ============================================================================
   ✅ PLANTILLA HTML EMPRESARIAL (
============================================================================ */
function corporate_template($title, $message, $details_html, $footer_note)
{
    return "
    <div style='background:#0d1117;padding:30px;font-family:Arial,sans-serif;color:#c9d1d9'>
        <div style='max-width:600px;margin:auto;background:#161b22;border-radius:14px;padding:28px;border:1px solid #30363d;'>

            <div style='text-align:center;margin-bottom:25px;'>
                <h2 style='color:#1f6feb;margin:0;font-size:26px;'>$title</h2>
                <p style='color:#8b949e;font-size:14px;margin-top:6px;'>$message</p>
            </div>

            <div style='background:#21262d;padding:22px;border-radius:10px;margin-bottom:25px;border:1px solid #30363d;'>
                $details_html
            </div>

            <div style='text-align:center;color:#8b949e;font-size:12px;margin-top:20px;'>
                $footer_note
            </div>

        </div>
    </div>";
}

/* ============================================================================
   ✅ CONFIGURAR Y CREAR OBJETO PHPMailer
============================================================================ */
function create_mailer()
{
    $mail = new PHPMailer(true);

    // Configuración SMTP empresarial
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = "UTF-8";

    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);

    return $mail;
}

/* ============================================================================
   ✅ 1) CORREO PARA EL ADMINISTRADOR → INTENTO 12 (ATAQUE)
============================================================================ */
function send_admin_alert($username, $ip, $ua, $attempts)
{
    try {
        $mail = create_mailer();
        $mail->addAddress(ADMIN_EMAIL);

        $mail->isHTML(true);
        $mail->Subject = "🚨 ALERTA CRÍTICA – Ataque detectado en el sistema";

        // HTML Empresarial
        $mail->Body = corporate_template(
            "🚨 ALERTA DE SEGURIDAD",
            "Se detectó un posible ataque de fuerza bruta en el sistema.",
            "
                <h3 style='color:#58a6ff;margin:0;'>Detalles del incidente:</h3>

                <p><strong>Usuario objetivo:</strong> " . htmlspecialchars($username) . "</p>
                <p><strong>Dirección IP:</strong> " . htmlspecialchars($ip) . "</p>
                <p><strong>User-Agent:</strong><br>
                    <span style='font-size:13px;'>" . htmlspecialchars($ua) . "</span>
                </p>
                <p><strong>Intentos acumulados:</strong> $attempts</p>
                <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>

                <a href='https://cornflowerblue-goldfinch-724248.hostingersite.com'
                    style='display:inline-block;margin-top:15px;padding:12px 22px;background:#ff4d4d;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>
                    Abrir Panel de Seguridad
                </a>
            ",
            "Este mensaje fue generado automáticamente por el Sistema de Seguridad Empresarial."
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer ERROR (ADMIN) → " . $e->getMessage());
        return false;
    }
}

/* ============================================================================
   ✅ 2) CORREO PARA EL USUARIO → INTENTO 5
============================================================================ */
function send_user_alert($userEmail, $username, $ip, $ua)
{
    try {
        $mail = create_mailer();
        $mail->addAddress($userEmail);

        $mail->isHTML(true);
        $mail->Subject = "🔔 Notificación de seguridad – Actividad inusual detectada";

        // HTML Empresarial
        $mail->Body = corporate_template(
            "🔔 ACTIVIDAD INUSUAL DETECTADA",
            "Detectamos intentos de acceso que pueden no coincidir con tu actividad.",
            "
                <h3 style='color:#58a6ff;margin:0;'>Detalles detectados:</h3>

                <p><strong>Usuario:</strong> " . htmlspecialchars($username) . "</p>
                <p><strong>IP detectada:</strong> " . htmlspecialchars($ip) . "</p>
                <p><strong>Navegador/Dispositivo:</strong><br>
                    <span style='font-size:13px;'>" . htmlspecialchars($ua) . "</span>
                </p>

                <p style='margin-top:12px;'>Si no fuiste tú, cambia tu contraseña inmediatamente.</p>

                <a href='https://cornflowerblue-goldfinch-724248.hostingersite.com/login/recuperar.php'
                    style='display:inline-block;margin-top:15px;padding:12px 22px;background:#1f6feb;color:white;text-decoration:none;border-radius:8px;font-weight:bold;'>
                    Cambiar contraseña
                </a>
            ",
            "Si desconoces esta actividad, contacta a soporte inmediatamente."
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer ERROR (USER) → " . $e->getMessage());
        return false;
    }
}

// Restableces contraseña por mensaje
function send_recovery_code($email, $username, $otp)
{
    try {
        $mail = create_mailer(); // <- esta función debe devolverte un PHPMailer configurado
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "🔐 Recuperación de contraseña – Código de verificación";

        // URL de tu página de validación con email y código en la URL
        $validationUrl = "https://cornflowerblue-goldfinch-724248.hostingersite.com/login/validar_codigo.php";


        $mail->Body = corporate_template(
            "🔐 Recuperación de Contraseña",
            "Hola <strong>$username</strong>, usa el siguiente código para restablecer tu contraseña.",
            "
                <h3 style='color:#58a6ff;margin:0;'>Tu código de verificación:</h3>

                <div style='font-size:32px;color:#1f6feb;font-weight:bold;margin:20px 0;'>
                    $otp
                </div>

                <p>Este código expira en <strong>10 minutos</strong>.</p>

                <p style='margin-top:20px;'>
                    También puedes hacer clic en el siguiente botón para ir a la página donde
                    podrás ingresar tu código:
                </p>

                <a href='$validationUrl'
                   style=\"
                       display:inline-block;
                       margin-top:10px;
                       padding:12px 22px;
                       background:#1f6feb;
                       color:#ffffff;
                       text-decoration:none;
                       font-size:16px;
                       border-radius:8px;
                       font-weight:bold;
                   \">
                    🔐 Ingresar código de verificación
                </a>
            ",
            "Si no solicitaste este código, puedes ignorar este mensaje."
        );

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log('RECOVERY EMAIL ERROR → ' . $e->getMessage());
        return false;
    }
}


