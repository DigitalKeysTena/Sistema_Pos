/**
 * BLOQUEO DE VENTAS SIN CAJA ABIERTA
 * Script para integrar en el POS/Sistema de ventas
 */

// Variable global para estado de caja
let estadoCaja = {
    tiene_caja_abierta: false,
    puede_vender: false,
    caja: null
};

// Verificar estado de caja al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔒 Verificando estado de caja...');
    verificarEstadoCajaActiva();
    
    // Verificar cada 2 minutos
    setInterval(verificarEstadoCajaActiva, 120000);
});

/**
 * Verificar si hay caja abierta
 */
async function verificarEstadoCajaActiva() {
    try {
        const url = '/api/vendedor/verificar_caja_activa.php?_t=' + Date.now();
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📊 Estado de caja:', data);
        
        estadoCaja = data;
        
        if (!data.tiene_caja_abierta) {
            bloquearSistemaVentas();
            mostrarAlertaCajaNoAbierta();
        } else {
            desbloquearSistemaVentas();
            ocultarAlertaCajaNoAbierta();
        }
        
        return data;
        
    } catch (error) {
        console.error('❌ Error verificando estado de caja:', error);
        // En caso de error, bloquear por seguridad
        bloquearSistemaVentas();
        return null;
    }
}

/**
 * Bloquear sistema de ventas
 */
function bloquearSistemaVentas() {
    console.log('🔒 Bloqueando sistema de ventas - No hay caja abierta');
    
    // Deshabilitar botones de venta
    const botonesVenta = document.querySelectorAll(
        '#btnProcesarVenta, #btnFinalizarVenta, #btnAgregarProducto, .btn-vender, .btn-agregar-carrito'
    );
    
    botonesVenta.forEach(btn => {
        if (btn) {
            btn.disabled = true;
            btn.classList.add('disabled');
            btn.title = 'Debe abrir caja antes de vender';
        }
    });
    
    // Deshabilitar inputs de productos
    const inputsProducto = document.querySelectorAll(
        '#codigoProducto, #buscarProducto, .producto-input'
    );
    
    inputsProducto.forEach(input => {
        if (input) {
            input.disabled = true;
            input.placeholder = 'Debe abrir caja primero';
        }
    });
    
    // Agregar overlay de bloqueo si existe
    const posContainer = document.querySelector('#pos-container, #venta-container, .pos-main');
    if (posContainer) {
        let overlay = document.getElementById('overlay-caja-bloqueada');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'overlay-caja-bloqueada';
            overlay.className = 'overlay-bloqueo-caja';
            overlay.innerHTML = `
                <div class="mensaje-bloqueo">
                    <i class="bi bi-lock-fill" style="font-size: 4rem; color: #dc3545; margin-bottom: 1rem;"></i>
                    <h3>Caja No Abierta</h3>
                    <p>Debe abrir caja antes de realizar ventas</p>
                    <button onclick="irAAperturaCaja()" class="btn btn-primary btn-lg">
                        <i class="bi bi-unlock me-2"></i>
                        Abrir Caja Ahora
                    </button>
                </div>
            `;
            posContainer.appendChild(overlay);
        }
        overlay.style.display = 'flex';
    }
}

/**
 * Desbloquear sistema de ventas
 */
function desbloquearSistemaVentas() {
    console.log('✅ Desbloqueando sistema de ventas - Caja abierta');
    
    // Habilitar botones de venta
    const botonesVenta = document.querySelectorAll(
        '#btnProcesarVenta, #btnFinalizarVenta, #btnAgregarProducto, .btn-vender, .btn-agregar-carrito'
    );
    
    botonesVenta.forEach(btn => {
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('disabled');
            btn.title = '';
        }
    });
    
    // Habilitar inputs de productos
    const inputsProducto = document.querySelectorAll(
        '#codigoProducto, #buscarProducto, .producto-input'
    );
    
    inputsProducto.forEach(input => {
        if (input) {
            input.disabled = false;
            input.placeholder = 'Buscar producto...';
        }
    });
    
    // Ocultar overlay de bloqueo
    const overlay = document.getElementById('overlay-caja-bloqueada');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

/**
 * Mostrar alerta de caja no abierta
 */
function mostrarAlertaCajaNoAbierta() {
    let alerta = document.getElementById('alerta-caja-no-abierta');
    
    if (!alerta) {
        alerta = document.createElement('div');
        alerta.id = 'alerta-caja-no-abierta';
        alerta.className = 'alert alert-danger alert-caja-fija';
        alerta.innerHTML = `
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <strong>¡Atención!</strong> No hay caja abierta. 
                        Debe abrir caja antes de realizar ventas.
                    </div>
                </div>
                <button onclick="irAAperturaCaja()" class="btn btn-light btn-sm">
                    <i class="bi bi-unlock me-2"></i>
                    Abrir Caja
                </button>
            </div>
        `;
        
        // Insertar al inicio del body
        document.body.insertBefore(alerta, document.body.firstChild);
    }
    
    alerta.style.display = 'block';
}

/**
 * Ocultar alerta de caja no abierta
 */
function ocultarAlertaCajaNoAbierta() {
    const alerta = document.getElementById('alerta-caja-no-abierta');
    if (alerta) {
        alerta.style.display = 'none';
    }
}

/**
 * Redirigir a apertura de caja
 */
function irAAperturaCaja() {
    window.location.href = '/apertura_caja.php';
}

/**
 * Interceptar intentos de venta
 */
function validarCajaAntesDeVender() {
    if (!estadoCaja.tiene_caja_abierta) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Caja No Abierta',
                text: 'Debe abrir caja antes de realizar ventas',
                confirmButtonText: 'Abrir Caja Ahora',
                showCancelButton: true,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    irAAperturaCaja();
                }
            });
        } else {
            if (confirm('Debe abrir caja antes de vender. ¿Ir a apertura de caja?')) {
                irAAperturaCaja();
            }
        }
        return false;
    }
    return true;
}

// Estilos CSS para el bloqueo (agregar a tu CSS o crear inline)
const estilosBloqueo = `
<style>
.overlay-bloqueo-caja {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.mensaje-bloqueo {
    background: white;
    padding: 3rem;
    border-radius: 12px;
    text-align: center;
    max-width: 500px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.mensaje-bloqueo h3 {
    color: #dc3545;
    margin-bottom: 1rem;
}

.alert-caja-fija {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9998;
    margin: 0;
    border-radius: 0;
    padding: 1rem;
}
</style>
`;

// Insertar estilos
document.head.insertAdjacentHTML('beforeend', estilosBloqueo);

console.log('✅ Sistema de bloqueo de ventas cargado');