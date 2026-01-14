<?php
// ⭐ Cargar configuración y seguridad mejorada
session_start();
// Configuración
require_once __DIR__ . '/../../../../src/config/app_config.php';
// Seguridad
require_once __DIR__ . '/../../../../src/security/auth.php';
// Verificar rol
require_role([3,1]);
// Interface Superior
require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<link rel="stylesheet" href="../css/style_inventario.css?v=1.0.0">

<!-- MAIN CONTENT -->
<div id="main-content">
    
    <!-- Header del Panel -->
    <div class="panel-header text-center">
        <div class="container">
            <i class="bi bi-boxes header-icon d-none d-md-block"></i>
            <h1><i class="bi bi-grid-1x2-fill me-2"></i>Panel de Inventario</h1>
            <p>Sistema de gestión de productos y control de stock</p>
            <div class="d-flex justify-content-center align-items-center gap-3 mt-3 flex-wrap">
                <button class="btn btn-refresh" id="btnActualizar" type="button">
                    <i class="bi bi-arrow-clockwise me-2"></i>Actualizar
                </button>
                <span class="live-indicator">
                    <span class="live-dot"></span>
                    <span>Datos en vivo</span>
                </span>
            </div>
            <small class="last-update d-block mt-2" id="lastUpdate"></small>
        </div>
    </div>
    
    <div class="container">
        
        <!-- Sección de Estadísticas -->
        <div class="stats-section">
            <div class="row g-3">
                <!-- Total Productos -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card primary">
                        <div class="stat-icon bg-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="totalProductos">--</h3>
                            <p>Total Productos</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Normal -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card success">
                        <div class="stat-icon bg-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="stockTotal">--</h3>
                            <p>Stock Total</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stock Bajo -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card warning">
                        <div class="stat-icon bg-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="stockBajo">--</h3>
                            <p>Stock Bajo</p>
                        </div>
                    </div>
                </div>
                
              
            </div>
            
        </div>
         <!-- Sección de Estadísticas -->
        <div class="stats-section">
            <div class="row g-3">
                
                
                <!-- Sin Stock -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card danger">
                        <div class="stat-icon bg-danger">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="sinStock">--</h3>
                            <p>Sin Stock</p>
                        </div>
                    </div>
                </div>
                
                <!-- Por Caducar -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card info">
                        <div class="stat-icon bg-info">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="porCaducar">--</h3>
                            <p>Por Caducar</p>
                        </div>
                    </div>
                </div>
                
                <!-- Caducados -->
                <div class="col-md-6 col-lg-4 animate-card">
                    <div class="stat-card dark">
                        <div class="stat-icon bg-dark">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="stat-info">
                            <h3 id="caducados">--</h3>
                            <p>Caducados</p>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Tarjetas de Opciones Principales -->
        <div class="row g-4 justify-content-center">
            
            <!-- Crear Productos -->
            <div class="col-md-6 col-lg-4 animate-card">
                <div class="option-card position-relative">
                    <span class="badge-floating">
                        <i class="bi bi-star-fill me-1"></i>Popular
                    </span>
                    <div class="card-icon-wrapper bg-gradient-primary">
                        <i class="bi bi-bag-plus-fill"></i>
                    </div>
                    <div class="card-content">
                        <h5>Crear Productos</h5>
                        <p>Registra nuevos productos al inventario con código de barras automático</p>
                        <a href="./agg_Productos.php" class="btn btn-card btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Crear Nuevo
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Ingreso de Mercadería -->
            <div class="col-md-6 col-lg-4 animate-card">
                <div class="option-card position-relative">
                    <span class="badge-floating">
                        <i class="bi bi-arrow-up me-1"></i>Entrada
                    </span>
                    <div class="card-icon-wrapper bg-gradient-success">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                    <div class="card-content">
                        <h5>Ingreso de Mercadería</h5>
                        <p>Actualiza el stock de productos existentes con nuevas entradas</p>
                        <a href="./ingreso_productos.php" class="btn btn-card btn-success">
                            <i class="bi bi-arrow-up-circle me-2"></i>Ingresar Stock
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Ver Inventario (Próximamente) -->
            <div class="col-md-6 col-lg-4 animate-card">
                <div class="option-card position-relative" style="opacity: 0.75;">
                    <span class="badge-floating" style="background: rgba(108, 117, 125, 0.9); color: white;">
                        <i class="bi bi-clock-history me-1"></i>Próximamente
                    </span>
                    <div class="card-icon-wrapper bg-gradient-info" style="filter: grayscale(40%);">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <div class="card-content">
                        <h5>Ver Inventario</h5>
                        <p>Consulta el listado completo de productos y su disponibilidad</p>
                        <button class="btn btn-card btn-secondary" disabled style="cursor: not-allowed; background: #6c757d !important;">
                            <i class="bi bi-tools me-2"></i>En Desarrollo
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
        
        <!-- Accesos Rápidos -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-lightning-fill text-warning me-2"></i>Accesos Rápidos
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="./agg_Productos.php" class="btn btn-outline-primary btn-sm rounded-pill">
                                <i class="bi bi-plus me-1"></i>Nuevo Producto
                            </a>
                            <a href="./ingreso_productos.php" class="btn btn-outline-success btn-sm rounded-pill">
                                <i class="bi bi-box-arrow-in-down me-1"></i>Ingresar Stock
                            </a>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled title="Próximamente">
                                <i class="bi bi-search me-1"></i>Buscar Producto
                            </button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled title="Próximamente">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i>Reportes
                            </button>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill" disabled title="Próximamente">
                                <i class="bi bi-tags me-1"></i>Categorías
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
// =============================================
// ⭐ CARGAR ESTADÍSTICAS EN TIEMPO REAL
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('📊 Iniciando panel de inventario...');
    cargarEstadisticas();
    
    // Botón actualizar
    var btnActualizar = document.getElementById('btnActualizar');
    if (btnActualizar) {
        btnActualizar.addEventListener('click', cargarEstadisticas);
    }
    
    // Auto-actualizar cada 30 segundos
    setInterval(cargarEstadisticas, 30000);
});

