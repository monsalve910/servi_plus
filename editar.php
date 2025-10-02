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
    echo "error_id_invalido";
    exit();
}

$id = $_GET["id"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar y validar datos
    $nombre = trim($_POST["nombre"]);
    $documento =trim($_POST["documento"]);
    $cargo = (int)$_POST["cargo"];
    $area = (int)$_POST["area"];
    $fecha = $_POST["fecha"];
    $salario = (int)$_POST["salario"];
    $correo = trim($_POST["correo"]);
    $telefono =trim($_POST["tel"]);
    $password = trim($_POST["pass"]);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $resultado = $mysql->efectuarConsulta("
        UPDATE empleados 
        SET nombre_empleado='$nombre', documento='$documento', cargo='$cargo', area='$area', 
            fecha_ingreso='$fecha', salario=$salario, correo='$correo', telefono='$telefono',pass='$passwordHash' 
        WHERE id_empleado=$id
    ");
    
    if ($resultado) {
        echo "ok"; // <- Para AJAX
    } else {
        echo "error_actualizar"; // <- Para AJAX
    }
    exit();
}
?>

<!-- FORMULARIO PARA SWEETALERT -->
<form id="formEditarEmpleado">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre Empleado</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>

    <div class="mb-3">
        <label for="documento" class="form-label">Documento</label>
        <input type="text" class="form-control" id="documento" name="documento" required>
    </div>

    <div class="mb-3">
        <label for="cargo" class="form-label">Cargo</label>
        <select class="form-select" name="cargo" id="cargo" required>
            <option value="1">Técnico</option>
            <option value="2">Administrador</option>
            <option value="3">Operario</option>
            <option value="4">Asistente</option>
        </select>
    </div>

    <div class="mb-3">
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
    </div>

    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha de Ingreso</label>
        <input type="date" class="form-control" id="fecha" name="fecha" required>
    </div>

    <div class="mb-3">
        <label for="salario" class="form-label">Salario</label>
        <input type="number" class="form-control" id="salario" name="salario" required>
    </div>

    <div class="mb-3">
        <label for="correo" class="form-label">Correo</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
    </div>

    <div class="mb-3">
        <label for="tel" class="form-label">Teléfono</label>
        <input type="tel" class="form-control" id="tel" name="tel" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="pass" name="pass" required>
    </div>
</form>
