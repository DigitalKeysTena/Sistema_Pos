
document.addEventListener("DOMContentLoaded", () => {
    const margen = document.getElementById("margenUtil");
    const costo = document.getElementById("precioCompra");
    const venta = document.getElementById("precioVenta");

    function calcularPrecioVenta() {
        let C = parseFloat(costo.value) || 0;
        let M = parseFloat(margen.value) || 0;

        if (C > 0 && M >= 0 && M < 100) {
            let P = C / (1 - (M / 100));
            venta.value = P.toFixed(2);
        } else {
            venta.value = "";
        }
    }

    margen.addEventListener("input", calcularPrecioVenta);
    costo.addEventListener("input", calcularPrecioVenta);
});
