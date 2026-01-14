<?php
// public_html/model/vendedor/php/vendedor.php
session_start();

// ⭐ RUTAS CORREGIDAS - Faltaba "/" después de __DIR__
require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';

require_role([2,1]); // Vendedor y Admin

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<style>
/* ============================================
   DASHBOARD VENDEDOR - ESTILOS MODERNOS
   ============================================ */
:root {
    --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.1);
    --shadow-md: 0 4px 15px rgba(0,0,0,0.15);
    --shadow-lg: 0 10px 40px rgba(0,0,0,0.2);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Tarjetas de características */
.feature-card {
    transition: var(--transition);
    border: none;
    border-radius: var(--border-radius);
    overflow: hidden;
    background: white;
    box-shadow: var(--shadow-sm);
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--shadow-lg);
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

/* Iconos de características */
.feature-icon {
    width: 90px;
    height: 90px;
    background: var(--gradient-1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    transition: var(--transition);
}

.feature-icon i {
    font-size: 2.8rem;
    color: white;
}

.feature-icon.gradient-2 {
    background: var(--gradient-2);
    box-shadow: 0 8px 20px rgba(240, 147, 251, 0.4);
}

.feature-icon.gradient-3 {
    background: var(--gradient-3);
    box-shadow: 0 8px 20px rgba(79, 172, 254, 0.4);
}

/* Tarjetas de estadísticas */
.stats-card {
    background: var(--gradient-1);
    color: white;
    border-radius: var(--border-radius);
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(30%, -30%);
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stats-number {
    font-size: 2.8rem;
    font-weight: bold;
    text-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.stats-label {
    opacity: 0.9;
    font-size: 0.95rem;
    font-weight: 500;
}

.stats-icon {
    font-size: 3.5rem;
    opacity: 0.3;
}

/* Header mejorado */
.header-card {
    background: linear-gradient(135deg, #198754 0%, #146c43 100%);
    border-radius: var(--border-radius);
    padding: 2rem;
    color: white;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

/* Botones mejorados */
.btn-modern {
    border-radius: 12px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: var(--transition);
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.25);
}

/* Tips mejorados */
.tips-alert {
    border-radius: var(--border-radius);
    border: none;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 5px solid #2196f3;
    box-shadow: var(--shadow-sm);
}

.tips-alert .alert-heading {
    color: #1976d2;
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-item {
    animation: fadeInUp 0.6s ease forwards;
}

/* Skeleton loader */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: skeleton 1.5s infinite;
}

@keyframes skeleton {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Responsive */
@media (max-width: 991px) {
    .feature-icon {
        width: 75px;
        height: 75px;
    }
    
    .feature-icon i {
        font-size: 2.2rem;
    }
    
    .stats-number {
        font-size: 2.2rem;
    }
    
    .stats-icon {
        font-size: 2.5rem;
    }
}

@media (max-width: 767px) {
    .header-card {
        padding: 1.5rem;
    }
    
    .stats-card {
        margin-bottom: 15px;
    }
}
</style>

<!-- CONTENIDO PRINCIPAL -->
<div id="main-content">
    <div class="container-fluid py-4">

        <!-- HEADER DEL PANEL -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-card animate-item">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="mb-2">
                                <i class="bi bi-shop me-3"></i>
                                Panel de Vendedor
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-person-circle me-2"></i>
                                Bienvenido, <?php echo htmlspecialchars($_SESSION['Nombre_Usuario'] ?? 'Vendedor'); ?>
                            </p>
                        </div>
                        <div>
                            <a href="./punto_venta.php" class="btn btn-light btn-lg">
                                <i class="bi bi-cart-plus me-2"></i>
                                Nueva Venta
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESTADÍSTICAS RÁPIDAS -->
        <div class="row mb-4">
            <div class="col-md-4 col-12">
                <div class="stats-card animate-item" style="animation-delay: 0.1s;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number" id="ventasHoy">--</div>
                            <div class="stats-label">Ventas de Hoy</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-12">
                <div class="stats-card animate-item" style="animation-delay: 0.2s; background: var(--gradient-success);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number" id="totalVendido">$--</div>
                            <div class="stats-label">Total Vendido Hoy</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-12">
                <div class="stats-card animate-item" style="animation-delay: 0.3s; background: var(--gradient-3);">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number" id="productosVendidos">--</div>
                            <div class="stats-label">Productos Vendidos</div>
                        </div>
                        <div class="stats-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OPCIONES PRINCIPALES -->
        <div class="row g-4 mb-4">
            
            <!-- Nueva Venta -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="feature-card animate-item" style="animation-delay: 0.4s;">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon">
                            <i class="bi bi-cart-plus"></i>
                        </div>
                        <h4 class="card-title mb-3 fw-bold">Nueva Venta</h4>
                        <p class="text-muted mb-4">
                            Registra una nueva venta de forma rápida y sencilla con scanner de código de barras
                        </p>
                        <a href="./punto_venta.php" class="btn btn-success btn-modern btn-lg w-100">
                            <i class="bi bi-cart-check me-2"></i>Iniciar Venta
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mis Ventas -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="feature-card animate-item" style="animation-delay: 0.5s;">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon gradient-2">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <h4 class="card-title mb-3 fw-bold">Mis Ventas</h4>
                        <p class="text-muted mb-4">
                            Consulta el historial completo de todas tus ventas con filtros avanzados y estadísticas
                        </p>
                        <a href="./mis_ventas.php" class="btn btn-primary btn-modern btn-lg w-100">
                            <i class="bi bi-clock-history me-2"></i>Ver Historial
                        </a>
                    </div>
                </div>
            </div>

            <!-- Clientes -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="feature-card animate-item" style="animation-delay: 0.6s;">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon gradient-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4 class="card-title mb-3 fw-bold">Gestión de Clientes</h4>
                        <p class="text-muted mb-4">
                            Administra la base de datos de clientes, registra nuevos y mantén su información actualizada
                        </p>
                        <button class="btn btn-info btn-modern btn-lg w-100" onclick="mostrarProximamente()">
                            <i class="bi bi-person-lines-fill me-2"></i>Administrar
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- ATAJOS RÁPIDOS -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm animate-item" style="animation-delay: 0.7s; border-radius: var(--border-radius);">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-lightning-charge me-2 text-warning"></i>
                            Accesos Rápidos
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <a href="./punto_venta.php" class="btn btn-outline-success w-100">
                                    <i class="bi bi-cart-plus d-block fs-3 mb-2"></i>
                                    Nueva Venta
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="./mis_ventas.php" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-clock-history d-block fs-3 mb-2"></i>
                                    Historial
                                </a>
                            </div>
                            <div class="col-md-3 col-6">
                                <button class="btn btn-outline-info w-100" onclick="buscarProducto()">
                                    <i class="bi bi-search d-block fs-3 mb-2"></i>
                                    Buscar Producto
                                </button>
                            </div>
                            <div class="col-md-3 col-6">
                                <button class="btn btn-outline-warning w-100" onclick="mostrarAyuda()">
                                    <i class="bi bi-question-circle d-block fs-3 mb-2"></i>
                                    Ayuda
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TIPS Y CONSEJOS -->
        <div class="row">
            <div class="col-12">
                <div class="alert tips-alert border-0 animate-item" style="animation-delay: 0.8s;">
                    <h5 class="alert-heading">
                        <i class="bi bi-lightbulb me-2"></i>Tips y Consejos
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong>Scanner de código de barras:</strong> Usa la cámara de tu dispositivo para escanear productos rápidamente
                                </li>
                                <li class="mb-2">
                                    <strong>Búsqueda inteligente:</strong> Busca productos por nombre, código o código de barras
                                </li>
                                <li>
                                    <strong>Gestión automática:</strong> El stock se actualiza automáticamente con cada venta
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="mb-0">
                                <li class="mb-2">
                                    <strong>Historial completo:</strong> Todas tus ventas quedan registradas y accesibles en cualquier momento
                                </li>
                                <li class="mb-2">
                                    <strong>Múltiples métodos de pago:</strong> Acepta efectivo, tarjeta, transferencias y más
                                </li>
                                <li>
                                    <strong>Reportes detallados:</strong> Consulta tus estadísticas diarias y mensuales
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// Cargar estadísticas del día
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
});

function cargarEstadisticas() {
    // ⭐ RUTA CORREGIDA
    var url = '/api/vendedor/obtener_estadisticas.php?_t=' + Date.now();
    
    fetch(url, {
        method: 'GET',
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            animarNumero('ventasHoy', data.ventas_hoy || 0);
            animarNumero('productosVendidos', data.productos_vendidos || 0);
            animarMoneda('totalVendido', data.total_vendido || 0);
        } else {
            throw new Error('Error al obtener estadísticas');
        }
    })
    .catch(function(error) {
        console.error('Error cargando estadísticas:', error);
        document.getElementById('ventasHoy').textContent = '0';
        document.getElementById('totalVendido').textContent = '$0.00';
        document.getElementById('productosVendidos').textContent = '0';
    });
}

// Animar números
function animarNumero(id, valorFinal) {
    var elemento = document.getElementById(id);
    var duracion = 1000;
    var inicio = 0;
    var incremento = valorFinal / (duracion / 16);
    var valorActual = inicio;
    
    var timer = setInterval(function() {
        valorActual += incremento;
        if (valorActual >= valorFinal) {
            clearInterval(timer);
            elemento.textContent = Math.floor(valorFinal);
        } else {
            elemento.textContent = Math.floor(valorActual);
        }
    }, 16);
}

// Animar moneda
function animarMoneda(id, valorFinal) {
    var elemento = document.getElementById(id);
    var duracion = 1000;
    var inicio = 0;
    var incremento = valorFinal / (duracion / 16);
    var valorActual = inicio;
    
    var timer = setInterval(function() {
        valorActual += incremento;
        if (valorActual >= valorFinal) {
            clearInterval(timer);
            elemento.textContent = '$' + valorFinal.toFixed(2);
        } else {
            elemento.textContent = '$' + valorActual.toFixed(2);
        }
    }, 16);
}

// Buscar producto
function buscarProducto() {
    Swal.fire({
        title: 'Buscar Producto',
        input: 'text',
        inputPlaceholder: 'Nombre o código del producto...',
        showCancelButton: true,
        confirmButtonText: 'Buscar',
        cancelButtonText: 'Cancelar',
        inputValidator: function(value) {
            if (!value) {
                return 'Debes ingresar algo para buscar';
            }
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            Swal.fire('Búsqueda', 'Funcionalidad en desarrollo', 'info');
        }
    });
}

// Mostrar ayuda
function mostrarAyuda() {
    Swal.fire({
        title: '<strong>Centro de Ayuda</strong>',
        icon: 'info',
        html: '<div class="text-start">' +
            '<h6 class="mb-3">¿Cómo usar el punto de venta?</h6>' +
            '<ol>' +
            '<li>Haz clic en "Nueva Venta"</li>' +
            '<li>Busca o escanea los productos</li>' +
            '<li>Selecciona el cliente</li>' +
            '<li>Confirma el método de pago</li>' +
            '<li>Procesa la venta</li>' +
            '</ol>' +
            '<hr>' +
            '<p class="small text-muted">' +
            '<strong>¿Necesitas más ayuda?</strong><br>' +
            'Contacta al administrador del sistema' +
            '</p>' +
            '</div>',
        showCloseButton: true,
        confirmButtonText: 'Entendido'
    });
}

// Mostrar próximamente
function mostrarProximamente() {
    Swal.fire({
        title: 'Próximamente',
        text: 'Esta funcionalidad estará disponible pronto',
        icon: 'info',
        confirmButtonText: 'Ok'
    });
}
</script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>