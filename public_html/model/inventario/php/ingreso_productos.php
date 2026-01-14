<?php
session_start();

// Cargar configuración y seguridad
require_once __DIR__ . '/../../../../src/config/app_config.php';
require_once __DIR__ . '/../../../../src/security/auth.php';
require_once __DIR__ . '/../../../../src/security/csrf.php';

// Verificar autenticación y rol
require_role([3,1]);

// Interface Superior
require_once __DIR__ . '/../../../utils/interfas/parte_superior.php';
?>

<!-- ESTILOS CORREGIDOS -->
<link rel="stylesheet" href="../css/style_ingreso_mercaderia.css">

<div id="main-content">
    <!-- Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="mb-0"><i class="bi bi-box-seam me-2"></i>Ingreso de Mercadería</h2>
                </div>
                <a href="./agg_Productos.php" class="btn btn-nuevo">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span class="d-none d-sm-inline">Nuevo Producto</span>
                    <span class="d-sm-none">Nuevo</span>
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card purple">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label mb-1">Total Productos</p>
                            <h3 class="stat-value mb-0" id="totalProductos">0</h3>
                        </div>
                        <div class="stat-icon bg-purple"><i class="bi bi-box-seam"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card green">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label mb-1">Stock Total</p>
                            <h3 class="stat-value mb-0" id="stockTotal">0</h3>
                        </div>
                        <div class="stat-icon bg-green"><i class="bi bi-stack"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card orange">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label mb-1">Stock Bajo</p>
                            <h3 class="stat-value mb-0" id="stockBajo">0</h3>
                        </div>
                        <div class="stat-icon bg-orange"><i class="bi bi-exclamation-triangle"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card blue">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="stat-label mb-1">Categorías</p>
                            <h3 class="stat-value mb-0" id="totalCategorias">0</h3>
                        </div>
                        <div class="stat-icon bg-blue"><i class="bi bi-tags"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <div class="main-card">
            <div class="card-header">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-3">
                        <h6><i class="bi bi-list-ul me-2"></i>Lista de Productos</h6>
                        <small class="text-muted" id="lastUpdate"></small>
                    </div>
                    <div class="col-12 col-md-5">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchInput" class="form-control" placeholder="Buscar por nombre, código o barras...">
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                            <button class="btn btn-reload" id="btnRecargar" type="button">
                                <i class="bi bi-arrow-clockwise"></i>
                                <span class="d-none d-sm-inline ms-1">Actualizar</span>
                            </button>
                            <button class="btn btn-scanner btn-scanner-desktop" id="btnAbrirScanner" type="button">
                                <i class="bi bi-upc-scan"></i>
                                <span class="d-none d-sm-inline">Escanear</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th class="hide-mobile">Barras</th>
                                <th>Producto</th>
                                <th class="hide-mobile">P. Compra</th>
                                <th>P. Venta</th>
                                <th>Stock</th>
                                <th class="hide-mobile">Margen</th>
                                <th class="hide-mobile">Caducidad</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productosTableBody">
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                                    <p class="mt-3 text-muted mb-0">Cargando productos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Botón flotante móvil -->
<button class="btn btn-scanner-mobile" id="btnAbrirScannerMobile" type="button">
    <i class="bi bi-upc-scan"></i>
</button>

<div class="toast-container"></div>

