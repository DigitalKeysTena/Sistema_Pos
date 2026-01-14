<?php
// public_html/model/vendedor/php/cierre_caja.php
session_start();

require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';

require_role([2,1]); // Vendedor y Admin

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<link rel="stylesheet" href="../css/cierre_caja.css?v=2.0">

<div id="main-content">
    <div class="container-fluid py-4">

        <!-- HEADER -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-card animate-item">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="mb-2">
                                <i class="bi bi-cash-coin me-3"></i>
                                Cierre de Caja
                            </h2>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-calendar-date me-2"></i>
                                <span id="fechaActual"></span>
                            </p>
                        </div>
                        <div>
                            <a href="./vendedor.php" class="btn btn-light btn-lg">
                                <i class="bi bi-arrow-left me-2"></i>
                                Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ESTADÍSTICAS DEL DÍA -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="stats-card success animate-item" style="animation-delay: 0.1s;">
                    <div class="stats-icon success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stats-value" id="totalVentas">$0.00</div>
                    <div class="stats-label">Total en Ventas</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="stats-card primary animate-item" style="animation-delay: 0.2s;">
                    <div class="stats-icon primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="stats-value" id="numeroVentas">0</div>
                    <div class="stats-label">Número de Ventas</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="stats-card info animate-item" style="animation-delay: 0.3s;">
                    <div class="stats-icon info">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stats-value" id="montoInicial">$0.00</div>
                    <div class="stats-label">Monto Inicial</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="stats-card warning animate-item" style="animation-delay: 0.4s;">
                    <div class="stats-icon warning">
                        <i class="bi bi-calculator"></i>
                    </div>
                    <div class="stats-value" id="esperadoCaja">$0.00</div>
                    <div class="stats-label">Esperado en Caja</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- FORMULARIO DE CIERRE -->
            <div class="col-lg-8 col-12 mb-4">
                <div class="main-card animate-item" style="animation-delay: 0.5s;">
                    <div class="card-header-custom">
                        <h5>
                            <i class="bi bi-pencil-square"></i>
                            Registro de Cierre
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- ========================================
                             SECCIÓN 1: CONTEO DE EFECTIVO
                        ======================================== -->
                        <div class="form-section">
                            <h6>
                                <i class="bi bi-currency-dollar"></i>
                                Totales por Método de Pago
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>
                                            <i class="bi bi-wallet2"></i>
                                            Total en Efectivo
                                        </label>
                                        <input type="number" 
                                               class="form-control input-currency" 
                                               id="totalEfectivo" 
                                               placeholder="0.00" 
                                               step="0.01" 
                                               min="0" 
                                               readonly 
                                               style="background-color: #e9ecef; cursor: not-allowed;">
                                        <small class="text-muted mt-2 d-block">
                                            <i class="bi bi-info-circle"></i> 
                                            Calculado desde el desglose
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="input-group-custom">
                                        <label>
                                            <i class="bi bi-arrow-left-right"></i>
                                            Total en Transferencias
                                        </label>
                                        <input type="number" 
                                               class="form-control input-currency" 
                                               id="totalTransferencias"
                                               placeholder="0.00" 
                                               step="0.01" 
                                               min="0" 
                                               readonly
                                               style="background-color: #e9ecef; cursor: not-allowed;">
                                        <small class="text-muted mt-2 d-block">
                                            <i class="bi bi-info-circle"></i> 
                                            Calculado automáticamente
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botón para expandir desglose - CORREGIDO -->
                            <div class="mt-3">
                                <button class="btn btn-outline-primary btn-sm" 
                                        type="button" 
                                        id="btnToggleDesglose"
                                        data-bs-target="#desgloseBilletes">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    Desglose Detallado de Billetes y Monedas
                                </button>
                                
                                <!-- DESGLOSE DE BILLETES Y MONEDAS - Sin collapse de Bootstrap -->
                                <div class="mt-3" id="desgloseBilletes" style="display: none;">
                                    <div class="desglose-container">
                                        
                                        <!-- BILLETES -->
                                        <div class="desglose-section">
                                            <h6>
                                                <i class="bi bi-cash"></i>
                                                Billetes
                                            </h6>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash-stack"></i> $100
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="100" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash-stack"></i> $50
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="50" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash-stack"></i> $20
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="20" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash-stack"></i> $10
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="10" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash-stack"></i> $5
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="5" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-cash"></i> $1
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="1" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                        </div>
                                        
                                        <!-- MONEDAS -->
                                        <div class="desglose-section">
                                            <h6>
                                                <i class="bi bi-coin"></i>
                                                Monedas
                                            </h6>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $1.00
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="1" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $0.50
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="0.50" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $0.25
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="0.25" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $0.10
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="0.10" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $0.05
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="0.05" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                            
                                            <div class="denominacion-item">
                                                <span class="denominacion-label">
                                                    <i class="bi bi-coin"></i> $0.01
                                                </span>
                                                <input type="number" class="denominacion-input" 
                                                       data-valor="0.01" min="0" placeholder="0">
                                                <span class="denominacion-total">$0.00</span>
                                            </div>
                                        </div>
                                        
                                        <!-- TOTAL DEL DESGLOSE -->
                                        <div class="desglose-total">
                                            <div class="desglose-total-label">Total Calculado</div>
                                            <div class="desglose-total-value" id="totalDesglose">$0.00</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================
                             SECCIÓN 2: GASTOS Y RETIROS
                        ======================================== -->
                        <div class="form-section">
                            <h6>
                                <i class="bi bi-dash-circle"></i>
                                Gastos y Retiros del Día
                            </h6>
                            
                            <!-- TABS -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="gastos-tab" data-bs-toggle="tab" 
                                            data-bs-target="#gastos-panel" type="button" role="tab">
                                        <i class="bi bi-receipt-cutoff me-2"></i>
                                        Gastos
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="retiros-tab" data-bs-toggle="tab" 
                                            data-bs-target="#retiros-panel" type="button" role="tab">
                                        <i class="bi bi-arrow-up-circle me-2"></i>
                                        Retiros
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content">
                                
                                <!-- TAB GASTOS -->
                                <div class="tab-pane fade show active" id="gastos-panel" role="tabpanel">
                                    
                                    <!-- Formulario de Gastos -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-chat-left-text"></i>
                                                    Concepto
                                                </label>
                                                <input type="text" class="form-control" id="conceptoGasto" 
                                                       placeholder="Ej: Compra de materiales">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-currency-dollar"></i>
                                                    Monto
                                                </label>
                                                <input type="number" class="form-control" id="montoGasto" 
                                                       placeholder="0.00" step="0.01" min="0">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-tag"></i>
                                                    Categoría
                                                </label>
                                                <select class="form-control" id="categoriaGasto">
                                                    <option value="General">General</option>
                                                    <option value="Compras">Compras</option>
                                                    <option value="Servicios">Servicios</option>
                                                    <option value="Personal">Personal</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-9">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-pencil"></i>
                                                    Observaciones (Opcional)
                                                </label>
                                                <input type="text" class="form-control" id="observacionesGasto" 
                                                       placeholder="Detalles adicionales...">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-registrar w-100" id="btnRegistrarGasto">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Registrar Gasto
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Tabla de Gastos -->
                                    <div class="tabla-movimientos">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Concepto</th>
                                                    <th>Monto</th>
                                                    <th>Categoría</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaGastos">
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">
                                                        No hay gastos registrados
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Total Gastos -->
                                    <div class="mt-3 text-end">
                                        <strong>Total Gastos: </strong>
                                        <span class="text-primary fs-5" id="totalGastosDisplay">$0.00</span>
                                        <input type="hidden" id="gastos" value="0">
                                    </div>
                                    
                                </div>
                                
                                <!-- TAB RETIROS -->
                                <div class="tab-pane fade" id="retiros-panel" role="tabpanel">
                                    
                                    <!-- Formulario de Retiros -->
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-currency-dollar"></i>
                                                    Monto
                                                </label>
                                                <input type="number" class="form-control" id="montoRetiro" 
                                                       placeholder="0.00" step="0.01" min="0">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-9">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-chat-left-text"></i>
                                                    Motivo
                                                </label>
                                                <input type="text" class="form-control" id="motivoRetiro" 
                                                       placeholder="Ej: Depósito bancario">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-9">
                                            <div class="input-group-custom">
                                                <label>
                                                    <i class="bi bi-pencil"></i>
                                                    Observaciones (Opcional)
                                                </label>
                                                <input type="text" class="form-control" id="observacionesRetiro" 
                                                       placeholder="Detalles adicionales...">
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-registrar w-100" id="btnRegistrarRetiro">
                                                <i class="bi bi-plus-circle me-2"></i>
                                                Registrar Retiro
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Tabla de Retiros -->
                                    <div class="tabla-movimientos">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Hora</th>
                                                    <th>Monto</th>
                                                    <th>Motivo</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaRetiros">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">
                                                        No hay retiros registrados
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Total Retiros -->
                                    <div class="mt-3 text-end">
                                        <strong>Total Retiros: </strong>
                                        <span class="text-primary fs-5" id="totalRetirosDisplay">$0.00</span>
                                        <input type="hidden" id="retiros" value="0">
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>

                        <!-- ========================================
                             SECCIÓN 3: OBSERVACIONES
                        ======================================== -->
                        <div class="form-section">
                            <h6>
                                <i class="bi bi-chat-left-text"></i>
                                Observaciones del Cierre
                            </h6>
                            
                            <div class="input-group-custom">
                                <textarea class="form-control" id="observaciones" rows="4" 
                                          placeholder="Añade cualquier observación sobre el cierre de caja..."></textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ========================================
                 COLUMNA DERECHA: RESUMEN Y ACCIONES
            ======================================== -->
            <div class="col-lg-4 col-12 mb-4">
                <div class="animate-item" style="animation-delay: 0.6s;">
                    
                    <!-- Resumen del Cierre -->
                    <div class="resumen-card">
                        <h5 class="mb-4">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Resumen del Cierre
                        </h5>
                        
                        <div class="resumen-item">
                            <span class="resumen-label">Monto Inicial:</span>
                            <span class="resumen-value" id="resumenMontoInicial">$0.00</span>
                        </div>
                        
                        <div class="resumen-item">
                            <span class="resumen-label">Total Ventas:</span>
                            <span class="resumen-value" id="resumenTotalVentas">$0.00</span>
                        </div>
                        
                        <div class="resumen-item">
                            <span class="resumen-label">Gastos:</span>
                            <span class="resumen-value text-danger" id="resumenGastos">-$0.00</span>
                        </div>
                        
                        <div class="resumen-item">
                            <span class="resumen-label">Retiros:</span>
                            <span class="resumen-value text-danger" id="resumenRetiros">-$0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="resumen-item">
                            <span class="resumen-label"><strong>Total Esperado:</strong></span>
                            <span class="resumen-value fw-bold" id="resumenEsperado">$0.00</span>
                        </div>
                        
                        <div class="resumen-item">
                            <span class="resumen-label"><strong>Total Contado:</strong></span>
                            <span class="resumen-value fw-bold" id="resumenContado">$0.00</span>
                        </div>
                        
                        <hr>
                        
                        <div class="resumen-item">
                            <span class="resumen-label"><strong>Diferencia:</strong></span>
                            <span class="resumen-value fw-bold fs-4" id="resumenDiferencia">$0.00</span>
                        </div>
                    </div>

                    <!-- Alerta de Diferencia -->
                    <div id="alertaDiferencia" class="diferencia-alert" style="display: none;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle me-3" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong id="tituloDiferencia"></strong>
                                <p class="mb-0 mt-1" id="textoDiferencia"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-modern btn-cerrar-caja" id="btnCerrarCaja">
                            <i class="bi bi-lock-fill"></i>
                            Cerrar Caja
                        </button>
                        
                        <button type="button" class="btn btn-modern btn-cancelar" id="btnCancelar">
                            <i class="bi bi-x-circle"></i>
                            Cancelar
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Spinner de Carga -->
<div class="spinner-overlay" id="spinnerOverlay" style="display: none;">
    <div class="spinner-content">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <h5>Procesando cierre de caja...</h5>
        <p class="text-muted mb-0">Por favor espere</p>
    </div>
</div>

<!-- Cargar JavaScript -->
<script src="../js/cierre_caja.js?v=2.0"></script>

<!-- Script para toggle del desglose sin Bootstrap collapse -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnDesglose = document.getElementById('btnToggleDesglose');
    const desglose = document.getElementById('desgloseBilletes');
    
    if (btnDesglose && desglose) {
        btnDesglose.addEventListener('click', function() {
            if (desglose.style.display === 'none') {
                desglose.style.display = 'block';
                btnDesglose.innerHTML = '<i class="bi bi-dash-circle me-2"></i>Ocultar Desglose';
            } else {
                desglose.style.display = 'none';
                btnDesglose.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Desglose Detallado de Billetes y Monedas';
            }
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>
