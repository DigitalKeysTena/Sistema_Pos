<?php
/**
 * env.php
 * Funciones para cargar .env de forma robusta.
 *
 * Uso:
 *   require_once __DIR__ . '/env.php';
 *   $loaded = load_dotenv_auto(); // busca .env y lo carga si lo encuentra
 *
 * Devuelve true si se cargó, false si no.
 */

function load_dotenv($path) {
    if (!is_string($path) || $path === '') return false;
    // Normalizar ruta
    $path = str_replace(['//','\\'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path) || !is_readable($path)) return false;

    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return false;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // quitar comillas alrededor
        if ((substr($value,0,1) === '"' && substr($value,-1) === '"') ||
            (substr($value,0,1) === "'" && substr($value,-1) === "'")) {
            $value = substr($value, 1, -1);
        }

        // No sobrescribir variables ya definidas en el entorno
        if (getenv($name) === false) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    return true;
}

/**
 * Busca automáticamente .env en varias ubicaciones razonables y lo carga.
 * Orden de búsqueda:
 *  - ruta absoluta proporcionada (si $candidates argument)
 *  - project root relativo a este archivo: __DIR__ . '/../../.env'
 *  - parent of document root: dirname($_SERVER['DOCUMENT_ROOT'], 1) . '/.env'
 *  - cwd() . '/.env'
 *  - $_SERVER['DOCUMENT_ROOT'] . '/.env'
 *
 * Retorna la ruta cargada o false si no encontró ninguno.
 */
function load_dotenv_auto(array $candidates = []) {
    // Si inyectaron rutas concretas, probarlas primero
    foreach ($candidates as $c) {
        if (!is_string($c) || $c === '') continue;
        if (load_dotenv($c)) return realpath($c);
    }

    // rutas por defecto
    $defaults = [
        __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env', // ../../.env
        getcwd() . DIRECTORY_SEPARATOR . '.env',
    ];

    // intentar document root si está definido
    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
        $defaults[] = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.env';
        // parent of document root
        $defaults[] = dirname(rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . '.env';
    }

    foreach ($defaults as $path) {
        if (load_dotenv($path)) return realpath($path);
    }

    return false;
}
