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

        <label for="cargo" class="form-label">rol</label>
        <select class="form-select" id="rol" name="rol" required>
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
        </select>
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
// EDITAR EMPLEADO
function editarEmpleado(IDuser) {
  // Acceder a datos del usuario a editar con AJAX
  $.ajax({
    url: "./datos_editar.php",
    type: "POST",
    data: { IDempleado: IDuser },
    dataType: "json",
    success: function (data) {
      // En caso de que finalize con exito, disparar la alerta
      // El parametro de la funcion permite acceder a cada valor
      // del JSON por medio del operador de objetos '.'
      Swal.fire({
        title: '<span class ="text-success fw-bold"> Editar usuario </span>',
        html: `
           <form action="" method="post" id="formEditarEmpleado" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="nombre" class="form-label fw-bold">Nombre:</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="${
                data.nombre_empleado
              }" required>

              <label for="documento" class="form-label fw-bold mt-2">Documento:</label>
              <input type="text" class="form-control" id="documento" name="documento" value="${
                data.documento
              }" required>

              <label for="cargo" class="form-label fw-bold mt-2">Cargo:</label>
              <select class="form-select" id="cargo" name="cargo" required>
                <option value="1" ${
                  data.cargo == 1 ? "selected" : ""
                }>Técnico</option>
                <option value="2" ${
                  data.cargo == 2 ? "selected" : ""
                }>Administrador</option>
                <option value="3" ${
                  data.cargo == 3 ? "selected" : ""
                }>Operario</option>
                <option value="4" ${
                  data.cargo == 4 ? "selected" : ""
                }>Asistente</option>
              </select>

              <label for="area" class="form-label fw-bold mt-2">Área:</label>
              <select class="form-select" id="area" name="area">
                <option value="1" ${
                  data.area == 1 ? "selected" : ""
                }>Electricidad</option>
                <option value="2" ${
                  data.area == 2 ? "selected" : ""
                }>Mantenimiento</option>
                <option value="3" ${
                  data.area == 3 ? "selected" : ""
                }>Recursos Humanos</option>
                <option value="4" ${
                  data.area == 4 ? "selected" : ""
                }>Contabilidad</option>
              </select>

              <label for="fecha" class="form-label fw-bold mt-2">Fecha de ingreso:</label>
              <input type="date" class="form-control" id="fecha" name="fecha" value="${
                data.fecha_ingreso
              }" required>

              <label for="salario" class="form-label fw-bold mt-2">Salario:</label>
              <input type="number" class="form-control" id="salario" name="salario" value="${
                data.salario
              }" required>

              <label for="correo" class="form-label fw-bold mt-2">Correo:</label>
              <input type="email" class="form-control" id="correo" name="correo" value="${
                data.correo
              }" required>

              <label for="tel" class="form-label fw-bold mt-2">Teléfono:</label>
              <input type="tel" class="form-control" id="tel" name="tel" value="${
                data.telefono
              }">

              <label for="newPass" class="form-label fw-bold mt-2">Nueva contraseña:</label>
              <input type="password" class="form-control" id="newPass" name="newPass">

              <label for="foto" class="form-label fw-bold mt-2">Foto:</label>
              <input type="file" class="form-control" id="foto" name="foto">

              <label for="rol" class="form-label">Rol</label>
              <select class="form-select" id="rol" name="rol" required>
              <option value="1" ${data.rol == 1 ? "selected" : ""}>Administrador</option>
              <option value="2" ${data.rol == 2 ? "selected" : ""}>Usuario</option>
              </select>

              <input type="hidden" id="IDempleado" name="IDempleado" value="${
                data.id_empleado
              }">
            </div>
          </form>
        `,
        showCancelButton: true,
        confirmButtonText: "Guardar",
        cancelButtonText: "Cancelar",
        customClass: {
          confirmButton: "btn btn-success",
          cancelButton: "btn btn-danger",
        },
        // Antes de finalizar la accion, realize esta cuestion
        preConfirm: () => {
          // Acceder a los datos ingresados en el formulario
          const formulario = document.getElementById("formEditarEmpleado");
          const formData = new FormData(formulario);
          // Esperar un retorno de respuesta en JSON por via AJAX
          return $.ajax({
            url: "./editar.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
          }).then((respuesta) => {
            // En caso de que la respuesta retorne false mostrar un mensaje de validacion
            if (!respuesta.success) {
              Swal.showValidationMessage(respuesta.message);
            }
            // Si no retornar la validacion
            return respuesta;
          });
        },
      }).then((resultado) => {
        // Si el resultado es exitoso y confirmado, dispare una alerta de confirmacion
        if (resultado.isConfirmed && resultado.value.success) {
          Swal.fire(
            "Actualizacion completada",
            resultado.value.message,
            "success"
          ).then(() => {
            location.reload();
          });
        }
      });
    },
  });
}
// ELIMINAR EMPLEADO
function eliminarEmpleado(IDusuario, estado) {
  Swal.fire({
    title: '<span class = "text-danger fw-bold"> Eliminar usuario </span>',
    html: "¿Esta seguro de realizar esta accion?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Si, eliminar usuario",
    cancelButtonText: "Cancelar",
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    preConfirm: () => {
      return $.ajax({
        url: "./eliminar.php",
        type: "POST",
        data: {
          id_empleado: IDusuario,
          estado: estado,
        },
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
      Swal.fire(
        "Eliminacion completada",
        resultado.value.message,
        "success"
      ).then(() => {
        location.reload();
      });
    }
  });
}
// Reintegrar empleado
function reintegrarEmpleado(IDusuario, estado) {
  console.log(estado);
  Swal.fire({
    title: "<span class='text-success fw-bold'> Reintegrar empleado </span>",
    html: "¿Esta seguro de reintegrar este empleado?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Si, reintegrar empleado",
    cancelButtonText: "Cancelar",
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
    },
    preConfirm: () => {
      return $.ajax({
        url: "./eliminar.php",
        type: "POST",
        data: {
          id_empleado: IDusuario,
          estado: estado,
        },
        dataType: "json",
      }).then((respuesta) => {
        if (!respuesta.success) {
          Swal.showValidationMessage(respuesta.message);
        }
        return respuesta;
      });
    },
  }).then((result) => {
    if (result.isConfirmed && result.value.success) {
      Swal.fire(
        "Reintegracion completada",
        result.value.message,
        "success"
      ).then(() => {
        location.reload();
      });
    }
  });
}