<!-- Modal Scanner -->
<div class="modal fade" id="modalScanner" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="bi bi-upc-scan me-2"></i>Escáner de Código de Barras</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success py-2 mb-3 d-none d-md-flex align-items-center" style="border-radius: 12px;">
                    <i class="bi bi-lightning-charge-fill fs-4 me-2"></i>
                    <div>
                        <strong>Modo TURBO Activado</strong>
                        <small class="d-block">Apunta el código de barras al centro del recuadro</small>
                    </div>
                </div>
                
                <div id="scanner-container">
                    <div id="scanner-video"></div>
                    <div class="scanner-overlay">
                        <div class="scanner-line"></div>
                    </div>
                    <div class="scanner-corners">
                        <div class="scanner-corner-bl"></div>
                        <div class="scanner-corner-br"></div>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <div id="scanner-status" class="text-muted">
                        <div class="spinner-border spinner-border-sm me-2"></div>
                        Iniciando cámara...
                    </div>
                </div>
                
                <div id="scanner-result" class="mt-3" style="display: none;">
                    <div class="alert alert-success py-2 mb-0" style="border-radius: 12px;">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <span id="codigo-escaneado"></span>
                    </div>
                </div>
                
                <div class="mt-3">
                    <label class="form-label text-muted small">Código manual:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="codigoManual" placeholder="Escribe el código...">
                        <button class="btn btn-primary px-4" type="button" id="btnProbarManual">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aumentar Stock -->
<div class="modal fade" id="modalAumentarStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Aumentar Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="product-info">
                    <h6 class="mb-2" id="modalProductoNombre">Nombre del Producto</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-tag me-1"></i><span id="modalProductoTipo">Categoría</span>
                        </span>
                        <span class="badge bg-secondary">
                            <i class="bi bi-box me-1"></i>Stock actual: <span id="modalStockActual">0</span>
                        </span>
                    </div>
                </div>
                
                <input type="hidden" id="modalProductoId">
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-plus-slash-minus me-1 text-primary"></i>Cantidad a Agregar
                    </label>
                    <input type="number" class="form-control form-control-lg text-center" id="cantidadAgregar" min="1" placeholder="0">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-box-seam me-1 text-success"></i>Nuevo Stock Total
                    </label>
                    <input type="text" class="form-control text-center fw-bold" id="nuevoStockTotal" readonly 
                           style="background: #e8f5e9; border-color: #a5d6a7; font-size: 1.2rem;">
                </div>

                <div class="mb-2">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-sticky me-1 text-warning"></i>Notas (Opcional)
                    </label>
                    <input type="text" class="form-control" id="notasIngreso" placeholder="Ej: Lote #123">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-save text-white" id="btnGuardarStock">
                    <i class="bi bi-check-circle me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.8.4/dist/quagga.min.js"></script>

<script>
console.log("⚡ SISTEMA DE INGRESO DE MERCADERÍA v2.3 MEJORADO ⚡");

// =============================================
// VARIABLES GLOBALES
// =============================================
var productos = [];
var scannerActivo = false;
var modalScanner = null;
var quaggaIniciado = false;
var audioCtx = null;
var esMovil = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
var ultimoScanTime = 0;
var scannerBloqueado = false;

// =============================================
// AUDIO
// =============================================
function initAudio() {
    try {
        audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    } catch(e) {
        console.warn('Audio no disponible');
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
    } catch(e) {
        console.warn('Error en beep:', e);
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
    } catch(e) {
        console.warn('Error en beep:', e);
    }
    if (navigator.vibrate) navigator.vibrate(300);
}

// =============================================
// TOAST
// =============================================
function toast(msg, tipo) {
    var t = tipo || 'success';
    var iconos = { 
        'success': 'check-circle-fill', 
        'warning': 'exclamation-triangle-fill', 
        'danger': 'x-circle-fill', 
        'info': 'info-circle-fill' 
    };
    
    var html = '<div class="toast align-items-center text-white bg-' + t + ' border-0 show" role="alert">' +
        '<div class="d-flex"><div class="toast-body"><i class="bi bi-' + (iconos[t] || 'check-circle') + ' me-2"></i>' + msg + '</div>' +
        '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>';
    
    var c = document.querySelector('.toast-container');
    if (c) {
        c.insertAdjacentHTML('beforeend', html);
        var el = c.lastElementChild;
        setTimeout(function() { 
            if (el && el.parentNode) {
                el.classList.remove('show');
                setTimeout(function() { el.remove(); }, 300);
            }
        }, 3000);
    }
}

