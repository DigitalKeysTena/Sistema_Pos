document.addEventListener("DOMContentLoaded", () => {
    const inputNombre = document.querySelector("input[name='Nombre_Producto']");

    let productoExisteGlobal = false;

    async function verificarProducto(nombre) {

        // LIMPIAR SI NO ESCRIBE NADA
        if (nombre.trim().length < 2) {
            productoExisteGlobal = false;
            inputNombre.classList.remove("is-valid", "is-invalid");
            let msg = document.getElementById("errorProductoExiste");
            if (msg) msg.remove();
            return;
        }

        try {
            const req = await fetch(
                "../../../controllers/Inventario/verificar_producto.php?nombre=" + 
                encodeURIComponent(nombre)
            );
            const data = await req.json();

            // -------------------------------
            // SI EL PRODUCTO YA EXISTE → ROJO
            // -------------------------------
            if (data.existe === true) {

                productoExisteGlobal = true;

                inputNombre.classList.remove("is-valid");
                inputNombre.classList.add("is-invalid");

                let msg = document.getElementById("errorProductoExiste");
                if (!msg) {
                    msg = document.createElement("div");
                    msg.id = "errorProductoExiste";
                    msg.className = "invalid-feedback d-block fw-bold";
                    msg.innerHTML = `⚠️ Ya existe (Código: <b>${data.codigo}</b>)`;
                    inputNombre.parentNode.appendChild(msg);
                }

                return;
            }

            // -------------------------------
            // SI NO EXISTE → VERDE
            // -------------------------------
            productoExisteGlobal = false;

            inputNombre.classList.remove("is-invalid");
            inputNombre.classList.add("is-valid");

            let msg = document.getElementById("errorProductoExiste");
            if (msg) msg.remove();

        } catch (e) {
            console.error("Error:", e);
        }
    }

    // Eventos
    inputNombre.addEventListener("input", () => verificarProducto(inputNombre.value));
    inputNombre.addEventListener("blur", () => verificarProducto(inputNombre.value));
});
