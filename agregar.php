<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

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

    $tel = $_POST["tel"] ?? "";
    if ($e = Validador::validarTelefono($tel)) $errores[] = $e;

    $pass = $_POST["pass"] ?? "";
    if ($e = Validador::validarPassword($pass)) $errores[] = $e;

    if (!empty($errores)) {
        echo "error: " . implode(", ", $errores);
        exit;
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
    $rol       = $_POST["rol"];

    // Validar duplicados
    $check = $mysql->efectuarConsulta("SELECT COUNT(*) AS total FROM empleados WHERE documento = '$documento'");
    if ($row = mysqli_fetch_assoc($check)) {
        if ($row["total"] > 0) {
            echo "error: El documento ya está registrado";
            exit;
        }
    }

    $check1 = $mysql->efectuarConsulta("SELECT COUNT(*) AS total FROM empleados WHERE correo = '$correo'");
    if ($row = mysqli_fetch_assoc($check1)) {
        if ($row["total"] > 0) {
            echo "error: El correo ya está registrado";
            exit;
        }
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $nombreNuevo = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $permitidos = ['image/jpg', 'image/jpeg', 'image/png'];
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $tipoArchivo = finfo_file($info, $_FILES['foto']['tmp_name']);
        finfo_close($info);

        if (!in_array($tipoArchivo, $permitidos)) {
            echo "error: Tipo de archivo no permitido";
            exit;
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $nombreNuevo = uniqid() . '.' . $ext;
        $destino = './assets/fotos_empleados/' . $nombreNuevo;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            echo "error: Error al guardar la imagen";
            exit;
        }
    }

    // Insertar en BD
    $consulta = "
        INSERT INTO empleados 
        (nombre_empleado, documento, cargo, area, fecha_ingreso, salario, correo, telefono, pass, foto_empleado, rol, estado) 
        VALUES 
        ('$nombre', '$documento', '$cargo', '$area', '$fecha', '$salario', '$correo', '$telefono', '$passwordHash', '$nombreNuevo', '$rol', 1)
    ";

    $resultado = $mysql->efectuarConsulta($consulta);
    $mysql->desconectar();

    if ($resultado) {
        echo "ok";
    } else {
        echo "error: Error al registrar empleado";
    }
    exit;
}

// --- Si no es POST → mostrar el formulario ---
?>
<form id="formAgregarEmpleado" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre Empleado</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
        
        <label for="documento" class="form-label">Documento</label>
        <input type="text" class="form-control mb-3" id="documento" name="documento" required>
        
        <label for="cargo" class="form-label">Cargo</label>
        <select class="form-select" name="cargo" required>
            <option value="" selected>Seleccione un cargo</option>
            <option value="1">Técnico</option>
            <option value="2">Administrador</option>
            <option value="3">Operario</option>
            <option value="4">Asistente</option>
        </select>

        <label class="form-label">Área</label><br>
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
</form>



