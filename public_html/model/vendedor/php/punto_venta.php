

<?php
session_start();

require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/config/conection.php';
require_once __DIR__ . '/../../../../src/security/auth.php';

// ⭐ AGREGAR ESTA LÍNEA:
require_once __DIR__ . '/../../../../src/middleware/verificar_caja_abierta.php';

require_role([2,1]); // Vendedor y Admin

// ⭐ AHORA SÍ PUEDES USAR LA FUNCIÓN:
$usuario_id = $_SESSION['Id_Login_Usuario'];
$caja = requiereCajaAbierta($pdo, $usuario_id, true);
// Si no hay caja, redirige automáticamente a apertura_caja.php

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';

?>
<script src="../js/bloqueo_ventas_sin_caja.js?=V2"></script>
<style>
/* ========================================
   PUNTO DE VENTA SIMPLIFICADO
======================================== */

/* Fondo general */
#main-content {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    min-height: calc(100vh - 60px);
    padding: 15px;
}

/* Header del punto de venta */
.venta-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    padding: 18px 20px;
    border-radius: 15px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
    margin-bottom: 15px;
}

.venta-header h3 {
    color: white;
    font-weight: 700;
    margin: 0;
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.venta-header small {
    color: rgba(255,255,255,0.9);
    position: relative;
    z-index: 1;
}

/* Cards mejorados */
.card-modern {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    border: none;
    transition: all 0.3s ease;
    overflow: hidden;
}

.card-modern:hover {
    box-shadow: 0 15px 50px rgba(0,0,0,0.12);
}

.card-header-modern {
    padding: 15px 20px;
    border: none;
    font-weight: 700;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    overflow: hidden;
}

.card-header-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    opacity: 0.1;
    background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
}

.card-header-buscar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.card-header-carrito {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.card-body-modern {
    padding: 20px;
}

/* Footer del carrito */
.card-footer-modern {
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-top: 2px solid #e9ecef;
}

/* Inputs mejorados */
.form-control-modern, .form-select-modern {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background-color: #fff;
}

.form-control-modern:focus, .form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.15);
}

/* Input group mejorado */
.input-group-modern {
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.input-group-modern .input-group-text {
    border: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-right: 2px solid #dee2e6;
    padding: 12px 16px;
}

.input-group-modern .form-control {
    border: none;
    border-radius: 0;
}

.input-group-modern .btn {
    border: none;
    padding: 12px 20px;
    font-weight: 600;
}

/* Sugerencias de búsqueda */
.search-suggestions {
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    margin-top: 8px;
    border: 2px solid #e9ecef;
}

.suggestion-item {
    padding: 12px 16px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.suggestion-item:hover {
    background: linear-gradient(135deg, #f0f7ff 0%, #e8f4ff 100%);
    transform: translateX(5px);
}

.suggestion-item:last-child {
    border-bottom: none;
}

/* Tabla del carrito */
.table-carrito {
    margin: 0;
}

.table-carrito thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table-carrito thead th {
    font-weight: 700;
    color: #495057;
    border: none;
    padding: 12px;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
}

.table-carrito tbody tr {
    transition: all 0.3s ease;
}

.table-carrito tbody tr:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #f0f0f0 100%);
}

.table-carrito tbody td {
    padding: 12px;
    vertical-align: middle;
    border-color: #f0f0f0;
    font-size: 0.9rem;
}

/* Botones de cantidad */
.btn-qty-modern {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border-radius: 10px;
    border: 2px solid #e9ecef;
    background: white;
    transition: all 0.3s ease;
}

.btn-qty-modern:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
    transform: scale(1.1);
}

.input-cantidad {
    width: 55px !important;
    text-align: center;
    font-weight: 700;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 8px 5px;
}

/* Badge mejorado */
.badge-modern {
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

/* Botón procesar pago */
.btn-procesar-pago {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    border: none;
    color: white;
    padding: 16px 24px;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 25px rgba(17, 153, 142, 0.3);
}

.btn-procesar-pago:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(17, 153, 142, 0.4);
    color: white;
}

.btn-procesar-pago:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #6c757d;
    box-shadow: none;
}

.btn-limpiar {
    background: white;
    border: 2px solid #e9ecef;
    color: #6c757d;
    padding: 14px 20px;
    border-radius: 15px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    width: 100%;
}

.btn-limpiar:hover {
    background: #f8f9fa;
    border-color: #dc3545;
    color: #dc3545;
    transform: translateY(-2px);
}

/* Scanner */
#scanner-container {
    position: relative;
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-radius: 15px;
    overflow: hidden;
    margin-top: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

#scanner-video video {
    width: 100%;
    height: auto;
    border-radius: 15px;
}

/* Carrito vacío */
.carrito-vacio {
    padding: 60px 20px;
    text-align: center;
}

.carrito-vacio .icon {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 20px;
    opacity: 0.5;
}

.carrito-vacio h5 {
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 10px;
}

.carrito-vacio p {
    color: #adb5bd;
    font-size: 0.95rem;
}

/* Modal mejorado */
.modal-content-modern {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}

.modal-header-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 25px 30px;
    border-radius: 20px 20px 0 0;
}

.modal-header-pago {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.modal-body-modern {
    padding: 30px;
}

.modal-footer-modern {
    border: none;
    padding: 20px 30px;
    background: #f8f9fa;
}

/* Asegurar que los modales anidados funcionen correctamente */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}

/* Resumen en modal */
.resumen-linea-modal {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 2px dashed #e9ecef;
}

.resumen-linea-modal:last-child {
    border-bottom: none;
}

.resumen-linea-modal .label {
    font-size: 0.95rem;
    color: #6c757d;
    font-weight: 600;
}

.resumen-linea-modal .valor {
    font-size: 1.1rem;
    font-weight: 700;
    color: #212529;
}

.resumen-total-modal {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 15px;
    margin: 20px 0;
    border-left: 4px solid #11998e;
}

.resumen-total-modal .label {
    font-size: 1.3rem;
    font-weight: 700;
    color: #495057;
}

