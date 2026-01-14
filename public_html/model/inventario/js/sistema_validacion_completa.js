// ==========================================
// SISTEMA COMPLETO DE VALIDACIÓN Y CÓDIGOS
// Versión CORREGIDA - Sin duplicados ni race conditions
// ==========================================

console.log("🔥 Sistema de validación iniciado");

// ==========================================
// VARIABLES GLOBALES
// ==========================================
let PRODUCTO_EXISTE = false;
let ultimaVerificacion = "";
let CODIGOS_GENERADOS = false;
let CODIGO_BARRAS_VALIDO = false;
let verificandoProducto = false; // 🆕 Prevenir race conditions
let verificandoCodigoBarras = false;

// Referencias a elementos del DOM
const form = document.getElementById("formProducto");
const inputNombre = document.querySelector("input[name='Nombre_Producto']");
const inputCodigoProducto = document.getElementById("Codigo_Producto");
const inputCodigoBarras = document.getElementById("Codigo_Barras");
const btnGenerar = document.getElementById("generarCodigoBtn");
const btnGuardar = document.getElementById("btnGuardarProducto");

const categoriaSelect = document.getElementById("categoriaSelect");
const descripcionSelect = document.getElementById("descripcionSelect");
const fechaCaducidad = document.querySelector("input[name='Fecha_Caducidad']");
const margenUtil = document.getElementById("margenUtil");
const precioCompra = document.getElementById("precioCompra");
const precioVenta = document.getElementById("precioVenta");
const stockProducto = document.querySelector("input[name='Stock_Producto']");