// =============================================
// VALIDAR CÓDIGO
// =============================================
function validarCodigo(c) {
    if (!c) return null;
    c = String(c).trim();
    
    // Código interno PROD-
    var m = c.match(/PROD-\d{8}-[A-F0-9]{6}/);
    if (m) return m[0];
    
    // Códigos de barras estándar
    if (/^\d{13}$/.test(c)) return c; // EAN-13
    if (/^\d{12}$/.test(c)) return c; // UPC-A
    if (/^\d{8}$/.test(c)) return c;  // EAN-8
    
    return null;
}

// =============================================
// ⭐ CARGAR PRODUCTOS - CON ANTI-CACHÉ
// =============================================
function cargarProductos() {
    console.log('📦 Cargando productos...');
    
    var btnRecargar = document.getElementById('btnRecargar');
    if (btnRecargar) {
        btnRecargar.classList.add('loading');
        btnRecargar.disabled = true;
    }
    
    var url = '/api/inventario/obtener_productos.php?_nocache=' + Date.now();
    
    fetch(url, {
        method: 'GET',
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache, no-store, must-revalidate',
            'Pragma': 'no-cache',
            'Expires': '0'
        }
    })
    .then(function(response) {
        if (!response.ok) throw new Error('Error HTTP: ' + response.status);
        return response.json();
    })
    .then(function(data) {
        console.log('📦 Datos recibidos:', data);
        
        if (data.success) {
            productos = data.productos || [];
            
            if (data.estadisticas) {
                document.getElementById('totalProductos').textContent = data.estadisticas.totalProductos || 0;
                document.getElementById('stockTotal').textContent = data.estadisticas.stockTotal || 0;
                document.getElementById('stockBajo').textContent = data.estadisticas.stockBajo || 0;
                document.getElementById('totalCategorias').textContent = data.estadisticas.categorias || 0;
            }
            
            renderizarProductos(productos);
            
            var el = document.getElementById('lastUpdate');
            if (el) el.textContent = 'Actualizado: ' + new Date().toLocaleTimeString('es-EC');
            
            console.log('✅ Cargados:', productos.length, 'productos');
        } else {
            throw new Error(data.error || 'Error desconocido');
        }
    })
    .catch(function(e) {
        console.error('❌ Error:', e);
        toast('Error al cargar: ' + e.message, 'danger');
        
        var tbody = document.getElementById('productosTableBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5 text-danger">' +
                '<i class="bi bi-exclamation-triangle fs-1 d-block mb-3"></i>Error al cargar<br>' +
                '<button class="btn btn-primary btn-sm mt-3" onclick="cargarProductos()">Reintentar</button></td></tr>';
        }
    })
    .finally(function() {
        if (btnRecargar) {
            btnRecargar.classList.remove('loading');
            btnRecargar.disabled = false;
        }
    });
}

// =============================================
// RENDERIZAR PRODUCTOS
// =============================================
function renderizarProductos(data) {
    var tbody = document.getElementById('productosTableBody');
    if (!tbody) return;
    
    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" class="text-center py-5">' +
            '<i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>' +
            '<span class="text-muted">No hay productos</span></td></tr>';
        return;
    }
    
    var html = '';
    for (var i = 0; i < data.length; i++) {
        var p = data[i];
        var stk = parseInt(p.stock) || 0;
        var stockClass = stk < 5 ? 'danger' : stk < 10 ? 'warning' : 'success';
        var fechaCad = (p.fechaCaducidad && p.fechaCaducidad !== '0000-00-00') ? 
            new Date(p.fechaCaducidad).toLocaleDateString('es-EC') : '-';
        
        html += '<tr data-id="' + p.id + '">' +
            '<td><span class="product-code">' + (p.codigo || '').substring(0, 18) + '</span></td>' +
            '<td class="hide-mobile">' + (p.codigoBarras ? '<span class="badge badge-barcode">' + p.codigoBarras + '</span>' : '-') + '</td>' +
            '<td><strong>' + (p.nombre || '').substring(0, 25) + '</strong></td>' +
            '<td class="hide-mobile">$' + parseFloat(p.precioCompra || 0).toFixed(2) + '</td>' +
            '<td><strong class="text-success">$' + parseFloat(p.precioVenta || 0).toFixed(2) + '</strong></td>' +
            '<td><span class="badge badge-stock ' + stockClass + '">' + stk + '</span></td>' +
            '<td class="hide-mobile">' + (p.margen || 0) + '%</td>' +
            '<td class="hide-mobile">' + fechaCad + '</td>' +
            '<td class="text-center">' +
                '<button class="btn-action btn-add" onclick="abrirModalStock(' + p.id + ')" title="Agregar Stock"><i class="bi bi-plus"></i></button>' +
                '<button class="btn-action btn-view" onclick="verDetalles(' + p.id + ')" title="Ver"><i class="bi bi-eye"></i></button>' +
            '</td></tr>';
    }
    tbody.innerHTML = html;
}