.resumen-total-modal .valor {
    font-size: 2.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.cambio-display {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    padding: 15px 20px;
    border-radius: 12px;
    margin-top: 12px;
    border-left: 4px solid #28a745;
}

.cambio-display strong {
    color: #155724;
    font-size: 0.95rem;
}

.cambio-display .cambio-monto {
    font-size: 1.8rem;
    font-weight: 800;
    color: #28a745;
}

/* Toast */
.toast-container {
    position: fixed;
    top: 90px;
    right: 20px;
    z-index: 9999;
}

.toast-modern {
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    border: none;
    overflow: hidden;
}

/* Lista de clientes */
.lista-clientes {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 300px;
    overflow-y: auto;
}

.cliente-item {
    padding: 15px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.cliente-item:hover {
    border-color: #667eea;
    background: linear-gradient(135deg, #f0f7ff 0%, #e8f4ff 100%);
    transform: translateX(5px);
}

.cliente-item.selected {
    border-color: #11998e;
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-card {
    animation: fadeInUp 0.5s ease forwards;
}

/* Loading spinner */
.spinner-modern {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    #main-content {
        padding: 10px;
    }
    
    .venta-header {
        padding: 15px;
    }
    
    .venta-header h3 {
        font-size: 1.2rem;
    }
    
    .table-carrito thead th {
        padding: 10px 8px;
        font-size: 0.75rem;
    }
    
    .table-carrito tbody td {
        padding: 10px 8px;
        font-size: 0.85rem;
    }
    
    .btn-qty-modern {
        width: 32px;
        height: 32px;
    }
    
    .input-cantidad {
        width: 45px !important;
    }
}

/* ⭐ NUEVOS ESTILOS */
#divFaltaPagar {
    background: linear-gradient(135deg, #fee 0%, #fdd 100%);
    padding: 15px 20px;
    border-radius: 12px;
    margin-top: 12px;
    border-left: 4px solid #dc3545;
    animation: pulseRed 2s ease-in-out infinite;
}

#divFaltaPagar strong {
    color: #721c24;
    font-size: 0.95rem;
}

#divFaltaPagar .fs-4 {
    font-size: 1.8rem !important;
    font-weight: 800;
    color: #dc3545;
}

#divComprobante {
    padding: 15px;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-radius: 12px;
    border-left: 4px solid #2196F3;
    margin-bottom: 15px;
}

#divComprobante .input-group {
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.2);
}

#divComprobante .input-group-text {
    background: white;
    border-color: #2196F3;
    color: #2196F3;
}

#numeroComprobante {
    border-color: #2196F3;
}

#numeroComprobante:focus {
    border-color: #1976D2;
    box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.15);
}

.cambio-display {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    padding: 15px 20px;
    border-radius: 12px;
    margin-top: 12px;
    border-left: 4px solid #28a745;
    animation: fadeIn 0.3s ease;
}

.cambio-display strong {
    color: #155724;
    font-size: 0.95rem;
}

.cambio-display .cambio-monto {
    font-size: 1.8rem;
    font-weight: 800;
    color: #28a745;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulseRed {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
}

</style>

<!-- CONTENIDO PRINCIPAL -->
<div id="main-content">
    <div class="container-fluid">
        
        <!-- HEADER -->
        <div class="venta-header animate-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3>
                        <i class="bi bi-cart-check-fill"></i>
                        Punto de Venta
                    </h3>
                    <small>
                        <i class="bi bi-person-badge me-1"></i>
                        Vendedor: <?php echo htmlspecialchars($_SESSION['Nombre_Usuario'] ?? 'Usuario'); ?>
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="./vendedor.php" class="btn btn-outline-light btn-sm" style="border-radius: 10px;">
                        <i class="bi bi-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- BÚSQUEDA DE PRODUCTOS -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card-modern animate-card">
                    <div class="card-header-modern card-header-buscar">
                        <i class="bi bi-search"></i>
                        Buscar Producto
                    </div>
                    <div class="card-body-modern">
                        <div class="position-relative">
                            <div class="input-group input-group-modern">
                                <span class="input-group-text">
                                    <i class="bi bi-upc-scan fs-5"></i>
                                </span>
                                <input 
                                    id="busquedaProducto" 
                                    type="text" 
                                    class="form-control form-control-modern" 
                                    placeholder="Buscar por nombre, código o escanear código de barras..." 
                                    autocomplete="off">
                                <button class="btn btn-primary" type="button" id="btnAbrirScanner">
                                    <i class="bi bi-camera-fill me-2"></i>
                                    <span class="d-none d-md-inline">Abrir Scanner</span>
                                    <span class="d-md-none">Scanner</span>
                                </button>
                            </div>
                            <div id="sugerenciasProductos" class="search-suggestions" style="display:none;"></div>
                        </div>
                        
                        <!-- Scanner container -->
                        <div id="scanner-container" style="display:none;">
                            <div id="scanner-video"></div>
                            <div class="text-center mt-3 p-2">
                                <button class="btn btn-danger" id="btnCerrarScanner" style="border-radius: 12px; padding: 12px 30px;">
                                    <i class="bi bi-x-circle me-2"></i>Cerrar Scanner
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARRITO DE COMPRAS -->
        <div class="row">
            <div class="col-12">
                <div class="card-modern animate-card">
                    <div class="card-header-modern card-header-carrito">
                        <i class="bi bi-basket2-fill"></i>
                        Carrito de Compras
                        <span class="badge badge-modern bg-light text-success ms-auto" id="contadorCarrito">0 items</span>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-carrito mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Producto</th>
                                        <th class="text-center" style="width: 15%;">Precio Unit.</th>
                                        <th class="text-center" style="width: 20%;">Cantidad</th>
                                        <th class="text-end" style="width: 15%;">Subtotal</th>
                                        <th class="text-center" style="width: 10%;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="carritoBody">
                                    <tr>
                                        <td colspan="5" class="carrito-vacio">
                                            <div class="icon">
                                                <i class="bi bi-cart-x"></i>
                                            </div>
                                            <h5>El carrito está vacío</h5>
                                            <p>Busca y agrega productos para comenzar la venta</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer-modern">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <button class="btn-limpiar" id="btnLimpiarCarrito">
                                    <i class="bi bi-trash3-fill me-2"></i>
                                    Limpiar Carrito
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button id="btnAbrirPago" class="btn-procesar-pago" disabled>
                                    <i class="bi bi-credit-card-fill"></i>
                                    Procesar Pago
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Toast container -->
<div class="toast-container"></div>

<!-- MODAL: PROCESAR PAGO -->
<div class="modal fade p-5" id="modalPago" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-content-modern">
            <div class="modal-header-modern modal-header-pago">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card-fill me-2"></i>Procesar Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-modern">
                
                <div class="row g-4">
                    
                    <!-- COLUMNA IZQUIERDA: CLIENTE -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-person-fill me-2"></i>
                            Información del Cliente
                        </h6>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Cliente <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-2">
                                <input 
                                    type="text" 
                                    id="clienteBusqueda" 
                                    class="form-control form-control-modern" 
                                    placeholder="Buscar cliente..."
                                    readonly
                                    onclick="abrirSelectorCliente()"
                                    style="cursor: pointer;">
                                <button class="btn btn-primary" type="button" onclick="abrirSelectorCliente()" style="border-radius: 10px;">
                                    <i class="bi bi-person-check-fill"></i>
                                </button>
                            </div>
                            <small class="text-muted" id="clienteInfoDisplay">Haga clic para seleccionar un cliente</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-credit-card-2-front-fill me-1"></i>
                                Método de Pago
                            </label>
                            <select id="metodoPago" class="form-select form-select-modern">
                                <option value="EFECTIVO">💵 Efectivo</option>
                              
                                <option value="TRANSFERENCIA">🏦 Transferencia</option>
                            </select>
                        </div>
                        
                 <!-- ⭐ Efectivo Recibido -->
                       <!-- ⭐ Efectivo Recibido -->
