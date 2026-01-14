<?php
session_start();

/* ============================================================================
   ✔ CARGA DE CONFIGURACIONES
============================================================================ */
require_once __DIR__ . '/../../config/app_config.php';
require_once __DIR__ . '/../../config/email_config.php';
require_once __DIR__ . '/../../security/csrf.php';
require_once __DIR__ . '../../../../public_html/utils/mailer.php';
require_once __DIR__ . '/../../config/conection.php';

/* Cargar .env */
if (function_exists('load_dotenv_auto')) {
    load_dotenv_auto([__DIR__ . '/../.env']);
}

$RECAPTCHA_SECRET = getenv("RECAPTCHA_SECRET");

/* ============================================================================
   ✔ FUNCIONES AUXILIARES
============================================================================ */
function get_ip() { 
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; 
}

function get_ua() { 
    return substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 200); 
}

$ip = get_ip();
$ua = get_ua();

/* ============================================================================
   ✔ 1) REVISAR FIREWALL BLOCKED_ENTITIES
============================================================================ */
try {
    $stmt = $pdo->prepare("
        SELECT reason, blocked_at 
        FROM blocked_entities 
        WHERE (entity = :ip AND entity_type = 'ip')
           OR (entity = :ua AND entity_type = 'user_agent')
        LIMIT 1
    ");
    $stmt->execute([
        ':ip'   => $ip,
        ':ua'   => $ua
    ]);

    $blocked = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($blocked) {
        security_log('Blocked entity tried to access', [
            'ip' => $ip,
            'reason' => $blocked['reason']
        ]);
        
        $_SESSION['login_message'] = "⛔ Acceso bloqueado por seguridad.";
        redirect(LOGIN_URL);
    }
} catch (PDOException $e) {
    app_log('Database error checking blocked entities: ' . $e->getMessage(), 'ERROR');
    die('Error de sistema. Contacta al administrador.');
}

/* ============================================================================
   ✔ 2) VALIDAR MÉTODO
============================================================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    app_log('Invalid request method to login', 'WARNING', ['method' => $_SERVER['REQUEST_METHOD']]);
    exit("Método no permitido");
}

/* ============================================================================
   ✔ 3) VALIDAR CSRF TOKEN
============================================================================ */
try {
    CSRF::validateRequest();
} catch (Exception $e) {
    $_SESSION['login_message'] = "Token de seguridad inválido. Recarga la página.";
    redirect(LOGIN_URL);
}

/* ============================================================================
   ✔ 4) OBTENER Y VALIDAR CAMPOS
============================================================================ */
$usuario  = trim($_POST['usuario'] ?? '');
$password = trim($_POST['password'] ?? '');

// Validación de entrada
if (empty($usuario) || empty($password)) {
    $_SESSION['login_message'] = "⚠️ Complete todos los campos.";
    redirect(LOGIN_URL);
}

// Validar longitud
if (strlen($usuario) > 50 || strlen($password) > 100) {
    security_log('Suspicious login attempt - field too long', [
        'username' => $usuario,
        'ip' => $ip
    ]);
    $_SESSION['login_message'] = "⚠️ Datos inválidos.";
    redirect(LOGIN_URL);
}

/* ============================================================================
   ✔ 5) OBTENER INTENTOS ANTERIORES
============================================================================ */
try {
    $stmt = $pdo->prepare("
        SELECT attempts, locked_until 
        FROM login_attempts 
        WHERE username = :u AND ip = :ip
        LIMIT 1
    ");
    $stmt->execute([':u' => $usuario, ':ip' => $ip]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    app_log('Database error fetching login attempts: ' . $e->getMessage(), 'ERROR');
    die('Error de sistema. Contacta al administrador.');
}

$prev_attempts = (int)($data['attempts'] ?? 0);
$now = new DateTimeImmutable();

/* ============================================================================
   ✔ 6) VERIFICAR BLOQUEO TEMPORAL
============================================================================ */
if ($data && $data['locked_until']) {
    $unlock = new DateTimeImmutable($data['locked_until']);

    if ($unlock > $now) {
        security_log('Login attempt while locked', [
            'username' => $usuario,
            'ip' => $ip,
            'locked_until' => $data['locked_until']
        ]);
        
        $_SESSION['login_message']  = "⛔ Cuenta bloqueada temporalmente.";
        $_SESSION['lock_remaining'] = $unlock->getTimestamp() - $now->getTimestamp();
        $_SESSION['show_captcha']   = true;
        redirect(LOGIN_URL);
    }
}

/* ============================================================================
   ✔ 7) VALIDAR CAPTCHA SI INTENTOS >= 2
============================================================================ */
if ($prev_attempts >= 2) {
    $_SESSION['show_captcha'] = true;

    $captchaResponse = $_POST['g-recaptcha-response'] ?? null;
    
    if (!$captchaResponse) {
        $_SESSION['login_message'] = "⚠️ Complete el reCAPTCHA.";
        redirect(LOGIN_URL);
    }

    try {
        $verify = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?" .
            http_build_query([
                "secret" => $RECAPTCHA_SECRET,
                "response" => $captchaResponse,
                "remoteip" => $ip
            ])
        );

        $captcha = json_decode($verify, true);

        if (empty($captcha["success"])) {
            security_log('reCAPTCHA validation failed', [
                'username' => $usuario,
                'ip' => $ip
            ]);
            
            $_SESSION['login_message'] = "❌ Error verificando reCAPTCHA.";
            redirect(LOGIN_URL);
        }
    } catch (Exception $e) {
        app_log('reCAPTCHA verification error: ' . $e->getMessage(), 'ERROR');
        $_SESSION['login_message'] = "❌ Error de verificación. Intenta nuevamente.";
        redirect(LOGIN_URL);
    }
}

/* ============================================================================
   ✔ 8) OBTENER USUARIO CON LÍMITE DE RATE
============================================================================ */
try {
    $stmt = $pdo->prepare("
        SELECT 
            l.Id_Login,
            l.Username,
            l.Password,
            l.Id_Login_Usuario,
            u.Nombre_Usuario,
            u.Apellido_Usuario,
            u.Correo_Usuario,
            u.Id_Usuario_Rol AS Id_Rol
        FROM login l
        INNER JOIN usuario u ON u.Id_Usuario = l.Id_Login_Usuario
        WHERE l.Username = :u
        LIMIT 1
    ");
    $stmt->execute([':u' => $usuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    app_log('Database error fetching user: ' . $e->getMessage(), 'ERROR');
    die('Error de sistema. Contacta al administrador.');
}

// Verificar credenciales
$valid = $user && password_verify($password, $user['Password']);

/* ============================================================================
   ✔ 9) LOGIN EXITOSO
============================================================================ */
if ($valid) {
    try {
        // Reset intentos
        $pdo->prepare("DELETE FROM login_attempts WHERE username = :u AND ip = :ip")
            ->execute([':u' => $usuario, ':ip' => $ip]);

        // Registrar login exitoso
        security_log('Successful login', [
            'username' => $usuario,
            'user_id' => $user['Id_Login_Usuario'],
            'ip' => $ip
        ]);

        // Regenerar sesión por seguridad
        session_regenerate_id(true);
        
        // Regenerar token CSRF
        CSRF::regenerateToken();

        // Establecer variables de sesión
        $_SESSION['Id_Login']         = $user['Id_Login'];
        $_SESSION['Username']         = $user['Username'];
        $_SESSION['Id_Login_Usuario'] = $user['Id_Login_Usuario'];
        $_SESSION['Id_Rol']           = $user['Id_Rol'];
        $_SESSION['Nombre_Usuario']   = $user['Nombre_Usuario'] . ' ' . $user['Apellido_Usuario'];
        $_SESSION['LAST_ACTIVITY']    = time();
        $_SESSION['CREATED']          = time();
        $_SESSION['IP_ADDRESS']       = $ip;

        // Redirigir según rol
        redirect_by_role((int)$user['Id_Rol']);
        
    } catch (PDOException $e) {
        app_log('Error during successful login: ' . $e->getMessage(), 'ERROR');
        $_SESSION['login_message'] = "Error al iniciar sesión. Intenta nuevamente.";
        redirect(LOGIN_URL);
    }
}

/* ============================================================================
   ❌ 10) LOGIN FALLIDO
============================================================================ */
$attempts = $prev_attempts + 1;

try {
    /* Guardar intento fallido */
    if ($data) {
        $pdo->prepare("
            UPDATE login_attempts 
            SET attempts=:a, last_attempt=NOW(), user_agent=:ua 
            WHERE username=:u AND ip=:ip
        ")->execute([':a'=>$attempts, ':ua'=>$ua, ':u'=>$usuario, ':ip'=>$ip]);
    } else {
        $pdo->prepare("
            INSERT INTO login_attempts (username, ip, attempts, last_attempt, user_agent)
            VALUES (:u, :ip, 1, NOW(), :ua)
        ")->execute([':u'=>$usuario, ':ip'=>$ip, ':ua'=>$ua]);
    }
} catch (PDOException $e) {
    app_log('Error recording failed login attempt: ' . $e->getMessage(), 'ERROR');
}

security_log('Failed login attempt', [
    'username' => $usuario,
    'attempts' => $attempts,
    'ip' => $ip
]);

/* ============================================================================
   ✔ 11) FIREWALL — BLOQUEO PERMANENTE POR ATAQUE
============================================================================ */
if ($attempts >= 15) {
    try {
        // Registrar bloqueo permanente
        $pdo->prepare("
            INSERT INTO blocked_entities (entity, entity_type, reason)
            VALUES (:ip, 'ip', 'Ataque detectado: más de 15 intentos fallidos')
        ")->execute([':ip'=>$ip]);

        security_log('IP blocked permanently', [
            'ip' => $ip,
            'username' => $usuario,
            'attempts' => $attempts
        ]);

        @send_admin_alert($usuario, $ip, $ua, $attempts);

        $_SESSION['login_message'] = "⛔ IP bloqueada por seguridad.";
        redirect(LOGIN_URL);
    } catch (PDOException $e) {
        app_log('Error blocking IP: ' . $e->getMessage(), 'ERROR');
    }
}

/* ============================================================================
   ✔ 12) BLOQUEOS PROGRESIVOS
============================================================================ */
$progressive = [
    5 => 1, 6 => 2, 7 => 4, 8 => 8,
    9 => 16, 10 => 32, 11 => 64
];

if (isset($progressive[$attempts])) {
    $mins = $progressive[$attempts];
    $unlock = $now->modify("+{$mins} minutes")->format("Y-m-d H:i:s");

    try {
        $pdo->prepare("
            UPDATE login_attempts SET locked_until=:lu
            WHERE username=:u AND ip=:ip
        ")->execute([':lu'=>$unlock, ':u'=>$usuario, ':ip'=>$ip]);

        security_log('Account temporarily locked', [
            'username' => $usuario,
            'minutes' => $mins,
            'attempts' => $attempts
        ]);

        // Enviar email en intento 5
        if ($attempts === 5 && !empty($user['Correo_Usuario'])) {
            @send_user_alert($user['Correo_Usuario'], $usuario, $ip, $ua);
        }

        $_SESSION['login_message']  = "⛔ Demasiados intentos. Bloqueado por {$mins} min.";
        $_SESSION['lock_remaining'] = $mins * 60;
        $_SESSION['show_captcha']   = true;

        redirect(LOGIN_URL);
    } catch (PDOException $e) {
        app_log('Error setting temporary lock: ' . $e->getMessage(), 'ERROR');
    }
}

/* ============================================================================
   ✔ 13) MENSAJES DE ERROR
============================================================================ */
$_SESSION['login_message'] = "❌ Usuario o contraseña incorrectos.";
if ($attempts > 2) {
    $_SESSION['show_captcha'] = true;
}

redirect(LOGIN_URL);
