<?php
// Proteger la página - requiere autenticación
require_once './controlador/sesion.php';
verificarAcceso();

// Conexión a la base de datos
require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();
//consulta para obtener los datos

$resultado = $mysql->efectuarConsulta("SELECT e.id_empleado, e.nombre_empleado, e.documento, c.cargo AS cargos, d.departamento AS areas, e.fecha_ingreso, e.salario, e.correo, e.telefono, e.foto_empleado FROM empleados e INNER JOIN cargo c ON e.cargo = c.id_cargo INNER JOIN departamento d ON e.area = d.id_departamento WHERE e.estado = 1");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>"ServiPlus S.A."</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
    <style>
        /* Mejora de contraste para tema oscuro */
        .container-fluid.bg-dark { background-color: #0b0b0b !important; }
        .container-fluid .table th, .container-fluid .table td { color: #e9ecef !important; }
        .container-fluid a { color: #f8f9fa; }
        .badge.bg-info { background-color: #17a2b8 !important; color: #000 !important; }
        .btn-outline-light { border-color: rgba(255,255,255,0.2); color: #f8f9fa; }
    </style>
</head>

<body class="text-center">
    <div class="container-fluid mt-5 bg-success text-white">
        <div class="row text-center">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <?php if (isset($_SESSION['nombre'])): ?>
                        <span class="badge bg-info text-dark">Usuario: <?php echo $_SESSION['nombre']; ?> 
                        (<?php echo esAdministrador() ? 'Administrador' : 'Usuario'; ?>)</span>
                    <?php endif; ?>
                </div>
                <h1 class="flex-grow-1">Datos de los Empleados</h1>
                <div>
                    <a href="logout.php" class="btn btn-danger btn-sm">Cerrar Sesión</a>
                </div>
            </div>

            <?php
            // Mostrar mensajes de éxito o error
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
                        <a class="btn btn-success fw-bold" href="./agregar.php">Agregar</a>
                        <a class="btn btn-warning text-white fw-bold" href="./gestion_roles.php">Gestionar Roles</a>
                        <a class="btn btn-info text-white fw-bold" href="./verificar_permisos.php">Ver Permisos</a>
                    </div>
                    <div class="btn-group me-2" role="group">
                        <a class="btn btn-info text-white fw-bold" href="./grafico.html">Dashboard</a>
                        <a class="btn btn-dark text-white fw-bold" href="./reportes.php">Reportes</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <strong>Permisos limitados:</strong> Solo puedes ver la lista de empleados. Los administradores pueden agregar, editar y eliminar empleados.
                    <a href="./verificar_permisos.php" class="btn btn-sm btn-outline-light ms-2">Ver mis permisos</a>
                </div>
            <?php endif; ?>

            <table class="table table-hover table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Nombre Empleado</th>
                    <th>Documento</th>
                    <th>Cargo</th>
                    <th>Area o Departamento</th>
                    <th>Fecha de Ingreso</th>
                    <th>Salario</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr class="selectable-row" data-id="<?php echo $fila['id_empleado']; ?>">
                        <td><?php echo $fila['id_empleado']; ?></td>
                        <td><?php echo $fila['nombre_empleado']; ?></td>
                        <td><?php echo $fila['documento']; ?></td>
                        <td><?php echo $fila['cargos']; ?></td>
                        <td><?php echo $fila['areas']; ?></td>
                        <td><?php echo $fila['fecha_ingreso']; ?></td>
                        <td><?php echo $fila['salario']; ?></td>
                        <td><?php echo $fila['correo']; ?></td>
                        <td><?php echo $fila['telefono']; ?></td>
                        <td><img src="./assets/fotos_empleados/<?= $fila['foto_empleado'] ?>"
                                                alt="Foto empleado"
                                                width="70"
                                                class="img-thumbnail"></td>
                        <td>
                            <?php if (esAdministrador()): ?>
                                <!-- Editar removido; usar doble clic en la fila para editar -->
                                 <a class="text-decoration-none mb-3 btn btn-success text-white fw-bold" href="./editar.php?id=<?php echo $fila['id_empleado']; ?>">Editar</a>
                                <a class="text-decoration-none mb-3 btn btn-danger text-white fw-bold eliminar-btn" href="./eliminar.php?id=<?php echo $fila['id_empleado']; ?>">Eliminar</a>
                            <?php else: ?>
                                <span class="text-muted">Solo administradores pueden editar/eliminar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Manejo de selección de fila y doble clic para editar
        (function(){
            const rows = document.querySelectorAll('.selectable-row');
            rows.forEach(r => {
                r.addEventListener('click', function(){
                    rows.forEach(x => x.classList.remove('table-primary'));
                    this.classList.add('table-primary');
                });
                r.addEventListener('dblclick', function(){
                    const id = this.getAttribute('data-id');
                    if (id) window.location.href = './editar.php?id=' + encodeURIComponent(id);
                });
            });
        })();

        // Confirmación personalizada para eliminar
        (function(){
            const botones = document.querySelectorAll('.eliminar-btn');
            botones.forEach(b => {
                b.addEventListener('click', function(e){
                    e.preventDefault();
                    const href = this.getAttribute('href');
                    if (confirm('¿SEGURO QUE DESEA ELIMINAR ESTE USUARIO_')) {
                        window.location.href = href;
                    }
                });
            });
        })();
    </script>
</body>

</html>

