// src/model/vendedor/js/factura.js

// ==========================================
// DATOS DE LA EMPRESA (CONFIGURA AQUÍ)
// ==========================================
const EMPRESA = {
    nombre: 'Chingu Market',
    direccion: 'san Jorge',
    telefono: '(+593) 968632274',
    email: 'digital.keys.tena@gmail.com',
    ruc: '1501090730001',
    logo: '../../../utils/img/logo.png'
};

/**
 * Generar HTML de la factura
 */
function generarHTMLFactura(datosFactura) {
    const {
        numero,
        fecha,
        vendedor,
        subtotal,
        descuento,
        iva,
        total,
        metodo_pago,
        efectivo_recibido,
        cambio,
        productos,
        notas
    } = datosFactura;
    
    // Formatear fecha
    const fechaObj = new Date(fecha);
    const fechaFormateada = fechaObj.toLocaleDateString('es-EC', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Generar filas de productos
    let filasProductos = '';
    productos.forEach(producto => {
        filasProductos += `
            <tr>
                <td class="producto-nombre">${producto.nombre}<br><small style="font-size: 8pt;">${producto.codigo}</small></td>
                <td class="cantidad">${producto.cantidad}</td>
                <td class="precio">$${parseFloat(producto.precio).toFixed(2)}</td>
                <td class="total">$${parseFloat(producto.subtotal).toFixed(2)}</td>
            </tr>
        `;
    });
    
    // HTML de la factura
    return `
        <div class="factura-ticket">
            
            <!-- Header -->
            <div class="factura-header">
                <img src="${EMPRESA.logo}" class="factura-logo" alt="Logo">
                <div class="factura-empresa">${EMPRESA.nombre}</div>
                <div class="factura-info">
                    ${EMPRESA.direccion}<br>
                    Tel: ${EMPRESA.telefono}<br>
                    Email: ${EMPRESA.email}<br>
                    RUC: ${EMPRESA.ruc}
                </div>
                <div class="factura-numero">
                    FACTURA N° ${numero}
                </div>
                <div class="factura-info">
                    ${fechaFormateada}
                </div>
            </div>
            
            <!-- Vendedor -->
            <div style="margin: 3mm 0; font-size: 9pt;">
                <strong>Vendedor:</strong> ${vendedor}
            </div>
            
            <!-- Productos -->
            <div class="factura-productos">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th class="cantidad">Cant.</th>
                            <th class="precio">Precio</th>
                            <th class="total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${filasProductos}
                    </tbody>
                </table>
            </div>
            
            <!-- Totales -->
            <div class="factura-totales">
                <table>
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="valor">$${parseFloat(subtotal).toFixed(2)}</td>
                    </tr>
                    ${descuento > 0 ? `
                    <tr>
                        <td class="label">Descuento:</td>
                        <td class="valor" style="color: #dc3545;">-$${parseFloat(descuento).toFixed(2)}</td>
                    </tr>
                    ` : ''}
                    <tr>
                        <td class="label">IVA (12%):</td>
                        <td class="valor">$${parseFloat(iva).toFixed(2)}</td>
                    </tr>
                    <tr class="factura-total-final">
                        <td class="label">TOTAL:</td>
                        <td class="valor">$${parseFloat(total).toFixed(2)}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Información de Pago -->
            <div class="factura-pago">
                <table style="width: 100%; font-size: 9pt;">
                    <tr>
                        <td><strong>Método de Pago:</strong></td>
                        <td style="text-align: right;">${metodo_pago}</td>
                    </tr>
                    ${metodo_pago === 'EFECTIVO' && efectivo_recibido ? `
                    <tr>
                        <td>Efectivo Recibido:</td>
                        <td style="text-align: right;">$${parseFloat(efectivo_recibido).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td><strong>Cambio:</strong></td>
                        <td style="text-align: right;"><strong>$${parseFloat(cambio).toFixed(2)}</strong></td>
                    </tr>
                    ` : ''}
                </table>
            </div>
            
            ${notas ? `
            <div style="margin: 3mm 0; font-size: 9pt; border-top: 1px dashed #000; padding-top: 2mm;">
                <strong>Notas:</strong> ${notas}
            </div>
            ` : ''}
            
            <!-- Footer -->
            <div class="factura-footer">
                <div class="factura-mensaje">
                    ¡Gracias por su compra!<br>
                    Vuelva pronto
                </div>
                <div style="margin-top: 3mm; font-size: 8pt;">
                    Este documento es una representación<br>
                    impresa de la factura electrónica
                </div>
            </div>
            
        </div>
    `;
}

/**
 * Mostrar vista previa de factura en modal
 */
function mostrarVistaPrevia(datosFactura) {
    const html = generarHTMLFactura(datosFactura);
    
    Swal.fire({
        title: '<i class="bi bi-receipt me-2"></i>Factura Generada',
        html: `
            <div style="max-height: 70vh; overflow-y: auto;">
                <div class="factura-preview">
                    ${html}
                </div>
            </div>
        `,
        width: '450px',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-printer me-2"></i>Imprimir Factura',
        cancelButtonText: 'Cerrar',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        customClass: {
            popup: 'factura-modal'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            imprimirFactura(datosFactura);
        }
    });
}

/**
 * Imprimir factura
 */
function imprimirFactura(datosFactura) {
    const area = document.getElementById('facturaImpresion');
    const fondoBlanco = document.getElementById('fondoBlancoFactura');
    
    // Mostrar fondo blanco
    fondoBlanco.classList.add('activo');
    
    // Generar HTML
    area.innerHTML = generarHTMLFactura(datosFactura);
    area.style.display = 'block';
    
    // Esperar un poco para que se renderice
    setTimeout(() => {
        window.print();
    }, 500);
    
    // Limpiar después de imprimir
    window.onafterprint = function() {
        fondoBlanco.classList.remove('activo');
        area.innerHTML = '';
        area.style.display = 'none';
    };
}

/**
 * Guardar factura como PDF (opcional - requiere library)
 */
function descargarFacturaPDF(datosFactura) {
    // Implementar con jsPDF o html2pdf si se necesita
    Swal.fire({
        icon: 'info',
        title: 'Próximamente',
        text: 'La descarga en PDF estará disponible pronto',
        confirmButtonColor: '#198754'
    });
}

console.log('✅ Sistema de facturación cargado correctamente');