// =============================================
// BUSCAR PRODUCTO
// =============================================
function buscarRapido(codigo) {
    var cod = validarCodigo(codigo);
    if (!cod) { 
        beepError(); 
        toast('Código inválido: ' + codigo, 'warning'); 
        return; 
    }
    
    console.log('🔍 Buscando código:', cod);
    
    // Primero buscar en local
    var local = productos.find(function(p) {
        return p.codigoBarras === cod || p.codigo === cod;
    });
    
    if (local) {
        beepExito();
        document.getElementById('searchInput').value = local.nombre;
        renderizarProductos([local]);
        toast("✓ " + local.nombre, 'success');
        
        // Preguntar si desea agregar stock
        setTimeout(function() {
            Swal.fire({
                title: '¿Agregar stock?',
                html: '<p>Producto: <strong>' + local.nombre + '</strong></p><p>Stock actual: <strong>' + local.stock + '</strong></p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, agregar',
                cancelButtonText: 'No',
                confirmButtonColor: '#667eea'
            }).then(function(result) {
                if (result.isConfirmed) {
                    abrirModalStock(local.id);
                }
            });
        }, 300);
        return;
    }
    
    // Si no está en local, buscar en servidor
    fetch("/api/inventario/buscar_producto_por_codigo.php?codigo=" + encodeURIComponent(cod) + "&_t=" + Date.now(), {
        cache: 'no-store',
        headers: {
            'Cache-Control': 'no-cache'
        }
    })
    .then(function(r) { 
        if (!r.ok) throw new Error('Error en servidor');
        return r.json(); 
    })
    .then(function(data) {
        if (data.success && data.encontrado) {
            beepExito();
            toast("✓ " + data.producto.nombre, 'success');
            cargarProductos();
            
            setTimeout(function() {
                abrirModalStock(data.producto.id);
            }, 500);
        } else {
            beepError();
            toast("No encontrado: " + cod, 'warning');
        }
    })
    .catch(function(err) { 
        console.error('Error búsqueda:', err);
        beepError(); 
        toast("Error de conexión", 'danger'); 
    });
}

// =============================================
// FILTRO DE BÚSQUEDA
// =============================================
var timeoutBusqueda = null;

function inicializarBusqueda() {
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(timeoutBusqueda);
            var q = e.target.value.toLowerCase().trim();
            
            if (!q) { 
                renderizarProductos(productos); 
                return; 
            }
            
            timeoutBusqueda = setTimeout(function() {
                var filtrados = productos.filter(function(p) {
                    return (p.nombre && p.nombre.toLowerCase().indexOf(q) !== -1) || 
                           (p.codigo && p.codigo.toLowerCase().indexOf(q) !== -1) ||
                           (p.codigoBarras && p.codigoBarras.indexOf(q) !== -1);
                });
                renderizarProductos(filtrados);
            }, 200);
        });
    }
}

