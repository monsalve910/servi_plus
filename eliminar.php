<?php
// Proteger la página - requiere autenticación y rol de administrador
require_once './controlador/sesion.php';
verificarAcceso();
requerirAdmin();

require_once './modelo/MYSQL.php';
$mysql = new MySQL();
$mysql->conectar();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    echo "error_id_invalido";
    exit();
}

$id = (int)$_GET["id"];

$check_sql = "SELECT nombre_empleado FROM empleados WHERE id_empleado = $id AND estado = 1";
$check_result = $mysql->efectuarConsulta($check_sql);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    $resultado = $mysql->efectuarConsulta("UPDATE empleados SET estado = 0 WHERE id_empleado = $id");

    if ($resultado) {
        echo "ok";
    } else {
        echo "error_eliminar";
    }
} else {
    echo "error_no_encontrado";
}

$mysql->desconectar();
exit();
?>
