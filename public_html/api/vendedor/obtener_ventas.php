
<?php
// public_html/api/vendedor/obtener_clientes.php
// ⭐ RUTA CORREGIDA: src está FUERA de public_html

// dirname(__DIR__, 2) = public_html
// Luego subimos un nivel más para llegar a la raíz del dominio
define("BASE_PATH", dirname(__DIR__, 3));

// Ahora BASE_PATH = /home/u.../domains/cornflower.../
// Y la ruta completa será: BASE_PATH/src/config/conection.php
require_once BASE_PATH . "/src/controllers/vendedor/obtener_ventas.php";