<div id="divEfectivo" style="display:none;">
    <label class="form-label fw-bold">Efectivo Recibido</label>
    <div class="input-group mb-2">
        <span class="input-group-text">$</span>
        <input 
            id="efectivoRecibido" 
            type="number" 
            class="form-control form-control-modern" 
            min="0" 
            step="0.01"
            placeholder="0.00">
    </div>
    
    <!-- ⭐ DIV ROJO - Falta por pagar -->
    <div id="divFaltaPagar" style="display:none; background: linear-gradient(135deg, #fee 0%, #fdd 100%); padding: 15px 20px; border-radius: 12px; margin-top: 12px; border-left: 4px solid #dc3545;">
        <div class="d-flex justify-content-between align-items-center">
            <strong style="color: #721c24;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Falta por pagar:
            </strong>
            <span style="font-size: 1.8rem; font-weight: 800; color: #dc3545;" id="faltaPagarMonto">$0.00</span>
        </div>
    </div>
    
    <!-- ⭐ DIV VERDE - Cambio -->
    <div id="divCambio" style="display:none; background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 15px 20px; border-radius: 12px; margin-top: 12px; border-left: 4px solid #28a745;">
        <div class="d-flex justify-content-between align-items-center">
            <strong style="color: #155724;">Cambio:</strong>
            <span style="font-size: 1.8rem; font-weight: 800; color: #28a745;" id="cambioMonto">$0.00</span>
        </div>
    </div>
</div>


                        <!-- ⭐ NUEVO: Número de Comprobante para Transferencia -->
                        <div id="divComprobante" style="display:none;">
                            <label class="form-label fw-bold">
                                <i class="bi bi-receipt me-1"></i>
                                Número de Comprobante <span class="text-danger">*</span>
                            </label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">
                                    <i class="bi bi-hash"></i>
                                </span>
                                <input 
                                    id="numeroComprobante" 
                                    type="text" 
                                    class="form-control form-control-modern" 
                                    placeholder="Ej: TRF-123456789"
                                    maxlength="50">
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Ingrese el número de referencia de la transferencia
                            </small>
                        </div>
                        <div>
                            <label class="form-label fw-bold">
                                <i class="bi bi-pencil-fill me-1"></i>
                                Notas (Opcional)
                            </label>
                            <textarea 
                                id="notasVenta" 
                                class="form-control form-control-modern" 
                                rows="3" 
                                placeholder="Observaciones adicionales..." 
                                maxlength="200"></textarea>
                        </div>
                        
                    </div>
                    
                    <!-- COLUMNA DERECHA: RESUMEN -->
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-calculator-fill me-2"></i>
                            Resumen de Venta
                        </h6>
                        
                        <!-- Subtotal -->
                        <div class="resumen-linea-modal">
                            <span class="label">Subtotal</span>
                            <strong class="valor" id="subtotalVentaModal">$0.00</strong>
                        </div>
                        
                        <!-- Descuento -->
                        <div class="resumen-linea-modal">
                            <span class="label">Descuento</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">$</span>
                                <input 
                                    id="descuentoInput" 
                                    type="number" 
                                    class="form-control form-control-sm text-end" 
                                    min="0" 
                                    step="0.01" 
                                    value="0.00" 
                                    style="width: 100px; border-radius: 8px; border: 2px solid #e9ecef;">
                            </div>
                        </div>
                        
                      
                        <!-- IVA - OCULTAR O ELIMINAR ESTA SECCIÓN -->
<div class="resumen-linea-modal" style="display: none;">
    <span class="label">IVA (15%)</span>
    <strong class="valor" id="ivaVentaModal">$0.00</strong>
</div>
                        
                        <!-- TOTAL -->
                        <div class="resumen-total-modal">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="label">TOTAL A PAGAR</span>
                                <span class="valor" id="totalVentaModal">$0.00</span>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mb-0" style="border-radius: 12px;">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong id="cantidadProductosModal">0 productos</strong> en el carrito
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success btn-lg" id="btnConfirmarVenta" style="border-radius: 10px; padding: 12px 30px;">
                    <i class="bi bi-check-circle-fill me-2"></i>Confirmar Venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: SELECCIONAR CLIENTE -->
<div class="modal fade" id="modalSeleccionarCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header-modern">
                <h5 class="modal-title">
                    <i class="bi bi-person-check-fill me-2"></i>Seleccionar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-modern">
                <div class="mb-3">
                    <input 
                        type="text" 
                        id="buscarClienteInput" 
                        class="form-control form-control-modern" 
                        placeholder="Buscar por nombre o cédula...">
                </div>
                <div id="listaClientes" class="lista-clientes">
                    <!-- Se llenará dinámicamente -->
                </div>
                <div class="text-center mt-3">
                    <button class="btn btn-primary" onclick="abrirNuevoCliente()" style="border-radius: 10px;">
                        <i class="bi bi-person-plus-fill me-2"></i>Agregar Nuevo Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: NUEVO CLIENTE -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-modern">
            <div class="modal-header-modern">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body-modern">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                    <input 
                        id="nuevoClienteNombre" 
                        type="text" 
                        class="form-control form-control-modern" 
                        placeholder="Ej: Juan Pérez">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Cédula / RUC</label>
                    <input 
                        id="nuevoClienteCedula" 
                        type="text" 
                        class="form-control form-control-modern" 
                        placeholder="1234567890"
                        maxlength="13">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Teléfono</label>
                    <input 
                        id="nuevoClienteTelefono" 
                        type="tel" 
                        class="form-control form-control-modern" 
                        placeholder="0999999999"
                        maxlength="10">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input 
                        id="nuevoClienteEmail" 
                        type="email" 
                        class="form-control form-control-modern" 
                        placeholder="cliente@example.com">
                </div>
            </div>
            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarCliente" style="border-radius: 10px;">
                    <i class="bi bi-check-circle me-2"></i>Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quagga para scanner -->
<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js"></script>

<!-- JavaScript del Punto de Venta -->
<script>
console.log("🛒 PUNTO DE VENTA v2.0 - CON SCANNER CORREGIDO");

// =============================================
// VARIABLES GLOBALES
// =============================================
var carrito = [];
var clientes = [];
var scannerActivo = false;
var quaggaIniciado = false;
var clienteSeleccionado = null;
var audioCtx = null;

// =============================================
// AUDIO - Sistema de sonidos
// =============================================
function initAudio() {
    try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        console.log('🔊 Audio inicializado');
    } catch(e) {
        console.log('⚠️ No se pudo inicializar audio');
    }
}

function beepExito() {
    if (!audioCtx) initAudio();
    if (!audioCtx) return;
    try {
        var o = audioCtx.createOscillator();
        var g = audioCtx.createGain();
        o.connect(g);
        g.connect(audioCtx.destination);
        o.frequency.value = 1800;
        o.type = 'square';
        g.gain.value = 0.3;
        o.start();
        o.frequency.setValueAtTime(2400, audioCtx.currentTime + 0.08);
        o.stop(audioCtx.currentTime + 0.15);
        console.log('🔊 Beep éxito');
    } catch(e) {
        console.error('Error beep:', e);
    }
    if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
}

function beepError() {
    if (!audioCtx) initAudio();
    if (!audioCtx) return;
    try {
        var o = audioCtx.createOscillator();
        var g = audioCtx.createGain();
        o.connect(g);
        g.connect(audioCtx.destination);
        o.frequency.value = 200;
        o.type = 'square';
        g.gain.value = 0.3;
        o.start();
        o.stop(audioCtx.currentTime + 0.2);
        console.log('🔊 Beep error');
    } catch(e) {
        console.error('Error beep:', e);
    }
    if (navigator.vibrate) navigator.vibrate(300);
}

