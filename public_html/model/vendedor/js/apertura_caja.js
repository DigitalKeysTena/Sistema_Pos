/**
 * APERTURA DE CAJA - JavaScript con SweetAlert2 Mejorado
 * Versión: 2.0 - Alertas profesionales
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('🔓 Inicializando módulo de apertura de caja');
    inicializarPagina();
    verificarEstadoCaja();
    configurarEventListeners();
});

function inicializarPagina() {
    const fechaActual = new Date();
    const opciones = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const fechaElement = document.getElementById('fechaActual');
    if (fechaElement) {
        fechaElement.textContent = fechaActual.toLocaleDateString('es-ES', opciones);
    }
    
    // Cargar saldo del día anterior
    cargarSaldoAnterior();
}

async function cargarSaldoAnterior() {
    try {
        const response = await fetch('/api/vendedor/obtener_saldo_anterior.php?_t=' + Date.now());
        const data = await response.json();
        
        if (data.success && data.tiene_saldo_anterior) {
            const saldo = parseFloat(data.saldo_anterior);
            
            // Mostrar alerta de saldo anterior
            const alertaContainer = document.querySelector('.container-fluid');
            if (alertaContainer && saldo !== 0) {
                const alertaHTML = `
                    <div class="alert ${saldo < 0 ? 'alert-warning' : 'alert-info'} alert-dismissible fade show mt-3" role="alert">
                        <h5 class="alert-heading">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Saldo del día anterior
                        </h5>
                        <hr>
                        <p class="mb-0">
                            <strong>Fecha cierre anterior:</strong> ${data.fecha_cierre}<br>
                            <strong>Saldo arrastrado:</strong> <span class="fs-5 ${saldo < 0 ? 'text-danger' : 'text-success'}">${formatearMoneda(saldo)}</span>
                            ${saldo < 0 ? '<br><small class="text-muted">Este déficit debe cubrirse con el monto inicial de hoy</small>' : ''}
                        </p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                
                alertaContainer.insertAdjacentHTML('afterbegin', alertaHTML);
            }
        }
        
    } catch (error) {
        console.error('Error al cargar saldo anterior:', error);
    }
}

function configurarEventListeners() {
    const btnAbrirCaja = document.getElementById('btnAbrirCaja');
    if (btnAbrirCaja) {
        btnAbrirCaja.addEventListener('click', confirmarApertura);
    }
    
    const btnCancelar = document.getElementById('btnCancelar');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Cancelar apertura?',
                text: 'Regresarás al panel principal',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                cancelButtonColor: '#11998e',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, quedarme',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = './vendedor.php';
                }
            });
        });
    }
}

async function verificarEstadoCaja() {
    try {
        const url = '/api/vendedor/verificar_estado_caja.php?_t=' + Date.now();
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('📊 Estado de caja:', data);
        
        if (data.success && data.tiene_caja_abierta) {
            mostrarCajaYaAbierta(data.caja);
        }
        
    } catch (error) {
        console.error('❌ Error al verificar estado:', error);
    }
}

function mostrarCajaYaAbierta(caja) {
    const contenedor = document.getElementById('estadoCajaContainer');
    if (!contenedor) return;
    
    contenedor.innerHTML = `
        <div class="estado-caja-card animate-item">
            <h5>
                <i class="bi bi-check-circle me-2"></i>
                Ya tienes una caja abierta para hoy
            </h5>
            <div class="estado-caja-info">
                <div>
                    <strong>Monto Inicial:</strong> $${parseFloat(caja.Monto_Inicial).toFixed(2)}
                </div>
                <div>
                    <strong>Hora:</strong> ${caja.hora_apertura}
                </div>
            </div>
            <div class="mt-3">
                <p class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Puedes proceder a realizar ventas o al cierre de caja
                </p>
            </div>
        </div>
    `;
    
    // Deshabilitar formulario
    document.getElementById('montoInicial').disabled = true;
    document.getElementById('observaciones').disabled = true;
    document.getElementById('btnAbrirCaja').disabled = true;
    
    // Mostrar alerta informativa
    Swal.fire({
        title: '¡Caja ya abierta!',
        html: `
            <div style="text-align: left;">
                <p><strong>Monto inicial:</strong> $${parseFloat(caja.Monto_Inicial).toFixed(2)}</p>
                <p><strong>Hora de apertura:</strong> ${caja.hora_apertura}</p>
                <p class="text-muted mb-0">Ya puedes realizar ventas normalmente</p>
            </div>
        `,
        icon: 'info',
        confirmButtonColor: '#11998e',
        confirmButtonText: 'Entendido'
    });
}

function confirmarApertura() {
    const montoInicial = parseFloat(document.getElementById('montoInicial').value) || 0;
    
    if (montoInicial < 0) {
        Swal.fire({
            title: 'Monto inválido',
            text: 'El monto inicial no puede ser negativo',
            icon: 'warning',
            confirmButtonColor: '#f6a625',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // ALERTA PROFESIONAL DE CONFIRMACIÓN
    Swal.fire({
        title: '¿Confirmar apertura de caja?',
        html: `
            <div style="text-align: left; padding: 20px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                            color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div style="font-size: 2rem; font-weight: bold;">
                        $${montoInicial.toFixed(2)}
                    </div>
                    <div style="opacity: 0.9; font-size: 0.9rem;">Monto inicial</div>
                </div>
                
                <div style="text-align: center; color: #6c757d;">
                    <p style="margin: 10px 0;">
                        <i class="bi bi-calendar-check" style="color: #11998e;"></i>
                        <strong>Fecha:</strong> ${new Date().toLocaleDateString('es-ES')}
                    </p>
                    <p style="margin: 10px 0;">
                        <i class="bi bi-clock" style="color: #11998e;"></i>
                        <strong>Hora:</strong> ${new Date().toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'})}
                    </p>
                </div>
                
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; 
                            padding: 15px; border-radius: 8px; margin-top: 20px; text-align: left;">
                    <strong style="color: #856404;">
                        <i class="bi bi-exclamation-triangle"></i> Importante:
                    </strong>
                    <p style="color: #856404; margin: 5px 0 0 0; font-size: 0.9rem;">
                        Verifica que el monto sea correcto antes de confirmar
                    </p>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-unlock-fill me-2"></i> Sí, abrir caja',
        cancelButtonText: '<i class="bi bi-x-circle me-2"></i> Cancelar',
        reverseButtons: true,
        width: '600px',
        customClass: {
            confirmButton: 'btn btn-lg',
            cancelButton: 'btn btn-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            procesarApertura();
        }
    });
}

async function procesarApertura() {
    try {
        // Mostrar loading
        Swal.fire({
            title: 'Abriendo caja...',
            html: `
                <div style="padding: 20px;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p style="margin-top: 20px; color: #6c757d;">Por favor espere</p>
                </div>
            `,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        const montoInicial = parseFloat(document.getElementById('montoInicial').value) || 0;
        const observaciones = document.getElementById('observaciones').value.trim();
        
        const datos = {
            monto_inicial: montoInicial,
            observaciones: observaciones
        };
        
        console.log('📤 Enviando apertura:', datos);
        
        const url = '/api/vendedor/procesar_apertura_caja.php';
        const response = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(datos)
        });
        
        console.log('📥 Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('✅ Resultado:', result);
        
        if (result.success) {
            // ALERTA DE ÉXITO PROFESIONAL
            Swal.fire({
                title: '¡Caja abierta exitosamente!',
                html: `
                    <div style="padding: 20px;">
                        <div style="font-size: 5rem; color: #28a745; margin-bottom: 20px;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        
                        <div style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); 
                                    color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                            <div style="font-size: 2rem; font-weight: bold;">
                                $${parseFloat(result.monto_inicial).toFixed(2)}
                            </div>
                            <div style="opacity: 0.9;">Monto inicial registrado</div>
                        </div>
                        
                        <div style="background: #d4edda; border-left: 4px solid #28a745; 
                                    padding: 15px; border-radius: 8px; text-align: left;">
                            <strong style="color: #155724;">
                                <i class="bi bi-info-circle"></i> Ahora puedes:
                            </strong>
                            <ul style="color: #155724; margin: 10px 0 0 0; padding-left: 20px;">
                                <li>Realizar ventas</li>
                                <li>Registrar gastos y retiros</li>
                                <li>Cerrar caja al final del día</li>
                            </ul>
                        </div>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: '<i class="bi bi-arrow-right-circle me-2"></i> Ir al panel',
                timer: 5000,
                timerProgressBar: true,
                width: '600px'
            }).then(() => {
                window.location.href = './vendedor.php';
            });
        } else {
            // ALERTA DE ERROR
            Swal.fire({
                title: 'Error al abrir caja',
                html: `
                    <div style="padding: 20px;">
                        <div style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <p style="color: #721c24; font-size: 1.1rem;">
                            ${result.message || 'Error desconocido'}
                        </p>
                    </div>
                `,
                icon: 'error',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        }
        
    } catch (error) {
        console.error('❌ Error completo:', error);
        
        Swal.fire({
            title: 'Error de conexión',
            html: `
                <div style="padding: 20px;">
                    <div style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;">
                        <i class="bi bi-wifi-off"></i>
                    </div>
                    <p style="color: #721c24;">
                        No se pudo conectar con el servidor. Por favor, verifica tu conexión e intenta nuevamente.
                    </p>
                </div>
            `,
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Entendido'
        });
    }
}

console.log('✅ Script de apertura de caja con SweetAlert2 cargado');

function formatearMoneda(valor) {
    const numero = parseFloat(valor) || 0;
    return "$" + numero.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}

console.log("✅ Script de apertura de caja con SweetAlert2 cargado");