function cargarEstadisticas() {
    var btn = document.getElementById('btnActualizar');
    if (btn) {
        btn.classList.add('loading');
        btn.disabled = true;
    }
    
    var url = '/api/inventario/estadisticas_inventario.php?_nocache=' + Date.now();
    
    fetch(url, {
        method: 'GET',
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache'
        }
    })
    .then(function(response) {
        if (!response.ok) throw new Error('Error HTTP: ' + response.status);
        return response.json();
    })
    .then(function(data) {
        console.log('📊 Estadísticas:', data);
        
        if (data.success && data.estadisticas) {
            var stats = data.estadisticas;
            
            // Actualizar valores
            animarNumero('totalProductos', stats.totalProductos);
            animarNumero('stockTotal', stats.stockTotal);
            animarNumero('stockBajo', stats.stockBajo);
            animarNumero('sinStock', stats.sinStock);
            animarNumero('porCaducar', stats.productosPorCaducar);
            animarNumero('caducados', stats.productosCaducados);
            
            // Timestamp
            var el = document.getElementById('lastUpdate');
            if (el) {
                el.textContent = 'Última actualización: ' + new Date().toLocaleTimeString('es-EC');
            }
        }
    })
    .catch(function(e) {
        console.error('❌ Error:', e);
    })
    .finally(function() {
        if (btn) {
            btn.classList.remove('loading');
            btn.disabled = false;
        }
    });
}

function animarNumero(elementId, valorFinal) {
    var elemento = document.getElementById(elementId);
    if (!elemento) return;
    
    var valorActual = parseInt(elemento.textContent) || 0;
    var fin = parseInt(valorFinal) || 0;
    
    if (valorActual === fin) {
        elemento.textContent = fin;
        return;
    }
    
    var duracion = 400;
    var pasos = 15;
    var incremento = (fin - valorActual) / pasos;
    var paso = 0;
    
    var intervalo = setInterval(function() {
        paso++;
        valorActual += incremento;
        elemento.textContent = Math.round(valorActual);
        if (paso >= pasos) {
            clearInterval(intervalo);
            elemento.textContent = fin;
        }
    }, duracion / pasos);
}
</script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>