// =============================================
// MODAL AUMENTAR STOCK
// =============================================
function abrirModalStock(id) {
    var p = productos.find(function(x) { return x.id == id; });
    if (!p) { 
        toast('Producto no encontrado', 'danger'); 
        return; 
    }
    
    document.getElementById('modalProductoId').value = p.id;
    document.getElementById('modalProductoNombre').textContent = p.nombre;
    document.getElementById('modalProductoTipo').textContent = p.tipoCategoria || '-';
    document.getElementById('modalStockActual').textContent = p.stock;
    document.getElementById('cantidadAgregar').value = '';
    document.getElementById('nuevoStockTotal').value = '';
    document.getElementById('notasIngreso').value = '';
    
    var modal = new bootstrap.Modal(document.getElementById('modalAumentarStock'));
    modal.show();
    
    setTimeout(function() { 
        document.getElementById('cantidadAgregar').focus(); 
    }, 500);
}

function inicializarModalStock() {
    var cantidadInput = document.getElementById('cantidadAgregar');
    if (cantidadInput) {
        cantidadInput.addEventListener('input', function() {
            var id = parseInt(document.getElementById('modalProductoId').value);
            var p = productos.find(function(x) { return x.id == id; });
            var cantidad = parseInt(this.value) || 0;
            
            if (p && cantidad > 0) {
                document.getElementById('nuevoStockTotal').value = (parseInt(p.stock) + cantidad) + ' unidades';
            } else {
                document.getElementById('nuevoStockTotal').value = '';
            }
        });
    }
    
    var btnGuardar = document.getElementById('btnGuardarStock');
    if (btnGuardar) {
        btnGuardar.addEventListener('click', function() {
            var btn = this;
            var id = parseInt(document.getElementById('modalProductoId').value);
            var cantidad = parseInt(document.getElementById('cantidadAgregar').value);
            var notas = document.getElementById('notasIngreso').value || '';
            
            if (!cantidad || cantidad < 1) { 
                toast('Ingresa una cantidad válida', 'warning'); 
                return; 
            }
            
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
            
            fetch('/api/inventario/actualizar_stock.php?_t=' + Date.now(), {
                method: 'POST',
                cache: 'no-store',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Cache-Control': 'no-cache' 
                },
                body: JSON.stringify({ 
                    id_producto: id, 
                    cantidad: cantidad, 
                    notas: notas 
                })
            })
            .then(function(r) { 
                if (!r.ok) throw new Error('Error en servidor');
                return r.json(); 
            })
            .then(function(data) {
                if (data.success) {
                    beepExito();
                    
                    var modalEl = document.getElementById('modalAumentarStock');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    setTimeout(function() { 
                        cargarProductos(); 
                    }, 300);
                    
                    Swal.fire({
                        icon: 'success',
                        title: '¡Stock Actualizado!',
                        html: '<p class="mb-0">Nuevo stock: <strong class="text-success fs-4">' + data.data.nuevo_stock + '</strong> unidades</p>',
                        confirmButtonColor: '#667eea',
                        timer: 2500,
                        timerProgressBar: true
                    });
                } else { 
                    throw new Error(data.error || 'Error al actualizar'); 
                }
            })
            .catch(function(e) { 
                console.error('Error:', e);
                beepError(); 
                toast('Error: ' + e.message, 'danger'); 
            })
            .finally(function() { 
                btn.disabled = false; 
                btn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Guardar';
            });
        });
    }
}

