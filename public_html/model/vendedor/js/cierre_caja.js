/**
 * CIERRE DE CAJA - VERSIÓN FINAL CORREGIDA
 * Correcciones realizadas:
 * 1. Muestra historial de gastos y retiros correctamente
 * 2. Calcula diferencia CORRECTA (sin duplicar gastos/retiros)
 * 3. Redirige a apertura si caja cerrada
 * 4. Desglose se oculta/muestra correctamente
 * 5. Muestra faltante/sobrante real
 */

let datosVentas = {
    montoInicial: 0,
    totalVentas: 0,
    numeroVentas: 0,
    ventasEfectivo: 0,
    ventasTransferencia: 0,
    gastos: 0,
    retiros: 0,
    saldoAcumulado: 0
};

let gastosDelDia = [];
let retirosDelDia = [];
let cargaInicial = false;
let desgloseVisible = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Inicializando módulo de cierre de caja');
    inicializarPagina();
    cargarTodosDatos();
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
}

async function cargarTodosDatos() {
    if (cargaInicial) {
        console.log('⚠️ Carga ya en progreso');
        return;
    }
    
    cargaInicial = true;
    mostrarSpinner(true);
    
    try {
        await cargarDatosCaja();
        await cargarGastos();
        await cargarRetiros();
        
        // Calcular totales AL FINAL cuando todos los datos están listos
        calcularTotales();
        
        console.log('✅ Todos los datos cargados:', datosVentas);
        
    } catch (error) {
        console.error('❌ Error al cargar datos:', error);
        // Los errores de caja cerrada o no abierta ya se manejan en cargarDatosCaja
    } finally {
        mostrarSpinner(false);
        cargaInicial = false;
    }
}

function configurarEventListeners() {
    // Listener para input de efectivo manual
    const totalEfectivoInput = document.getElementById('totalEfectivo');
    if (totalEfectivoInput) {
        totalEfectivoInput.addEventListener('input', calcularTotales);
    }
    
    // Listeners para denominaciones
    const inputsDenominacion = document.querySelectorAll('.denominacion-input');
    inputsDenominacion.forEach(input => {
        input.addEventListener('input', function() {
            calcularDenominacion(this);
            calcularTotalDesglose();
        });
    });
    
    // Botón de desglose - toggle manual
    const btnDesglose = document.querySelector('[data-bs-target="#desgloseBilletes"]');
    if (btnDesglose) {
        btnDesglose.addEventListener('click', toggleDesglose);
    }
    
    // Botón cerrar caja
    const btnCerrarCaja = document.getElementById('btnCerrarCaja');
    if (btnCerrarCaja) {
        btnCerrarCaja.addEventListener('click', mostrarOpcionesCierre);
    }
    
    // Botón cancelar
    const btnCancelar = document.getElementById('btnCancelar');
    if (btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            Swal.fire({
                title: '¿Cancelar cierre?',
                text: 'Los cambios no guardados se perderán',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, continuar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = './vendedor.php';
                }
            });
        });
    }
    
    // Botones de gastos y retiros
    const btnRegistrarGasto = document.getElementById('btnRegistrarGasto');
    if (btnRegistrarGasto) {
        btnRegistrarGasto.addEventListener('click', registrarGasto);
    }
    
    const btnRegistrarRetiro = document.getElementById('btnRegistrarRetiro');
    if (btnRegistrarRetiro) {
        btnRegistrarRetiro.addEventListener('click', registrarRetiro);
    }
}

// ============================================
// TOGGLE DESGLOSE - CORREGIDO
// ============================================
function toggleDesglose() {
    const desglose = document.getElementById('desgloseBilletes');
    const btnDesglose = document.querySelector('[data-bs-target="#desgloseBilletes"]');
    
    if (!desglose) return;
    
    desgloseVisible = !desgloseVisible;
    
    if (desgloseVisible) {
        desglose.classList.add('show');
        if (btnDesglose) {
            btnDesglose.innerHTML = '<i class="bi bi-dash-circle me-2"></i>Ocultar Desglose';
        }
    } else {
        desglose.classList.remove('show');
        if (btnDesglose) {
            btnDesglose.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Desglose Detallado de Billetes y Monedas';
        }
    }
}

