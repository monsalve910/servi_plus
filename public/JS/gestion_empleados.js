//agregar empleado
let btnCrear = document.querySelector("#btnAgregar");
btnCrear.addEventListener("click", () => {
  Swal.fire({
    title: '<span class="text-success fw-bold">Crear usuario</span>',
    html: `
    <form action="" method="post" id="formAgregarEmpleado">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre Empleado</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>

        <label for="documento" class="form-label">Documento</label>
        <input type="text" class="form-control mb-3" id="documento" name="documento" required>

        <label for="cargo" class="form-label">Cargo</label>
        <select class="form-select" id="cargo" name="cargo" required>
            <option value="1">Técnico</option>
            <option value="2">Administrador</option>
            <option value="3">Operario</option>
            <option value="4">Asistente</option>
        </select>

        <label for="cargo" class="form-label">Area</label>
        <select class="form-select" id="area" name="area" required>
            <option value="1">Electricidad</option>
            <option value="2">Mantenimiento</option>
            <option value="3">Recusrsos Humanos</option>
            <option value="4">Contabilidad</option>
        </select>

        <label for="fecha" class="form-label">Fecha de Ingreso</label>
        <input type="date" class="form-control" id="fecha" name="fecha" required>

        <label for="salario" class="form-label">Salario</label>
        <input type="number" class="form-control" id="salario" name="salario" required>

        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" id="correo" name="correo" required>

        <label for="tel" class="form-label">Teléfono</label>
        <input type="tel" class="form-control" id="tel" name="tel">

        <label for="pass" class="form-label">Password</label>
        <input type="password" class="form-control" id="pass" name="pass" required>

        <label for="foto" class="form-label">Foto</label>
        <input type="file" class="form-control" id="foto" name="foto">

        <label for="rol" class="form-label">Rol</label>
        <input type="number" class="form-control" id="rol" name="rol" required>
    </div>
</form>`,
showCancelButton: true,
    confirmButtonText: "Agregar",
    cancelButtonText: "Cancelar",
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    preConfirm: () => {
      const form = document.getElementById("formAgregarEmpleado");
      const formData = new FormData(form);
      return $.ajax({
        url: "/PRIMER_PROYECTO_1/agregar.php",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
      }).then((respuesta) => {
        if (!respuesta.success) {
          Swal.showValidationMessage(respuesta.message);
        }
        return respuesta;
      });
    },
  }).then((resultado) => {
    if (resultado.isConfirmed && resultado.value.success) {
      Swal.fire("Exito", resultado.value.message, "success").then(() => {
        location.reload();
      });
    }
  });
});