// =============================================
// VER DETALLES
// =============================================
function verDetalles(id) {
    var p = productos.find(function(x) { return x.id == id; });
    if (!p) { 
        Swal.fire({ 
            icon: 'error', 
            title: 'Error', 
            text: 'Producto no encontrado' 
        }); 
        return; 
    }
    
    var stk = parseInt(p.stock) || 0;
    var stockClass = stk < 5 ? '#ff6b6b' : stk < 10 ? '#f6d365' : '#38ef7d';
    var stockText = stk < 5 ? 'CRÍTICO' : stk < 10 ? 'BAJO' : 'NORMAL';
    var fechaCad = (p.fechaCaducidad && p.fechaCaducidad !== '0000-00-00') ? 
        new Date(p.fechaCaducidad).toLocaleDateString('es-EC', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        }) : 'No especificada';
    
    Swal.fire({
        title: '<i class="bi bi-box-seam" style="color: #667eea;"></i> Detalles',
        html: '<div class="text-start">' +
            '<h5 class="mb-3" style="border-bottom: 2px solid #667eea; padding-bottom: 10px;">' + p.nombre + '</h5>' +
            '<p><strong>Código:</strong> ' + (p.codigo || 'N/A') + '</p>' +
            '<p><strong>Barras:</strong> ' + (p.codigoBarras || 'Sin código') + '</p>' +
            '<hr>' +
            '<div class="row text-center">' +
                '<div class="col-4"><small class="text-muted">Compra</small><br><strong>$' + parseFloat(p.precioCompra || 0).toFixed(2) + '</strong></div>' +
                '<div class="col-4"><small class="text-muted">Venta</small><br><strong class="text-success">$' + parseFloat(p.precioVenta || 0).toFixed(2) + '</strong></div>' +
                '<div class="col-4"><small class="text-muted">Margen</small><br><strong>' + (p.margen || 0) + '%</strong></div>' +
            '</div>' +
            '<hr>' +
            '<div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px; border-left: 4px solid ' + stockClass + ';">' +
                '<small class="text-muted">Stock Actual</small><br>' +
                '<strong class="fs-3">' + stk + '</strong><br>' +
                '<small>' + stockText + '</small>' +
            '</div>' +
            '<p class="mt-3 text-center"><small class="text-muted">Caducidad: ' + fechaCad + '</small></p>' +
            '</div>',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-plus-circle"></i> Agregar Stock',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#667eea',
        width: '450px'
    }).then(function(result) {
        if (result.isConfirmed) abrirModalStock(id);
    });
}

// =============================================
// ⭐ SCANNER CORREGIDO
// =============================================
function inicializarScanner() {
    var modalEl = document.getElementById('modalScanner');
    if (!modalEl) return;
    
    modalScanner = new bootstrap.Modal(modalEl);
    
    var btnScanner = document.getElementById('btnAbrirScanner');
    var btnScannerMobile = document.getElementById('btnAbrirScannerMobile');
    
    if (btnScanner) btnScanner.addEventListener('click', abrirScanner);
    if (btnScannerMobile) btnScannerMobile.addEventListener('click', abrirScanner);
    
    // ⭐ IMPORTANTE: Limpiar completamente al cerrar
    modalEl.addEventListener('hidden.bs.modal', function() {
        console.log('🔴 Modal cerrado, deteniendo scanner...');
        detenerScanner();
        resetearScanner();
    });
    
    // Input manual
    var btnManual = document.getElementById('btnProbarManual');
    var codigoManual = document.getElementById('codigoManual');
    
    if (btnManual) {
        btnManual.addEventListener('click', function() {
            var c = codigoManual.value.trim();
            if (c) { 
                buscarRapido(c); 
                detenerScanner(); 
                modalScanner.hide(); 
            }
        });
    }
    
    if (codigoManual) {
        codigoManual.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                var c = this.value.trim();
                if (c) { 
                    buscarRapido(c); 
                    detenerScanner(); 
                    modalScanner.hide(); 
                }
            }
        });
    }
}

function abrirScanner() {
    console.log('🟢 Abriendo scanner...');
    initAudio();
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume();
    
    resetearScanner();
    modalScanner.show();
    
    setTimeout(function() {
        iniciarQuagga();
    }, 500);
}

function resetearScanner() {
    scannerBloqueado = false;
    ultimoScanTime = 0;
    
    var resultado = document.getElementById('scanner-result');
    if (resultado) resultado.style.display = 'none';
    
    var codigoManual = document.getElementById('codigoManual');
    if (codigoManual) codigoManual.value = '';
    
    var scannerStatus = document.getElementById('scanner-status');
    if (scannerStatus) scannerStatus.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Preparando cámara...';
}

