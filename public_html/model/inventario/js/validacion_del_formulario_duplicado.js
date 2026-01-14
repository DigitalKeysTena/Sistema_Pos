document.addEventListener("DOMContentLoaded", () => {
    const inputNombre = document.querySelector("input[name='Nombre_Producto']");
    const inputCodigo = document.getElementById("Codigo_Producto");
    const btnGenerar = document.getElementById("generarCodigoBtn");

    let productoExisteGlobal = false;

    // -----------------------------
    // FUNCIÓN PRINCIPAL DE VERIFICACIÓN
    // -----------------------------
    async function verificarProducto(nombre) {

        // limpiar cuando está vacío
        if (nombre.trim().length < 3) {
            productoExisteGlobal = false;
            inputNombre.classList.remove("border-danger", "border-success", "campo-error");
            inputNombre.style.border = ""; // Limpiar estilo inline
            let msg = document.getElementById("errorProductoExiste");
            if (msg) msg.remove();
            return false;
        }

        try {
            const req = await fetch("../../../controllers/Inventario/verificar_producto.php?nombre=" + encodeURIComponent(nombre));
            const data = await req.json();

            // ------------------ SI EXISTE → MARCAR ROJO ------------------
            if (data.existe === true) {
                productoExisteGlobal = true;

                // Limpiar estilos previos
                inputNombre.classList.remove("border-success");
                inputNombre.style.border = ""; // Limpiar estilo inline
                
                // Aplicar estilo de error
                inputNombre.classList.add("border-danger", "campo-error");

                let msgError = document.getElementById("errorProductoExiste");
                if (!msgError) {
                    msgError = document.createElement("small");
                    msgError.id = "errorProductoExiste";
                    msgError.className = "text-danger fw-bold d-block mt-1";
                    msgError.innerHTML = `⚠️ Este producto ya existe (Código: <b>${data.codigo}</b>)`;
                    inputNombre.parentNode.appendChild(msgError);
                }

                return true;
            }

            // ------------------ SI NO EXISTE → MARCAR VERDE ------------------
            productoExisteGlobal = false;
            console.log("✅ OK: NO existe - aplicando borde verde");
            
            // Limpiar estilos de error
            inputNombre.classList.remove("border-danger", "campo-error");
            
            // Aplicar estilo de éxito con inline para mayor especificidad
            inputNombre.classList.add("border-success");
            inputNombre.style.border = "2px solid #198754";
            inputNombre.style.boxShadow = "0 0 8px rgba(25, 135, 84, 0.3)";

            // Remover mensaje de error si existe
            let msgError = document.getElementById("errorProductoExiste");
            if (msgError) msgError.remove();

            return false;

        } catch (err) {
            console.error("Error verificando producto:", err);
            return false;
        }
    }

    // VERIFICACIÓN AL ESCRIBIR
    inputNombre.addEventListener("input", function() {
        const nombre = this.value.trim();
        verificarProducto(nombre);
    });

    // VERIFICACIÓN AL SALIR DEL CAMPO
    inputNombre.addEventListener("blur", function() {
        const nombre = this.value.trim();
        verificarProducto(nombre);
    });

    // BOTÓN GENERAR CÓDIGO
    btnGenerar.addEventListener("click", async function() {
        const nombre = inputNombre.value.trim();

        if (nombre.length < 3) {
            Swal.fire({
                icon: "warning",
                title: "⚠️ Nombre Requerido",
                html: "<strong>Primero ingresa el nombre del producto</strong>",
                confirmButtonText: "Entendido"
            });
            inputNombre.focus();
            return;
        }

        const existe = await verificarProducto(nombre);
        if (existe) return;

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
                    
                    // Aplicar estilo de error
                    inputNombre.classList.remove("border-success");
                    inputNombre.style.border = "";
                    inputNombre.classList.add("border-danger", "campo-error");

                    Swal.fire({
                        icon: "error",
                        title: "❌ Producto duplicado",
                        html: `El producto ya existe<br>Código: <b>${data.codigo}</b>`
                    });
                } else {
                    productoExisteGlobal = false;
                    inputCodigo.value = data.codigo;

                    // Aplicar estilo de éxito
                    inputNombre.classList.remove("border-danger", "campo-error");
                    inputNombre.classList.add("border-success");
                    inputNombre.style.border = "2px solid #198754";
                    inputNombre.style.boxShadow = "0 0 8px rgba(25, 135, 84, 0.3)";

                    Swal.fire({
                        icon: "success",
                        title: "Código generado",
                        text: data.codigo,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
    });
});