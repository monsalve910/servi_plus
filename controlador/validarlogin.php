<?php
session_start();
require_once '../modelo/MYSQL.php';

$documento = trim($_POST['documento'] ?? '');
$pass = $_POST['pass'] ?? '';

if (empty($documento) || empty($pass)) {
    echo "error:campos_vacios";
    exit;
}

$mysql = new MySQL();
$mysql->conectar();

$documento = mysqli_real_escape_string($mysql->getConnection(), $documento);
$sql = "SELECT id_empleado, nombre_empleado, documento, pass, rol 
        FROM empleados 
        WHERE documento = '$documento' LIMIT 1";
$result = $mysql->efectuarConsulta($sql);

if ($result && mysqli_num_rows($result) > 0) {
    $fila = mysqli_fetch_assoc($result);

    if (!empty($fila['pass']) && password_verify($pass, $fila['pass'])) {
        $_SESSION['user_id'] = $fila['id_empleado'];
        $_SESSION['nombre'] = $fila['nombre_empleado'];
        $_SESSION['documento'] = $fila['documento'];
        $_SESSION['rol'] = $fila['rol'];
        $_SESSION['acceso'] = true;
        $_SESSION['login_time'] = time();

        echo "ok";
    } else {
        echo "error:password";
    }
} else {
    echo "error:usuario";
}

$mysql->desconectar();
?>



