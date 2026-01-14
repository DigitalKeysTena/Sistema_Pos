<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. PHP funcionando ✅<br>";

session_start();
echo "2. Session iniciada ✅<br>";

$base_path = __DIR__ . '/../../../../';
echo "3. Base path: $base_path<br>";

if (file_exists($base_path . 'src/config/app_config.php')) {
    echo "4. app_config.php existe ✅<br>";
} else {
    echo "4. app_config.php NO existe ❌<br>";
}

if (file_exists($base_path . 'src/config/conection.php')) {
    echo "5. conection.php existe ✅<br>";
} else {
    echo "5. conection.php NO existe ❌<br>";
}

if (file_exists($base_path . 'src/security/auth.php')) {
    echo "6. auth.php existe ✅<br>";
} else {
    echo "6. auth.php NO existe ❌<br>";
}

echo "<br><strong>Sesión actual:</strong><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>