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
           e.foto_empleado,e.estado
    FROM empleados e 
    INNER JOIN cargo c ON e.cargo = c.id_cargo 
    INNER JOIN departamento d ON e.area = d.id_departamento;
");
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                <h1 class="flex-grow-1 text-white">Datos de los Empleados</h1>
                <div>
                    <a href="logout.php" class="btn btn-sm text-white" style="background-color: #dc3545;">Cerrar Sesión</a>
                </div>
            </div>

            <?php if (esAdministrador()): ?>
                <div class="btn-toolbar mb-3 justify-content-center">
                    <div>
                        <button class="btn fw-bold text-white" style="background-color: #28a745;" id="btnAgregar">Agregar</button>
                        <a class="btn text-white fw-bold" style="background-color: #007bff;" href="./grafico.html">Graficos</a>
                        <a class="btn text-white fw-bold" style="background-color: #007bff;" href="./reportes.php">Reportes</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Permisos limitados:</strong> Solo puedes ver la lista de empleados.
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="tablaEmpleados" class="table table-hover text-center display nowrap responsive" style="width:100%; background-color: #f8f9fa; color:#212529;">
                    <thead>
                        <tr>
                            <th>Nombre Empleado</th>
                            <th>Documento</th>
                            <th>Cargo</th>
                            <th>Área</th>
                            <th>Ingreso</th>
                            <th>Salario</th>
                            <th>Correo</th>
                            <th>Teléfono</th>
                            <th>Foto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr data-id="<?php echo $fila['id_empleado']; ?>">
                                <td><?= $fila['nombre_empleado'] ?></td>
                                <td><?= $fila['documento'] ?></td>
                                <td><?= $fila['cargos'] ?></td>
                                <td><?= $fila['areas'] ?></td>
                                <td><?= $fila['fecha_ingreso'] ?></td>
                                <td><?= $fila['salario'] ?></td>
                                <td><?= $fila['correo'] ?></td>
                                <td><?= $fila['telefono'] ?></td>
                                <td><img src="./assets/fotos_empleados/<?= $fila['foto_empleado'] ?>" width="70" class="img-thumbnail"></td>
                                <td><?= $fila['estado'] ?></td>
                                <td>
                                    <?php if (esAdministrador()): ?>
                                        <?php if ($fila["estado"] == "Activo"): ?>
                                            <button
                                                class="btn btn-sm text-white fw-bold editar-btn"
                                                style="background-color: #28a745;"
                                                onclick="editarEmpleado(<?php echo $fila['id_empleado']; ?>)">
                                                Editar
                                            </button>

                                            <button
                                                class="btn btn-sm text-white fw-bold eliminar-btn"
                                                style="background-color: #dc3545;"
                                                onclick="eliminarEmpleado(<?php echo $fila['id_empleado']; ?>, '<?php echo $fila['estado']; ?>')"
                                                data-id="<?php echo $fila['id_empleado']; ?>">
                                                Eliminar
                                            </button>
                                        <?php else: ?>
                                            <button
                                                class="btn btn-sm text-white fw-bold reintegrar-btn"
                                                style="background-color: #ffc107;"
                                                onclick="reintegrarEmpleado(<?php echo $fila['id_empleado']; ?>, '<?php echo $fila['estado']; ?>')"
                                                data-id="<?php echo $fila['id_empleado']; ?>">
                                                Reintegrar
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <strong>Permisos limitados:</strong> Solo puedes ver la lista de empleados.
                                        </div>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="./public/JS/gestion_empleados.js"></script>
        <script>
            $(document).ready(function() {
                $('#tablaEmpleados').DataTable({
                    responsive: true,
                    dom: 'Bfrtip',
                });
            });
        </script>

</body>

</html>