// ==========================================
// VALIDACIÓN DE MARGEN DE UTILIDAD (0-99)
// ==========================================
function validarMargen(event) {
    const key = event.key;
    const input = event.target;
    const valorActual = input.value;
    
    const teclasPermitidas = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
    
    if (teclasPermitidas.includes(key)) {
        return true;
    }
    
    // 🆕 BLOQUEAR símbolos negativos desde el inicio
    if (key === 'e' || key === 'E' || key === '+' || key === '-' || /[a-zA-Z]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    if (key === '.') {
        if (valorActual.includes('.')) {
            event.preventDefault();
            return false;
        }
        return true;
    }
    
    if (!/[0-9]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

function limitarMargen(input) {
    let valor = input.value;
    
    // 🆕 Eliminar cualquier signo negativo
    valor = valor.replace(/[^0-9.]/g, '');
    
    const partes = valor.split('.');
    if (partes.length > 2) {
        valor = partes[0] + '.' + partes.slice(1).join('');
    }
    
    if (partes.length === 2 && partes[1].length > 2) {
        valor = partes[0] + '.' + partes[1].substring(0, 2);
    }
    
    const numero = parseFloat(valor);
    
    if (!isNaN(numero) && numero > 99) {
        valor = '99';
        input.style.border = '2px solid #ffc107';
        input.style.background = '#fff3cd';
        setTimeout(() => {
            input.style.border = '';
            input.style.background = '';
        }, 1000);
    }
    
    input.value = valor;
    actualizarProgreso();
}

// ==========================================
// VALIDACIÓN DE PRECIOS
// ==========================================
function validarPrecio(event) {
    const key = event.key;
    const input = event.target;
    const valorActual = input.value;
    
    const teclasPermitidas = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
    
    if (teclasPermitidas.includes(key)) {
        return true;
    }
    
    // 🆕 BLOQUEAR símbolos negativos
    if (key === 'e' || key === 'E' || key === '+' || key === '-' || /[a-zA-Z]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    if (key === '.') {
        if (valorActual.includes('.')) {
            event.preventDefault();
            return false;
        }
        return true;
    }
    
    if (!/[0-9]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

function limitarPrecio(input) {
    let valor = input.value;
    
    // 🆕 Eliminar signos negativos
    valor = valor.replace(/[^0-9.]/g, '');
    
    const partes = valor.split('.');
    if (partes.length > 2) {
        valor = partes[0] + '.' + partes.slice(1).join('');
    }
    
    if (partes.length === 2 && partes[1].length > 2) {
        valor = partes[0] + '.' + partes[1].substring(0, 2);
    }
    
    input.value = valor;
    actualizarProgreso();
}

// ==========================================
// VALIDACIÓN DE STOCK
// ==========================================
function validarStock(event) {
    const key = event.key;
    
    const teclasPermitidas = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
    
    if (teclasPermitidas.includes(key)) {
        return true;
    }
    
    // 🆕 BLOQUEAR todo excepto números
    if (key === 'e' || key === 'E' || key === '+' || key === '-' || key === '.' || /[a-zA-Z]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    if (!/[0-9]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

function limitarStock(input) {
    let valor = input.value;
    
    valor = valor.replace(/[^0-9]/g, '');
    
    if (valor.length > 1 && valor.startsWith('0')) {
        valor = valor.replace(/^0+/, '');
    }
    
    input.value = valor;
    actualizarProgreso();
}

// ==========================================
// VALIDACIÓN DE CÓDIGO DE BARRAS EAN-13
// ==========================================
function soloNumeros(event) {
    const key = event.key;
    const teclasPermitidas = ['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
    
    if (teclasPermitidas.includes(key)) {
        return true;
    }
    
    if (!/[0-9]/.test(key)) {
        event.preventDefault();
        return false;
    }
    
    return true;
}

function validarEAN13(input) {
    let valor = input.value;
    const mensajeDiv = document.getElementById('mensajeBarras');
    
    valor = valor.replace(/[^0-9]/g, '');
    input.value = valor;
    
    if (valor.length === 0) {
        CODIGO_BARRAS_VALIDO = false;
        input.classList.remove('valido', 'invalido');
        mensajeDiv.innerHTML = '';
        actualizarEstadoBotones();
        return;
    }
    
    if (valor.length < 13) {
        CODIGO_BARRAS_VALIDO = false;
        input.classList.remove('valido');
        input.classList.add('invalido');
        mensajeDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Faltan ${13 - valor.length} dígito(s)
            </div>
        `;
        actualizarEstadoBotones();
        return;
    }
    
    if (valor.length === 13) {
        if (!validarChecksumEAN13(valor)) {
            CODIGO_BARRAS_VALIDO = false;
            input.classList.remove('valido');
            input.classList.add('invalido');
            mensajeDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    Código EAN-13 inválido (checksum incorrecto)
                </div>
            `;
            actualizarEstadoBotones();
            return;
        }
        
        verificarCodigoBarrasEnBD(valor, input, mensajeDiv);
        return;
    }
    
    if (valor.length > 13) {
        input.value = valor.substring(0, 13);
        validarEAN13(input);
    }
}

// ==========================================
// VERIFICAR SI EL CÓDIGO DE BARRAS YA EXISTE
// ==========================================
async function verificarCodigoBarrasEnBD(codigoBarras, input, mensajeDiv) {
    if (verificandoCodigoBarras) return;
    
    verificandoCodigoBarras = true;
    
    mensajeDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="bi bi-hourglass-split me-2"></i>
            Verificando disponibilidad...
        </div>
    `;
    
    try {
        const url = `/api/inventario/verificar_producto.php?codigo_barras=${encodeURIComponent(codigoBarras)}&_=${Date.now()}`;
        const res = await fetch(url);
        const data = await res.json();
        
        if (data.existe === true && data.tipo === 'codigo_barras') {
            CODIGO_BARRAS_VALIDO = false;
            input.classList.remove('valido');
            input.classList.add('invalido');
            mensajeDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle-fill me-2"></i>
                    <strong>Código ya registrado</strong><br>
                    <small>Producto: ${data.nombre}</small>
                </div>
            `;
        } else {
            CODIGO_BARRAS_VALIDO = true;
            input.classList.remove('invalido');
            input.classList.add('valido');
            mensajeDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Código EAN-13 válido y disponible
                </div>
            `;
        }
        
        actualizarEstadoBotones();
        
    } catch (error) {
        console.error("Error verificando código de barras:", error);
        mensajeDiv.innerHTML = `
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                No se pudo verificar. Intenta de nuevo.
            </div>
        `;
        CODIGO_BARRAS_VALIDO = false;
        actualizarEstadoBotones();
    } finally {
        verificandoCodigoBarras = false;
    }
}

function validarChecksumEAN13(codigo) {
    if (codigo.length !== 13) return false;
    
    let suma = 0;
    for (let i = 0; i < 12; i++) {
        const digito = parseInt(codigo[i]);
        suma += (i % 2 === 0) ? digito : digito * 3;
    }
    
    const checksum = (10 - (suma % 10)) % 10;
    return checksum === parseInt(codigo[12]);
}

function generarEAN13() {
    let codigo = '890';
    for (let i = 0; i < 9; i++) {
        codigo += Math.floor(Math.random() * 10);
    }
    
    let suma = 0;
    for (let i = 0; i < 12; i++) {
        const digito = parseInt(codigo[i]);
        suma += (i % 2 === 0) ? digito : digito * 3;
    }
    
    const checksum = (10 - (suma % 10)) % 10;
    codigo += checksum;
    
    return codigo;
}

// 🆕 GENERAR CÓDIGO DE BARRAS ÚNICO (CON VERIFICACIÓN)
async function generarCodigoBarrasUnico() {
    let intentos = 0;
    const maxIntentos = 10;
    
    while (intentos < maxIntentos) {
        const codigo = generarEAN13();
        
        try {
            const url = `/api/inventario/verificar_producto.php?codigo_barras=${codigo}&_=${Date.now()}`;
            const res = await fetch(url);
            const data = await res.json();
            
            if (!data.existe) {
                return codigo;
            }
            
            intentos++;
        } catch (error) {
            console.error("Error verificando código:", error);
            intentos++;
        }
    }
    
    return null;
}

// ==========================================
// PROGRESO Y ESTADO DE BOTONES
// ==========================================
function actualizarProgreso() {
    const pasos = document.querySelectorAll('.progress-step');
    
    const paso1 = categoriaSelect?.value && descripcionSelect?.value;
    pasos[0].classList.toggle('completed', paso1);
    pasos[0].classList.toggle('active', !paso1);
    
    const paso2 = inputNombre?.value.trim().length >= 3 && 
                  fechaCaducidad?.value && 
                  stockProducto?.value && parseInt(stockProducto.value) > 0;
    pasos[1].classList.toggle('completed', paso2);
    pasos[1].classList.toggle('active', paso1 && !paso2);
    
    const paso3 = precioCompra?.value && parseFloat(precioCompra.value) > 0 &&
                  margenUtil?.value && parseFloat(margenUtil.value) > 0;
    pasos[2].classList.toggle('completed', paso3);
    pasos[2].classList.toggle('active', paso2 && !paso3);
    
    const paso4 = CODIGOS_GENERADOS && CODIGO_BARRAS_VALIDO;
    pasos[3].classList.toggle('completed', paso4);
    pasos[3].classList.toggle('active', paso3 && !paso4);
}

function nombreValido() {
    const nombre = inputNombre.value.trim();
    return nombre.length >= 3 && !PRODUCTO_EXISTE;
}

function todosLosCamposLlenos() {
    const margenValor = parseFloat(margenUtil?.value) || 0;
    
    const campos = {
        categoria: categoriaSelect?.value,
        descripcion: descripcionSelect?.value,
        nombre: inputNombre?.value.trim().length >= 3,
        fechaCaducidad: fechaCaducidad?.value,
        margenUtil: margenUtil?.value && margenValor > 0 && margenValor <= 99,
        precioCompra: precioCompra?.value && parseFloat(precioCompra.value) > 0,
        precioVenta: precioVenta?.value && parseFloat(precioVenta.value) > 0,
        stock: stockProducto?.value && parseInt(stockProducto.value) > 0,
        codigoInterno: inputCodigoProducto?.value,
        codigoBarras: CODIGO_BARRAS_VALIDO
    };
    
    return Object.values(campos).every(valor => valor);
}

function actualizarBotonGenerar() {
    if (nombreValido() && !CODIGOS_GENERADOS) {
        btnGenerar.disabled = false;
        btnGenerar.classList.add('btn-enabled-animation');
    } else {
        btnGenerar.disabled = true;
        btnGenerar.classList.remove('btn-enabled-animation');
    }
}

function actualizarBotonGuardar() {
    if (todosLosCamposLlenos() && !PRODUCTO_EXISTE && CODIGOS_GENERADOS) {
        btnGuardar.disabled = false;
        btnGuardar.classList.add('btn-enabled-animation');
        btnGenerar.disabled = true;
    } else {
        btnGuardar.disabled = true;
        btnGuardar.classList.remove('btn-enabled-animation');
    }
}

function actualizarEstadoBotones() {
    actualizarBotonGenerar();
    actualizarBotonGuardar();
    actualizarProgreso();
}

// ==========================================
// VERIFICACIÓN DE PRODUCTO EXISTENTE
// ==========================================
async function verificarProducto(nombre) {
    nombre = nombre.trim();
    
    if (nombre.length < 3) {
        PRODUCTO_EXISTE = false;
        limpiarEstilosInput();
        actualizarEstadoBotones();
        return;
    }
    
    if (nombre === ultimaVerificacion) {
        return;
    }
    
    // 🆕 Prevenir verificaciones simultáneas
    if (verificandoProducto) {
        return;
    }
    
    verificandoProducto = true;
    ultimaVerificacion = nombre;
    
    try {
        const url = `/api/inventario/verificar_producto.php?nombre=${encodeURIComponent(nombre)}&_=${Date.now()}`;
        const res = await fetch(url);
        const data = await res.json();
        
        if (data.existe === true) {
            PRODUCTO_EXISTE = true;
            mostrarError(data.codigo, data.nombre);
        } else {
            PRODUCTO_EXISTE = false;
            mostrarExito();
        }
        
        actualizarEstadoBotones();
        
    } catch (error) {
        console.error("❌ Error en verificación:", error);
        PRODUCTO_EXISTE = false;
        limpiarEstilosInput();
        actualizarEstadoBotones();
    } finally {
        verificandoProducto = false;
    }
}

function mostrarError(codigo, nombre) {
    inputNombre.style.border = "3px solid #dc3545";
    inputNombre.style.boxShadow = "0 0 15px rgba(220, 53, 69, 0.5)";
    inputNombre.style.backgroundColor = "#ffe6e6";
    
    let msg = document.getElementById("errorProducto");
    if (msg) msg.remove();
    
    msg = document.createElement("div");
    msg.id = "errorProducto";
    msg.style.cssText = `
        color: #dc3545;
        font-weight: bold;
        margin-top: 8px;
        padding: 12px 15px;
        background: linear-gradient(135deg, #ffe6e6 0%, #ffcccc 100%);
        border-left: 4px solid #dc3545;
        border-radius: 8px;
        font-size: 0.9rem;
    `;
    msg.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>PRODUCTO YA EXISTE</strong><br><small>Nombre: ${nombre || 'N/A'} | Código: <strong>${codigo}</strong></small>`;
    inputNombre.parentNode.appendChild(msg);
}

function mostrarExito() {
    inputNombre.style.border = "3px solid #198754";
    inputNombre.style.boxShadow = "0 0 15px rgba(25, 135, 84, 0.3)";
    inputNombre.style.backgroundColor = "#e6ffe6";
    
    let msg = document.getElementById("errorProducto");
    if (msg) msg.remove();
}

function limpiarEstilosInput() {
    inputNombre.style.border = "";
    inputNombre.style.boxShadow = "";
    inputNombre.style.backgroundColor = "";
    
    let msg = document.getElementById("errorProducto");
    if (msg) msg.remove();
}

// ==========================================
// EVENT LISTENERS
// ==========================================

// 🆕 BOTÓN GENERAR CÓDIGO DE BARRAS (UN SOLO LISTENER)
document.getElementById('btnGenerarBarras').addEventListener('click', async function() {
    const inputBarras = document.getElementById('Codigo_Barras');
    
    if (inputBarras.value.length > 0) {
        const result = await Swal.fire({
            title: '¿Generar nuevo código?',
            text: 'Ya existe un código. ¿Deseas reemplazarlo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, generar nuevo',
            cancelButtonText: 'Cancelar'
        });
        
        if (!result.isConfirmed) return;
    }
    
    Swal.fire({
        title: 'Generando código único...',
        html: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    const nuevoEAN = await generarCodigoBarrasUnico();
    
    Swal.close();
    
    if (nuevoEAN) {
        inputBarras.value = nuevoEAN;
        validarEAN13(inputBarras);
        
        Swal.fire({
            icon: 'success',
            title: 'Código Generado',
            html: `<div style="font-family: monospace; font-size: 1.5rem; font-weight: bold; color: #ffc107; letter-spacing: 2px;">${nuevoEAN}</div>`,
            confirmButtonColor: '#ffc107',
            timer: 3000
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo generar un código único. Intenta de nuevo.',
            confirmButtonColor: '#dc3545'
        });
    }
});

// BOTÓN GENERAR CÓDIGO INTERNO
btnGenerar.addEventListener("click", async function() {
    const nombre = inputNombre.value.trim();
    
    if (nombre.length < 3) {
        Swal.fire({
            icon: "warning",
            title: "Campo Requerido",
            text: "Ingresa el nombre del producto (mínimo 3 caracteres)",
            confirmButtonColor: "#667eea"
        });
        return;
    }
    
    await verificarProducto(nombre);
    
    if (PRODUCTO_EXISTE) {
        Swal.fire({
            icon: "error",
            title: "⛔ Producto Duplicado",
            html: "<strong>Este producto ya existe en el inventario.</strong>",
            confirmButtonColor: "#dc3545"
        });
        return;
    }
    
    Swal.fire({
        title: '<i class="bi bi-magic text-primary"></i> Generando código interno...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });
    
    try {
        const url = `/api/inventario/generar_codigo.php?nombre=${encodeURIComponent(nombre)}&_=${Date.now()}`;
        const res = await fetch(url);
        const data = await res.json();
        
        Swal.close();
        
        if (data.existe) {
            PRODUCTO_EXISTE = true;
            Swal.fire({
                icon: "error",
                title: "Producto Ya Existe",
                html: `Código: <strong>${data.codigo}</strong>`,
                confirmButtonColor: "#dc3545"
            });
        } else if (data.success) {
            inputCodigoProducto.value = data.codigo;
            CODIGOS_GENERADOS = true;
            
            inputCodigoProducto.style.border = "2px solid #198754";
            inputCodigoProducto.style.background = "#e6ffe6";
            
            setTimeout(() => {
                inputCodigoProducto.style.border = "";
                inputCodigoProducto.style.background = "#fff3e0";
            }, 2000);
            
            actualizarEstadoBotones();
            
            Swal.fire({
                icon: "success",
                title: "✅ Código Interno Generado",
                html: `
                    <div style="padding: 20px; background: #f8f9fa; border-radius: 15px;">
                        <div style="font-family: monospace; font-size: 1.3rem; font-weight: bold; color: #667eea;">${data.codigo}</div>
                    </div>
                    <p class="mt-3 text-muted"><i class="bi bi-info-circle me-2"></i>Ahora puedes ingresar o generar el código de barras</p>
                `,
                confirmButtonColor: "#11998e",
                timer: 4000
            });
        }
    } catch (error) {
        Swal.close();
        console.error("Error:", error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo generar el código",
            confirmButtonColor: "#dc3545"
        });
    }
});

// 🆕 INPUT NOMBRE - Verificación con debounce mejorado
let timeoutVerificacion;
inputNombre.addEventListener("input", function() {
    // Resetear códigos si se modifica el nombre
    if (inputCodigoProducto.value || inputCodigoBarras.value) {
        inputCodigoProducto.value = "";
        inputCodigoBarras.value = "";
        CODIGOS_GENERADOS = false;
        CODIGO_BARRAS_VALIDO = false;
        document.getElementById('mensajeBarras').innerHTML = '';
    }
    
    // Cancelar verificación anterior
    clearTimeout(timeoutVerificacion);
    
    // Nueva verificación con delay
    timeoutVerificacion = setTimeout(async () => {
        await verificarProducto(this.value);
    }, 700);
    
    actualizarEstadoBotones();
});

// Observar cambios en campos
const camposObservados = [
    categoriaSelect,
    descripcionSelect,
    fechaCaducidad,
    margenUtil,
    precioCompra,
    stockProducto
];

camposObservados.forEach(campo => {
    if (campo) {
        campo.addEventListener('change', actualizarEstadoBotones);
        campo.addEventListener('input', actualizarEstadoBotones);
    }
});

// Observer precio de venta
const observerPrecioVenta = new MutationObserver(actualizarEstadoBotones);
if (precioVenta) {
    observerPrecioVenta.observe(precioVenta, { attributes: true, attributeFilter: ['value'] });
    precioVenta.addEventListener('change', actualizarEstadoBotones);
    
    setInterval(() => {
        if (precioVenta.value) {
            actualizarEstadoBotones();
        }
    }, 500);
}

// ==========================================
// SUBMIT DEL FORMULARIO
// ==========================================
form.addEventListener("submit", async function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const nombreActual = inputNombre.value.trim();
    if (nombreActual.length >= 3) {
        await verificarProducto(nombreActual);
        await new Promise(resolve => setTimeout(resolve, 300));
    }
    
    if (PRODUCTO_EXISTE) {
        Swal.fire({
            icon: "error",
            title: "⛔ NO SE PUEDE CREAR",
            html: "<strong>Este producto ya existe en el inventario.</strong>",
            confirmButtonColor: "#dc3545"
        });
        return false;
    }
    
    if (!CODIGO_BARRAS_VALIDO) {
        Swal.fire({
            icon: "warning",
            title: "Código de Barras Inválido",
            html: "Debes ingresar un código EAN-13 válido de 13 dígitos o generarlo automáticamente.",
            confirmButtonColor: "#ffc107"
        });
        inputCodigoBarras.focus();
        return false;
    }
    
    const errores = [];
    
    if (!categoriaSelect?.value) errores.push("Categoría");
    if (!descripcionSelect?.value) errores.push("Tipo Específico");
    if (nombreActual.length < 3) errores.push("Nombre del Producto");
    if (!fechaCaducidad?.value) errores.push("Fecha de Caducidad");
    if (!margenUtil?.value || parseFloat(margenUtil.value) <= 0 || parseFloat(margenUtil.value) > 99) 
        errores.push("Margen de Utilidad (0.01 - 99)");
    if (!precioCompra?.value || parseFloat(precioCompra.value) <= 0) errores.push("Precio de Compra");
    if (!precioVenta?.value || parseFloat(precioVenta.value) <= 0) errores.push("Precio de Venta");
    if (!stockProducto?.value || parseInt(stockProducto.value) <= 0) errores.push("Stock");
    if (!inputCodigoProducto.value) errores.push("Código Interno");
    
    if (errores.length > 0) {
        Swal.fire({
            icon: "warning",
            title: "⚠️ Campos Incompletos",
            html: `<ul style="text-align:left;">${errores.map(e => `<li>${e}</li>`).join('')}</ul>`,
            confirmButtonColor: "#ffc107"
        });
        return false;
    }
    
    Swal.fire({
        title: '<i class="bi bi-box-seam text-primary"></i> Resumen del Producto',
        html: `
            <div style="text-align: left; padding: 20px; background: #f8f9fa; border-radius: 15px;">
                <p><strong><i class="bi bi-tag me-2"></i>Nombre:</strong> ${nombreActual}</p>
                <p><strong><i class="bi bi-qr-code me-2"></i>Código:</strong> ${inputCodigoProducto.value}</p>
                <p><strong><i class="bi bi-upc me-2"></i>Barras:</strong> ${inputCodigoBarras.value}</p>
                <hr>
                <p><strong><i class="bi bi-cash me-2"></i>Precio Venta:</strong> ${precioVenta.value}</p>
                <p><strong><i class="bi bi-stack me-2"></i>Stock:</strong> ${stockProducto.value} unidades</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#11998e',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-check-circle me-2"></i>Guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Guardando producto...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            form.submit();
        }
    });
    
    return false;
});

// ==========================================
// INICIALIZACIÓN
// ==========================================
console.log("✅ Sistema de validación LISTO");
actualizarEstadoBotones();