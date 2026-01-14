document.addEventListener("DOMContentLoaded", function() {

/* ===================================================

   FUNCIÓN PARA GENERAR AUTOMÁTICAMENTE UN CÓDIGO EAN-13

   =================================================== */

function generarEAN13(base) {

    // Si ya viene con 13 dígitos → lo devolvemos

    if (/^\d{13}$/.test(base)) return base;



    // Si viene con 12 dígitos → generar dígito verificador

    if (/^\d{12}$/.test(base)) {

        let suma = 0;

        for (let i = 0; i < 12; i++) {

            let num = parseInt(base[i]);

            suma += (i % 2 === 0) ? num : num * 3;

        }

        let digito = (10 - (suma % 10)) % 10;

        return base + digito;

    }



    // Si viene vacío o incorrecto → crear 12 dígitos automáticos

    let auto = Date.now().toString().slice(-12);

    return generarEAN13(auto);

}





const modalEtiqueta = new bootstrap.Modal(document.getElementById('modalImprimirEtiqueta'));



const btnImprimirEtiqueta = document.getElementById('btnImprimirEtiqueta');

const btnAumentar = document.getElementById('btnAumentar');

const btnDisminuir = document.getElementById('btnDisminuir');

const inputCantidad = document.getElementById('cantidadEtiquetas');

const formProducto = document.getElementById('formProducto');



let datosProducto = { nombre: "", codigo: "", precio: 0, stock: 0 };



function actualizarCantidad(cambio) {

    let cantidad = parseInt(inputCantidad.value);

    cantidad += cambio;

    cantidad = Math.max(1, Math.min(cantidad, datosProducto.stock));

    inputCantidad.value = cantidad;

}



btnAumentar.addEventListener("click", () => actualizarCantidad(1));

btnDisminuir.addEventListener("click", () => actualizarCantidad(-1));



/* ===================================================

   FUNCIÓN MOSTRAR MODAL DE IMPRESIÓN

   =================================================== */

function mostrarModalImpresion(nombre, codigo, precio, stock) {



    // Convertir el código recibido en EAN-13 válido

    let codigoEAN = generarEAN13(codigo);



    datosProducto = { nombre, codigo: codigoEAN, precio, stock };



    document.getElementById('nombreEtiqueta').textContent = nombre.toUpperCase();

    document.getElementById('precioEtiqueta').textContent = parseFloat(precio).toFixed(2);

    document.getElementById('codigoTexto').textContent = codigoEAN;



    document.getElementById('stockDisponible').textContent = stock;

    document.getElementById('maxEtiquetas').textContent = stock;



    document.getElementById('infoNombreProducto').textContent = nombre;

    document.getElementById('infoCodigoProducto').textContent = codigoEAN;



    inputCantidad.value = 1;



    // Mostrar código de barras en modal

    JsBarcode("#codigoBarras", codigoEAN, {

        format: "EAN13",

        width: 1.8,

        height: 40,

        displayValue: true,

        background: "#ffffff"

    });



    modalEtiqueta.show();

}





/* ===================================================

   GUARDAR PRODUCTO EN SESSIONSTORAGE

   =================================================== */

if (formProducto) {

    formProducto.addEventListener('submit', function() {

        const nombre = document.getElementById('Nombre_Producto').value;



        // 👉 TOMAR EL VALOR DEL INPUT GENERADO AUTOMÁTICAMENTE

        const codigo = document.getElementById('Codigo_Barras').value;



        const precio = document.getElementById('precioVenta').value;

        const stock = document.querySelector('input[name="Stock_Producto"]').value;



        sessionStorage.setItem('productoGuardado', JSON.stringify({

            nombre, codigo, precio, stock

        }));

    });

}





/* ===================================================

   MOSTRAR PREGUNTA SI QUIERE IMPRIMIR

   =================================================== */

window.addEventListener("load", function() {



    const productoGuardado = sessionStorage.getItem('productoGuardado');

    if (!productoGuardado) return;



    const datos = JSON.parse(productoGuardado);

    sessionStorage.removeItem('productoGuardado');



    setTimeout(() => {

        Swal.fire({

            icon: "question",

            title: "¿Desea imprimir etiquetas?",

            html: `

            <p>Producto guardado correctamente</p>

            <div class="alert alert-info">

                <strong>Stock:</strong> ${datos.stock}

            </div>`,

            showCancelButton: true,

            confirmButtonText: "Sí, imprimir"

        }).then(r => {

            if (r.isConfirmed) {

                mostrarModalImpresion(datos.nombre, datos.codigo, datos.precio, datos.stock);

            }

        });



    }, 1200);

});





/* ===================================================

   BOTÓN IMPRIMIR ETIQUETAS

   =================================================== */

btnImprimirEtiqueta.addEventListener("click", function() {



    const cantidad = parseInt(inputCantidad.value);

    const area = document.getElementById('areaImpresion');

    const fondoBlanco = document.getElementById('fondoBlanco');



    fondoBlanco.classList.add('activo');



    area.innerHTML = "";

    area.style.display = 'block';

    area.style.position = 'absolute';

    area.style.left = '0';

    area.style.top = '0';



    // GENERAR BARRA EAN13 EN CANVAS PARA IMPRIMIR

    const canvas = document.createElement("canvas");

    JsBarcode(canvas, generarEAN13(datosProducto.codigo), {

        format: "EAN13",

        width: 1.8,

        height: 30,

        displayValue: true,

        background: "#ffffff",

        margin: 0

    });



    const barraIMG = canvas.toDataURL("image/png");



    // GENERAR TODAS LAS ETIQUETAS

    for (let i = 0; i < cantidad; i++) {



        const div = document.createElement("div");

        div.className = "etiqueta-impresion";



        div.innerHTML = `

            <div class="borde-interior"></div>



            <div class="contenido-etiqueta">



                <div>

                    <img src="../../../utils/img/logo.png" class="logo-etiqueta">

                    <div class="nombre-producto">${datosProducto.nombre}</div>

                </div>



                <div class="codigo-barras-container">

                    <img src="${barraIMG}" alt="Código de barras">

                </div>



                <div class="seccion-inferior">

                    

                    <div class="precio-box">

                        <div class="precio-valor">$${parseFloat(datosProducto.precio).toFixed(2)}</div>

                    </div>



                    <div class="codigo-info">

                        <div class="codigo-label">CÓDIGO</div>

                        <div class="codigo-valor">${datosProducto.codigo}</div>

                    </div>



                </div>



            </div>

        `;



        area.appendChild(div);

    }



    modalEtiqueta.hide();



    setTimeout(() => {

        window.print();

    }, 500);



    window.onafterprint = function() {

        fondoBlanco.classList.remove('activo');

        

        area.innerHTML = "";

        area.style.display = '';

        area.style.position = '';

        area.style.left = '';

        area.style.top = '';

    };

});



window.mostrarModalImpresion = mostrarModalImpresion;

});