<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

// Conexión a la base de datos
require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

// Procesar cambios de rol
if (isset($_POST['cambiar_rol'])) {
    $id_empleado = (int)$_POST['id_empleado'];
    $nuevo_rol = (int)$_POST['nuevo_rol'];
    
    if ($nuevo_rol >= 1 && $nuevo_rol <= 2) {
        $resultado = $mysql->efectuarConsulta("UPDATE empleados SET rol = $nuevo_rol WHERE id_empleado = $id_empleado");
        
        if ($resultado) {
            $mensaje = "Rol actualizado correctamente.";
            $tipo_mensaje = "success";
        } else {
            $mensaje = "Error al actualizar el rol.";
            $tipo_mensaje = "danger";
        }
    }
}

// Obtener lista de empleados con sus roles
$sql = "SELECT id_empleado, nombre_empleado, documento, rol FROM empleados ORDER BY nombre_empleado";
$resultado = $mysql->efectuarConsulta($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Gestión de Roles de Usuario</h1>
                
                <?php if (isset($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h5>Empleados y sus Roles</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Documento</th>
                                        <th>Rol Actual</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                                        <tr>
                                            <td><?php echo $fila['id_empleado']; ?></td>
                                            <td><?php echo $fila['nombre_empleado']; ?></td>
                                            <td><?php echo $fila['documento']; ?></td>
                                            <td>
                                                <span class="badge <?php echo $fila['rol'] == 1 ? 'bg-danger' : 'bg-secondary'; ?>">
                                                    <?php echo $fila['rol'] == 1 ? 'Administrador' : 'Usuario'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="id_empleado" value="<?php echo $fila['id_empleado']; ?>">
                                                    <select name="nuevo_rol" class="form-select form-select-sm d-inline" style="width: auto;">
                                                        <option value="1" <?php echo $fila['rol'] == 1 ? 'selected' : ''; ?>>Administrador</option>
                                                        <option value="2" <?php echo $fila['rol'] == 2 ? 'selected' : ''; ?>>Usuario</option>
                                                    </select>
                                                    <button type="submit" name="cambiar_rol" class="btn btn-primary btn-sm">Cambiar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-secondary">Volver al Inicio</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$mysql->desconectar();
?>
