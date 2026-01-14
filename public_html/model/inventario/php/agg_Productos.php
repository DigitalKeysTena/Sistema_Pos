<?php
session_start();

require_once __DIR__ . '/../../../../src/controllers/Inventario/Mostar_Categoria.php';
require_once __DIR__ . '/../../../../src/config/app_config.php';

// Seguridad
require_once __DIR__ . '/../../../../src/security/auth.php';


require_once __DIR__ . '/../../../../src/security/csrf.php';
// Verificar rol
require_role([3, 1]); // solo inventario

// Interface Superior
require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>
<!-- ESTILOS CORREGIDOS -->
<link rel="stylesheet" href="../css/style_formulario.css">
<!-- ESTILOS CORREGIDOS -->
<link rel="stylesheet" href="../css/modal_impresora.css">

<!-- MAIN CONTENT -->
<div id="main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                
                <!-- Card Principal -->
                <div class="main-card">
                    
                    <!-- Header -->
                    <div class="form-header">
                        <i class="bi bi-boxes header-icon"></i>
                        <h4>
                            <i class="bi bi-bag-plus-fill"></i>
                            Crear Nuevo Producto
                        </h4>
                        <p>Complete los campos para registrar un nuevo producto en el inventario</p>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        
                        <!-- Progress Indicator -->
                        <div class="form-progress">
                            <div class="progress-step active" title="Categoría"></div>
                            <div class="progress-step" title="Información"></div>
                            <div class="progress-step" title="Precios"></div>
                            <div class="progress-step" title="Códigos"></div>
                        </div>

                        <form method="POST" action="/api/inventario/agg_Inventario.php" id="formProducto" novalidate>

                            <!-- CSRF TOKEN -->
                            <?= csrf_field() ?>
                            
                            <!-- ========== SECCIÓN 1: CATEGORÍA ========== -->
                            <div class="form-section animate-section">
                                <div class="section-title">
                                    <i class="bi bi-tags-fill bg-purple"></i>
                                    Clasificación del Producto
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="bi bi-folder2 text-primary"></i>
                                            Tipo de Producto <span class="required">*</span>
                                        </label>
                                        <select name="Id_Categoria" id="categoriaSelect" class="form-select">
                                            <option value="">-- Seleccione una categoría --</option>
                                            <?php foreach ($categorias as $c): ?>
                                                <option value="<?= $c['Id_Categoria'] ?>">
                                                    <?= htmlspecialchars($c['Tipo_Categoria']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            <i class="bi bi-bookmark text-primary"></i>
                                            Tipo Específico <span class="required">*</span>
                                        </label>
                                        <select name="Id_Descripcion_Categoria" id="descripcionSelect" class="form-select" disabled>
                                            <option value="">Primero seleccione una categoría</option>
                                        </select>
                                        <div id="mensaje" class="mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========== SECCIÓN 2: INFORMACIÓN BÁSICA ========== -->
                            <div class="form-section animate-section">
                                <div class="section-title">
                                    <i class="bi bi-info-circle-fill bg-blue"></i>
                                    Información del Producto
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label">
                                            <i class="bi bi-box-seam text-info"></i>
                                            Nombre del Producto <span class="required">*</span>
                                        </label>
                                        <input type="text" 
                                               id="Nombre_Producto"
                                               name="Nombre_Producto" 
                                               maxlength="50" 
                                               class="form-control"
                                               placeholder="Ej: Coca Cola 600ml">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">
                                            <i class="bi bi-calendar-check text-info"></i>
                                            Fecha de Entrada <span class="required">*</span>
                                        </label>
                                        <input type="date" name="Fecha_Entrada" class="form-control"
                                            value="<?= date('Y-m-d') ?>" readonly>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="bi bi-calendar-x text-warning"></i>
                                            Fecha de Caducidad <span class="required">*</span>
                                        </label>
                                        <input type="date" name="Fecha_Caducidad" class="form-control"
                                            min="<?= date('Y-m-d') ?>">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="bi bi-stack text-info"></i>
                                            Stock Inicial <span class="required">*</span>
                                        </label>
                                        <input type="text" name="Stock_Producto" class="form-control" 
                                            maxlength="7" placeholder="Cantidad de unidades" inputmode="numeric"
                                            onkeypress="return validarStock(event)"
                                            oninput="limitarStock(this)">
                                    </div>
                                </div>
                            </div>

                            <!-- ========== SECCIÓN 3: PRECIOS ========== -->
                            <div class="form-section animate-section">
                                <div class="section-title">
                                    <i class="bi bi-currency-dollar bg-green"></i>
                                    Precios y Márgenes
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="bi bi-cash text-success"></i>
                                            Precio de Compra <span class="required">*</span>
                                        </label>
                                        <div class="input-icon-wrapper">
                                            <span class="input-icon">$</span>
                                            <input type="text" id="precioCompra" name="Precio_Compra_Producto" 
                                                class="form-control" maxlength="10" placeholder="0.00" 
                                                inputmode="decimal" style="padding-left: 35px;"
                                                onkeypress="return validarPrecio(event)"
                                                oninput="limitarPrecio(this)">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">
                                            <i class="bi bi-percent text-success"></i>
                                            Margen de Utilidad <span class="required">*</span>
                                        </label>
                                        <div class="input-icon-wrapper">
                                            <input type="text" id="margenUtil" name="Margen_Utilidad" 
                                                class="form-control" maxlength="5" placeholder="0 - 99" 
                                                inputmode="decimal"
                                                onkeypress="return validarMargen(event)"
                                                oninput="limitarMargen(this)">
                                            <span class="input-icon" style="left: auto; right: 15px;">%</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="auto-field">
                                            <span class="auto-badge">
                                                <i class="bi bi-lightning-fill"></i> Automático
                                            </span> 
                                            <label class="form-label ">
                                                <i class="bi bi-tag text-success"></i>
                                                Precio de Venta
                                            </label>
                                            <div class="input-icon-wrapper">
                                                <span class="input-icon">$</span>
                                                <input type="number" id="precioVenta" name="Precio_Venta_Producto"
                                                    class="form-control" step="0.01" readonly 
                                                    style="padding-left: 35px; background-color: #e8f5e9; border-color: #a5d6a7;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                          <!-- ========== SECCIÓN 4: CÓDIGOS (MODIFICADA) ========== -->
<div class="form-section animate-section">
    <div class="section-title">
        <i class="bi bi-upc-scan bg-orange"></i>
        Códigos de Identificación
    </div>
    
    <div class="row g-3">
        <!-- CÓDIGO INTERNO (Automático) -->
        <div class="col-md-6">
            <div class="auto-field">
                <span class="auto-badge">
                    <i class="bi bi-lightning-fill"></i> Automático
                </span>
                <label class="form-label">
                    <i class="bi bi-qr-code text-warning"></i>
                    Código Interno
                </label>
                <input type="text" 
                       id="Codigo_Producto" 
                       name="Codigo_Producto"
                       class="form-control" 
                       maxlength="50" 
                       readonly 
                       placeholder="Se genera automáticamente"
                       style="background-color: #fff3e0; border-color: #ffcc80;">
            </div>
        </div>

        <!-- CÓDIGO DE BARRAS (Manual/Automático) -->
        <div class="col-md-6">
            <label class="form-label">
                <i class="bi bi-upc text-warning"></i>
                Código de Barras EAN-13
                <span class="info-tooltip" title="Puedes ingresarlo manualmente o generarlo automáticamente">
                    <i class="bi bi-question-circle"></i>
                </span>
            </label>
            
            <div class="input-group">
                <input type="text" 
                       id="Codigo_Barras"
                       name="Codigo_Barras" 
                       maxlength="13" 
                       class="form-control"
                       placeholder="Escribe o genera EAN-13"
                       inputmode="numeric"
                       onkeypress="return soloNumeros(event)"
                       oninput="validarEAN13(this)">
                
                <button class="btn btn-outline-warning" 
                        type="button" 
                        id="btnGenerarBarras"
                        title="Generar código de barras automáticamente"
                        style="border-radius: 0 12px 12px 0;">
                    <i class="bi bi-magic"></i>
                </button>
            </div>
            
            <div id="mensajeBarras" class="mt-2"></div>
            
            <small class="text-muted d-block mt-2">
                <i class="bi bi-info-circle me-1"></i>
                13 dígitos numéricos. Puedes escribirlo o generarlo automáticamente.
            </small>
        </div>

        <!-- Botón Generar SOLO Código Interno -->
        <div class="col-12 mt-3">
            <button type="button" 
                    id="generarCodigoBtn" 
                    class="btn-generate" 
                    disabled>
                <i class="bi bi-magic fs-5"></i>
                Generar Código Interno
            </button>
            <small class="text-muted d-block text-center mt-2">
                <i class="bi bi-info-circle me-1"></i>
                Primero ingresa el nombre del producto para habilitar este botón
            </small>
        </div>
    </div>
</div>
                            <!-- ========== BOTÓN GUARDAR ========== -->
                            <div class="mt-4">
                                <button type="submit" id="btnGuardarProducto" class="btn-save" disabled>
                                    <i class="bi bi-check-circle-fill fs-5"></i>
                                    Guardar Producto en Inventario
                                </button>
                                <small class="text-muted d-block text-center mt-2">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Complete todos los campos y genere los códigos para guardar
                                </small>
                            </div>

                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>

<!-- Modal de Impresión de Etiqueta - Diseño Moderno -->
<div class="modal fade" id="modalImprimirEtiqueta" tabindex="-1" aria-labelledby="modalEtiquetaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg p-5">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">

            <!-- HEADER -->
            <div class="modal-header border-0 text-white position-relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.5rem;">
                <div class="position-relative z-1">
                    <h4 class="modal-title mb-1" id="modalEtiquetaLabel">
                        <i class="bi bi-printer-fill me-2"></i>Imprimir Etiquetas del Producto
                    </h4>
                    <small class="opacity-75">Configura e imprime tus etiquetas adhesivas</small>
                </div>
                <button type="button" class="btn-close btn-close-white position-relative z-1" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4" style="background: #f8f9fa;">
                <div class="row g-4">

                    <!-- COL IZQUIERDA: PREVIEW -->
                    <div class="col-lg-7">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-header bg-white border-bottom" style="border-radius: 15px 15px 0 0;">
                                <h6 class="mb-0 text-secondary">
                                    <i class="bi bi-eye me-2"></i>Vista Previa de la Etiqueta
                                </h6>
                            </div>

                            <div class="card-body d-flex align-items-center justify-content-center p-4">

                                <!-- Etiqueta 10x5 CM -->
                                <div id="etiquetaPreview" class="bg-white shadow-lg"
                                     style="width: 400px; border: 3px solid #dee2e6; border-radius: 12px; padding: 15px; position: relative; overflow: hidden;">

                                    <div style="position: absolute; top: 8px; left: 8px; right: 8px; bottom: 8px;
                                        border: 2px dashed #e0e0e0; border-radius: 8px; pointer-events: none;">
                                    </div>

                                    <div class="position-relative h-100 d-flex flex-column justify-content-between">

                                        <div class="text-center">
                                            <img src="../../../utils/img/logo.png" 
                                                 style="max-height: 45px; margin-bottom: 5px;">
                                            <h6 class="fw-bold mb-0" id="nombreEtiqueta" style="font-size: 13px;">NOMBRE</h6>
                                        </div>

                                        <div class="d-flex justify-content-center">
                                            <svg id="codigoBarras" style="max-width: 100%;"></svg>
                                        </div>

                                        <div class="row g-2 align-items-center">
                                            <div class="col-5">
                                                <div class="text-center py-2 rounded"
                                                     style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);">
                                                    <div style="font-size: 24px; font-weight: 900;">
                                                        $<span id="precioEtiqueta">0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 text-center">
                                                <small class="text-muted d-block" style="font-size: 9px; font-weight: 700;">CÓDIGO</small>
                                                <small class="fw-bold" id="codigoTexto" style="font-size: 10px;">000000</small>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- COL DERECHA: CONFIGURACIÓN -->
                    <div class="col-lg-5">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                            <div class="card-header bg-white border-bottom" style="border-radius: 15px 15px 0 0;">
                                <h6 class="mb-0 text-secondary">
                                    <i class="bi bi-gear me-2"></i>Configuración de Impresión
                                </h6>
                            </div>

                            <div class="card-body p-3">

                                <div class="alert alert-info" style="border-radius: 12px;">
                                    <i class="bi bi-box-seam fs-3 me-3"></i>
                                    <div>
                                        <small class="text-muted">Stock Disponible</small>
                                        <h3 id="stockDisponible" class="fw-bold">0</h3>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-muted small mb-2">Producto</label>
                                    <div class="p-3 bg-light rounded border" style="border-radius: 12px !important;">
                                        <div class="fw-bold" id="infoNombreProducto">-</div>
                                        <small class="text-muted">Código: <span id="infoCodigoProducto">-</span></small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Cantidad de Etiquetas</label>
                                    <div class="input-group input-group-lg">
                                        <button class="btn btn-outline-secondary" id="btnDisminuir" style="border-radius: 12px 0 0 12px;">
                                            <i class="bi bi-dash-lg"></i>
                                        </button>
                                        <input type="number" id="cantidadEtiquetas"
                                               class="form-control text-center fw-bold fs-4"
                                               value="1" min="1" readonly>
                                        <button class="btn btn-outline-secondary" id="btnAumentar" style="border-radius: 0 12px 12px 0;">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Máximo: <span id="maxEtiquetas">0</span>
                                    </small>
                                </div>

                                <div class="alert alert-light border" style="border-radius: 12px;">
                                    <small class="text-muted d-block mb-2"><strong>Dimensiones:</strong></small>
                                    <div class="d-flex justify-content-around">
                                        <div class="text-center"><div class="badge bg-secondary">10 cm</div><small style="padding-left: 10px;">Ancho</small></div>
                                        <div class="text-center"><div class="badge bg-secondary">5 cm</div><small style="padding-left: 10px;">Alto</small></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light border-0 p-4">
                <button class="btn btn-lg btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 12px;">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button class="btn btn-lg text-white" id="btnImprimirEtiqueta" 
                    style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-printer-fill me-2"></i>Imprimir Etiquetas
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Área oculta donde se preparan las etiquetas -->
<div id="areaImpresion"></div>

<!-- Fondo blanco para tapar mientras se prepara la impresión -->
<div id="fondoBlanco">
    <div class="text-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Preparando...</span>
        </div>
        <h4 class="mt-3 text-primary">Preparando etiquetas...</h4>
        <p class="text-muted">Por favor espera un momento</p>
    </div>
</div>



<!-- SCRIPT CORREGIDO -->
<script src="../js/modal_impresora.js"></script>

<!-- CALCULAR PRECIO DE VENTA -->
<script src="../js/sistema_validacion_completa.js"></script>

</div>

<!-- ALERTA DE SESIÓN PHP -> SweetAlert2 -->
<?php include '../js/alerta_sesion.php'; ?>

<!-- CALCULAR PRECIO DE VENTA -->
<script src="../js/calcular_precio_venta.js"></script>

<!-- SELECT DEPENDIENTE -->
<script src="../js/select_categoria.js"></script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>