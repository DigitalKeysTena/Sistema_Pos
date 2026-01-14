const catSelect = document.getElementById("categoriaSelect");
const descSelect = document.getElementById("descripcionSelect");
const msg = document.getElementById("mensaje");

catSelect.addEventListener("change", () => {
  const catId = catSelect.value;
  descSelect.innerHTML = "";
  msg.innerHTML = "";
  descSelect.disabled = true;

  if (!catId) {
    descSelect.innerHTML = "<option>Primero seleccione una categoría</option>";
    return;
  }

  descSelect.innerHTML = "<option>Cargando...</option>";
  msg.innerHTML = `<small class="text-info"><i class="bi bi-hourglass-split"></i> Cargando...</small>`;

  const url = `${location.pathname}?ajax=descripciones&cat=${catId}`;

  fetch(url)
    .then((r) => r.json())
    .then((data) => {
      if (data.error) throw new Error(data.error);

      if (!data.length) {
        descSelect.innerHTML = "<option>Sin descripciones</option>";
        msg.innerHTML = `<small class="text-warning"><i class="bi bi-exclamation-triangle"></i> No hay descripciones.</small>`;
        return;
      }

      descSelect.innerHTML =
        '<option value="">-- Seleccione una descripción --</option>';

      data.forEach((d) => {
        descSelect.innerHTML += `<option value="${d.Id_Descripcion_Categoria}">${d.Descrip_Categoria}</option>`;
      });

      descSelect.disabled = false;
      msg.innerHTML = `<small class="text-success"><i class="bi bi-check-circle"></i> ${data.length} disponibles.</small>`;
    })
    .catch((err) => {
      descSelect.innerHTML = "<option>Error</option>";
      msg.innerHTML = `<small class="text-danger"><i class="bi bi-x-circle"></i> ${err.message}</small>`;
    });
});