function iniciarQuagga() {
    if (scannerActivo) {
        console.warn('⚠️ Scanner ya está activo');
        return;
    }
    
    if (typeof Quagga === 'undefined') {
        console.error('❌ Quagga no disponible');
        document.getElementById('scanner-status').innerHTML = '<span class="text-danger">Error: Librería no cargada</span>';
        return;
    }
    
    console.log('🎥 Iniciando Quagga...');
    document.getElementById('scanner-status').innerHTML = '<span class="text-warning"><i class="bi bi-lightning-charge"></i> Iniciando cámara...</span>';
    
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
                "upc_reader",
                "upc_e_reader"
            ] 
        },
        locate: true,
        frequency: 10
    };
    
    Quagga.init(config, function(err) {
        if (err) {
            console.error('❌ Error Quagga:', err);
            document.getElementById('scanner-status').innerHTML = '<span class="text-danger">Error al acceder a la cámara</span>';
            return;
        }
        
        console.log('✅ Quagga iniciado correctamente');
        document.getElementById('scanner-status').innerHTML = '<span class="text-success"><i class="bi bi-camera-video-fill"></i> ¡Cámara lista! Escanea el código</span>';
        
        Quagga.start();
        scannerActivo = true;
        quaggaIniciado = true;
        scannerBloqueado = false;
    });
    
    // ⭐ LIMPIAR eventos anteriores
    Quagga.offDetected();
    
    // ⭐ NUEVO manejador con mejor control
    Quagga.onDetected(function(result) {
        if (scannerBloqueado) {
            console.log('🔒 Scanner bloqueado, ignorando detección');
            return;
        }
        
        var codigo = result.codeResult.code;
        var ahora = Date.now();
        
        // Evitar duplicados en 2 segundos
        if (ahora - ultimoScanTime < 2000) {
            console.log('⏱️ Demasiado rápido, ignorando');
            return;
        }
        
        var limpio = validarCodigo(codigo);
        if (!limpio) {
            console.log('❌ Código inválido:', codigo);
            return;
        }
        
        console.log('✅ Código detectado:', limpio);
        
        // Bloquear nuevas detecciones
        scannerBloqueado = true;
        ultimoScanTime = ahora;
        
        // Mostrar resultado
        document.getElementById('codigo-escaneado').textContent = limpio;
        document.getElementById('scanner-result').style.display = 'block';
        document.getElementById('scanner-status').innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> ¡Código capturado!</span>';
        
        // Buscar producto
        buscarRapido(limpio);
        
        // Cerrar modal después de un momento
        setTimeout(function() { 
            detenerScanner(); 
            modalScanner.hide(); 
        }, 800);
    });
}

function detenerScanner() {
    console.log('🛑 Deteniendo scanner...');
    
    if (scannerActivo && quaggaIniciado) {
        try {
            Quagga.offDetected();
            Quagga.offProcessed();
            Quagga.stop();
            console.log('✅ Quagga detenido');
        } catch(e) {
            console.error('Error al detener Quagga:', e);
        }
    }
    
    scannerActivo = false;
    quaggaIniciado = false;
    scannerBloqueado = false;
}

// =============================================
// LECTOR USB/BLUETOOTH
// =============================================
var bufferLector = "";
var timerLector = null;

document.addEventListener("keypress", function(e) {
    // No interferir con inputs
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    
    if (timerLector) clearTimeout(timerLector);
    
    if (e.key === "Enter") {
        if (bufferLector.length > 5) {
            console.log('📟 Lector USB detectó:', bufferLector);
            buscarRapido(bufferLector);
        }
        bufferLector = "";
        return;
    }
    
    bufferLector += e.key;
    
    timerLector = setTimeout(function() {
        if (bufferLector.length > 5) {
            console.log('📟 Lector USB detectó:', bufferLector);
            buscarRapido(bufferLector);
        }
        bufferLector = "";
    }, 50);
});

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log("⚡ Inicializando sistema...");
    
    // Cargar datos
    cargarProductos();
    
    // Inicializar componentes
    inicializarBusqueda();
    inicializarModalStock();
    inicializarScanner();
    
    // Botón recargar
    var btnRecargar = document.getElementById('btnRecargar');
    if (btnRecargar) {
        btnRecargar.addEventListener('click', function() {
            toast('Actualizando datos...', 'info');
            cargarProductos();
        });
    }
    
    console.log("✅ Sistema listo");
    console.log("📱 Dispositivo móvil:", esMovil);
    console.log("🎯 Total productos en memoria:", productos.length);
});

