<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

// Conexión a la base de datos
require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

// Verificar que se recibió un ID válido
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php?error=id_invalido");
    exit();
}

$id = (int)$_GET["id"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar datos
    $nombre = mysqli_real_escape_string($mysql->getConnection(), trim($_POST["nombre"]));
    $documento = mysqli_real_escape_string($mysql->getConnection(), trim($_POST["documento"]));
    $cargo = (int)$_POST["cargo"];
    $area = (int)$_POST["area"];
    $fecha = mysqli_real_escape_string($mysql->getConnection(), $_POST["fecha"]);
    $salario = (int)$_POST["salario"];
    $correo = mysqli_real_escape_string($mysql->getConnection(), trim($_POST["correo"]));
    $telefono = mysqli_real_escape_string($mysql->getConnection(), trim($_POST["tel"]));
    
    $resultado = $mysql->efectuarConsulta("UPDATE empleados SET nombre_empleado='$nombre', documento='$documento', cargo='$cargo', area='$area', fecha_ingreso='$fecha', salario=$salario, correo='$correo', telefono='$telefono' WHERE id_empleado=$id");
    
    if ($resultado) {
        header('Location: index.php?msg=actualizado');
    } else {
        header('Location: index.php?error=actualizar_fallido');
    }
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
</head>

<body>
    <div class="container mt-5 bg-success text-white"">
        <div class=" row">
        <h1 class="text-center">Agregar Nuevo Empleado</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Empleado</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre del empleado">
                <label for="documento" class="form-label">Documento del empleado</label>
                <input type="text" class="form-control mb-3" id="documento" name="documento" placeholder="Ingresa el documento del empleado">
                <select class="form-select" aria-label="Default select example" name="cargo">
                    <option selected>Cargo</option>
                    <option value="1">Tecnico</option>
                    <option value="2">Administrador</option>
                    <option value="3">Operario</option>
                    <option value="4">Asistente</option>
                </select>
                <label class="form-label">Area</label><br>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="1" id="electricidad" required>
                    <label for="electricidad" class="form-check-label">Electricidad</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="2" id="mantenimiento">
                    <label for="mantenimiento" class="form-check-label">Mantenimiento</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="3" id="rrhh">
                    <label for="rrhh" class="form-check-label">Recursos Humanos</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="4" id="contabilidad">
                    <label for="contabilidad" class="form-check-label">Contabilidad</label>
                </div>
                <label for="fecha" class="form-label">Fecha de Ingreso</label>
                <input type="date" class="form-control" id="fecha" name="fecha" placeholder="">
                <label for="salario" class="form-label">Salario</label>
                <input type="number" class="form-control" id="salario" name="salario" placeholder="Ingresa el salario del empleado">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa el correo del empleado">
                <label for="telefono" class="form-label">Telefono del empleado</label>
                <input type="tel" class="form-control" id="tel" name="tel" placeholder="Ingresa el telefono del empleado">
                <br>
                <button type="submit" class="btn btn-primary">Editar</button>
            </div>
        </form>
        <a class="text-decoration-none mb-3 text-white fw-bold" href="index.php">Volver al Listado</a>
    </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>