<?php
// public_html/model/vendedor/php/mis_ventas.php
session_start();

require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';

require_role([2,1]); // Vendedor y Admin

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<style>
/* ============================================
   ESTILOS MEJORADOS PARA HISTORIAL DE VENTAS
   ============================================ */
:root {
    --primary-color: #198754;
    --secondary-color: #0d6efd;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #0dcaf0;
    --border-radius: 12px;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

/* Cards mejoradas */
.venta-card {
    transition: var(--transition);
    border-radius: var(--border-radius);
    border: 1px solid #e9ecef;
    margin-bottom: 0.5rem;
}

.venta-card:hover {
    transform: translateX(5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    border-color: var(--primary-color);
    background: linear-gradient(to right, #f8f9fa, #ffffff);
}

/* Badges personalizados */
.badge-metodo {
    font-size: 0.9rem;
    padding: 8px 15px;
    border-radius: 8px;
    font-weight: 600;
}

.total-venta {
    font-size: 1.8rem;
    font-weight: bold;
    color: var(--primary-color);
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Filtros responsivos */
.filtros-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: var(--border-radius);
}

/* Estadísticas */
.stat-card {
    border-left: 4px solid var(--primary-color);
    transition: var(--transition);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.3;
}

/* Loading mejorado */
.loading-container {
    padding: 4rem 0;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
    border-width: 4px;
}

/* Responsive */
@media (max-width: 767px) {
    .venta-card {
        font-size: 0.9rem;
    }
    
    .total-venta {
        font-size: 1.4rem;
    }
    
    .badge-metodo {
        font-size: 0.75rem;
        padding: 6px 10px;
    }
    
    .stat-icon {
        font-size: 2rem;
    }
    
    .filtros-card .row > div {
        margin-bottom: 1rem;
    }
}

/* Animaciones */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.venta-item {
    animation: slideIn 0.3s ease forwards;
}

/* Empty state */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-state-icon {
    font-size: 5rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

/* Modal detalle mejorado */
.modal-detalle-producto {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}

.table-detalle {
    font-size: 0.95rem;
}

.table-detalle th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    font-weight: 600;
}
</style>

<!-- MAIN CONTENT -->
<div id="main-content">
    <div class="container-fluid py-4">
        
        <!-- HEADER MEJORADO -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body bg-gradient-primary text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h2 class="mb-1"><i class="bi bi-clock-history me-2"></i>Historial de Ventas</h2>
                                <small class="opacity-75">Vendedor: <?php echo htmlspecialchars($_SESSION['Nombre_Usuario']); ?></small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="./punto_venta.php" class="btn btn-light btn-sm">
                                    <i class="bi bi-cart-plus me-2"></i>
                                    <span class="d-none d-sm-inline">Nueva Venta</span>
                                </a>
                                <a href="./vendedor.php" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-arrow-left me-2"></i>
                                    <span class="d-none d-sm-inline">Volver</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTROS MEJORADOS -->
        <div class="card shadow-sm mb-4 filtros-card">
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-3 col-sm-6 col-12">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar me-2"></i>Fecha Desde
                        </label>
                        <input 
                            type="date" 
                            class="form-control" 
                            id="fechaDesde" 
                            value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-check me-2"></i>Fecha Hasta
                        </label>
                        <input 
                            type="date" 
                            class="form-control" 
                            id="fechaHasta" 
                            value="<?php echo date('Y-m-d'); ?>">
                    
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <label class="form-label fw-bold">
                            <i class="bi bi-credit-card me-2"></i>Método de Pago
                        </label>
                        <select class="form-select" id="filtroMetodo">
                            <option value="">Todos los métodos</option>
                            <option value="EFECTIVO">💵 Efectivo</option>
                            <option value="TARJETA">💳 Tarjeta</option>
                            <option value="TRANSFERENCIA">🏦 Transferencia</option>
                        </select>
                    </div>

                    <div class="col-md-3 col-sm-6 col-12">
                        <label class="form-label fw-bold d-block">
                            <i class="bi bi-funnel me-2"></i>Acciones
                        </label>
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-primary" onclick="cargarVentas()">
                                <i class="bi bi-search me-1"></i>Buscar
                            </button>
                            <button class="btn btn-outline-secondary" onclick="filtrarHoy()">
                                <i class="bi bi-calendar-day me-1"></i>Hoy
                            </button>
                        </div>
                    </div>

                </div>
                
                <!-- Indicador de rango actual -->
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Rango actual:</strong> 
                        <span id="rangoActual">Últimos 7 días</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- ESTADÍSTICAS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card shadow-sm stat-card border-start border-5 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted fw-bold">VENTAS REALIZADAS</small>
                                <h3 class="mb-0 mt-2 text-primary" id="totalVentas">0</h3>
                            </div>
                            <i class="bi bi-receipt-cutoff stat-icon text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-12">
                <div class="card shadow-sm stat-card border-start border-5 border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted fw-bold">MONTO TOTAL</small>
                                <h3 class="mb-0 mt-2 text-success" id="montoTotal">$0.00</h3>
                            </div>
                            <i class="bi bi-cash-stack stat-icon text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-6 col-12">
                <div class="card shadow-sm stat-card border-start border-5 border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted fw-bold">PRODUCTOS VENDIDOS</small>
                                <h3 class="mb-0 mt-2 text-info" id="productosTotal">0</h3>
                            </div>
                            <i class="bi bi-box-seam stat-icon text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTA DE VENTAS -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="bi bi-list-check me-2"></i>Listado de Ventas
                    </h5>
                    <button class="btn btn-success btn-sm" onclick="exportarVentas()">
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        <span class="d-none d-sm-inline">Exportar</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="ventasContainer">
                    <!-- Las ventas se cargan aquí dinámicamente -->
                </div>
            </div>
        </div>

    </div>
</div>

<!-- MODAL DETALLE VENTA -->
<div class="modal fade" id="modalDetalleVenta" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Detalle de Venta
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detalleVentaContent">
                    <!-- El contenido se carga dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-primary" onclick="imprimirFactura()">
                    <i class="bi bi-printer me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// VARIABLES GLOBALES
// ============================================
let modalDetalle;
let ventasData = [];

// ============================================
// INICIALIZACIÓN
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando Historial de Ventas');
    
    // Inicializar modal
    const modalElement = document.getElementById('modalDetalleVenta');
    if (modalElement) {
        modalDetalle = new bootstrap.Modal(modalElement);
    }
    
    // Event listeners para actualizar el rango
    document.getElementById('fechaDesde').addEventListener('change', function() {
        actualizarRangoActual();
        cargarVentas();
    });
    
    document.getElementById('fechaHasta').addEventListener('change', function() {
        actualizarRangoActual();
        cargarVentas();
    });
    
    // Actualizar rango inicial
    actualizarRangoActual();
    
    // Cargar ventas inicial
    cargarVentas();
});

// ============================================
// FUNCIONES AUXILIARES DE FILTRADO
// ============================================

function filtrarHoy() {
    const hoy = '<?php echo date("Y-m-d"); ?>'; // Usar fecha del servidor PHP
    console.log('📅 Filtrando por hoy:', hoy);
    
    document.getElementById('fechaDesde').value = hoy;
    document.getElementById('fechaHasta').value = hoy;
    document.getElementById('rangoActual').textContent = 'Solo hoy - ' + formatearFecha(hoy);
    cargarVentas();
}

function filtrarUltimos7Dias() {
    const hoy = '<?php echo date("Y-m-d"); ?>';
    const hace7dias = '<?php echo date("Y-m-d", strtotime("-7 days")); ?>';
    
    console.log('📅 Filtrando últimos 7 días:', hace7dias, 'al', hoy);
    
    document.getElementById('fechaDesde').value = hace7dias;
    document.getElementById('fechaHasta').value = hoy;
    document.getElementById('rangoActual').textContent = 'Últimos 7 días';
    cargarVentas();
}

function actualizarRangoActual() {
    const desde = document.getElementById('fechaDesde').value;
    const hasta = document.getElementById('fechaHasta').value;
    const rangoSpan = document.getElementById('rangoActual');
    
    const hoy = new Date().toISOString().split('T')[0];
    
    if (desde === hasta && desde === hoy) {
        rangoSpan.textContent = 'Solo hoy';
    } else if (desde === hasta) {
        rangoSpan.textContent = `Solo ${formatearFecha(desde)}`;
    } else {
        rangoSpan.textContent = `${formatearFecha(desde)} al ${formatearFecha(hasta)}`;
    }
}

function formatearFecha(fecha) {
    const [y, m, d] = fecha.split('-');
    return `${d}/${m}/${y}`;
}

// ============================================
// FUNCIÓN PRINCIPAL: CARGAR VENTAS
// ============================================
async function cargarVentas() {
    mostrarCargando();
    
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    const metodo = document.getElementById('filtroMetodo').value;
    
    const params = new URLSearchParams({
        desde: fechaDesde,
        hasta: fechaHasta
    });
    
    if (metodo) params.append('metodo', metodo);
    
    const url = `/api/vendedor/obtener_ventas.php?${params.toString()}`;
    console.log('🔄 Cargando ventas desde:', url);
    console.log('📅 Filtros:', {
        desde: fechaDesde,
        hasta: fechaHasta,
        metodo: metodo || 'TODOS'
    });
    
    try {
        const response = await fetch(url);
        console.log('📡 Response status:', response.status);
        
        const responseText = await response.text();
        console.log('📄 Response text:', responseText.substring(0, 500));
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (parseError) {
            console.error('❌ Error parseando JSON:', parseError);
            console.error('📄 Respuesta recibida:', responseText);
            throw new Error('La respuesta del servidor no es JSON válido');
        }
        
        console.log('✅ Datos recibidos:', data);
        
        if (data.success) {
            ventasData = data.ventas || [];
            console.log(`✅ ${ventasData.length} ventas cargadas`);
            
            if (ventasData.length > 0) {
                renderizarVentas(ventasData);
                actualizarResumen(data.resumen || {});
            } else {
                mostrarEstadoVacio();
            }
            
            if (data.debug) {
                console.log('🐛 Debug info:', data.debug);
            }
        } else {
            throw new Error(data.error || data.message || 'Error desconocido');
        }
        
    } catch (error) {
        console.error('❌ Error al cargar ventas:', error);
        mostrarError(error.message);
    }
}

// ============================================
// FUNCIÓN: MOSTRAR LOADING
// ============================================
function mostrarCargando() {
    const container = document.getElementById('ventasContainer');
    container.innerHTML = `
        <div class="loading-container text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando ventas...</p>
        </div>
    `;
}

// ============================================
// FUNCIÓN: RENDERIZAR VENTAS
// ============================================
function renderizarVentas(ventas) {
    const container = document.getElementById('ventasContainer');
    
    if (!ventas || ventas.length === 0) {
        mostrarEstadoVacio();
        return;
    }
    
    let html = '<div class="list-group list-group-flush">';
    
    ventas.forEach((venta, index) => {
        const fecha = new Date(venta.fecha);
        const fechaFormateada = fecha.toLocaleDateString('es-EC', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Badge de método de pago
        let metodoBadge = '';
        switch(venta.metodo_pago) {
            case 'EFECTIVO':
                metodoBadge = '<span class="badge bg-success badge-metodo">💵 Efectivo</span>';
                break;
            case 'TARJETA':
                metodoBadge = '<span class="badge bg-primary badge-metodo">💳 Tarjeta</span>';
                break;
            case 'TRANSFERENCIA':
                metodoBadge = '<span class="badge bg-info badge-metodo">🏦 Transferencia</span>';
                break;
            default:
                metodoBadge = `<span class="badge bg-secondary badge-metodo">${venta.metodo_pago}</span>`;
        }
        
        // Badge de estado
        let estadoBadge = '';
        switch(venta.estado) {
            case 'COMPLETADA':
                estadoBadge = '<span class="badge bg-success">✓ Completada</span>';
                break;
            case 'PENDIENTE':
                estadoBadge = '<span class="badge bg-warning">⏳ Pendiente</span>';
                break;
            case 'CANCELADA':
                estadoBadge = '<span class="badge bg-danger">✗ Cancelada</span>';
                break;
            default:
                estadoBadge = `<span class="badge bg-secondary">${venta.estado}</span>`;
        }
        
        html += `
            <div class="list-group-item list-group-item-action venta-card venta-item" 
                 style="animation-delay: ${index * 0.05}s;"
                 onclick="verDetalle(${venta.id})">
                <div class="row align-items-center">
                    
                    <!-- Columna 1: Info básica -->
                    <div class="col-lg-4 col-md-6 col-12 mb-2 mb-md-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-receipt text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold">Venta #${venta.id}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3 me-1"></i>${fechaFormateada}
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna 2: Productos y método -->
                    <div class="col-lg-3 col-md-6 col-6 mb-2 mb-lg-0">
                        <small class="text-muted d-block">Productos</small>
                        <strong>${venta.total_items || 0} items</strong>
                        <div class="mt-1">${metodoBadge}</div>
                    </div>
                    
                    <!-- Columna 3: Total -->
                    <div class="col-lg-3 col-md-6 col-6 text-md-center mb-2 mb-lg-0">
                        <small class="text-muted d-block">Total</small>
                        <div class="total-venta">$${parseFloat(venta.total).toFixed(2)}</div>
                    </div>
                    
                    <!-- Columna 4: Estado y acción -->
                    <div class="col-lg-2 col-md-6 col-12 text-lg-end">
                        <div class="mb-2">${estadoBadge}</div>
                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); verDetalle(${venta.id})">
                            <i class="bi bi-eye me-1"></i>Ver detalle
                        </button>
                    </div>
                    
                </div>
                ${venta.notas ? `<div class="mt-2 pt-2 border-top"><small class="text-muted"><i class="bi bi-sticky me-1"></i><strong>Nota:</strong> ${venta.notas}</small></div>` : ''}
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// ============================================
// FUNCIÓN: ACTUALIZAR RESUMEN
// ============================================
function actualizarResumen(resumen) {
    document.getElementById('totalVentas').textContent = resumen.total_ventas || 0;
    document.getElementById('montoTotal').textContent = '$' + parseFloat(resumen.monto_total || 0).toFixed(2);
    document.getElementById('productosTotal').textContent = resumen.productos_total || 0;
}

// ============================================
// FUNCIÓN: MOSTRAR ESTADO VACÍO
// ============================================
function mostrarEstadoVacio() {
    const container = document.getElementById('ventasContainer');
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    const hoy = '<?php echo date("Y-m-d"); ?>';
    
    const esHoy = (fechaDesde === hoy && fechaHasta === hoy);
    const esMismoDia = (fechaDesde === fechaHasta);
    
    let mensaje = '';
    let sugerencias = '';
    
    if (esHoy) {
        mensaje = 'No hay ventas registradas hoy';
        sugerencias = `
            <ul class="text-muted text-start" style="max-width: 400px; margin: 0 auto;">
                <li><small>¿Aún no has realizado ventas hoy?</small></li>
                <li><small>Usa el botón "Nueva Venta" para registrar una venta</small></li>
                <li><small>O amplía el rango para ver ventas anteriores</small></li>
            </ul>
        `;
    } else if (esMismoDia) {
        mensaje = `No hay ventas registradas el ${formatearFecha(fechaDesde)}`;
        sugerencias = `
            <ul class="text-muted text-start" style="max-width: 400px; margin: 0 auto;">
                <li><small>No se realizaron ventas en esta fecha</small></li>
                <li><small>Prueba ampliando el rango de fechas</small></li>
                <li><small>O usa el botón "Hoy" para ver ventas de hoy</small></li>
            </ul>
        `;
    } else {
        mensaje = 'No hay ventas en el período seleccionado';
        sugerencias = `
            <ul class="text-muted text-start" style="max-width: 400px; margin: 0 auto;">
                <li><small>No se realizaron ventas en este rango de fechas</small></li>
                <li><small>Intenta ampliar el rango de fechas</small></li>
                <li><small>Verifica el método de pago seleccionado</small></li>
            </ul>
        `;
    }
    
    container.innerHTML = `
        <div class="empty-state">
            <i class="bi bi-inbox empty-state-icon d-block"></i>
            <h4 class="text-muted">${mensaje}</h4>
            <div class="alert alert-info d-inline-block mt-3">
                <small>
                    <strong>📅 Período:</strong> 
                    ${fechaDesde === fechaHasta ? formatearFecha(fechaDesde) : formatearFecha(fechaDesde) + ' al ' + formatearFecha(fechaHasta)}
                </small>
            </div>
            <div class="mt-3">
                <p class="text-muted"><small><strong>💡 Sugerencias:</strong></small></p>
                ${sugerencias}
            </div>
            <div class="mt-3 d-flex gap-2 justify-content-center flex-wrap">
                ${!esHoy ? `
                    <button class="btn btn-primary" onclick="filtrarHoy()">
                        <i class="bi bi-calendar-day me-2"></i>Ver ventas de hoy
                    </button>
                ` : ''}
                <button class="btn btn-outline-secondary" onclick="filtrarUltimos7Dias()">
                    <i class="bi bi-calendar-week me-2"></i>Últimos 7 días
                </button>
                <button class="btn btn-outline-secondary" onclick="cargarVentas()">
                    <i class="bi bi-arrow-clockwise me-2"></i>Recargar
                </button>
            </div>
        </div>
    `;
    actualizarResumen({total_ventas: 0, monto_total: 0, productos_total: 0});
}

// ============================================
// FUNCIÓN: MOSTRAR ERROR
// ============================================
function mostrarError(mensaje = null) {
    const container = document.getElementById('ventasContainer');
    
    container.innerHTML = `
        <div class="alert alert-danger m-4">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar ventas
            </h5>
            <p class="mb-3">No se pudieron cargar las ventas del servidor.</p>
            ${mensaje ? `
                <div class="alert alert-warning mb-3">
                    <small><strong>Detalle técnico:</strong></small><br>
                    <code style="font-size: 0.85em;">${mensaje}</code>
                </div>
            ` : ''}
            <div class="border-top pt-3 mt-3">
                <p class="mb-2"><small><strong>🔍 Para diagnosticar:</strong></small></p>
                <ol class="small">
                    <li>Abre la consola (F12) → Pestaña Console</li>
                    <li>Busca mensajes de error en rojo</li>
                    <li>Verifica que existe: <code>/api/vendedor/obtener_ventas.php</code></li>
                    <li>Verifica que el puente apunta correctamente a <code>/src/controllers/vendedor/obtener_ventas.php</code></li>
                </ol>
            </div>
            <hr>
            <button class="btn btn-danger" onclick="cargarVentas()">
                <i class="bi bi-arrow-clockwise me-2"></i>Reintentar
            </button>
        </div>
    `;
}

// ============================================
// FUNCIÓN: VER DETALLE DE VENTA
// ============================================
async function verDetalle(ventaId) {
    const content = document.getElementById('detalleVentaContent');
    
    content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 text-muted">Cargando detalle...</p>
        </div>
    `;
    
    modalDetalle.show();
    
    try {
        const url = `/api/vendedor/detalle_venta.php?id_venta=${ventaId}`;
        console.log('🔍 Cargando detalle de venta #' + ventaId);
        console.log('📡 URL:', url);
        
        const response = await fetch(url);
        const responseText = await response.text();
        console.log('📄 Response:', responseText.substring(0, 500));
        
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        
        const data = JSON.parse(responseText);
        console.log('✅ Detalle recibido:', data);
        
        if (data.success) {
            renderizarDetalle(data.data);
        } else {
            throw new Error(data.message || data.error || 'Error desconocido');
        }
        
    } catch (error) {
        console.error('❌ Error al cargar detalle:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error al cargar el detalle
                <br><small>${error.message}</small>
            </div>
        `;
    }
}

// ============================================
// FUNCIÓN: RENDERIZAR DETALLE
// ============================================
function renderizarDetalle(detalles) {
    console.log('📦 Renderizando detalle:', detalles);
    
    if (!detalles || detalles.length === 0) {
        document.getElementById('detalleVentaContent').innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-circle me-2"></i>
                No se encontraron detalles para esta venta
            </div>
        `;
        return;
    }
    
    let html = `
        <h6 class="mb-3 fw-bold"><i class="bi bi-box-seam me-2"></i>Productos Vendidos</h6>
        <div class="table-responsive mb-4">
            <table class="table table-hover table-detalle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio Unit.</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>`;
    
    let totalGeneral = 0;
    
    detalles.forEach(d => {
        const subtotal = parseFloat(d.SubTotal || 0);
        totalGeneral += subtotal;
        
        html += `<tr>
            <td><span class="badge bg-secondary">#${d.Id_Detalle}</span></td>
            <td>
                <strong>${d.Nombre_Producto || 'Producto sin nombre'}</strong><br>
                <small class="text-muted">Código: ${d.Codigo_Producto || 'N/A'}</small>
            </td>
            <td class="text-center"><span class="badge bg-info">${d.Cantidad}</span></td>
            <td class="text-end">$${parseFloat(d.Precio_Unitario).toFixed(2)}</td>
            <td class="text-end"><strong class="text-primary">$${subtotal.toFixed(2)}</strong></td>
        </tr>`;
    });
    
    html += `</tbody></table></div>
        <div class="border-top pt-4">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">TOTAL:</h5>
                        <h3 class="mb-0 text-success">$${totalGeneral.toFixed(2)}</h3>
                    </div>
                </div>
            </div>
        </div>`;
    
    document.getElementById('detalleVentaContent').innerHTML = html;
}

// ============================================
// FUNCIONES ADICIONALES
// ============================================
function exportarVentas() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Exportación de Ventas',
            html: `
                <p>El módulo de exportación se encuentra en desarrollo.</p>
                <p class="mb-2"><strong>Formatos disponibles próximamente:</strong></p>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <div class="text-center">
                        <i class="bi bi-file-earmark-excel text-success" style="font-size: 2rem;"></i>
                        <p class="small mb-0 mt-1">Excel</p>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-file-earmark-pdf text-danger" style="font-size: 2rem;"></i>
                        <p class="small mb-0 mt-1">PDF</p>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-filetype-csv text-info" style="font-size: 2rem;"></i>
                        <p class="small mb-0 mt-1">CSV</p>
                    </div>
                </div>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#198754',
            footer: '<small class="text-muted">Mientras tanto, puedes ver y copiar los datos de la tabla</small>'
        });
    } else {
        alert('⚠️ Módulo de Exportación en Desarrollo\n\nFormatos disponibles próximamente: Excel, PDF y CSV');
    }
}

function imprimirFactura() {
    // Verificar si SweetAlert2 está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Módulo en Desarrollo',
            html: `
                <p>El módulo de impresión se encuentra actualmente en desarrollo.</p>
                <p class="mb-0"><strong>Próximamente podrás:</strong></p>
                <ul class="text-start mt-2">
                    <li>Imprimir facturas en formato PDF</li>
                    <li>Personalizar el diseño de impresión</li>
                    <li>Incluir logo de tu empresa</li>
                    <li>Enviar por correo electrónico</li>
                </ul>
            `,
            confirmButtonText: 'Entendido',
            confirmButtonColor: '#0d6efd'
        });
    } else {
        alert('⚠️ Módulo de Impresión en Desarrollo\n\nEsta funcionalidad estará disponible próximamente.');
    }
}
</script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>