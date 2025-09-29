<?php
require_once './controlador/sesion.php';
verificarAcceso();

require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

$resultado = $mysql->efectuarConsulta("
    SELECT e.id_empleado, e.nombre_empleado, e.documento, 
           c.cargo AS cargos, d.departamento AS areas, 
           e.fecha_ingreso, e.salario, e.correo, e.telefono, 
           e.foto_empleado 
    FROM empleados e 
    INNER JOIN cargo c ON e.cargo = c.id_cargo 
    INNER JOIN departamento d ON e.area = d.id_departamento 
    WHERE e.estado = 1
");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>"ServiPlus S.A."</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="text-center">
    <div class="container-fluid mt-5 text-white" style="background-color: #008000;">
        <div class="row text-center">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <span class="badge text-white" style="background-color: #007bff;">Usuario: <?php echo $_SESSION['nombre']; ?>
                            (<?php echo esAdministrador() ? 'Administrador' : 'Usuario'; ?>)</span>
                    <?php endif; ?>
                </div>
                <h1 class="flex-grow-1" style="color:#212529">Datos de los Empleados</h1>
                <div>
                    <a href="logout.php" class="btn btn-sm text-white" style="background-color: #dc3545;">Cerrar Sesión</a>
                </div>
            </div>

            <?php
            // Mensajes de éxito o error
            if (isset($_GET['msg'])) {
                switch ($_GET['msg']) {
                    case 'success':
                        echo '<div class="alert alert-success">Empleado agregado correctamente.</div>';
                        break;
                    case 'actualizado':
                        echo '<div class="alert alert-success">Empleado actualizado correctamente.</div>';
                        break;
                    case 'eliminado':
                        echo '<div class="alert alert-success">Empleado eliminado correctamente.</div>';
                        break;
                }
            }

            if (isset($_GET['error'])) {
                switch ($_GET['error']) {
                    case 'acceso_denegado':
                        echo '<div class="alert alert-danger">Acceso denegado. Solo los administradores pueden realizar esta acción.</div>';
                        break;
                    case 'id_invalido':
                        echo '<div class="alert alert-danger">ID de empleado inválido.</div>';
                        break;
                    case 'eliminar_fallido':
                        echo '<div class="alert alert-danger">Error al eliminar el empleado.</div>';
                        break;
                    case 'actualizar_fallido':
                        echo '<div class="alert alert-danger">Error al actualizar el empleado.</div>';
                        break;
                    case 'empleado_no_encontrado':
                        echo '<div class="alert alert-danger">Empleado no encontrado.</div>';
                        break;
                }
            }
            ?>

            <?php if (esAdministrador()): ?>
                <div class="btn-toolbar mb-3" role="toolbar">
                    <div class="btn-group me-2" role="group">
                        <a class="btn fw-bold text-white" style="background-color: #28a745;" href="./agregar.php" id="agregar-btn">Agregar</a>
                        <a class="btn text-white fw-bold" style="background-color: #ffc107;" href="./gestion_roles.php">Gestionar Roles</a>
                        <a class="btn text-white fw-bold" style="background-color: #007bff;" href="./verificar_permisos.php">Ver Permisos</a>
                    </div>
                    <div class="btn-group me-2" role="group">
                        <a class="btn text-white fw-bold" style="background-color: #007bff;" href="./grafico.html">Dashboard</a>
                        <a class="btn text-white fw-bold" style="background-color: #007bff;" href="./reportes.php">Reportes</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Permisos limitados:</strong> Solo puedes ver la lista de empleados.
                    <a href="./verificar_permisos.php" class="btn btn-sm btn-outline-light ms-2">Ver mis permisos</a>
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="tablaEmpleados" class="table table-hover text-center display nowrap responsive" style="width:100%; background-color: #f8f9fa; color:#212529;">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Empleado</th>
                            <th>Documento</th>
                            <th>Cargo</th>
                            <th>Área</th>
                            <th>Ingreso</th>
                            <th>Salario</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Foto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr data-id="<?php echo $fila['id_empleado']; ?>">
                                <td><?= $fila['id_empleado'] ?></td>
                                <td><?= $fila['nombre_empleado'] ?></td>
                                <td><?= $fila['documento'] ?></td>
                                <td><?= $fila['cargos'] ?></td>
                                <td><?= $fila['areas'] ?></td>
                                <td><?= $fila['fecha_ingreso'] ?></td>
                                <td><?= $fila['salario'] ?></td>
                                <td><?= $fila['correo'] ?></td>
                                <td><?= $fila['telefono'] ?></td>
                                <td><img src="./assets/fotos_empleados/<?= $fila['foto_empleado'] ?>" width="70" class="img-thumbnail"></td>
                                <td>
                                    <?php if (esAdministrador()): ?>
                                        <a href="./editar.php?id=<?= $fila['id_empleado'] ?>" class="btn btn-sm text-white fw-bold editar-btn" style="background-color: #28a745;">Editar</a>
                                        <a href="./eliminar.php?id=<?= $fila['id_empleado'] ?>" class="btn btn-sm text-white fw-bold eliminar-btn" style="background-color: #dc3545;">Eliminar</a>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <strong>Permisos limitados:</strong> Solo puedes ver la lista de empleados.
                                            <a href="./verificar_permisos.php" class="btn btn-sm btn-outline-light ms-2">Ver mis permisos</a>
                                        </div>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#tablaEmpleados').DataTable({
                    dom: 'Bfrtip',
                    pageLength: 10,
                    responsive: true,
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });
            });

            // --- ELIMINAR ---
            document.querySelectorAll('.eliminar-btn').forEach(b => {
                b.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esta acción!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(href)
                                .then(r => r.text())
                                .then(res => {
                                    Swal.fire('Eliminado', 'El empleado fue eliminado.', 'success')
                                        .then(() => location.reload());
                                })
                                .catch(() => {
                                    Swal.fire('Error', 'No se pudo eliminar el empleado.', 'error');
                                });
                        }
                    });
                });
            });

            // --- EDITAR ---
            document.querySelectorAll('.editar-btn').forEach(b => {
                b.addEventListener('click', function(e) {
                    e.preventDefault();
                    const href = this.getAttribute('href');

                    fetch(href)
                        .then(response => response.text())
                        .then(html => {
                            Swal.fire({
                                title: 'Editar Empleado',
                                html: html,
                                showCancelButton: true,
                                confirmButtonText: 'Guardar cambios',
                                cancelButtonText: 'Cancelar',
                                width: '800px',
                                preConfirm: () => {
                                    const form = document.querySelector('#formEditarEmpleado');
                                    const formData = new FormData(form);

                                    return fetch(href, {
                                            method: 'POST',
                                            body: formData
                                        })
                                        .then(r => r.text())
                                        .then(data => {
                                            if (data === "ok") {
                                                Swal.fire('Actualizado', 'Empleado actualizado con éxito', 'success')
                                                    .then(() => location.reload());
                                            } else {
                                                Swal.showValidationMessage('Error al actualizar');
                                            }
                                        });
                                }
                            });
                        });
                });
            });
            // --- AGREGAR ---
            document.querySelector('#agregar-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');

                fetch(href)
                    .then(response => response.text())
                    .then(html => {
                        Swal.fire({
                            title: 'Agregar Empleado',
                            html: html,
                            showCancelButton: true,
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            width: '800px',
                            preConfirm: () => {
                                const form = document.querySelector('form'); // tu form de agregar.php
                                const formData = new FormData(form);

                                return fetch(href, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(r => r.text())
                                    .then(data => {
                                        if (data.trim() === "ok") {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Empleado agregado',
                                                text: 'El empleado fue registrado correctamente',
                                                timer: 2000,
                                                showConfirmButton: false
                                            }).then(() => location.reload());
                                        } else {
                                            Swal.showValidationMessage('Error: ' + data);
                                        }
                                    })
                                    .catch(() => {
                                        Swal.showValidationMessage('Error en el servidor');
                                    });
                            }
                        });
                    });
            });
        </script>
</body>

</html>