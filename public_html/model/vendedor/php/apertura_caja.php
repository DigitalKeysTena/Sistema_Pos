<?php
// public_html/model/vendedor/php/apertura_caja.php
session_start();

require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';

require_role([2,1]); // Vendedor y Admin

require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<link rel="stylesheet" href="../css/apertura_caja.css">

<div id="main-content">
    <div class="container-fluid py-4">

        <!-- HEADER -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="header-card animate-item">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h2 class="mb-2">
                                <i class="bi bi-unlock me-3"></i>
                                Apertura de Caja
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

        <div class="apertura-container">

            <!-- Contenedor para estado de caja (si ya está abierta) -->
            <div id="estadoCajaContainer"></div>

            <!-- INFO CARD -->
            <div class="info-card animate-item" style="animation-delay: 0.1s;">
                <i class="bi bi-cash-coin"></i>
                <h4>Iniciar Jornada de Trabajo</h4>
                <p>Registra el monto inicial con el que comenzarás las operaciones del día</p>
            </div>

            <!-- FORMULARIO DE APERTURA -->
            <div class="main-card animate-item" style="animation-delay: 0.2s;">
                <div class="card-header-custom">
                    <h5>
                        <i class="bi bi-pencil-square me-2"></i>
                        Datos de Apertura
                    </h5>
                </div>

                <div class="form-section">

                    <!-- ALERTA INFORMATIVA -->
                    <div class="alert-info-box">
                        <i class="bi bi-info-circle"></i>
                        <strong>Importante:</strong> Verifica el monto inicial antes de confirmar. 
                        Este será el punto de partida para el cierre de caja.
                    </div>

                    <!-- MONTO INICIAL - CORREGIDO: sin value predeterminado -->
                    <div class="input-group-custom">
                        <label>
                            <i class="bi bi-currency-dollar"></i>
                            Monto Inicial en Caja
                        </label>
                        <input type="number" 
                               class="form-control input-money" 
                               id="montoInicial" 
                               placeholder="0.00" 
                               step="0.01" 
                               min="0"
                               onfocus="this.select()">
                        <small class="text-muted">Ingresa el dinero con el que inicias el día (deja vacío o 0 si no tienes efectivo)</small>
                    </div>

                    <!-- OBSERVACIONES -->
                    <div class="input-group-custom">
                        <label>
                            <i class="bi bi-chat-left-text"></i>
                            Observaciones (Opcional)
                        </label>
                        <textarea class="form-control" 
                                  id="observaciones" 
                                  rows="3"
                                  placeholder="Añade cualquier observación sobre la apertura de caja..."></textarea>
                    </div>

                    <!-- ALERTAS -->
                    <div class="alert-warning-box">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Recuerda:</strong> Solo puedes abrir caja una vez al día. 
                        Asegúrate de contar correctamente el dinero inicial.
                    </div>

                    <!-- BOTONES -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <button type="button" 
                                    class="btn btn-modern btn-abrir-caja w-100" 
                                    id="btnAbrirCaja">
                                <i class="bi bi-unlock-fill"></i>
                                Abrir Caja
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="button" 
                                    class="btn btn-modern btn-cancelar w-100" 
                                    id="btnCancelar">
                                <i class="bi bi-x-circle"></i>
                                Cancelar
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- INFORMACIÓN ADICIONAL -->
            <div class="main-card mt-4 animate-item" style="animation-delay: 0.3s;">
                <div class="card-header-custom">
                    <h5>
                        <i class="bi bi-question-circle me-2"></i>
                        ¿Qué sucede al abrir caja?
                    </h5>
                </div>
                <div class="form-section">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Se registra el <strong>monto inicial</strong> con el que comienzas
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Se habilita el sistema para <strong>realizar ventas</strong>
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Se puede <strong>registrar gastos y retiros</strong> durante el día
                        </li>
                        <li class="mb-3">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Al final del día podrás <strong>cerrar caja</strong> y verificar diferencias
                        </li>
                    </ul>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Spinner de Carga -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-content">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <h5>Abriendo caja...</h5>
        <p class="text-muted mb-0">Por favor espere</p>
    </div>
</div>

<!-- Cargar JavaScript -->
<script src="../js/apertura_caja.js?v=2.0"></script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>