// =============================================
// VALIDAR CÓDIGO DE BARRAS
// =============================================
function validarCodigo(c) {
    if (!c) return null;
    c = String(c).trim();
    
    // Código de producto interno
    var m = c.match(/PROD-\d{8}-[A-F0-9]{6}/);
    if (m) return m[0];
    
    // Códigos de barras estándar
    if (/^\d{13}$/.test(c)) return c; // EAN-13
    if (/^\d{12}$/.test(c)) return c; // UPC-A
    if (/^\d{8}$/.test(c)) return c;  // EAN-8
    
    return null;
}

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Punto de venta iniciado');
    initAudio(); // Inicializar audio al cargar
    cargarClientes();
    inicializarEventos();
});

// =============================================
// CLIENTES
// =============================================
function cargarClientes() {
    var url = '/api/vendedor/obtener_clientes.php?_t=' + Date.now();
    
    fetch(url, { method: 'GET', cache: 'no-store', credentials: 'same-origin' })
    .then(function(r) { 
        if (!r.ok) throw new Error('Error HTTP: ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
        if (!data.success) {
            throw new Error(data.error || 'Error cargando clientes');
        }
        
        if (data.clientes && data.clientes.length > 0) {
            clientes = data.clientes;
            renderizarListaClientes(data.clientes);
            console.log('✅ Clientes cargados:', data.clientes.length);
        }
    })
    .catch(function(e) {
        console.error('❌ Error cargando clientes:', e);
        toast('Error cargando clientes: ' + e.message, 'danger');
    });
}

function renderizarListaClientes(clientesFiltrados) {
    var lista = document.getElementById('listaClientes');
    if (!lista) return;
    
    if (!clientesFiltrados || clientesFiltrados.length === 0) {
        lista.innerHTML = '<div class="text-center text-muted p-4">No hay clientes disponibles</div>';
        return;
    }
    
    var html = '';
    clientesFiltrados.forEach(function(c) {
        var isSelected = clienteSeleccionado && clienteSeleccionado.id === c.id;
        html += '<div class="cliente-item' + (isSelected ? ' selected' : '') + '" onclick="seleccionarCliente(' + c.id + ')">' +
            '<div class="d-flex justify-content-between align-items-center">' +
            '<div>' +
            '<div class="fw-bold text-dark">' + escapeHtml(c.nombre) + '</div>' +
            '<small class="text-muted">' + 
            (c.cedula ? '<i class="bi bi-card-text me-1"></i>' + escapeHtml(c.cedula) : '') +
            (c.telefono ? ' <i class="bi bi-telephone ms-2 me-1"></i>' + escapeHtml(c.telefono) : '') +
            '</small>' +
            '</div>' +
            (isSelected ? '<i class="bi bi-check-circle-fill text-success fs-4"></i>' : '') +
            '</div></div>';
    });
    
    lista.innerHTML = html;
}

function seleccionarCliente(clienteId) {
    var cliente = clientes.find(function(c) { return c.id === clienteId; });
    if (!cliente) return;
    
    clienteSeleccionado = cliente;
    
    document.getElementById('clienteBusqueda').value = cliente.nombre;
    document.getElementById('clienteInfoDisplay').innerHTML = 
        '<i class="bi bi-check-circle-fill text-success me-1"></i>' +
        (cliente.cedula ? 'Cédula: ' + cliente.cedula : '') +
        (cliente.telefono ? ' | Tel: ' + cliente.telefono : '');
    
    var modalEl = document.getElementById('modalSeleccionarCliente');
    if (typeof bootstrap !== 'undefined') {
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
    
    setTimeout(function() {
        limpiarBackdrops();
    }, 300);
    
    toast('✅ Cliente seleccionado: ' + cliente.nombre, 'success');
}

function buscarClienteEnLista(query) {
    if (!query) {
        renderizarListaClientes(clientes);
        return;
    }
    
    var busqueda = query.toLowerCase();
    var filtrados = clientes.filter(function(c) {
        return (c.nombre && c.nombre.toLowerCase().includes(busqueda)) ||
               (c.cedula && c.cedula.includes(busqueda));
    });
    
    renderizarListaClientes(filtrados);
}

// =============================================
// BÚSQUEDA DE PRODUCTOS
// =============================================
var timeoutBusqueda = null;

function buscarProductos(query) {
    if (query.length < 2) {
        document.getElementById('sugerenciasProductos').style.display = 'none';
        return;
    }
    
    clearTimeout(timeoutBusqueda);
    timeoutBusqueda = setTimeout(function() {
        var url = '/api/vendedor/buscar_producto.php?query=' + encodeURIComponent(query) + '&_t=' + Date.now();
        
        fetch(url, { cache: 'no-store' })
        .then(function(r) { 
            if (!r.ok) throw new Error('Error HTTP: ' + r.status);
            return r.json(); 
        })
        .then(function(data) {
            var container = document.getElementById('sugerenciasProductos');
            
            if (data.success && data.productos && data.productos.length > 0) {
                var html = '';
                data.productos.forEach(function(p) {
                    html += '<div class="suggestion-item" onclick=\'agregarProducto(' + JSON.stringify(p).replace(/'/g, "\\'") + ')\'>' +
                        '<div class="d-flex justify-content-between align-items-center">' +
                        '<div>' +
                        '<div class="fw-bold text-dark">' + escapeHtml(p.nombre) + '</div>' +
                        '<small class="text-muted"><i class="bi bi-upc me-1"></i>' + escapeHtml(p.codigo || '') + '</small>' +
                        '</div>' +
                        '<div class="text-end">' +
                        '<div class="fs-5 fw-bold text-success">$' + parseFloat(p.precio_venta).toFixed(2) + '</div>' +
                        '<small class="text-muted"><i class="bi bi-box me-1"></i>Stock: ' + p.stock + '</small>' +
                        '</div>' +
                        '</div></div>';
                });
                container.innerHTML = html;
                container.style.display = 'block';
            } else {
                container.innerHTML = '<div class="suggestion-item text-center text-muted">' +
                    '<i class="bi bi-search me-2"></i>No se encontraron productos</div>';
                container.style.display = 'block';
            }
        })
        .catch(function(e) {
            console.error('Error buscando:', e);
            toast('Error en búsqueda: ' + e.message, 'danger');
        });
    }, 300);
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// =============================================
// CARRITO
// =============================================
function agregarProducto(producto) {
    document.getElementById('sugerenciasProductos').style.display = 'none';
    document.getElementById('busquedaProducto').value = '';
    
    var existe = null;
    for (var i = 0; i < carrito.length; i++) {
        if (carrito[i].id === producto.id) {
            existe = carrito[i];
            break;
        }
    }
    
    if (existe) {
        if (existe.cantidad < producto.stock) {
            existe.cantidad++;
            beepExito(); // Sonido al actualizar
            toast('✅ Cantidad actualizada: ' + producto.nombre, 'success');
        } else {
            beepError(); // Sonido de error
            toast('⚠️ Stock insuficiente', 'warning');
            return;
        }
    } else {
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            codigo: producto.codigo,
            precio: parseFloat(producto.precio_venta),
            stock: producto.stock,
            cantidad: 1
        });
        beepExito(); // Sonido al agregar
        toast('✅ Agregado: ' + producto.nombre, 'success');
    }
    
    renderizarCarrito();
    actualizarBotonPagar();
}

function renderizarCarrito() {
    var tbody = document.getElementById('carritoBody');
    
    if (carrito.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="carrito-vacio">' +
            '<div class="icon"><i class="bi bi-cart-x"></i></div>' +
            '<h5>El carrito está vacío</h5>' +
            '<p>Busca y agrega productos para comenzar la venta</p>' +
            '</td></tr>';
        document.getElementById('contadorCarrito').textContent = '0 items';
        return;
    }
    
    var html = '';
    var totalItems = 0;
    
    for (var i = 0; i < carrito.length; i++) {
        var item = carrito[i];
        totalItems += item.cantidad;
        var subtotal = item.precio * item.cantidad;
        
        html += '<tr>' +
            '<td>' +
            '<div class="fw-bold text-dark">' + escapeHtml(item.nombre) + '</div>' +
            '<small class="text-muted">' + escapeHtml(item.codigo || '') + '</small>' +
            '</td>' +
            '<td class="text-center">' +
            '<span class="fw-bold">$' + item.precio.toFixed(2) + '</span>' +
            '</td>' +
            '<td>' +
            '<div class="d-flex align-items-center justify-content-center gap-2">' +
            '<button class="btn btn-qty-modern" onclick="cambiarCantidad(' + i + ', -1)">' +
            '<i class="bi bi-dash-lg"></i></button>' +
            '<input type="number" class="form-control input-cantidad" ' +
            'value="' + item.cantidad + '" min="1" max="' + item.stock + '" ' +
            'onchange="setCantidad(' + i + ', this.value)">' +
            '<button class="btn btn-qty-modern" onclick="cambiarCantidad(' + i + ', 1)">' +
            '<i class="bi bi-plus-lg"></i></button>' +
            '</div>' +
            '</td>' +
            '<td class="text-end">' +
            '<div class="fs-5 fw-bold text-success">$' + subtotal.toFixed(2) + '</div>' +
            '</td>' +
            '<td class="text-center">' +
            '<button class="btn btn-sm btn-outline-danger" onclick="eliminarItem(' + i + ')" ' +
            'style="border-radius: 10px;">' +
            '<i class="bi bi-trash3-fill"></i></button>' +
            '</td>' +
            '</tr>';
    }
    
    tbody.innerHTML = html;
    document.getElementById('contadorCarrito').textContent = totalItems + ' item' + (totalItems !== 1 ? 's' : '');
}

function cambiarCantidad(index, delta) {
    var item = carrito[index];
    var nueva = item.cantidad + delta;
    
    if (nueva < 1) {
        eliminarItem(index);
        return;
    }
    
    if (nueva > item.stock) {
        beepError();
        toast('⚠️ Stock insuficiente', 'warning');
        return;
    }
    
    item.cantidad = nueva;
    renderizarCarrito();
}

function setCantidad(index, valor) {
    var item = carrito[index];
    var nueva = parseInt(valor) || 1;
    
    if (nueva < 1) nueva = 1;
    if (nueva > item.stock) {
        nueva = item.stock;
        beepError();
        toast('Cantidad ajustada al stock disponible', 'warning');
    }
    
    item.cantidad = nueva;
    renderizarCarrito();
}

function eliminarItem(index) {
    var item = carrito[index];
    carrito.splice(index, 1);
    toast('🗑️ Eliminado: ' + item.nombre, 'info');
    renderizarCarrito();
    actualizarBotonPagar();
}

function actualizarBotonPagar() {
    document.getElementById('btnAbrirPago').disabled = carrito.length === 0;
}

// =============================================
// MODAL DE PAGO
// =============================================
function abrirModalPago() {
    if (carrito.length === 0) {
        beepError();
        toast('⚠️ El carrito está vacío', 'warning');
        return;
    }
    
    actualizarTotalesModal();
    
    var modalEl = document.getElementById('modalPago');
    if (typeof bootstrap !== 'undefined') {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}

// Buscar y reemplazar esta función:
function actualizarTotalesModal() {
    var subtotal = 0;
    var totalItems = 0;
    
    for (var i = 0; i < carrito.length; i++) {
        subtotal += carrito[i].precio * carrito[i].cantidad;
        totalItems += carrito[i].cantidad;
    }
    
    var descuento = parseFloat(document.getElementById('descuentoInput').value) || 0;
    var base = subtotal - descuento;
    if (base < 0) base = 0;
    
    // ⭐ SIN IVA - El total es solo subtotal menos descuento
    var total = base;
    
    document.getElementById('subtotalVentaModal').textContent = '$' + subtotal.toFixed(2);
    // ⭐ IVA OCULTO - Se muestra como $0.00
    document.getElementById('ivaVentaModal').textContent = '$0.00';
    document.getElementById('totalVentaModal').textContent = '$' + total.toFixed(2);
    document.getElementById('cantidadProductosModal').textContent = totalItems + ' producto' + (totalItems !== 1 ? 's' : '');
    
    calcularCambio();
}

function calcularCambio() {
    var metodo = document.getElementById('metodoPago').value;
    var divEfectivo = document.getElementById('divEfectivo');
    var divComprobante = document.getElementById('divComprobante');
    
    if (metodo === 'EFECTIVO') {
        divEfectivo.style.display = 'block';
        divComprobante.style.display = 'none';
        
        var totalText = document.getElementById('totalVentaModal').textContent.replace('$', '');
        var total = parseFloat(totalText) || 0;
        var efectivo = parseFloat(document.getElementById('efectivoRecibido').value) || 0;
        var diferencia = efectivo - total;
        
        var divCambio = document.getElementById('divCambio');
        var divFalta = document.getElementById('divFaltaPagar');
        
        if (efectivo > 0) {
            if (diferencia >= 0) {
                // ✅ HAY CAMBIO - Mostrar VERDE
                divCambio.style.display = 'block';
                divFalta.style.display = 'none';
                document.getElementById('cambioMonto').textContent = '$' + diferencia.toFixed(2);
            } else {
                // ❌ FALTA DINERO - Mostrar ROJO
                divCambio.style.display = 'none';
                divFalta.style.display = 'block';
                document.getElementById('faltaPagarMonto').textContent = '$' + Math.abs(diferencia).toFixed(2);
            }
        } else {
            // No hay efectivo ingresado
            divCambio.style.display = 'none';
            divFalta.style.display = 'none';
        }
        
    } else if (metodo === 'TRANSFERENCIA') {
        divEfectivo.style.display = 'none';
        divComprobante.style.display = 'block';
        
    } else {
        // TARJETA
        divEfectivo.style.display = 'none';
        divComprobante.style.display = 'none';
    }
}

// =============================================
// EVENT LISTENERS
// =============================================
function inicializarEventos() {
    // Búsqueda de productos
    document.getElementById('busquedaProducto').addEventListener('input', function(e) {
        buscarProductos(e.target.value);
    });
    
    // Buscar cliente en modal
    var buscarClienteInput = document.getElementById('buscarClienteInput');
    if (buscarClienteInput) {
        buscarClienteInput.addEventListener('input', function(e) {
            buscarClienteEnLista(e.target.value);
        });
    }
    
    // Click en input de cliente
    var clienteBusqueda = document.getElementById('clienteBusqueda');
    if (clienteBusqueda) {
        clienteBusqueda.addEventListener('click', abrirSelectorCliente);
    }
    
    // Cerrar sugerencias al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#busquedaProducto') && !e.target.closest('#sugerenciasProductos')) {
            document.getElementById('sugerenciasProductos').style.display = 'none';
        }
    });
    
    // Actualizar totales
    document.getElementById('descuentoInput').addEventListener('input', actualizarTotalesModal);
    document.getElementById('metodoPago').addEventListener('change', calcularCambio);
    document.getElementById('efectivoRecibido').addEventListener('input', calcularCambio);
    
    // Botón limpiar carrito
    document.getElementById('btnLimpiarCarrito').addEventListener('click', function() {
        if (carrito.length === 0) return;
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '¿Limpiar carrito?',
                text: 'Se eliminarán todos los productos',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Sí, limpiar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    carrito = [];
                    renderizarCarrito();
                    actualizarBotonPagar();
                    toast('🗑️ Carrito limpiado', 'info');
                }
            });
        } else {
            if (confirm('¿Está seguro de limpiar el carrito?')) {
                carrito = [];
                renderizarCarrito();
                actualizarBotonPagar();
                toast('Carrito limpiado', 'info');
            }
        }
    });
    
    // Botones principales
    document.getElementById('btnAbrirPago').addEventListener('click', abrirModalPago);
    document.getElementById('btnConfirmarVenta').addEventListener('click', confirmarVenta);
    document.getElementById('btnGuardarCliente').addEventListener('click', guardarNuevoCliente);
    
    // Scanner
    var btnAbrir = document.getElementById('btnAbrirScanner');
    if (btnAbrir) btnAbrir.addEventListener('click', abrirScanner);
    
    var btnCerrar = document.getElementById('btnCerrarScanner');
    if (btnCerrar) btnCerrar.addEventListener('click', cerrarScanner);
}

