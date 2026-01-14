document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formProducto");
    const inputNombre = document.querySelector("input[name='Nombre_Producto']");
    const inputCodigo = document.getElementById("Codigo_Producto");
    const btnGenerar = document.getElementById("generarCodigoBtn");
    const categoriaSelect = document.getElementById("categoriaSelect");
    const descripcionSelect = document.getElementById("descripcionSelect");
    const fechaCaducidad = document.querySelector("input[name='Fecha_Caducidad']");
    const margenUtil = document.getElementById("margenUtil");
    const precioCompra = document.getElementById("precioCompra");
    const precioVenta = document.getElementById("precioVenta");
    const stock = document.querySelector("input[name='Stock_Producto']");

    let productoExisteGlobal = false;
    let verificacionEnCurso = false;

    // ===========================================
    // FUNCIÓN DE VERIFICACIÓN DE PRODUCTO
    // ===========================================
    async function verificarProducto(nombre) {
        // Limpiar cuando está vacío o muy corto
        if (nombre.trim().length < 3) {
            productoExisteGlobal = false;
            inputNombre.classList.remove("border-danger", "border-success", "campo-error");
            inputNombre.style.border = "";
            inputNombre.style.boxShadow = "";
            let msg = document.getElementById("errorProductoExiste");
            if (msg) msg.remove();
            return false;
        }

        verificacionEnCurso = true;

        try {
            const req = await fetch(
                "../../../controllers/Inventario/verificar_producto.php?nombre=" + 
                encodeURIComponent(nombre)
            );
            const data = await req.json();

            // SI EXISTE → MARCAR ROJO
            if (data.existe === true) {
                productoExisteGlobal = true;

                inputNombre.classList.remove("border-success");
                inputNombre.style.border = "";
                inputNombre.style.boxShadow = "";
                
                inputNombre.classList.add("border-danger", "campo-error");
                inputNombre.style.border = "2px solid #dc3545";
                inputNombre.style.boxShadow = "0 0 10px rgba(220, 53, 69, 0.3)";

                let msgError = document.getElementById("errorProductoExiste");
                if (!msgError) {
                    msgError = document.createElement("small");
                    msgError.id = "errorProductoExiste";
                    msgError.className = "text-danger fw-bold d-block mt-1";
                    msgError.innerHTML = `⚠️ Este producto ya existe (Código: <b>${data.codigo}</b>)`;
                    inputNombre.parentNode.appendChild(msgError);
                }

                verificacionEnCurso = false;
                return true;
            }

            // SI NO EXISTE → MARCAR VERDE
            productoExisteGlobal = false;
            
            inputNombre.classList.remove("border-danger", "campo-error");
            inputNombre.classList.add("border-success");
            inputNombre.style.border = "2px solid #198754";
            inputNombre.style.boxShadow = "0 0 8px rgba(25, 135, 84, 0.3)";

            let msgError = document.getElementById("errorProductoExiste");
            if (msgError) msgError.remove();

            verificacionEnCurso = false;
            return false;

        } catch (err) {
            console.error("Error verificando producto:", err);
            verificacionEnCurso = false;
            return false;
        }
    }

    // ===========================================
    // EVENTOS DE VERIFICACIÓN
    // ===========================================
    inputNombre.addEventListener("input", function() {
        verificarProducto(this.value.trim());
    });

    inputNombre.addEventListener("blur", function() {
        verificarProducto(this.value.trim());
    });

    // ===========================================
    // BOTÓN GENERAR CÓDIGO
    // ===========================================
    btnGenerar.addEventListener("click", async function() {
        const nombre = inputNombre.value.trim();

        if (nombre.length < 3) {
            Swal.fire({
                icon: "warning",
                title: "⚠️ Nombre Requerido",
                html: "<strong>Primero ingresa el nombre del producto (mínimo 3 caracteres)</strong>",
                confirmButtonText: "Entendido"
            });
            inputNombre.focus();
            return;
        }

        // Verificar si existe antes de generar código
        const existe = await verificarProducto(nombre);
        if (existe) {
            Swal.fire({
                icon: "error",
                title: "❌ Producto Duplicado",
                html: "Este producto ya existe en el inventario.<br>No se puede generar un código nuevo.",
                confirmButtonText: "Entendido"
            });
            return;
        }

        Swal.fire({
            title: 'Generando código...',
            html: '<div class="spinner-border text-primary" role="status"></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });

        fetch("../../../controllers/Inventario/generar_codigo.php?nombre=" + encodeURIComponent(nombre))
            .then(res => res.json())
            .then(data => {
                Swal.close();

                if (data.existe) {
                    productoExisteGlobal = true;
                    inputCodigo.value = "";
                    
                    inputNombre.classList.remove("border-success");
                    inputNombre.style.border = "";
                    inputNombre.classList.add("border-danger", "campo-error");
                    inputNombre.style.border = "2px solid #dc3545";

                    Swal.fire({
                        icon: "error",
                        title: "❌ Producto duplicado",
                        html: `El producto ya existe<br>Código: <b>${data.codigo}</b>`
                    });
                } else {
                    productoExisteGlobal = false;
                    inputCodigo.value = data.codigo;

                    inputNombre.classList.remove("border-danger", "campo-error");
                    inputNombre.classList.add("border-success");
                    inputNombre.style.border = "2px solid #198754";
                    inputNombre.style.boxShadow = "0 0 8px rgba(25, 135, 84, 0.3)";

                    Swal.fire({
                        icon: "success",
                        title: "✅ Código generado",
                        text: data.codigo,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            })
            .catch(err => {
                Swal.close();
                console.error("Error:", err);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "No se pudo generar el código"
                });
            });
    });

    // ===========================================
    // VALIDACIÓN COMPLETA DEL FORMULARIO
    // ===========================================
    function validarFormularioCompleto() {
        let errores = [];

        // Validar categoría
        if (!categoriaSelect.value) {
            errores.push("Debe seleccionar una categoría");
            categoriaSelect.classList.add("campo-error");
        } else {
            categoriaSelect.classList.remove("campo-error");
        }

        // Validar descripción
        if (!descripcionSelect.value || descripcionSelect.disabled) {
            errores.push("Debe seleccionar un tipo específico");
            descripcionSelect.classList.add("campo-error");
        } else {
            descripcionSelect.classList.remove("campo-error");
        }

        // Validar nombre
        if (inputNombre.value.trim().length < 3) {
            errores.push("El nombre del producto debe tener al menos 3 caracteres");
            inputNombre.classList.add("campo-error");
        } else {
            inputNombre.classList.remove("campo-error");
        }

        // Validar si producto ya existe
        if (productoExisteGlobal) {
            errores.push("Este producto ya existe en el inventario");
            inputNombre.classList.add("campo-error");
        }

        // Validar fecha de caducidad
        if (!fechaCaducidad.value) {
            errores.push("Debe ingresar la fecha de caducidad");
            fechaCaducidad.classList.add("campo-error");
        } else {
            fechaCaducidad.classList.remove("campo-error");
        }

        // Validar margen
        if (!margenUtil.value || parseFloat(margenUtil.value) <= 0) {
            errores.push("El margen de utilidad debe ser mayor a 0");
            margenUtil.classList.add("campo-error");
        } else {
            margenUtil.classList.remove("campo-error");
        }

        // Validar precio compra
        if (!precioCompra.value || parseFloat(precioCompra.value) <= 0) {
            errores.push("El precio de compra debe ser mayor a 0");
            precioCompra.classList.add("campo-error");
        } else {
            precioCompra.classList.remove("campo-error");
        }

        // Validar precio venta
        if (!precioVenta.value || parseFloat(precioVenta.value) <= 0) {
            errores.push("El precio de venta debe ser mayor a 0");
            precioVenta.classList.add("campo-error");
        } else {
            precioVenta.classList.remove("campo-error");
        }

        // Validar stock
        if (!stock.value || parseInt(stock.value) <= 0) {
            errores.push("El stock debe ser mayor a 0");
            stock.classList.add("campo-error");
        } else {
            stock.classList.remove("campo-error");
        }

        // Validar código
        if (!inputCodigo.value) {
            errores.push("Debe generar el código del producto");
            inputCodigo.classList.add("campo-error");
        } else {
            inputCodigo.classList.remove("campo-error");
        }

        return errores;
    }

    // ===========================================
    // INTERCEPTAR ENVÍO DEL FORMULARIO
    // ===========================================
    form.addEventListener("submit", async function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Mostrar cargando mientras se verifica
        if (verificacionEnCurso) {
            Swal.fire({
                title: 'Verificando...',
                html: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Esperar a que termine la verificación
            await new Promise(resolve => {
                const interval = setInterval(() => {
                    if (!verificacionEnCurso) {
                        clearInterval(interval);
                        Swal.close();
                        resolve();
                    }
                }, 100);
            });
        }

        // Verificar una última vez el nombre del producto
        const nombreActual = inputNombre.value.trim();
        if (nombreActual.length >= 3) {
            await verificarProducto(nombreActual);
        }

        // Validar todos los campos
        const errores = validarFormularioCompleto();

        if (errores.length > 0) {
            let listaErrores = "<ul style='text-align: left; padding-left: 20px;'>";
            errores.forEach(error => {
                listaErrores += `<li>${error}</li>`;
            });
            listaErrores += "</ul>";

            Swal.fire({
                icon: "warning",
                title: "⚠️ Formulario Incompleto",
                html: `<strong>Debe corregir los siguientes errores:</strong><br>${listaErrores}`,
                confirmButtonText: "Entendido",
                confirmButtonColor: "#dc3545"
            });

            return false;
        }

        // Si todo está correcto, enviar el formulario
        Swal.fire({
            title: 'Guardando producto...',
            html: '<div class="spinner-border text-primary" role="status"></div>',
            allowOutsideClick: false,
            showConfirmButton: false
        });

        form.submit();
    });

    // ===========================================
    // LIMPIAR ERRORES AL MODIFICAR CAMPOS
    // ===========================================
    [categoriaSelect, descripcionSelect, inputNombre, fechaCaducidad, 
     margenUtil, precioCompra, precioVenta, stock, inputCodigo].forEach(campo => {
        if (campo) {
            campo.addEventListener("input", function() {
                this.classList.remove("campo-error");
            });
            campo.addEventListener("change", function() {
                this.classList.remove("campo-error");
            });
        }
    });
});