// =============================================
// FUNCIONES DE UTILIDAD
// =============================================

// Limpiar búsqueda
function limpiarBusqueda() {
    var searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        renderizarProductos(productos);
    }
}

// Validar producto antes de abrir modal
function validarProducto(id) {
    var p = productos.find(function(x) { return x.id == id; });
    if (!p) {
        console.error('❌ Producto no encontrado:', id);
        return null;
    }
    return p;
}

// Formatear fecha
function formatearFecha(fecha) {
    if (!fecha || fecha === '0000-00-00') return 'Sin fecha';
    try {
        return new Date(fecha).toLocaleDateString('es-EC', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    } catch(e) {
        return 'Fecha inválida';
    }
}

// Calcular días hasta caducidad
function diasHastaCaducidad(fecha) {
    if (!fecha || fecha === '0000-00-00') return null;
    try {
        var hoy = new Date();
        var caducidad = new Date(fecha);
        var diff = Math.ceil((caducidad - hoy) / (1000 * 60 * 60 * 24));
        return diff;
    } catch(e) {
        return null;
    }
}

// Exportar estadísticas (opcional)
function exportarEstadisticas() {
    var stats = {
        fecha: new Date().toISOString(),
        total_productos: productos.length,
        stock_total: productos.reduce(function(sum, p) { return sum + (parseInt(p.stock) || 0); }, 0),
        stock_bajo: productos.filter(function(p) { return (parseInt(p.stock) || 0) < 10; }).length,
        valor_inventario: productos.reduce(function(sum, p) { 
            return sum + ((parseInt(p.stock) || 0) * (parseFloat(p.precioCompra) || 0)); 
        }, 0).toFixed(2)
    };
    
    console.log('📊 Estadísticas:', stats);
    return stats;
}

// Verificar permisos de cámara
function verificarPermisosCamara() {
    if (navigator.permissions && navigator.permissions.query) {
        navigator.permissions.query({ name: 'camera' }).then(function(result) {
            console.log('📷 Permiso de cámara:', result.state);
            if (result.state === 'denied') {
                toast('Permisos de cámara denegados. Verifica la configuración de tu navegador.', 'warning');
            }
        }).catch(function(err) {
            console.warn('No se pudo verificar permisos:', err);
        });
    }
}

// =============================================
// MANEJO DE ERRORES GLOBAL
// =============================================
window.addEventListener('error', function(e) {
    console.error('💥 Error global:', e.message);
    if (e.message.includes('Quagga')) {
        detenerScanner();
        toast('Error en el scanner. Intenta nuevamente.', 'danger');
    }
});

// =============================================
// PREVENIR CIERRE ACCIDENTAL CON SCANNER ACTIVO
// =============================================
window.addEventListener('beforeunload', function(e) {
    if (scannerActivo) {
        detenerScanner();
    }
});

// =============================================
// DEBUGGING (solo en desarrollo)
// =============================================
if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    window.debug = {
        productos: function() { return productos; },
        scanner: function() { return { activo: scannerActivo, iniciado: quaggaIniciado, bloqueado: scannerBloqueado }; },
        stats: exportarEstadisticas,
        resetScanner: function() { detenerScanner(); resetearScanner(); },
        testCodigo: function(codigo) { buscarRapido(codigo); }
    };
    console.log('🔧 Modo DEBUG activado. Usa window.debug para herramientas de desarrollo.');
}

console.log('🎉 Sistema completamente cargado y operativo');
</script>

<?php
require_once __DIR__ . '/../../../utils/interfas/parte_Inferior.php';
?>