// =============================================
// MODALES
// =============================================
function abrirSelectorCliente() {
    var modalSelCliente = document.getElementById('modalSeleccionarCliente');
    if (typeof bootstrap !== 'undefined') {
        var modal = new bootstrap.Modal(modalSelCliente);
        modal.show();
        
        modalSelCliente.addEventListener('hidden.bs.modal', function () {
            limpiarBackdrops();
        }, { once: true });
    }
}

function abrirNuevoCliente() {
    var modalSelCliente = document.getElementById('modalSeleccionarCliente');
    if (typeof bootstrap !== 'undefined') {
        var modal = bootstrap.Modal.getInstance(modalSelCliente);
        if (modal) modal.hide();
    }
    
    setTimeout(function() {
        var modalNuevoCliente = document.getElementById('modalNuevoCliente');
        if (typeof bootstrap !== 'undefined') {
            var modal = new bootstrap.Modal(modalNuevoCliente);
            modal.show();
            
            modalNuevoCliente.addEventListener('hidden.bs.modal', function () {
                limpiarBackdrops();
            }, { once: true });
        }
    }, 300);
}

function limpiarBackdrops() {
    var backdrops = document.querySelectorAll('.modal-backdrop');
    var modalesAbiertos = document.querySelectorAll('.modal.show').length;
    
    if (backdrops.length > modalesAbiertos) {
        for (var i = modalesAbiertos; i < backdrops.length; i++) {
            if (backdrops[i]) {
                backdrops[i].remove();
            }
        }
    }
    
    if (modalesAbiertos === 0) {
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }
}

