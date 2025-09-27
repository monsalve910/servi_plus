<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Empleado</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css" />
</head>

<body>
    <div class="container mt-5 bg-success text-white"">
        <div class=" row">
        <h1 class="text-center">Agregar Nuevo Empleado</h1>
        <form action="#" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre Empleado</label>
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingresa el nombre del empleado">
                <label for="documento" class="form-label">Documento del empleado</label>
                <input type="text" class="form-control mb-3" id="documento" name="documento" placeholder="Ingresa el documento del empleado">
                <select class="form-select" aria-label="Default select example" name="cargo">
                    <option value="" selected>Cargo</option>
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
                    <input type="radio" class="form-check-input" name="area" value="2" id="mantenimiento" required>
                    <label for="mantenimiento" class="form-check-label">Mantenimiento</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="3" id="rrhh" required>
                    <label for="rrhh" class="form-check-label">Recursos Humanos</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" name="area" value="4" id="contabilidad" required>
                    <label for="contabilidad" class="form-check-label">Contabilidad</label>
                </div>
                <label for="fecha" class="form-label">Fecha de Ingreso</label>
                <input type="date" class="form-control" id="fecha" name="fecha" placeholder="" required>
                <label for="salario" class="form-label">Salario</label>
                <input type="number" class="form-control" id="salario" name="salario" placeholder="Ingresa el salario del empleado">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingresa el correo del empleado">
                <label for="telefono" class="form-label">Telefono del empleado</label>
                <input type="tel" class="form-control" id="tel" name="tel" placeholder="Ingresa el telefono del empleado">
                <label for="nombre" class="form-label">Password Empleado</label>
                <input type="password" class="form-control" id="pass" name="pass" placeholder="Ingresa el password del empleado">
                <label for="nombre" class="form-label">Foto del Empleado</label>
                <input type="file" class="form-control" id="foto" name="foto" placeholder="Ingresa la foto del empleado">
                <label for="rol" class="form-label">Rol</label>
                <input type="number" class="form-control" id="rol" name="rol" placeholder="Ingresa el rol del empleado">
                <br>
                <button type="submit" class="btn btn-primary">Agregar</button>
            </div>
        </form>
        <a class="text-decoration-none mb-3 text-white fw-bold" href="index.php">Volver al Listado</a>
    </div>
    </div>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
require_once './modelo/MYSQL.php';
require_once './controlador/validardatos.php';

$mysql = new MySQL();
$mysql->conectar();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errores = [];

    // Validaciones
    if ($e = Validador::validarNombre($_POST["nombre"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarDocumento($_POST["documento"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarCargo($_POST["cargo"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarArea($_POST["area"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarFecha($_POST["fecha"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarSalario($_POST["salario"] ?? "")) $errores[] = $e;
    if ($e = Validador::validarCorreo($_POST["correo"] ?? "")) $errores[] = $e;
    // Validación de teléfono - mostrar detalles para depuración
    $tel = $_POST["tel"] ?? "";
    if ($e = Validador::validarTelefono($tel)) {
        $errores[] = $e;
        // Información adicional para depuración
        $errores[] = "Teléfono ingresado: '$tel', solo dígitos: '" . preg_replace('/[^0-9]/', '', $tel) . "', cantidad de dígitos: " . strlen(preg_replace('/[^0-9]/', '', $tel));
    }
    
    // Validación de contraseña - mostrar detalles para depuración
    $pass = $_POST["pass"] ?? "";
    if ($e = Validador::validarPassword($pass)) {
        $errores[] = $e;
        // Información adicional para depuración
        $tieneLetras = preg_match('/[A-Za-z]/', $pass) ? "Sí" : "No";
        $tieneNumeros = preg_match('/[0-9]/', $pass) ? "Sí" : "No";
        $errores[] = "Contraseña: longitud: " . strlen($pass) . ", tiene letras: $tieneLetras, tiene números: $tieneNumeros";
    }
   

    if (!empty($errores)) {
        echo "<div class='alert alert-warning text-center mt-3'><h3> Errores encontrados:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul></div>";
        // No salir, permitir que la página se siga mostrando con los errores
    }
    $nombre    = Validador::limpiar($_POST["nombre"]);
    $documento = Validador::limpiar($_POST["documento"]);
    $cargo     = Validador::limpiar($_POST["cargo"]);
    $area      = Validador::limpiar($_POST["area"]);
    $fecha     = Validador::limpiar($_POST["fecha"]);
    $salario   = Validador::limpiar($_POST["salario"]);
    $correo    = Validador::limpiar($_POST["correo"]);
    $telefono  = Validador::limpiar($_POST["tel"]);
    $password  = Validador::limpiar($_POST["pass"]);
    $rol=$_POST["rol"];
    
    // Validar documento único en BD
    $check = $mysql->efectuarConsulta("SELECT COUNT(*) AS total FROM empleados WHERE documento = '$documento'");
    if ($row = mysqli_fetch_assoc($check)) {
        if ($row["total"] > 0) {
            echo "<script>alert('El documento ya está registrado')</script>";
            $mysql->desconectar();
            exit;
        }
    }
    $check1 = $mysql->efectuarConsulta("SELECT COUNT(*) AS total FROM empleados WHERE correo = '$correo'");
    if ($row = mysqli_fetch_assoc($check1)) {
        if ($row["total"] > 0) {
            echo "<script>alert('El correo ya está registrado')</script>";
            $mysql->desconectar();
            exit;
        }
    }
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $nombreNuevo = null; // nombre de archivo final de la foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {

        $tipo_archivoPermito = ['image/jpg', 'image/jpeg', 'image/png'];
    $informacionFoto = finfo_open(FILEINFO_MIME_TYPE);
    $tipoArchivo = finfo_file($informacionFoto, $_FILES['foto']['tmp_name']);
        if (!in_array($tipoArchivo, $tipo_archivoPermito)) {
            echo "<script>alert('Tipo de archivo no permitido');</script>";
            exit;
        }
    finfo_close($informacionFoto);

        $extencion = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $tiempo = microtime(true);
        $microSegundos = sprintf("%06d", ($tiempo - floor($tiempo)) * 1000000);
        $nombreNuevo = date('YmdHis') . $microSegundos . '.' . $extencion;

        $destino = './assets/fotos_empleados/' . $nombreNuevo;
    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            echo "<script>alert('Error al guardar la imagen');</script>";
        }
    }

    // Insertar datos
    $consulta = "
        INSERT INTO empleados 
        (nombre_empleado, documento, cargo, area, fecha_ingreso, salario, correo, telefono, pass, foto_empleado,rol) 
        VALUES 
        ('$nombre', '$documento', '$cargo', '$area', '$fecha', '$salario', '$correo', '$telefono', '$passwordHash', '$nombreNuevo','$rol')
    ";
    
    echo "<div class='alert alert-info'>Ejecutando consulta: " . htmlspecialchars($consulta) . "</div>";
    
    $resultado = $mysql->efectuarConsulta($consulta);
    
    // El mensaje de error ya se muestra en el método efectuarConsulta

    $mysql->desconectar();

    if ($resultado) {
        echo "<div class='alert alert-success'>Empleado registrado correctamente.</div>";
        echo "<script>
            setTimeout(function() {
                window.location.href = 'index.php?msg=success';
            }, 2000);
        </script>";
    } else {
        echo "<div class='alert alert-danger'>Error al registrar empleado.</div>";
    }
}
?>