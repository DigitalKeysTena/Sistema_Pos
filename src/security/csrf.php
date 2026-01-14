<?php
/**
 * Clase CSRF - Protección contra Cross-Site Request Forgery
 * Genera y valida tokens CSRF para formularios
 */

class CSRF {
    
    /**
     * Genera un token CSRF y lo almacena en sesión
     * @return string Token generado
     */
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generar token aleatorio seguro
        $token = bin2hex(random_bytes(32));
        
        // Almacenar en sesión con timestamp
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        
        return $token;
    }
    
    /**
     * Obtiene el token CSRF actual de la sesión
     * @return string|null Token o null si no existe
     */
    public static function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Si no hay token o expiró (1 hora), generar uno nuevo
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) ||
            (time() - $_SESSION['csrf_token_time'] > 3600)) {
            return self::generateToken();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Valida un token CSRF
     * @param string $token Token a validar
     * @return bool True si es válido
     */
    public static function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar que existe token en sesión
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Verificar que no haya expirado (1 hora)
        if (!isset($_SESSION['csrf_token_time']) || 
            (time() - $_SESSION['csrf_token_time'] > 3600)) {
            return false;
        }
        
        // Comparación segura contra timing attacks
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Genera un campo hidden HTML con el token CSRF
     * @return string HTML del campo hidden
     */
    public static function tokenField() {
        $token = self::getToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Valida el token CSRF desde $_POST
     * @throws Exception Si el token es inválido
     */
    public static function validateRequest() {
        $token = $_POST['csrf_token'] ?? '';
        
        if (!self::validateToken($token)) {
            security_log('CSRF token validation failed', [
                'post_data' => array_keys($_POST),
                'referer' => $_SERVER['HTTP_REFERER'] ?? 'none'
            ]);
            
            throw new Exception('Token de seguridad inválido. Por favor, recarga la página e intenta nuevamente.');
        }
        
        return true;
    }
    
    /**
     * Middleware para validar CSRF en controladores
     * Redirige con mensaje de error si falla
     * @param string $redirectUrl URL de redirección en caso de fallo
     */
    public static function check($redirectUrl = null) {
        try {
            self::validateRequest();
        } catch (Exception $e) {
            session_start();
            $_SESSION['swal'] = [
                'icon' => 'error',
                'title' => 'Error de Seguridad',
                'text' => $e->getMessage(),
                'timer' => 4000
            ];
            
            if ($redirectUrl) {
                redirect($redirectUrl);
            } else {
                redirect($_SERVER['HTTP_REFERER'] ?? LOGIN_URL);
            }
        }
    }
    
    /**
     * Regenera el token CSRF (útil después de login)
     */
    public static function regenerateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_token_time']);
        
        return self::generateToken();
    }
}

// ==========================================
// FUNCIONES HELPER GLOBALES
// ==========================================

/**
 * Genera campo hidden CSRF para formularios
 * @return string HTML del campo
 */
function csrf_field() {
    return CSRF::tokenField();
}

/**
 * Obtiene token CSRF actual
 * @return string Token
 */
function csrf_token() {
    return CSRF::getToken();
}

/**
 * Valida token CSRF de la petición
 * @return bool
 */
function csrf_check() {
    try {
        CSRF::validateRequest();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