// =============================================
// FUNCIÓN confirmarVenta() - VERSIÓN FINAL CORREGIDA
// REEMPLAZAR COMPLETA (desde línea ~1398 hasta ~1550)
// =============================================

function confirmarVenta() {
    // ⭐ 1. VALIDACIÓN: Carrito no vacío
    if (carrito.length === 0) {
        beepError();
        toast('⚠️ El carrito está vacío', 'warning');
        return;
    }
    
    // ⭐ 2. VALIDACIÓN: Cliente seleccionado
    if (!clienteSeleccionado || !clienteSeleccionado.id) {
        beepError();
        toast('⚠️ Debe seleccionar un cliente', 'warning');
        return;
    }
    
    // ⭐ 3. OBTENER MÉTODO DE PAGO
    var metodo = document.getElementById('metodoPago').value;
    
    // ⭐ 4. VALIDACIÓN: Efectivo - debe cubrir el total
    if (metodo === 'EFECTIVO') {
        var totalText = document.getElementById('totalVentaModal').textContent.replace('$', '');
        var total = parseFloat(totalText) || 0;
        var efectivo = parseFloat(document.getElementById('efectivoRecibido').value) || 0;
        
        if (efectivo <= 0) {
            beepError();
            toast('⚠️ Ingrese el efectivo recibido', 'warning');
            document.getElementById('efectivoRecibido').focus();
            return;
        }
        
        if (efectivo < total) {
            beepError();
            var falta = total - efectivo;
            toast('⚠️ Falta $' + falta.toFixed(2) + ' para completar el pago', 'warning');
            document.getElementById('efectivoRecibido').focus();
            return;
        }
    }
    
    // ⭐ 5. VALIDACIÓN: Transferencia - requiere número de comprobante
    if (metodo === 'TRANSFERENCIA') {
        var comprobante = document.getElementById('numeroComprobante').value.trim();
        if (!comprobante) {
            beepError();
            toast('⚠️ Ingrese el número de comprobante de la transferencia', 'warning');
            document.getElementById('numeroComprobante').focus();
            return;
        }
    }
    
    // ⭐ 6. DESHABILITAR BOTÓN Y MOSTRAR LOADING
    var btn = document.getElementById('btnConfirmarVenta');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-modern me-2"></span>Procesando...';
    
    // ⭐ 7. PREPARAR PRODUCTOS
    var productos = [];
    for (var i = 0; i < carrito.length; i++) {
        productos.push({ 
            id: carrito[i].id, 
            cantidad: carrito[i].cantidad, 
            precio: carrito[i].precio 
        });
    }
    
    // ⭐ 8. PREPARAR DATOS DE LA VENTA
    var datos = {
        id_cliente: parseInt(clienteSeleccionado.id),
        productos: productos,
        subtotal: parseFloat(document.getElementById('subtotalVentaModal').textContent.replace('$', '')),
        descuento: parseFloat(document.getElementById('descuentoInput').value) || 0,
           iva: 0, // ⭐ Siempre 0
        total: parseFloat(document.getElementById('totalVentaModal').textContent.replace('$', '')),
        metodo_pago: metodo,
        notas: document.getElementById('notasVenta').value
    };
    
    // ⭐ 9. AGREGAR NÚMERO DE COMPROBANTE SI ES TRANSFERENCIA
    if (metodo === 'TRANSFERENCIA') {
        datos.numero_comprobante = document.getElementById('numeroComprobante').value.trim();
    }
    
    console.log('📤 Enviando venta:', datos);
    
    // ⭐ 10. ENVIAR AL SERVIDOR
    fetch('/api/vendedor/procesar_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('Error HTTP: ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
        console.log('📥 Respuesta:', data);
        
        if (data.success) {
            beepExito();
            
            var modalPago = bootstrap.Modal.getInstance(document.getElementById('modalPago'));
            if (modalPago) modalPago.hide();
            
            setTimeout(function() {
                limpiarBackdrops();
            }, 300);
            
            // ⭐ Mensaje de éxito mejorado
            var mensajeMetodo = '';
            if (metodo === 'EFECTIVO') {
                var efectivo = parseFloat(document.getElementById('efectivoRecibido').value) || 0;
                var total = parseFloat(datos.total);
                var cambio = efectivo - total;
                mensajeMetodo = '<p class="mb-2">Efectivo: $' + efectivo.toFixed(2) + '</p>' +
                               '<p class="mb-2 text-success fw-bold">Cambio: $' + cambio.toFixed(2) + '</p>';
            } else if (metodo === 'TRANSFERENCIA') {
                mensajeMetodo = '<p class="mb-2"><small class="text-muted">Comprobante:</small> <strong>' + datos.numero_comprobante + '</strong></p>';
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Venta Exitosa!',
                    html: '<p class="mb-2">Venta #' + data.venta_id + '</p>' +
                          mensajeMetodo +
                          '<div class="fs-2 fw-bold" style="color: #11998e;">$' + data.total.toFixed(2) + '</div>',
                    confirmButtonText: 'Nueva Venta',
                    showCancelButton: true,
                    cancelButtonText: 'Ver Historial',
                    confirmButtonColor: '#11998e',
                    cancelButtonColor: '#6c757d'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        limpiarFormulario();
                    } else {
                        window.location.href = './mis_ventas.php';
                    }
                });
            } else {
                alert('¡Venta exitosa! #' + data.venta_id);
                limpiarFormulario();
            }
        } else {
            throw new Error(data.error || 'Error al procesar');
        }
    })
    .catch(function(e) {
        console.error('❌ Error:', e);
        beepError();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: e.message,
                confirmButtonColor: '#dc3545'
            });
        } else {
            alert('Error: ' + e.message);
        }
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirmar Venta';
    });
}

