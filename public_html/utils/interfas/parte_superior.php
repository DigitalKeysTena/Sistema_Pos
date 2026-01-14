<?php $uri = $_SERVER['REQUEST_URI']; ?>
<?php
// src/utils/interfas/parte_superior.php

$uri = $_SERVER['REQUEST_URI'];
$rol = $_SESSION['Id_Rol'] ?? null;

// Determinar la ruta base según desde dónde se llama
$currentPath = $_SERVER['SCRIPT_NAME'];

// Detectar contexto (administrador, inventario, vendedor)
$contexto = 'general';
if (strpos($currentPath, '/administrador/') !== false) {
    $contexto = 'administrador';
} elseif (strpos($currentPath, '/inventario/') !== false) {
    $contexto = 'inventario';
} elseif (strpos($currentPath, '/vendedor/') !== false) {
    $contexto = 'vendedor';
}

// Función helper para rutas relativas
function getMenuPath($path, $contexto) {
    $basePaths = [
        'administrador' => '/',
        'inventario' => '/',
        'vendedor' => '/'
    ];
    
    $base = $basePaths[$contexto] ?? '../../';
    return $base . $path;
}

// Función para verificar si un link está activo
function isActive($paths) {
    global $uri;
    foreach ((array)$paths as $path) {
        if (strpos($uri, $path) !== false) {
            return 'active';
        }
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="/login/img/fabicon.ico">
     <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- CSS -->

 <link rel="stylesheet" href="../../../utils/interfas/css_interfas/interfas.css?v=3.3.6">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.4/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.4/dist/sweetalert2.all.min.js"></script>

<!-- JsBarcode -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>


<!-- NAVBAR SUPERIOR -->
<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm fixed-top">
    <div class="container-fluid">

        <!-- IZQUIERDA -->
        <div class="d-flex align-items-center gap-3">

            <!-- LOGO -->
            <img src="<?= getMenuPath('login/img/logo_optimizado_800.webp', $contexto) ?>"
                 alt="Logo" class="logo">

            <!-- BOTÓN MENU -->
            <button class="btn btn-dark" id="toggleMenu">
                <i class="bi bi-list"></i>
            </button>
            
            <!-- TOGGLE DARK MODE -->
            <button class="btn btn-outline-dark" id="themeToggle">
                <i class="bi bi-moon"></i>
            </button>
        </div>

        <!-- CENTRO - Indicador de Rol -->
        <div class="d-none d-md-block">
            <span class="badge bg-primary fs-6 m-3">
                <i class="bi bi-person-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['Nombre_Usuario'] ?? 'Usuario') ?>
            </span>
        </div>

        <!-- DERECHA -->
        <div class="ms-auto d-flex align-items-center gap-2">
             <!-- CENTRO - Indicador de Rol -->
        <div class="d-none d-md-block">
            <span class="badge bg-success fs-6 m-3">
                <i class="bi bi-person-gear me-2"></i>
                <?php
                $roles = [1 => 'Administrador', 2 => 'Vendedor', 3 => 'Inventario'];
                echo $roles[$rol] ?? 'Usuario';
                ?>
            </span>
        </div>
            <a href="<?= getMenuPath('routers/logout.php', $contexto) ?>" class="btn btn-outline-danger">
                <i class="bi bi-box-arrow-right"></i> 
                <span class="d-none d-md-inline">Salir</span>
            </a>
        </div>
    
    </div>
</nav>

<!-- SIDEBAR -->
<div id="sidebar" class="sidebar sidebar-expanded">
    <nav class="nav flex-column mt-3">

        <?php
        // ========================================
        // MENÚ PARA ADMINISTRADOR (ROL 1)
        // ========================================
        if ($rol == 1): ?>
        
            <!-- Dashboard Admin -->
            <a class="nav-link <?= isActive(['administrador.php']) ?>"
               href="<?= getMenuPath('model/administrador/php/administrador.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-speedometer2"></i></div>
                <div class="label-box">Dashboard</div>
            </a>

            <!-- Separador -->
            <div class="nav-separator">
               <center> <small class="text text-white px-3 ">INVENTARIO</small></center>
            </div>

            <!-- Crear Productos -->
            <a class="nav-link <?= isActive(['agg_Productos.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/agg_Productos.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-bag-plus"></i></div>
                <div class="label-box">Crear Productos</div>
            </a>

            <!-- Ingreso Mercadería -->
            <a class="nav-link <?= isActive(['ingreso_productos.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/ingreso_productos.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="label-box">Ingreso Mercadería</div>
            </a>

            <!-- Ver Inventario -->
            <a class="nav-link <?= isActive(['inventario.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/inventario.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-boxes"></i></div>
                <div class="label-box" style="color: red;">Ver Inventario</div>
            </a>

            <!-- Separador -->
            <div class="nav-separator">
                <small class="text-white px-3">VENTAS</small>
            </div>

            <!-- Punto de Venta -->
            <a class="nav-link <?= isActive(['punto_venta.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/punto_venta.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-cart-plus"></i></div>
                <div class="label-box">Punto de Venta</div>
            </a>

            <!-- Todas las Ventas -->
            <a class="nav-link <?= isActive(['todas_ventas.php', 'mis_ventas.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/mis_ventas.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-receipt"></i></div>
                <div class="label-box">Ver Ventas</div>
            </a>

            <!-- Separador -->
            <div class="nav-separator">
                <small class="text-white px-3">GESTIÓN</small>
            </div>

            <!-- Reportes -->
            <a class="nav-link <?= isActive(['reporte.php']) ?>"
               href="<?= getMenuPath('model/administrador/php/reporte.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-bar-chart"></i></div>
                <div class="label-box">Reportes</div>
            </a>

            <!-- Usuarios (futuro) -->
            <a class="nav-link <?= isActive(['usuarios.php']) ?>"
               href="#" onclick="Swal.fire({icon: 'info', title: 'Módulo en Desarrollo', text: 'El módulo de Usuarios estará disponible próximamente', confirmButtonText: 'Entendido', confirmButtonColor: '#0d6efd'}); return false;">
                <div class="icon-box"><i class="bi bi-people"></i></div>
                <div class="label-box">Usuarios</div>
            </a>

            <!-- Clientes -->
            <a class="nav-link <?= isActive(['clientes.php']) ?>"
               href="#" onclick="Swal.fire({icon: 'info', title: 'Módulo en Desarrollo', text: 'El módulo de Clientes estará disponible próximamente', confirmButtonText: 'Entendido', confirmButtonColor: '#0d6efd'}); return false;">
                <div class="icon-box"><i class="bi bi-person-lines-fill"></i></div>
                <div class="label-box">Clientes</div>
            </a>

        <?php 
        // ========================================
        // MENÚ PARA VENDEDOR (ROL 2)
        // ========================================
        elseif ($rol == 2): ?>
        
            <!-- Dashboard Vendedor -->
            <a class="nav-link <?= isActive(['vendedor.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/vendedor.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-speedometer2"></i></div>
                <div class="label-box">Dashboard</div>
            </a>

            <!-- Separador -->
            <div class="nav-separator">
                <small class="text-muted px-3">VENTAS</small>
            </div>

            <!-- Nueva Venta -->
            <a class="nav-link <?= isActive(['punto_venta.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/punto_venta.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-cart-plus"></i></div>
                <div class="label-box">Nueva Venta</div>
            </a>

            <!-- Mis Ventas -->
            <a class="nav-link <?= isActive(['mis_ventas.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/mis_ventas.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                <div class="label-box">Mis Ventas</div>
            </a>
<!-- Mis Ventas -->
            <a class="nav-link <?= isActive(['cierre_caja.php']) ?>"
               href="<?= getMenuPath('model/vendedor/php/cierre_caja.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-clock-history"></i></div>
                <div class="label-box">Cierre de caja</div>
            </a>
            <!-- Separador -->
            <div class="nav-separator">
                <small class="text-muted px-3">CLIENTES</small>
            </div>

            <!-- Clientes -->
            <a class="nav-link <?= isActive(['clientes.php']) ?>"
               href="#" onclick="Swal.fire({icon: 'info', title: 'Módulo en Desarrollo', html: '<p>El módulo de <strong>Gestión de Clientes</strong> estará disponible próximamente.</p><p class=\'mt-2\'><small>Funcionalidades incluidas:</small></p><ul class=\'text-start\'><li>Registro de clientes</li><li>Historial de compras</li><li>Datos de contacto</li></ul>', confirmButtonText: 'Entendido', confirmButtonColor: '#0d6efd', width: 500}); return false;">
                <div class="icon-box"><i class="bi bi-people"></i></div>
                <div class="label-box">Clientes</div>
            </a>

            <!-- Consultar Productos -->
            <a class="nav-link <?= isActive(['consultar_productos.php']) ?>"
               href="#" onclick="Swal.fire({icon: 'info', title: 'Módulo en Desarrollo', html: '<p>El módulo de <strong>Consulta de Stock</strong> estará disponible próximamente.</p><p class=\'mt-2\'><small>Funcionalidades incluidas:</small></p><ul class=\'text-start\'><li>Búsqueda de productos</li><li>Verificación de stock disponible</li><li>Información de precios</li><li>Alertas de stock bajo</li></ul>', confirmButtonText: 'Entendido', confirmButtonColor: '#0d6efd', width: 500}); return false;">
                <div class="icon-box"><i class="bi bi-search"></i></div>
                <div class="label-box">Consultar Stock</div>
            </a>

        <?php 
        // ========================================
        // MENÚ PARA INVENTARIO (ROL 3)
        // ========================================
        elseif ($rol == 3): ?>
        
            <!-- Dashboard Inventario -->
            <a class="nav-link <?= isActive(['inventario.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/inventario.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-speedometer2"></i></div>
                <div class="label-box">Dashboard</div>
            </a>

            <!-- Separador -->
            <div class="nav-separator">
                <small class="text-muted px-3">PRODUCTOS</small>
            </div>

            <!-- Crear Productos -->
            <a class="nav-link <?= isActive(['agg_Productos.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/agg_Productos.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-bag-plus"></i></div>
                <div class="label-box">Crear Productos</div>
            </a>

            <!-- Ingreso Mercadería -->
            <a class="nav-link <?= isActive(['ingreso_productos.php']) ?>"
               href="<?= getMenuPath('model/inventario/php/ingreso_productos.php', $contexto) ?>">
                <div class="icon-box"><i class="bi bi-box-arrow-in-down"></i></div>
                <div class="label-box">Ingreso Mercadería</div>
            </a>

          

        <?php endif; ?> 

        <!-- Separador Final -->
        <div class="nav-separator mt-4">
            <small class="text-muted px-3">SISTEMA</small>
        </div>

       

        <!-- Ayuda -->
        <a class="nav-link" href="#" onclick="mostrarAyuda(); return false;">
            <div class="icon-box"><i class="bi bi-question-circle"></i></div>
            <div class="label-box">Ayuda</div>
        </a>

    </nav>
</div>

<!-- OVERLAY -->
<div id="overlay"></div>

<script>
// Función de ayuda contextual
function mostrarAyuda() {
    const contexto = '<?= $contexto ?>';
    const rol = <?= $rol ?>;
    
    let titulo = 'Ayuda';
    let contenido = '';
    
    if (rol === 1) {
        titulo = 'Ayuda - Administrador';
        contenido = `
            <div class="text-start">
                <h6>Panel de Administrador</h6>
                <ul>
                    <li>Tienes acceso completo al sistema</li>
                    <li>Puedes gestionar inventario, ventas y reportes</li>
                    <li>Usa el menú lateral para navegar entre módulos</li>
                </ul>
            </div>
        `;
    } else if (rol === 2) {
        titulo = 'Ayuda - Vendedor';
        contenido = `
            <div class="text-start">
                <h6>Panel de Vendedor</h6>
                <ul>
                    <li><strong>Nueva Venta:</strong> Escanea productos o búscalos manualmente</li>
                    <li><strong>Mis Ventas:</strong> Consulta tu historial de ventas</li>
                    <li><strong>Consultar Stock:</strong> Verifica disponibilidad de productos</li>
                </ul>
            </div>
        `;
    } else if (rol === 3) {
        titulo = 'Ayuda - Inventario';
        contenido = `
            <div class="text-start">
                <h6>Panel de Inventario</h6>
                <ul>
                    <li><strong>Crear Productos:</strong> Registra nuevos productos con código de barras</li>
                    <li><strong>Ingreso Mercadería:</strong> Actualiza stock de productos existentes</li>
                </ul>
            </div>
        `;
    }
    
    Swal.fire({
        title: titulo,
        html: contenido,
        icon: 'question',
        confirmButtonText: 'Entendido',
        width: 600
    });
}
</script>