// ============================================
// CARGA DE DATOS DE CAJA
// ============================================
async function cargarDatosCaja() {
    const url = '/api/vendedor/obtener_datos_caja.php?_t=' + Date.now();
    const response = await fetch(url);
    
    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('📊 Datos de caja recibidos:', data);
    
    // CASO 1: No hay caja abierta - REDIRIGIR A APERTURA
    if (!data.success) {
        Swal.fire({
            title: 'Caja no abierta',
            text: 'No hay una caja abierta para hoy. Debes abrir caja primero.',
            icon: 'warning',
            confirmButtonColor: '#11998e',
            confirmButtonText: 'Ir a Apertura de Caja',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            window.location.href = './apertura_caja.php';
        });
        throw new Error('No hay caja abierta');
    }
    
    // CASO 2: Caja ya cerrada - REDIRIGIR A APERTURA
    if (data.estado === 'CERRADA') {
        Swal.fire({
            title: 'Caja cerrada',
            text: 'La caja ya fue cerrada. Si deseas continuar, abre una nueva caja.',
            icon: 'info',
            confirmButtonColor: '#11998e',
            confirmButtonText: 'Ir a Apertura de Caja',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then(() => {
            window.location.href = './apertura_caja.php';
        });
        throw new Error('Caja cerrada');
    }
    
    // Actualizar datos de ventas
    datosVentas.montoInicial = parseFloat(data.monto_inicial) || 0;
    datosVentas.totalVentas = parseFloat(data.total_ventas) || 0;
    datosVentas.numeroVentas = parseInt(data.numero_ventas) || 0;
    datosVentas.ventasEfectivo = parseFloat(data.ventas_efectivo) || 0;
    datosVentas.ventasTransferencia = parseFloat(data.ventas_transferencia) || 0;
    datosVentas.saldoAcumulado = parseFloat(data.saldo_acumulado) || 0;
    
    console.log('💰 Ventas cargadas:', {
        total: datosVentas.totalVentas,
        numero: datosVentas.numeroVentas,
        efectivo: datosVentas.ventasEfectivo,
        transferencia: datosVentas.ventasTransferencia
    });
    
    // Actualizar UI
    actualizarElemento('totalVentas', formatearMoneda(datosVentas.totalVentas));
    actualizarElemento('numeroVentas', datosVentas.numeroVentas);
    actualizarElemento('montoInicial', formatearMoneda(datosVentas.montoInicial));
    actualizarInput('totalTransferencias', datosVentas.ventasTransferencia.toFixed(2));
}

// ============================================
// GASTOS - CORREGIDO
// ============================================
async function cargarGastos() {
    const url = '/api/vendedor/obtener_gastos.php?_t=' + Date.now();
    const response = await fetch(url);
    
    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('💸 Gastos recibidos:', data);
    
    if (data.success) {
        gastosDelDia = data.gastos || [];
        datosVentas.gastos = parseFloat(data.total) || 0;
        
        console.log('💸 Total gastos:', datosVentas.gastos, 'Cantidad:', gastosDelDia.length);
        
        actualizarTablaGastos();
        actualizarInput('gastos', datosVentas.gastos.toFixed(2));
        
        const totalGastosDisplay = document.getElementById('totalGastosDisplay');
        if (totalGastosDisplay) {
            totalGastosDisplay.textContent = formatearMoneda(datosVentas.gastos);
        }
    }
}

async function registrarGasto() {
    try {
        const concepto = document.getElementById('conceptoGasto')?.value.trim();
        const monto = parseFloat(document.getElementById('montoGasto')?.value) || 0;
        const categoria = document.getElementById('categoriaGasto')?.value || 'General';
        const observaciones = document.getElementById('observacionesGasto')?.value.trim() || '';
        
        if (!concepto) {
            Swal.fire('Concepto obligatorio', 'Debes ingresar el concepto del gasto', 'warning');
            return;
        }
        
        if (monto <= 0) {
            Swal.fire('Monto inválido', 'El monto debe ser mayor a 0', 'warning');
            return;
        }
        
        mostrarSpinner(true);
        
        const response = await fetch('/api/vendedor/registrar_gasto.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ concepto, monto, categoria, observaciones })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: 'Gasto registrado',
                text: 'El gasto se registró correctamente',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Limpiar formulario
            document.getElementById('conceptoGasto').value = '';
            document.getElementById('montoGasto').value = '';
            document.getElementById('categoriaGasto').value = 'General';
            document.getElementById('observacionesGasto').value = '';
            
            // Recargar gastos y recalcular
            await cargarGastos();
            calcularTotales();
            
        } else {
            Swal.fire('Error', result.message || 'No se pudo registrar el gasto', 'error');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire('Error', 'No se pudo registrar el gasto', 'error');
    } finally {
        mostrarSpinner(false);
    }
}

function actualizarTablaGastos() {
    const tbody = document.getElementById('tablaGastos');
    if (!tbody) return;
    
    if (!gastosDelDia || gastosDelDia.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No hay gastos registrados</td></tr>';
        return;
    }
    
    tbody.innerHTML = gastosDelDia.map(gasto => `
        <tr>
            <td>${gasto.hora || '--:--'}</td>
            <td>${gasto.Concepto || '-'}</td>
            <td>${formatearMoneda(gasto.Monto)}</td>
            <td>${gasto.Categoria || 'General'}</td>
            <td>${gasto.Observaciones || '-'}</td>
        </tr>
    `).join('');
}

// ============================================
// RETIROS - CORREGIDO
// ============================================
async function cargarRetiros() {
    const url = '/api/vendedor/obtener_retiros.php?_t=' + Date.now();
    const response = await fetch(url);
    
    if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
    }
    
    const data = await response.json();
    console.log('🏦 Retiros recibidos:', data);
    
    if (data.success) {
        retirosDelDia = data.retiros || [];
        datosVentas.retiros = parseFloat(data.total) || 0;
        
        console.log('🏦 Total retiros:', datosVentas.retiros, 'Cantidad:', retirosDelDia.length);
        
        actualizarTablaRetiros();
        actualizarInput('retiros', datosVentas.retiros.toFixed(2));
        
        const totalRetirosDisplay = document.getElementById('totalRetirosDisplay');
        if (totalRetirosDisplay) {
            totalRetirosDisplay.textContent = formatearMoneda(datosVentas.retiros);
        }
    }
}

async function registrarRetiro() {
    try {
        const monto = parseFloat(document.getElementById('montoRetiro')?.value) || 0;
        const motivo = document.getElementById('motivoRetiro')?.value.trim();
        const observaciones = document.getElementById('observacionesRetiro')?.value.trim() || '';
        
        if (!motivo) {
            Swal.fire('Motivo obligatorio', 'Debes ingresar el motivo del retiro', 'warning');
            return;
        }
        
        if (monto <= 0) {
            Swal.fire('Monto inválido', 'El monto debe ser mayor a 0', 'warning');
            return;
        }
        
        mostrarSpinner(true);
        
        const response = await fetch('/api/vendedor/registrar_retiro.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ monto, motivo, observaciones })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                title: 'Retiro registrado',
                text: 'El retiro se registró correctamente',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            
            // Limpiar formulario
            document.getElementById('montoRetiro').value = '';
            document.getElementById('motivoRetiro').value = '';
            document.getElementById('observacionesRetiro').value = '';
            
            // Recargar retiros y recalcular
            await cargarRetiros();
            calcularTotales();
            
        } else {
            Swal.fire('Error', result.message || 'No se pudo registrar el retiro', 'error');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire('Error', 'No se pudo registrar el retiro', 'error');
    } finally {
        mostrarSpinner(false);
    }
}

function actualizarTablaRetiros() {
    const tbody = document.getElementById('tablaRetiros');
    if (!tbody) return;
    
    if (!retirosDelDia || retirosDelDia.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">No hay retiros registrados</td></tr>';
        return;
    }
    
    tbody.innerHTML = retirosDelDia.map(retiro => `
        <tr>
            <td>${retiro.hora || '--:--'}</td>
            <td>${formatearMoneda(retiro.Monto)}</td>
            <td>${retiro.Motivo || '-'}</td>
            <td>${retiro.Observaciones || '-'}</td>
        </tr>
    `).join('');
}

// ============================================
// DESGLOSE DE DENOMINACIONES
// ============================================
function calcularDenominacion(input) {
    const valor = parseFloat(input.dataset.valor) || 0;
    const cantidad = parseInt(input.value) || 0;
    const total = valor * cantidad;
    
    const denominacionItem = input.closest('.denominacion-item');
    if (denominacionItem) {
        const totalElement = denominacionItem.querySelector('.denominacion-total');
        if (totalElement) {
            totalElement.textContent = formatearMoneda(total);
        }
    }
}

function calcularTotalDesglose() {
    let totalDesglose = 0;
    
    const inputsDenominacion = document.querySelectorAll('.denominacion-input');
    inputsDenominacion.forEach(input => {
        const valor = parseFloat(input.dataset.valor) || 0;
        const cantidad = parseInt(input.value) || 0;
        totalDesglose += valor * cantidad;
    });
    
    actualizarElemento('totalDesglose', formatearMoneda(totalDesglose));
    actualizarInput('totalEfectivo', totalDesglose.toFixed(2));
    
    calcularTotales();
}

// ============================================
// CÁLCULOS TOTALES - CORREGIDO
// ============================================
function calcularTotales() {
    const totalEfectivo = parseFloat(document.getElementById('totalEfectivo')?.value) || 0;
    
    // Obtener valores actuales
    const gastos = datosVentas.gastos;
    const retiros = datosVentas.retiros;
    const montoInicial = datosVentas.montoInicial;
    const ventasEfectivo = datosVentas.ventasEfectivo;
    
    // ===================================
    // FÓRMULA CORRECTA DEL TOTAL ESPERADO
    // Total Esperado = Monto Inicial + Ventas Efectivo - Gastos - Retiros
    // ===================================
    const totalEsperado = montoInicial + ventasEfectivo - gastos - retiros;
    const totalContado = totalEfectivo;
    
    // ===================================
    // DIFERENCIA SIMPLE Y DIRECTA
    // Diferencia = Lo que contaste - Lo que deberías tener
    // Positivo = SOBRANTE (tienes más de lo esperado)
    // Negativo = FALTANTE (tienes menos de lo esperado)
    // ===================================
    const diferencia = totalContado - totalEsperado;
    
    // Determinar mensaje según la diferencia
    let mensajeDiferencia;
    if (Math.abs(diferencia) < 0.01) {
        mensajeDiferencia = '¡Cuadre exacto!';
    } else if (diferencia > 0) {
        mensajeDiferencia = `Sobrante de ${formatearMoneda(diferencia)}`;
    } else {
        mensajeDiferencia = `Faltante de ${formatearMoneda(Math.abs(diferencia))}`;
    }
    
    console.log('🧮 CÁLCULO TOTALES:', {
        montoInicial,
        ventasEfectivo,
        gastos,
        retiros,
        totalEsperado,
        totalContado,
        diferencia,
        mensaje: mensajeDiferencia
    });
    
    // Actualizar UI
    actualizarElemento('esperadoCaja', formatearMoneda(totalEsperado));
    actualizarResumen(totalEsperado, totalContado, diferencia);
    mostrarAlertaDiferencia(diferencia, mensajeDiferencia);
}

function actualizarResumen(totalEsperado, totalContado, diferencia) {
    actualizarElemento('resumenMontoInicial', formatearMoneda(datosVentas.montoInicial));
    actualizarElemento('resumenTotalVentas', formatearMoneda(datosVentas.totalVentas));
    actualizarElemento('resumenGastos', formatearMoneda(datosVentas.gastos));
    actualizarElemento('resumenRetiros', formatearMoneda(datosVentas.retiros));
    actualizarElemento('resumenEsperado', formatearMoneda(totalEsperado));
    actualizarElemento('resumenContado', formatearMoneda(totalContado));
    
    // Mostrar diferencia con signo
    const signo = diferencia >= 0 ? '+' : '-';
    actualizarElemento('resumenDiferencia', signo + formatearMoneda(Math.abs(diferencia)));
    
    // Colorear diferencia
    const elementoDiferencia = document.getElementById('resumenDiferencia');
    if (elementoDiferencia) {
        elementoDiferencia.classList.remove('text-success', 'text-danger', 'text-primary');
        if (Math.abs(diferencia) < 0.01) {
            elementoDiferencia.classList.add('text-primary');
        } else if (diferencia > 0) {
            elementoDiferencia.classList.add('text-success');
        } else {
            elementoDiferencia.classList.add('text-danger');
        }
    }
}

function mostrarAlertaDiferencia(diferencia, mensaje) {
    const alerta = document.getElementById('alertaDiferencia');
    const titulo = document.getElementById('tituloDiferencia');
    const texto = document.getElementById('textoDiferencia');
    
    if (!alerta || !titulo || !texto) return;
    
    alerta.classList.remove('positiva', 'negativa', 'exacta');
    
    if (Math.abs(diferencia) < 0.01) {
        alerta.classList.add('exacta');
        titulo.textContent = '¡Cuadre Exacto!';
    } else if (diferencia > 0) {
        alerta.classList.add('positiva');
        titulo.textContent = 'Sobrante';
    } else {
        alerta.classList.add('negativa');
        titulo.textContent = 'Faltante';
    }
    
    texto.textContent = mensaje;
    alerta.style.display = 'block';
}

// ============================================
// CIERRE DE CAJA - CORREGIDO
// ============================================
function mostrarOpcionesCierre() {
    const totalEfectivo = parseFloat(document.getElementById('totalEfectivo')?.value) || 0;
    const gastos = datosVentas.gastos;
    const retiros = datosVentas.retiros;
    const montoInicial = datosVentas.montoInicial;
    const ventasEfectivo = datosVentas.ventasEfectivo;
    
    // Calcular esperado y diferencia
    const totalEsperado = montoInicial + ventasEfectivo - gastos - retiros;
    const diferencia = totalEfectivo - totalEsperado;
    
    // Determinar estado
    let estadoTexto, estadoColor;
    if (Math.abs(diferencia) < 0.01) {
        estadoTexto = 'Cuadre exacto';
        estadoColor = '#28a745';
    } else if (diferencia > 0) {
        estadoTexto = `Sobrante: +${formatearMoneda(diferencia)}`;
        estadoColor = '#28a745';
    } else {
        estadoTexto = `Faltante: -${formatearMoneda(Math.abs(diferencia))}`;
        estadoColor = '#dc3545';
    }
    
    Swal.fire({
        title: '¿Cómo deseas cerrar la caja?',
        html: `
            <div style="padding: 20px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                            color: white; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
                    <div style="font-size: 1.3rem; margin-bottom: 15px;">📊 Resumen del Cierre</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; font-size: 0.95rem;">
                        <div>
                            <div style="opacity: 0.8;">Monto Inicial</div>
                            <div style="font-weight: bold;">${formatearMoneda(montoInicial)}</div>
                        </div>
                        <div>
                            <div style="opacity: 0.8;">Ventas Efectivo</div>
                            <div style="font-weight: bold;">${formatearMoneda(ventasEfectivo)}</div>
                        </div>
                        <div>
                            <div style="opacity: 0.8;">Gastos</div>
                            <div style="font-weight: bold;">-${formatearMoneda(gastos)}</div>
                        </div>
                        <div>
                            <div style="opacity: 0.8;">Retiros</div>
                            <div style="font-weight: bold;">-${formatearMoneda(retiros)}</div>
                        </div>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <div style="opacity: 0.8; font-size: 0.85rem;">Total Esperado</div>
                                <div style="font-size: 1.3rem; font-weight: bold;">${formatearMoneda(totalEsperado)}</div>
                            </div>
                            <div>
                                <div style="opacity: 0.8; font-size: 0.85rem;">Total Contado</div>
                                <div style="font-size: 1.3rem; font-weight: bold;">${formatearMoneda(totalEfectivo)}</div>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 15px; padding: 10px; background: rgba(255,255,255,0.2); border-radius: 8px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: bold; color: ${estadoColor === '#dc3545' ? '#ffcdd2' : '#c8e6c9'};">
                            ${estadoTexto}
                        </div>
                    </div>
                </div>
                
                <div style="text-align: left; margin-bottom: 20px;">
                    <h5 style="color: #333; margin-bottom: 15px;">Selecciona una opción:</h5>
                    
                    <div style="border: 2px solid #28a745; background: #d4edda; padding: 15px; 
                                border-radius: 8px; margin-bottom: 15px; cursor: pointer;"
                         onclick="document.getElementById('tipo_deposito').checked = true;">
                        <div style="display: flex; align-items: center;">
                            <input type="radio" id="tipo_deposito" name="tipo_cierre" value="DEPOSITO" 
                                   checked style="width: 20px; height: 20px; margin-right: 15px;">
                            <div>
                                <strong style="color: #28a745; font-size: 1.1rem;">
                                    🏦 Depositar en Banco
                                </strong>
                                <p style="margin: 5px 0 0 0; color: #155724; font-size: 0.9rem;">
                                    Retiras todo el efectivo. Próxima apertura: $0.00
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="border: 2px solid #17a2b8; background: #d1ecf1; padding: 15px; 
                                border-radius: 8px; cursor: pointer;"
                         onclick="document.getElementById('tipo_continuacion').checked = true;">
                        <div style="display: flex; align-items: center;">
                            <input type="radio" id="tipo_continuacion" name="tipo_cierre" value="CONTINUACION"
                                   style="width: 20px; height: 20px; margin-right: 15px;">
                            <div>
                                <strong style="color: #17a2b8; font-size: 1.1rem;">
                                    🔄 Continuar con Efectivo
                                </strong>
                                <p style="margin: 5px 0 0 0; color: #0c5460; font-size: 0.9rem;">
                                    Dejas el efectivo en caja. Próxima apertura: ${formatearMoneda(totalEfectivo)}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '🔒 Confirmar Cierre',
        cancelButtonText: '❌ Cancelar',
        reverseButtons: true,
        width: '600px',
        preConfirm: () => {
            const tipoCierre = document.querySelector('input[name="tipo_cierre"]:checked');
            if (!tipoCierre) {
                Swal.showValidationMessage('Debes seleccionar una opción');
                return false;
            }
            return tipoCierre.value;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            procesarCierreCaja(result.value);
        }
    });
}

async function procesarCierreCaja(tipoCierre) {
    try {
        mostrarSpinner(true);
        
        const totalEfectivo = parseFloat(document.getElementById('totalEfectivo')?.value) || 0;
        const observaciones = document.getElementById('observaciones')?.value.trim() || '';
        
        const datos = {
            totalContado: totalEfectivo,
            tipo_cierre: tipoCierre,
            observaciones: observaciones
        };
        
        console.log('📤 Enviando cierre:', datos);
        
        const response = await fetch('/api/vendedor/procesar_cierre_caja.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(datos)
        });
        
        const result = await response.json();
        console.log('✅ Resultado:', result);
        
        if (result.success) {
            let mensajeAdicional = tipoCierre === 'CONTINUACION' 
                ? `<p style="color: #17a2b8; margin-top: 15px;">
                    <strong>Nueva caja abierta</strong><br>
                    Monto inicial: ${formatearMoneda(totalEfectivo)}
                </p>`
                : `<p style="color: #28a745; margin-top: 15px;">
                    <strong>Efectivo listo para depositar</strong>
                </p>`;
            
            Swal.fire({
                title: '¡Caja cerrada exitosamente!',
                html: `
                    <div style="padding: 20px;">
                        <div style="font-size: 4rem; color: #28a745; margin-bottom: 20px;">✅</div>
                        <p>El cierre se registró correctamente.</p>
                        ${mensajeAdicional}
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#28a745',
                timer: 3000,
                timerProgressBar: true,
                allowOutsideClick: false
            }).then(() => {
                window.location.href = './vendedor.php';
            });
        } else {
            Swal.fire('Error', result.message || 'No se pudo procesar el cierre', 'error');
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire('Error de conexión', 'No se pudo conectar con el servidor', 'error');
    } finally {
        mostrarSpinner(false);
    }
}

// ============================================
// UTILIDADES
// ============================================
function actualizarElemento(id, valor) {
    const elemento = document.getElementById(id);
    if (elemento) {
        elemento.textContent = valor;
    }
}

function actualizarInput(id, valor) {
    const input = document.getElementById(id);
    if (input) {
        input.value = valor;
    }
}

function formatearMoneda(valor) {
    const numero = parseFloat(valor) || 0;
    return "$" + numero.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, "$&,");
}

function mostrarSpinner(mostrar) {
    const spinner = document.getElementById('spinnerOverlay');
    if (spinner) {
        spinner.style.display = mostrar ? 'flex' : 'none';
    }
}

console.log('✅ Sistema de cierre de caja cargado correctamente - v2.0');