function limpiarFormulario() {
    carrito = [];
    renderizarCarrito();
    actualizarBotonPagar();
    
    clienteSeleccionado = null;
    document.getElementById('clienteBusqueda').value = '';
    document.getElementById('clienteInfoDisplay').textContent = 'Haga clic para seleccionar un cliente';
    
    document.getElementById('descuentoInput').value = 0;
    document.getElementById('notasVenta').value = '';
    document.getElementById('efectivoRecibido').value = '';
    document.getElementById('metodoPago').value = 'EFECTIVO';
    document.getElementById('numeroComprobante').value = '';
    document.getElementById('divCambio').style.display = 'none';
    document.getElementById('divFaltaPagar').style.display = 'none';
    document.getElementById('divComprobante').style.display = 'none';
}

// =============================================
// GUARDAR CLIENTE
// =============================================
function guardarNuevoCliente() {
    var nombre = document.getElementById('nuevoClienteNombre').value.trim();
    if (!nombre) {
        beepError();
        toast('⚠️ Ingrese el nombre del cliente', 'warning');
        return;
    }
    
    var datos = {
        nombre: nombre,
        cedula: document.getElementById('nuevoClienteCedula').value.trim(),
        telefono: document.getElementById('nuevoClienteTelefono').value.trim(),
        email: document.getElementById('nuevoClienteEmail').value.trim()
    };
    
    fetch('/api/vendedor/guardar_cliente.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('Error HTTP: ' + r.status);
        return r.json(); 
    })
    .then(function(data) {
        if (data.success) {
            beepExito(); // Sonido de éxito
            toast('✅ Cliente guardado exitosamente', 'success');
            
            var modalEl = document.getElementById('modalNuevoCliente');
            if (typeof bootstrap !== 'undefined') {
                var modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
            
            document.getElementById('nuevoClienteNombre').value = '';
            document.getElementById('nuevoClienteCedula').value = '';
            document.getElementById('nuevoClienteTelefono').value = '';
            document.getElementById('nuevoClienteEmail').value = '';
            
            cargarClientes();
            
            setTimeout(function() {
                limpiarBackdrops();
                
                if (data.cliente_id) {
                    var nuevoCliente = {
                        id: data.cliente_id,
                        nombre: datos.nombre,
                        cedula: datos.cedula,
                        telefono: datos.telefono,
                        email: datos.email
                    };
                    
                    var existe = clientes.find(function(c) { return c.id === data.cliente_id; });
                    if (!existe) {
                        clientes.push(nuevoCliente);
                    }
                    
                    clienteSeleccionado = nuevoCliente;
                    document.getElementById('clienteBusqueda').value = nuevoCliente.nombre;
                    document.getElementById('clienteInfoDisplay').innerHTML = 
                        '<i class="bi bi-check-circle-fill text-success me-1"></i>' +
                        (nuevoCliente.cedula ? 'Cédula: ' + nuevoCliente.cedula : '') +
                        (nuevoCliente.telefono ? ' | Tel: ' + nuevoCliente.telefono : '');
                }
            }, 500);
        } else {
            throw new Error(data.error || 'Error al guardar');
        }
    })
    .catch(function(e) {
        beepError(); // Sonido de error
        toast('❌ Error: ' + e.message, 'danger');
    });
}
// =============================================
// ⭐ SCANNER CORREGIDO - SOLUCIÓN AL DOBLE ESCANEO
// =============================================

// Variables globales para control
var scannerActivo = false;
var quaggaIniciado = false;
var ultimoCodigo = '';
var tiempoUltimo = 0;
var procesandoCodigo = false; // ⭐ NUEVO: Flag para evitar procesamiento simultáneo

function abrirScanner() {
    console.log('📷 Abriendo scanner...');
    
    // Inicializar audio
    initAudio();
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    
    // Resetear variables de control
    ultimoCodigo = '';
    tiempoUltimo = 0;
    procesandoCodigo = false;
    
    var container = document.getElementById('scanner-container');
    if (container) {
        container.style.display = 'block';
    }
    
    setTimeout(iniciarQuagga, 400);
}

function iniciarQuagga() {
    if (scannerActivo) {
        console.log('⚠️ Scanner ya activo, evitando duplicado');
        return;
    }
    
    if (typeof Quagga === 'undefined') {
        console.error('❌ Quagga no disponible');
        beepError();
        toast('⚠️ Scanner no disponible', 'warning');
        return;
    }
    
    var config = {
        inputStream: {
            type: "LiveStream",
            target: document.querySelector('#scanner-video'),
            constraints: { 
                facingMode: "environment", 
                width: { ideal: 1280 }, 
                height: { ideal: 720 } 
            },
            area: { 
                top: "25%", 
                right: "5%", 
                left: "5%", 
                bottom: "25%" 
            }
        },
        decoder: { 
            readers: [
                "ean_reader", 
                "ean_8_reader", 
                "code_128_reader", 
                "upc_reader"
            ],
            multiple: false // ⭐ IMPORTANTE: Solo un código a la vez
        },
        locate: true,
        frequency: 10 // ⭐ Reducir frecuencia de escaneo
    };
    
    Quagga.init(config, function(err) {
        if (err) {
            console.error('❌ Quagga error:', err);
            beepError();
            toast('❌ No se pudo iniciar la cámara', 'danger');
            return;
        }
        
        console.log('✅ Quagga iniciado');
        Quagga.start();
        scannerActivo = true;
        quaggaIniciado = true;
    });
    
    // ⭐ CLAVE 1: Limpiar TODOS los eventos previos
    Quagga.offDetected();
    Quagga.offProcessed();
    
    // ⭐ CLAVE 2: Un SOLO listener con control estricto
    Quagga.onDetected(procesarCodigoEscaneado);
}

// ⭐ FUNCIÓN SEPARADA con control total de duplicados
function procesarCodigoEscaneado(result) {
    // 1. Verificar si ya estamos procesando
    if (procesandoCodigo) {
        console.log('⏭️ Ya procesando, ignorando...');
        return;
    }
    
    var codigo = result.codeResult.code;
    var ahora = Date.now();
    
    // 2. Validar código INMEDIATAMENTE
    var limpio = validarCodigo(codigo);
    if (!limpio) {
        console.log('❌ Código inválido ignorado:', codigo);
        return;
    }
    
    // 3. Debounce: mismo código en menos de 1000ms
    if (codigo === ultimoCodigo && (ahora - tiempoUltimo) < 1000) {
        console.log('⏭️ Ignorado por debounce (mismo código):', codigo);
        return;
    }
    
    // 4. BLOQUEAR procesamiento
    procesandoCodigo = true;
    ultimoCodigo = codigo;
    tiempoUltimo = ahora;
    
    console.log('═══════════════════════════════');
    console.log('📸 PROCESANDO CÓDIGO:', limpio);
    console.log('═══════════════════════════════');
    
    // 5. Buscar y agregar producto
    fetch('/api/vendedor/buscar_producto.php?codigo=' + encodeURIComponent(limpio) + '&_t=' + Date.now())
        .then(function(r) { 
            if (!r.ok) throw new Error('Error HTTP');
            return r.json(); 
        })
        .then(function(data) {
            if (data.success && data.encontrado) {
                console.log('✅ Producto encontrado:', data.producto.nombre);
                agregarProducto(data.producto);
                
                // ⭐ Cerrar scanner después de éxito
                setTimeout(function() {
                    cerrarScanner();
                }, 800);
            } else {
                console.log('⚠️ Producto no encontrado');
                beepError();
                toast('⚠️ Producto no encontrado: ' + limpio, 'warning');
                
                // Permitir seguir escaneando
                setTimeout(function() {
                    procesandoCodigo = false;
                }, 500);
            }
        })
        .catch(function(e) {
            console.error('❌ Error:', e);
            beepError();
            toast('Error de búsqueda', 'danger');
            
            // Liberar en caso de error
            setTimeout(function() {
                procesandoCodigo = false;
            }, 500);
        });
}

function cerrarScanner() {
    console.log('🛑 Cerrando scanner...');
    
    if (scannerActivo && quaggaIniciado) {
        try {
            // ⭐ Limpiar TODOS los eventos
            Quagga.offDetected();
            Quagga.offProcessed();
            Quagga.stop();
            console.log('✅ Scanner cerrado');
        } catch(e) {
            console.error('Error cerrando:', e);
        }
        scannerActivo = false;
        quaggaIniciado = false;
    }
    
    // Resetear variables
    procesandoCodigo = false;
    ultimoCodigo = '';
    tiempoUltimo = 0;
    
    var container = document.getElementById('scanner-container');
    if (container) {
        container.style.display = 'none';
    }
}

// ⭐ Asegurar limpieza al cerrar página
window.addEventListener('beforeunload', function() {
    if (scannerActivo) {
        cerrarScanner();
    }
});

console.log('✅ Scanner configurado sin duplicados');

// =============================================
// 🔌 LECTOR USB - BONUS
// =============================================
var bufferLector = "";
var timerLector = null;

document.addEventListener("keypress", function(e) {
    // Ignorar si estamos en un input
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    
    if (timerLector) clearTimeout(timerLector);
    
    if (e.key === "Enter") {
        if (bufferLector.length > 5) {
            console.log('🔌 Lector USB detectado:', bufferLector);
            var limpio = validarCodigo(bufferLector);
            if (limpio) {
                fetch('/api/vendedor/buscar_producto.php?codigo=' + encodeURIComponent(limpio) + '&_t=' + Date.now())
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success && data.encontrado) {
                        agregarProducto(data.producto);
                    } else {
                        beepError();
                        toast('Producto no encontrado', 'warning');
                    }
                })
                .catch(function() {
                    beepError();
                    toast('Error de búsqueda', 'danger');
                });
            }
        }
        bufferLector = "";
        return;
    }
    
    bufferLector += e.key;
    
    timerLector = setTimeout(function() {
        if (bufferLector.length > 5) {
            console.log('🔌 Lector USB (timeout):', bufferLector);
            var limpio = validarCodigo(bufferLector);
            if (limpio) {
                fetch('/api/vendedor/buscar_producto.php?codigo=' + encodeURIComponent(limpio) + '&_t=' + Date.now())
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success && data.encontrado) {
                        agregarProducto(data.producto);
                    } else {
                        beepError();
                        toast('Producto no encontrado', 'warning');
                    }
                })
                .catch(function() {
                    beepError();
                    toast('Error de búsqueda', 'danger');
                });
            }
        }
        bufferLector = "";
    }, 50);
});

// =============================================
// TOAST
// =============================================
function toast(msg, tipo) {
    var t = tipo || 'success';
    var colores = {
        'success': '#11998e',
        'warning': '#f6d365',
        'danger': '#dc3545',
        'info': '#4facfe'
    };
    var iconos = { 
        'success': 'check-circle-fill', 
        'warning': 'exclamation-triangle-fill', 
        'danger': 'x-circle-fill', 
        'info': 'info-circle-fill' 
    };
    
    var html = '<div class="toast toast-modern align-items-center text-white border-0 show" role="alert" ' +
        'style="background: linear-gradient(135deg, ' + colores[t] + ' 0%, ' + colores[t] + '99 100%);">' +
        '<div class="d-flex">' +
        '<div class="toast-body">' +
        '<i class="bi bi-' + (iconos[t] || 'check-circle') + ' me-2"></i>' + msg + 
        '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
        '</div></div>';
    
    var container = document.querySelector('.toast-container');
    if (container) {
        container.insertAdjacentHTML('beforeend', html);
        var el = container.lastElementChild;
        setTimeout(function() { 
            if (el && el.parentNode) el.remove(); 
        }, 3500);
    }
}

console.log('✅ Punto de venta cargado - VERSION CORREGIDA CON SCANNER FUNCIONAL');
</script>


<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>